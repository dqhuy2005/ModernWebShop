<?php

namespace App\Repositories\Eloquent;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductRepository implements ProductRepositoryInterface
{
    public function __construct(
        private Product $model
    ) {}

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model
            ->select('products.id', 'products.name', 'products.slug', 'products.price', 'products.currency', 'products.category_id', 'products.status', 'products.is_hot', 'products.views', 'products.created_at', 'products.updated_at');

        $this->applyFilters($query, $filters);

        if (!empty($filters['sort_by']) && $filters['sort_by'] === 'category_id') {
            $query->leftJoin('categories', 'products.category_id', '=', 'categories.id')
                ->select('products.*', 'categories.name as category_name')
                ->orderBy('categories.name', $filters['sort_order'] ?? 'desc');
        } elseif (!empty($filters['sort_by'])) {
            $sortOrder = $filters['sort_order'] ?? 'desc';
            $query->orderBy($filters['sort_by'], $sortOrder);
        } else {
            $query->orderBy('id', 'desc');
        }

        return $query->with('category:id,name,slug')->paginate($perPage);
    }

    public function find(int $id): ?Product
    {
        return $this->model->with(['category', 'images'])->find($id);
    }

    public function findWithTrashed(int $id): ?Product
    {
        return $this->model->withTrashed()->with(['images'])->find($id);
    }

    public function findBySlug(string $slug): ?Product
    {
        return $this->model->where('slug', $slug)->first();
    }

    public function findBySlugWithRelations(string $slug): ?Product
    {
        return $this->model
            ->where('slug', $slug)
            ->with(['category', 'images', 'approvedReviews'])
            ->first();
    }

    public function create(array $data): Product
    {
        return $this->model->create($data);
    }

    public function update(Product $product, array $data): bool
    {
        return $product->update($data);
    }

    public function delete(Product $product): bool
    {
        return $product->delete();
    }

    public function restore(int $id): bool
    {
        $product = $this->model->withTrashed()->find($id);

        if (!$product) {
            return false;
        }

        return $product->restore();
    }

    public function forceDelete(int $id): bool
    {
        $product = $this->model->withTrashed()->find($id);

        if (!$product) {
            return false;
        }

        return $product->forceDelete();
    }

    public function toggleHotStatus(Product $product): bool
    {
        $product->is_hot = !$product->is_hot;
        return $product->save();
    }

    public function toggleStatus(Product $product): bool
    {
        $product->status = !$product->status;
        return $product->save();
    }

    public function getFilteredProducts(int $categoryId, array $filters = [])
    {
        $query = $this->model
            ->select('products.id', 'products.name', 'products.slug', 'products.price', 'products.category_id', 'products.status', 'products.is_hot', 'products.views', 'products.specifications', 'products.created_at')
            ->where('products.status', true)
            ->where('products.category_id', $categoryId)
            ->with(['category:id,name,slug', 'images:id,product_id,path,sort_order']);

        if (!empty($filters['price_range'])) {
            $query = $this->filterByPrice($query, $filters['price_range']);
        }

        return $query;
    }

    public function getNewProducts(int $limit = 8): Collection
    {
        return $this->model
            ->select('id', 'name', 'slug', 'price', 'category_id', 'status', 'is_hot', 'views', 'created_at')
            ->where('status', true)
            ->with(['category:id,name,slug', 'images:id,product_id,path,sort_order'])
            ->latest('created_at')
            ->limit($limit)
            ->get();
    }

    public function getHotDeals(int $limit = 8): Collection
    {
        return $this->model
            ->select('id', 'name', 'slug', 'price', 'category_id', 'status', 'is_hot', 'views', 'created_at')
            ->where('status', true)
            ->where('is_hot', true)
            ->with(['category:id,name,slug', 'images:id,product_id,path,sort_order'])
            ->limit($limit)
            ->get();
    }

    public function getPaginatedHotDeals(int $perPage = 12): LengthAwarePaginator
    {
        return $this->model
            ->select('id', 'name', 'slug', 'price', 'category_id', 'status', 'is_hot', 'views', 'created_at')
            ->where('status', true)
            ->where('is_hot', true)
            ->with(['category:id,name,slug', 'images:id,product_id,path,sort_order'])
            ->paginate($perPage);
    }

    public function getRelatedProducts(int $productId, int $categoryId, int $limit = 8): Collection
    {
        return $this->model
            ->select('id', 'name', 'slug', 'price', 'category_id', 'is_hot', 'views', 'created_at')
            ->where('status', true)
            ->where('category_id', $categoryId)
            ->where('id', '!=', $productId)
            ->with(['images:id,product_id,path,sort_order'])
            ->limit($limit)
            ->get();
    }

    public function searchProducts(string $keyword, int $perPage = 10): LengthAwarePaginator
    {
        return $this->model
            ->select('id', 'name', 'slug', 'description', 'price', 'currency', 'category_id', 'is_hot', 'views', 'created_at')
            ->with([
                'category:id,name,slug',
                'images' => function ($query) {
                    $query->select('id', 'product_id', 'path', 'sort_order')
                        ->orderBy('sort_order')
                        ->limit(1);
                }
            ])
            ->where('status', true)
            ->where('name', 'LIKE', '%' . $keyword . '%')
            ->orderByDesc('views')
            ->paginate($perPage);
    }

    public function searchSuggestions(string $keyword, int $limit = 10): Collection
    {
        if (strlen($keyword) < 2) {
            return collect([]);
        }

        return $this->model
            ->where('status', true)
            ->where('name', 'LIKE', '%' . $keyword . '%')
            ->select('id', 'name', 'slug', 'price')
            ->limit($limit)
            ->get();
    }

    public function slugExists(string $slug, ?int $excludeId = null): bool
    {
        $query = $this->model->where('slug', $slug);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    public function countBySlugPattern(string $slug): int
    {
        return $this->model->where('slug', 'like', "{$slug}%")->count();
    }

    private function applyFilters($query, array $filters): void
    {
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('products.name', 'like', "%{$search}%")
                    ->orWhere('products.description', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['category_id'])) {
            $query->where('products.category_id', $filters['category_id']);
        }

        if (isset($filters['status'])) {
            $query->where('products.status', (bool) $filters['status']);
        }

        if (isset($filters['is_hot'])) {
            $query->where('products.is_hot', (bool) $filters['is_hot']);
        }

        if (!empty($filters['price_min'])) {
            $query->where('products.price', '>=', $filters['price_min']);
        }

        if (!empty($filters['price_max'])) {
            $query->where('products.price', '<=', $filters['price_max']);
        }
    }

    private function filterByPrice($query, $priceRange)
    {
        return match ($priceRange) {
            'under_10' => $query->where('products.price', '<', 10000000),
            '10_20' => $query->whereBetween('products.price', [10000000, 20000000]),
            '20_30' => $query->whereBetween('products.price', [20000000, 30000000]),
            'over_30' => $query->where('products.price', '>', 30000000),
            default => $query
        };
    }
}

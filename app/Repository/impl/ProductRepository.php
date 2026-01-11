<?php

namespace App\Repository\impl;

use App\Models\Product;
use Illuminate\Support\Facades\DB;
use App\Repository\IProductRepository;
use App\Services\impl\ProductSalesCache;
use App\Services\impl\RedisService;

class ProductRepository extends BaseRepository implements IProductRepository
{
    protected ProductSalesCache $salesCache;
    protected RedisService $redis;

    public function __construct(ProductSalesCache $salesCache, RedisService $redis)
    {
        $this->salesCache = $salesCache;
        $this->redis = $redis;
        parent::__construct(app());
    }

    public function model()
    {
        return Product::class;
    }

    public function findBuild()
    {
        return $this->with(['category', 'carts', 'orderDetails']);
    }

    public function filterByPrice($query, $priceRange)
    {
        return match ($priceRange) {
            'under_10' => $query->where('products.price', '<', 10000000),
            '10_20' => $query->whereBetween('products.price', [10000000, 20000000]),
            '20_30' => $query->whereBetween('products.price', [20000000, 30000000]),
            'over_30' => $query->where('products.price', '>', 30000000),
            default => $query
        };
    }

    public function sortProducts($query, $sortBy)
    {
        return match ($sortBy) {
            'best_selling' => $this->sortByBestSelling($query),
            'newest' => $query->latest('created_at'),
            'name_asc' => $query->orderBy('name', 'asc'),
            'name_desc' => $query->orderBy('name', 'desc'),
            'price_asc' => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            default => $this->sortByBestSelling($query)
        };
    }

    private function sortByBestSelling($query)
    {
        $salesRanking = $this->salesCache->getSalesRanking();

        if (empty($salesRanking)) {
            return $query->latest('created_at');
        }

        $products = $query->get();

        $sorted = $products->sortByDesc(function ($product) use ($salesRanking) {
            return $salesRanking[$product->id] ?? 0;
        });

        return $sorted;
    }

    public function getFilteredProducts($categoryId, $filters = [])
    {
        $cacheKey = 'filtered_products:' . $categoryId . ':' . md5(json_encode($filters));

        return $this->redis->remember($cacheKey, 600, function () use ($categoryId, $filters) {
            $query = $this->model
                ->select('products.id', 'products.name', 'products.slug', 'products.price', 'products.category_id', 'products.status', 'products.is_hot', 'products.views', 'products.specifications', 'products.created_at')
                ->where('products.status', true)
                ->where('products.category_id', $categoryId)
                ->with(['category:id,name,slug', 'images:id,product_id,path,sort_order']);

            if (!empty($filters['price_range'])) {
                $query = $this->filterByPrice($query, $filters['price_range']);
            }

            $result = $this->sortProducts($query, $filters['sort'] ?? 'best_selling');

            return $result instanceof \Illuminate\Database\Eloquent\Collection
                ? $result
                : $result->get();
        });
    }

    public function getNewProducts($limit = 8)
    {
        return $this->model
            ->select('id', 'name', 'slug', 'price', 'category_id', 'status', 'is_hot', 'views', 'created_at')
            ->active()
            ->with(['category:id,name,slug', 'images:id,product_id,path,sort_order'])
            ->latest('created_at')
            ->limit($limit)
            ->get();
    }

    public function getTopSellingProducts($limit = 12)
    {
        return $this->model
            ->select('id', 'name', 'slug', 'price', 'category_id', 'status', 'is_hot', 'views', 'created_at')
            ->active()
            ->with(['category:id,name,slug', 'images:id,product_id,path,sort_order'])
            ->mostViewed($limit)
            ->get();
    }

    public function getHotDeals($limit = 8)
    {
        return $this->model
            ->select('id', 'name', 'slug', 'price', 'category_id', 'status', 'is_hot', 'views', 'created_at')
            ->active()
            ->hot()
            ->with(['category:id,name,slug', 'images:id,product_id,path,sort_order'])
            ->limit($limit)
            ->get();
    }

    public function getPaginatedHotDeals($perPage = 12)
    {
        return $this->model
            ->select('id', 'name', 'slug', 'price', 'category_id', 'status', 'is_hot', 'views', 'created_at')
            ->active()
            ->hot()
            ->with(['category:id,name,slug', 'images:id,product_id,path,sort_order'])
            ->paginate($perPage);
    }

    public function searchSuggestions($keyword, $limit = 10)
    {
        if (strlen($keyword) < 2) {
            return collect([]);
        }

        return $this->model
            ->active()
            ->search($keyword)
            ->select('id', 'name', 'slug', 'price')
            ->limit($limit)
            ->get()
            ->map->toSearchSuggestion();
    }

    public function getHomeProducts(int $perPage = 10)
    {
        return $this->model
            ->select('id', 'name', 'slug', 'description', 'price', 'currency', 'category_id', 'status', 'is_hot', 'views', 'created_at')
            ->with([
                'category:id,name,slug',
                'images' => function ($query) {
                    $query->select('id', 'product_id', 'path', 'sort_order')
                        ->orderBy('sort_order')
                        ->limit(1);
                }
            ])
            ->where('status', true)
            ->whereNull('parent_id')
            ->orderByDesc('is_hot')
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    public function searchProducts(string $keyword, int $perPage = 10)
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
            ->whereNull('parent_id')
            ->where('name', 'LIKE', '%' . $keyword . '%')
            ->orderByRaw("CASE WHEN name LIKE ? THEN 1 ELSE 2 END", [$keyword . '%'])
            ->orderByDesc('views')
            ->paginate($perPage);
    }

    public function getSearchResults(string $keyword, array $filters = [])
    {
        // Create a sanitized cache key
        $sanitizedKeyword = preg_replace('/[^a-z0-9]/i', '', $keyword);
        $cacheKey = 'search_results:' . $sanitizedKeyword . ':' . md5(json_encode($filters));

        // Store and retrieve from Redis
        return $this->redis->remember($cacheKey, 600, function () use ($keyword, $filters) {
            $query = $this->model
                ->with(['category:id,name,slug', 'images:id,product_id,path,sort_order' => fn($q) => $q->orderBy('sort_order')->limit(1)])
                ->select('products.id', 'products.name', 'products.slug', 'products.price', 'products.category_id', 'products.status', 'products.is_hot', 'products.views', 'products.specifications', 'products.created_at')
                ->where('products.status', true)
                ->where('products.name', 'LIKE', '%' . $keyword . '%');

            if (!empty($filters['price_range'])) {
                $query = $this->filterByPrice($query, $filters['price_range']);
            }

            $result = $this->sortProducts($query, $filters['sort'] ?? 'best_selling');

            return $result instanceof \Illuminate\Database\Eloquent\Collection
                ? $result
                : $result->get();
        });
    }

    public function findBySlugWithRelations(string $slug)
    {
        return $this->model
            ->select('id', 'name', 'slug', 'description', 'specifications', 'price', 'currency', 'category_id', 'status', 'is_hot', 'views', 'created_at', 'updated_at')
            ->with([
                'category:id,name,slug',
                'images' => function ($query) {
                    $query->select('id', 'product_id', 'path', 'sort_order')
                        ->orderBy('sort_order')
                        ->orderBy('id');
                }
            ])
            ->where('slug', $slug)
            ->firstOrFail();
    }

    public function getRelatedProducts(int $productId, int $categoryId, int $limit = 8)
    {
        return $this->model
            ->where('category_id', $categoryId)
            ->where('id', '!=', $productId)
            ->where('status', true)
            ->select('id', 'name', 'slug', 'price', 'views', 'is_hot')
            ->with([
                'images' => function ($query) {
                    $query->select('id', 'product_id', 'path')->orderBy('sort_order')->limit(1);
                }
            ])
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }

    public function getHotProductsPaginated(int $perPage = 20)
    {
        return $this->model
            ->where('is_hot', true)
            ->where('status', true)
            ->select('id', 'name', 'slug', 'price', 'views', 'is_hot', 'category_id')
            ->with('category:id,name')
            ->orderBy('views', 'desc')
            ->paginate($perPage);
    }

    public function getApprovedReviews(Product $product, int $perPage = 10)
    {
        return $product->approvedReviews()
            ->select('id', 'product_id', 'user_id', 'rating', 'comment', 'status', 'images', 'videos', 'created_at')
            ->with('user:id,fullname,email')
            ->latest()
            ->paginate($perPage);
    }
}

<?php

namespace App\Repository;

use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ProductRepository extends BaseRepository
{
    public function model()
    {
        return Product::class;
    }

    public function findBuild()
    {
        return $this->with(['category', 'carts', 'orderDetails']);
    }

    public function findByCategory($categoryId)
    {
        return $this->findWhere(['category_id' => $categoryId]);
    }

    public function findActive()
    {
        return $this->findWhere(['status' => true]);
    }

    public function findByLanguage($language)
    {
        return $this->findWhere(['language' => $language]);
    }

    public function findActiveByCategoryId($categoryId)
    {
        return $this->findWhere([
            'category_id' => $categoryId,
            'status' => true
        ]);
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
            'best_selling' => $query->leftJoin(DB::raw('(
                    SELECT order_details.product_id,
                           COALESCE(SUM(order_details.quantity), 0) as total_sold
                    FROM order_details
                    INNER JOIN orders ON order_details.order_id = orders.id
                                     AND orders.status = "completed"
                    GROUP BY order_details.product_id
                ) as sales'), 'sales.product_id', '=', 'products.id')
                ->selectRaw('products.*, COALESCE(sales.total_sold, 0) as total_sold')
                ->orderByRaw('COALESCE(sales.total_sold, 0) DESC, products.created_at DESC'),

            'newest' => $query->latest('created_at'),
            'name_asc' => $query->orderBy('name', 'asc'),
            'name_desc' => $query->orderBy('name', 'desc'),
            'price_asc' => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),

            default => $query->leftJoin(DB::raw('(
                    SELECT order_details.product_id,
                           COALESCE(SUM(order_details.quantity), 0) as total_sold
                    FROM order_details
                    INNER JOIN orders ON order_details.order_id = orders.id
                                     AND orders.status = "completed"
                    GROUP BY order_details.product_id
                ) as sales'), 'sales.product_id', '=', 'products.id')
                ->selectRaw('products.*, COALESCE(sales.total_sold, 0) as total_sold')
                ->orderByRaw('COALESCE(sales.total_sold, 0) DESC, products.created_at DESC')
        };
    }

    public function getFilteredProducts($categoryId, $filters = [])
    {
        $query = $this->model
            ->where('products.status', true)
            ->where('products.category_id', $categoryId)
            ->with(['category:id,name,slug']);

        if (!empty($filters['price_range'])) {
            $query = $this->filterByPrice($query, $filters['price_range']);
        }

        $query = $this->sortProducts($query, $filters['sort'] ?? 'best_selling');

        return $query;
    }

    public function getNewProducts($limit = 8)
    {
        return $this->model
            ->active()
            ->with('category')
            ->latest('created_at')
            ->limit($limit)
            ->get();
    }

    public function getTopSellingProducts($limit = 12)
    {
        return $this->model
            ->active()
            ->with('category')
            ->mostViewed($limit)
            ->get();
    }

    public function getHotDeals($limit = 8)
    {
        return $this->model
            ->active()
            ->hot()
            ->with('category')
            ->limit($limit)
            ->get();
    }

    public function getPaginatedHotDeals($perPage = 12)
    {
        return $this->model
            ->active()
            ->hot()
            ->with('category')
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
            ->select('id', 'name', 'slug', 'image', 'price')
            ->limit($limit)
            ->get()
            ->map->toSearchSuggestion();
    }
}

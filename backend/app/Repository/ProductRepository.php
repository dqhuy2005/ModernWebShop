<?php

namespace App\Repository;

use App\Models\Product;

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
        return match($priceRange) {
            'under_10' => $query->where('price', '<', 10000000),
            '10_20' => $query->whereBetween('price', [10000000, 20000000]),
            '20_30' => $query->whereBetween('price', [20000000, 30000000]),
            'over_30' => $query->where('price', '>', 30000000),
            default => $query
        };
    }

    public function sortProducts($query, $sortBy)
    {
        return match($sortBy) {
            'name_asc' => $query->orderBy('name', 'asc'),
            'name_desc' => $query->orderBy('name', 'desc'),
            'price_asc' => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'newest' => $query->latest('created_at'),
            'popular' => $query->orderBy('views', 'desc'),
            default => $query->latest('updated_at')
        };
    }

    public function getFilteredProducts($categoryId, $filters = [])
    {
        $query = $this->model
            ->where('status', true)
            ->where('category_id', $categoryId)
            ->with(['category:id,name,slug']);

        if (!empty($filters['price_range'])) {
            $query = $this->filterByPrice($query, $filters['price_range']);
        }

        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('description', 'like', '%' . $filters['search'] . '%');
            });
        }

        $query = $this->sortProducts($query, $filters['sort'] ?? 'default');

        return $query;
    }
}

<?php

namespace App\Repository;

use App\Models\Category;

class CategoryRepository extends BaseRepository
{
    public function model()
    {
        return Category::class;
    }

    public function findBuild()
    {
        return $this->with(['products']);
    }

    /**
     * Get featured categories with product count
     */
    public function getFeaturedCategories($limit = 3)
    {
        return $this->model
            ->active()
            ->withCount('products')
            ->limit($limit)
            ->get();
    }

    /**
     * Get active categories with hot products
     */
    public function getCategoriesWithHotProducts($limit = 5, $productsLimit = 15)
    {
        return $this->model
            ->active()
            ->with(['products' => function ($query) use ($productsLimit) {
                $query->active()
                    ->where('is_hot', true)
                    ->latest('updated_at')
                    ->limit($productsLimit);
            }])
            ->limit($limit)
            ->get();
    }

    /**
     * Find category by slug
     */
    public function findBySlug($slug, $columns = ['id', 'name', 'slug', 'image'])
    {
        return $this->model
            ->where('slug', $slug)
            ->select($columns)
            ->firstOrFail();
    }
}

<?php

namespace App\Repository\impl;

use App\Models\Category;
use App\Repository\ICategoryRepository;

class CategoryRepository extends BaseRepository implements ICategoryRepository
{
    public function model()
    {
        return Category::class;
    }

    public function findBuild()
    {
        return $this->with(['products']);
    }

    public function getFeaturedCategories($limit = 3)
    {
        return $this->model
            ->select('id', 'name', 'slug', 'created_at')
            ->active()
            ->withCount('products')
            ->limit($limit)
            ->get();
    }

    public function getCategoriesWithHotProducts($limit = 5, $productsLimit = 15)
    {
        return $this->model
            ->select('id', 'name', 'slug', 'image', 'created_at')
            ->active()
            ->with([
                'products' => function ($query) use ($productsLimit) {
                    $query->select('id', 'name', 'slug', 'price', 'category_id', 'status', 'is_hot', 'views', 'updated_at')
                        ->active()
                        ->where('is_hot', true)
                        ->with([
                            'images:id,product_id,path,sort_order',
                            'category:id,name,image',
                            'approvedReviews:id,product_id,rating'
                        ])
                        ->withCount('approvedReviews')
                        ->withAvg('approvedReviews', 'rating')
                        ->latest('updated_at')
                        ->limit($productsLimit);
                }
            ])
            ->limit($limit)
            ->get();
    }

    public function findBySlug($slug, $columns = ['id', 'name', 'slug'])
    {
        return $this->model
            ->where('slug', $slug)
            ->select($columns)
            ->firstOrFail();
    }

    /**
     * Get categories for navigation menu with children
     */
    public function getNavigationCategories()
    {
        return $this->model
            ->with(['children' => function ($query) {
                $query->limit(5)
                      ->select('id', 'name', 'slug', 'parent_id');
            }])
            ->withCount('products')
            ->whereNull('parent_id')
            ->orderBy('name')
            ->limit(6)
            ->get(['id', 'name', 'slug', 'parent_id']);
    }

    /**
     * Get categories for display grid
     */
    public function getDisplayCategories()
    {
        return $this->model
            ->withCount('products')
            ->whereNull('parent_id')
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get(['id', 'name', 'slug', 'image']);
    }
}

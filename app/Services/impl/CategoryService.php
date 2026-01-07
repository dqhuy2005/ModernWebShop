<?php

namespace App\Services\impl;

use App\Repository\impl\ProductRepository;
use App\Services\ICategoryService;

class CategoryService implements ICategoryService
{
    protected ProductRepository $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * Get filtered products as paginated result (legacy method)
     * Note: This now returns pre-paginated collection from cache
     */
    public function getFilteredProducts(int $categoryId, array $filters, int $perPage = 12)
    {
        // Get all products from cache
        $allProducts = $this->productRepository->getFilteredProducts($categoryId, $filters);
        
        // Return as paginated (for backward compatibility)
        return new \Illuminate\Pagination\LengthAwarePaginator(
            $allProducts->forPage(request()->input('page', 1), $perPage),
            $allProducts->count(),
            $perPage,
            request()->input('page', 1),
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }

    /**
     * Get filtered products as collection (for manual pagination)
     */
    public function getFilteredProductsCollection(int $categoryId, array $filters)
    {
        return $this->productRepository->getFilteredProducts($categoryId, $filters);
    }

    public function formatAjaxResponse($products): array
    {
        return [
            'success' => true,
            'html' => view('user.partials.product-grid', compact('products'))->render(),
            'pagination' => view('user.partials.pagination', compact('products'))->render()
        ];
    }
}

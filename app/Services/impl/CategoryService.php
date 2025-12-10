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

    public function getFilteredProducts(int $categoryId, array $filters, int $perPage = 12)
    {
        return $this->productRepository
            ->getFilteredProducts($categoryId, $filters)
            ->paginate($perPage)
            ->withQueryString();
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

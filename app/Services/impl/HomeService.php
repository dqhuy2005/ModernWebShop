<?php

namespace App\Services\impl;

use App\Repository\impl\ProductRepository;
use App\Services\IHomeService;

class HomeService implements IHomeService
{
    protected ProductRepository $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function getHomeProducts(int $perPage = 10, int $page = 1): array
    {
        $products = $this->productRepository->getHomeProducts($perPage);

        return $this->formatPaginatedResponse($products);
    }

    public function searchProducts(string $keyword, int $perPage = 10): array
    {
        $products = $this->productRepository->searchProducts($keyword, $perPage);

        return $this->formatPaginatedResponse($products, $keyword);
    }

    private function formatPaginatedResponse($products, ?string $searchQuery = null): array
    {
        $data = [
            'total_products' => $products->total(),
            'current_page' => $products->currentPage(),
            'per_page' => $products->perPage(),
            'last_page' => $products->lastPage(),
            'has_more' => $products->hasMorePages(),
            'next_page' => $products->hasMorePages() ? $products->currentPage() + 1 : null,
            'products' => $products->getCollection()->map(function ($product) {
                return $this->formatProductData($product);
            })->values()->toArray()
        ];

        if ($searchQuery !== null) {
            $data['search_query'] = $searchQuery;
        }

        return $data;
    }

    private function formatProductData($product): array
    {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'description' => $product->description ?? null,
            'price' => $product->price,
            'formatted_price' => $product->formatted_price,
            'currency' => $product->currency,
            'is_hot' => $product->is_hot,
            'views' => $product->views,
            'image_url' => $product->image_url,
            'category' => $product->category ? [
                'id' => $product->category->id,
                'name' => $product->category->name,
                'slug' => $product->category->slug,
            ] : null,
            'created_at' => $product->created_at->format('Y-m-d H:i:s'),
        ];
    }
}

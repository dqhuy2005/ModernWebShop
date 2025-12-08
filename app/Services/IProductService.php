<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Http\UploadedFile;

interface IProductService
{
    public function createProduct(array $data, ?UploadedFile $mainImage = null, ?array $images = []): Product;
    
    public function updateProduct(Product $product, array $data, ?UploadedFile $mainImage = null, ?array $images = []): Product;
    
    public function deleteProduct(Product $product): bool;
    
    public function deleteProductImage(Product $product, int $imageId): bool;
    
    public function getProductDetail(string $slug, $request): array;
    
    public function getProductBySlug(string $slug);
    
    public function trackProductView(Product $product, $request): void;
    
    public function getRelatedProducts(Product $product);
    
    public function getViewStats(Product $product): array;
    
    public function getProductReviews(Product $product, $request);
    
    public function getReviewStats(Product $product): array;
    
    public function getHotProducts(int $perPage = 20);
    
    public function formatSpecifications($specifications): ?array;
}

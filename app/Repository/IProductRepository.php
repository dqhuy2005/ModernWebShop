<?php

namespace App\Repository;

use App\Models\Product;

interface IProductRepository
{
    public function model();

    public function findBuild();

    public function filterByPrice($query, $priceRange);

    public function sortProducts($query, $sortBy);

    public function getFilteredProducts($categoryId, $filters = []);

    public function getNewProducts($limit = 8);

    public function getTopSellingProducts($limit = 12);

    public function getHotDeals($limit = 8);

    public function getPaginatedHotDeals($perPage = 12);

    public function searchSuggestions($keyword, $limit = 10);

    public function getHomeProducts(int $perPage = 10);

    public function searchProducts(string $keyword, int $perPage = 10);

    public function findBySlugWithRelations(string $slug);

    public function getRelatedProducts(int $productId, int $categoryId, int $limit = 8);

    public function getHotProductsPaginated(int $perPage = 20);

    public function getApprovedReviews(Product $product, int $perPage = 10);
}

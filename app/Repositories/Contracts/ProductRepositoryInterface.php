<?php

namespace App\Repositories\Contracts;

use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ProductRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function find(int $id): ?Product;

    public function findWithTrashed(int $id): ?Product;

    public function findBySlug(string $slug): ?Product;

    public function findBySlugWithRelations(string $slug): ?Product;

    public function create(array $data): Product;

    public function update(Product $product, array $data): bool;

    public function delete(Product $product): bool;

    public function restore(int $id): bool;

    public function forceDelete(int $id): bool;

    public function toggleHotStatus(Product $product): bool;

    public function toggleStatus(Product $product): bool;

    public function getFilteredProducts(int $categoryId, array $filters = []);

    public function getNewProducts(int $limit = 8): Collection;

    public function getHotDeals(int $limit = 8): Collection;

    public function getPaginatedHotDeals(int $perPage = 12): LengthAwarePaginator;

    public function getRelatedProducts(int $productId, int $categoryId, int $limit = 8): Collection;

    public function searchProducts(string $keyword, int $perPage = 10): LengthAwarePaginator;

    public function searchSuggestions(string $keyword, int $limit = 10): Collection;

    public function slugExists(string $slug, ?int $excludeId = null): bool;

    public function countBySlugPattern(string $slug): int;
}

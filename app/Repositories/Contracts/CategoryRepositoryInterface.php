<?php

namespace App\Repositories\Contracts;

use App\Models\Category;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface CategoryRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function find(int $id): ?Category;

    public function findBySlug(string $slug, array $columns = ['*']): ?Category;

    public function create(array $data): Category;

    public function update(Category $category, array $data): bool;

    public function delete(Category $category): bool;

    public function restore(int $id): bool;

    public function forceDelete(int $id): bool;

    public function getAllExcept(int $excludeId): Collection;

    public function findWithTrashed(int $id): ?Category;

    public function getFeaturedCategories(int $limit = 3): Collection;

    public function getCategoriesWithHotProducts(int $limit = 5, int $productsLimit = 15): Collection;

    public function slugExists(string $slug, ?int $excludeId = null): bool;
}

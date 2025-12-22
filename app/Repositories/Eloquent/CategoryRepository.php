<?php

namespace App\Repositories\Eloquent;

use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function __construct(
        private Category $model
    ) {}

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model
            ->select('id', 'name', 'slug', 'image', 'created_at', 'updated_at', 'deleted_at')
            ->withTrashed();

        if (!empty($filters['search'])) {
            $query->where('name', 'like', "%{$filters['search']}%");
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function find(int $id): ?Category
    {
        return $this->model->find($id);
    }

    public function findBySlug(string $slug, array $columns = ['*']): ?Category
    {
        return $this->model
            ->where('slug', $slug)
            ->select($columns)
            ->firstOrFail();
    }

    public function create(array $data): Category
    {
        return $this->model->create($data);
    }

    public function update(Category $category, array $data): bool
    {
        return $category->update($data);
    }

    public function delete(Category $category): bool
    {
        return $category->delete();
    }

    public function restore(int $id): bool
    {
        $category = $this->model->withTrashed()->find($id);

        if (!$category) {
            return false;
        }

        return $category->restore();
    }

    public function forceDelete(int $id): bool
    {
        $category = $this->model->withTrashed()->find($id);

        if (!$category) {
            return false;
        }

        return $category->forceDelete();
    }

    public function getAllExcept(int $excludeId): Collection
    {
        return $this->model
            ->select('id', 'name', 'slug')
            ->whereNull('deleted_at')
            ->where('id', '!=', $excludeId)
            ->get();
    }

    public function findWithTrashed(int $id): ?Category
    {
        return $this->model->withTrashed()->find($id);
    }

    public function slugExists(string $slug, ?int $excludeId = null): bool
    {
        $query = $this->model->where('slug', $slug);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }
}

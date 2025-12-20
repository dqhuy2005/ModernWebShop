<?php

namespace App\Services\CMS;

use App\DTOs\CategoryData;
use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Services\impl\ImageService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategoryService
{
    public function __construct(
        private CategoryRepositoryInterface $categoryRepository,
        private ImageService $imageService
    ) {
    }

    public function createCategory(CategoryData $data): Category
    {
        return DB::transaction(function () use ($data) {
            if (empty($data->slug)) {
                $data->slug = $this->generateUniqueSlug($data->name);
            }

            return $this->categoryRepository->create($data->toArray());
        });
    }

    public function updateCategory(Category $category, CategoryData $data): Category
    {
        return DB::transaction(function () use ($category, $data) {
            if (empty($data->slug)) {
                $data->slug = $this->generateUniqueSlug($data->name, $category->id);
            }

            $this->categoryRepository->update($category, $data->toArray());

            return $category->fresh();
        });
    }

    public function deleteCategory(Category $category): bool
    {
        return $this->categoryRepository->delete($category);
    }

    public function restoreCategory(int $id): bool
    {
        return $this->categoryRepository->restore($id);
    }

    public function forceDeleteCategory(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $category = $this->categoryRepository->findWithTrashed($id);

            if (!$category) {
                throw new \Exception('Category not found');
            }

            if ($category->image) {
                $this->imageService->deleteImage($category->image);
            }

            return $this->categoryRepository->forceDelete($id);
        });
    }

    private function generateUniqueSlug(string $name, ?int $excludeId = null): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        while ($this->categoryRepository->slugExists($slug, $excludeId)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}

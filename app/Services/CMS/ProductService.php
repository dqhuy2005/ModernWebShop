<?php

namespace App\Services\CMS;

use App\DTOs\ProductData;
use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Services\impl\ImageService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductService
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private ImageService $imageService
    ) {
    }

    public function createProduct(ProductData $data, ?array $imagePaths = []): Product
    {
        return DB::transaction(function () use ($data, $imagePaths) {
            if (empty($data->slug)) {
                $data->slug = $this->generateUniqueSlug($data->name);
            }

            $product = $this->productRepository->create($data->toArray());

            if (!empty($imagePaths)) {
                $this->addProductImages($product, $imagePaths);
            }

            return $product->fresh();
        });
    }

    public function updateProduct(Product $product, ProductData $data, ?array $imagePaths = []): Product
    {
        return DB::transaction(function () use ($product, $data, $imagePaths) {
            if (empty($data->slug) || $product->name !== $data->name) {
                $data->slug = $this->generateUniqueSlug($data->name, $product->id);
            }

            $this->productRepository->update($product, $data->toArray());

            if (!empty($imagePaths)) {
                $this->addProductImages($product, $imagePaths);
            }

            return $product->fresh();
        });
    }

    public function deleteProduct(Product $product): bool
    {
        return $this->productRepository->delete($product);
    }

    public function restoreProduct(int $id): bool
    {
        return $this->productRepository->restore($id);
    }

    public function forceDeleteProduct(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $product = $this->productRepository->findWithTrashed($id);

            if (!$product) {
                throw new \Exception('Product not found');
            }

            foreach ($product->images as $image) {
                if ($image->path) {
                    $this->imageService->deleteImage($image->path);
                }
                $image->delete();
            }

            return $this->productRepository->forceDelete($id);
        });
    }

    public function toggleHotStatus(Product $product): bool
    {
        return $this->productRepository->toggleHotStatus($product);
    }

    public function toggleStatus(Product $product): bool
    {
        return $this->productRepository->toggleStatus($product);
    }

    public function deleteProductImage(Product $product, int $imageId): bool
    {
        return DB::transaction(function () use ($product, $imageId) {
            $image = $product->images()->find($imageId);

            if (!$image) {
                throw new \Exception('Image not found');
            }

            if ($image->path) {
                $this->imageService->deleteImage($image->path);
            }

            return $image->delete();
        });
    }

    public function formatSpecifications(?array $specifications): ?array
    {
        if (empty($specifications)) {
            return null;
        }

        $formatted = [];
        foreach ($specifications as $key => $value) {
            if (!empty($value)) {
                $formatted[$key] = $value;
            }
        }

        return !empty($formatted) ? $formatted : null;
    }

    private function addProductImages(Product $product, array $imagePaths): void
    {
        foreach ($imagePaths as $index => $path) {
            $product->images()->create([
                'path' => $path,
                'sort_order' => $index,
            ]);
        }
    }

    private function generateUniqueSlug(string $name, ?int $excludeId = null): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        while ($this->productRepository->slugExists($slug, $excludeId)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}

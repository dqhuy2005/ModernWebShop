<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\UploadedFile;

class ProductService
{
    protected ImageService $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function createProduct(array $data, ?UploadedFile $mainImage = null, ?array $images = []): Product
    {
        DB::beginTransaction();
        try {
            $product = Product::create($data);

            if (!empty($images)) {
                $this->addProductImages($product, $images);
            }

            DB::commit();
            return $product;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateProduct(Product $product, array $data, ?UploadedFile $mainImage = null, ?array $images = []): Product
    {
        DB::beginTransaction();
        try {
            $product->update($data);

            if (!empty($images)) {
                $this->addProductImages($product, $images);
            }

            DB::commit();
            return $product->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    protected function addProductImages(Product $product, array $images): void
    {
        $currentMax = $product->images()->max('sort_order') ?? -1;
        $sort = $currentMax + 1;

        foreach ($images as $imagePath) {
            if ($imagePath instanceof UploadedFile) {
                if (!$this->imageService->validateImage($imagePath)) {
                    continue;
                }
                $path = $this->imageService->uploadProductImage($imagePath);
            }
            elseif (is_string($imagePath) && !empty($imagePath)) {
                $path = $imagePath;
            }
            else {
                continue;
            }

            ProductImage::create([
                'product_id' => $product->id,
                'path' => $path,
                'alt' => null,
                'sort_order' => $sort++,
            ]);
        }
    }

    public function deleteProductImage(Product $product, int $imageId): bool
    {
        DB::beginTransaction();
        try {
            $image = ProductImage::where('product_id', $product->id)
                ->where('id', $imageId)
                ->firstOrFail();

            $this->imageService->deleteImage($image->path);

            $deletedSortOrder = $image->sort_order;

            $image->delete();

            ProductImage::where('product_id', $product->id)
                ->where('sort_order', '>', $deletedSortOrder)
                ->decrement('sort_order');

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function formatSpecifications($specifications): ?array
    {
        if (!is_array($specifications)) {
            return null;
        }

        $specs = [];
        foreach ($specifications as $spec) {
            if (is_array($spec) && !empty($spec['key']) && !empty($spec['value'])) {
                $specs[$spec['key']] = $spec['value'];
            }
        }

        return !empty($specs) ? $specs : null;
    }

    public function deleteProduct(Product $product): bool
    {
        DB::beginTransaction();
        try {
            foreach ($product->images as $image) {
                $this->imageService->deleteImage($image->path);
                $image->delete();
            }

            $product->delete();

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}

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

    /**
     * Create a new product with images
     */
    public function createProduct(array $data, ?UploadedFile $mainImage = null, ?array $images = []): Product
    {
        DB::beginTransaction();
        try {
            // Handle main image upload if provided
            if ($mainImage && $this->imageService->validateImage($mainImage)) {
                $data['image'] = $this->imageService->uploadProductImage($mainImage);
            }

            // Create product
            $product = Product::create($data);

            // Handle multiple images if provided
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

    /**
     * Update an existing product
     */
    public function updateProduct(Product $product, array $data, ?UploadedFile $mainImage = null, ?array $images = []): Product
    {
        DB::beginTransaction();
        try {
            // Handle main image upload if provided
            if ($mainImage && $this->imageService->validateImage($mainImage)) {
                $data['image'] = $this->imageService->uploadProductImage($mainImage, $product->image);
            }

            // Update product
            $product->update($data);

            // Handle multiple images if provided
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

    /**
     * Add multiple images to a product
     */
    protected function addProductImages(Product $product, array $images): void
    {
        $currentMax = $product->images()->max('sort_order') ?? -1;
        $sort = $currentMax + 1;

        foreach ($images as $file) {
            if (!($file instanceof UploadedFile) || !$this->imageService->validateImage($file)) {
                continue;
            }

            $path = $this->imageService->uploadProductImage($file);
            ProductImage::create([
                'product_id' => $product->id,
                'path' => $path,
                'alt' => null,
                'sort_order' => $sort++,
            ]);
        }
    }

    /**
     * Delete a product image and reorder remaining images
     */
    public function deleteProductImage(Product $product, int $imageId): bool
    {
        DB::beginTransaction();
        try {
            $image = ProductImage::where('product_id', $product->id)
                ->where('id', $imageId)
                ->firstOrFail();

            // Delete physical file
            $this->imageService->deleteImage($image->path);

            $deletedSortOrder = $image->sort_order;

            // Delete the image record
            $image->delete();

            // Update sort_order for remaining images
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

    /**
     * Parse and format product specifications
     */
    public function formatSpecifications($specifications): ?string
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

        return !empty($specs) ? json_encode($specs) : null;
    }

    /**
     * Delete a product and its associated images
     */
    public function deleteProduct(Product $product): bool
    {
        DB::beginTransaction();
        try {
            // Delete main image if exists
            if ($product->image) {
                $this->imageService->deleteImage($product->image);
            }

            // Delete all product images
            foreach ($product->images as $image) {
                $this->imageService->deleteImage($image->path);
                $image->delete();
            }

            // Soft delete product
            $product->delete();

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}

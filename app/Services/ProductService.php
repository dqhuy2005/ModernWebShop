<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\UploadedFile;
use App\Repository\ProductRepository;

class ProductService
{
    protected ImageService $imageService;
    protected ProductRepository $productRepository;
    protected ProductViewService $productViewService;
    protected ReviewService $reviewService;
    protected RedisService $redisService;

    public function __construct(
        ImageService $imageService,
        ProductRepository $productRepository,
        ProductViewService $productViewService,
        ReviewService $reviewService,
        RedisService $redisService
    ) {
        $this->imageService = $imageService;
        $this->productRepository = $productRepository;
        $this->productViewService = $productViewService;
        $this->reviewService = $reviewService;
        $this->redisService = $redisService;
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

    public function getProductDetail(string $slug, $request): array
    {
        $product = $this->getProductBySlug($slug);

        $this->trackProductView($product, $request);

        return [
            'product' => $product,
            'relatedProducts' => $this->getRelatedProducts($product),
            'viewStats' => $this->getViewStats($product),
            'reviews' => $this->getProductReviews($product, $request),
            'reviewStats' => $this->getReviewStats($product),
        ];
    }

    public function getProductBySlug(string $slug)
    {
        $cacheKey = "product_detail_{$slug}";

        return $this->redisService->remember(
            $cacheKey,
            600,
            fn() => $this->productRepository->findBySlugWithRelations($slug)
        );
    }

    public function trackProductView(Product $product, $request): void
    {
        $this->productViewService->trackView(
            $product,
            $request->ip(),
            $request->userAgent()
        );
    }

    public function getRelatedProducts(Product $product)
    {
        $cacheKey = "related_products_{$product->id}";

        return $this->redisService->remember(
            $cacheKey,
            3600,
            fn() => $this->productRepository->getRelatedProducts($product->id, $product->category_id, 8)
        );
    }

    public function getViewStats(Product $product): array
    {
        $cacheKey = "product_view_stats_{$product->id}";

        return $this->redisService->remember(
            $cacheKey,
            300,
            function () use ($product) {
                return [
                    'total_views' => $product->views ?? 0,
                    'recent_views_7days' => $this->productViewService->getRecentViewCount($product->id, 7),
                    'unique_visitors' => $this->productViewService->getUniqueVisitorsCount($product->id, 7),
                    'is_hot' => $product->is_hot,
                ];
            }
        );
    }

    public function getProductReviews(Product $product, $request)
    {
        $page = $request->get('page', 1);
        $cacheKey = "product_reviews_{$product->id}_page_{$page}";

        return $this->redisService->remember(
            $cacheKey,
            600,
            fn() => $this->productRepository->getApprovedReviews($product, 10)
        );
    }

    public function getReviewStats(Product $product): array
    {
        $cacheKey = "product_review_stats_{$product->id}";

        return $this->redisService->remember(
            $cacheKey,
            600,
            fn() => $this->reviewService->getProductReviewStats($product)
        );
    }

    /**
     * Get hot products with pagination
     */
    public function getHotProducts(int $perPage = 20)
    {
        return $this->productRepository->getHotProductsPaginated($perPage);
    }
}

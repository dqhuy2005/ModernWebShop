<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ReviewService
{
    private const MAX_IMAGE_SIZE = 2048;
    private const MAX_IMAGES = 5;
    private const ALLOWED_IMAGE_TYPES = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    public function canUserReviewProduct(User $user, Product $product, Order $order): array
    {
        if ($order->user_id !== $user->id) {
            return [
                'can_review' => false,
                'reason' => 'Order does not belong to this user',
            ];
        }

        if ($order->status !== Order::STATUS_COMPLETED) {
            return [
                'can_review' => false,
                'reason' => 'Order must be completed before reviewing',
            ];
        }

        $orderDetail = $order->orderDetails()
            ->where('product_id', $product->id)
            ->first();

        if (!$orderDetail) {
            return [
                'can_review' => false,
                'reason' => 'Product not found in this order',
            ];
        }

        $existingReview = ProductReview::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->where('order_id', $order->id)
            ->first();

        if ($existingReview) {
            return [
                'can_review' => false,
                'reason' => 'You have already reviewed this product for this order',
                'existing_review' => $existingReview,
            ];
        }

        return [
            'can_review' => true,
            'order_detail' => $orderDetail,
        ];
    }

    /**
     * Create a new review
     */
    public function createReview(array $data, ?array $images = null, ?array $videos = null): ProductReview
    {
        $imagePaths = $images ? $this->uploadImages($images) : [];

        $review = ProductReview::create([
            'product_id' => $data['product_id'],
            'user_id' => $data['user_id'],
            'order_id' => $data['order_id'],
            'order_detail_id' => $data['order_detail_id'] ?? null,
            'rating' => $data['rating'],
            'title' => $data['title'] ?? null,
            'comment' => $data['comment'],
            'images' => !empty($imagePaths) ? $imagePaths : null,
            'videos' => null,
            'status' => ProductReview::STATUS_APPROVED,
            'is_verified_purchase' => true,
        ]);

        return $review->load(['user', 'product', 'order']);
    }

    /**
     * Update an existing review
     */
    public function updateReview(ProductReview $review, array $data, ?array $newImages = null, ?array $newVideos = null): ProductReview
    {
        $updateData = [
            'rating' => $data['rating'],
            'title' => $data['title'] ?? null,
            'comment' => $data['comment'],
        ];

        if ($newImages !== null) {
            if ($review->images) {
                $this->deleteFiles($review->images);
            }
            $updateData['images'] = !empty($newImages) ? $this->uploadImages($newImages) : null;
        }

        $review->update($updateData);

        return $review->fresh(['user', 'product', 'order']);
    }

    /**
     * Delete a review and its media files
     */
    public function deleteReview(ProductReview $review): bool
    {
        if ($review->images) {
            $this->deleteFiles($review->images);
        }

        return $review->delete();
    }

    /**
     * Upload review images
     */
    private function uploadImages(array $images): array
    {
        $paths = [];

        foreach (array_slice($images, 0, self::MAX_IMAGES) as $image) {
            if ($image instanceof UploadedFile && $image->isValid()) {
                $extension = $image->getClientOriginalExtension();

                if (!in_array(strtolower($extension), self::ALLOWED_IMAGE_TYPES)) {
                    continue;
                }

                if ($image->getSize() > self::MAX_IMAGE_SIZE * 1024) {
                    continue;
                }

                $filename = 'review_' . Str::random(20) . '_' . time() . '.' . $extension;
                $path = $image->storeAs('reviews/images', $filename, 'public');
                $paths[] = $path;
            }
        }

        return $paths;
    }

    /**
     * Delete files from storage
     */
    private function deleteFiles(array $paths): void
    {
        foreach ($paths as $path) {
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }
    }

    /**
     * Get review statistics for a product
     */
    public function getProductReviewStats(Product $product): array
    {
        $reviews = $product->approvedReviews;

        $stats = [
            'total_reviews' => $reviews->count(),
            'average_rating' => $reviews->avg('rating') ?: 0,
            'rating_breakdown' => [
                5 => $reviews->where('rating', 5)->count(),
                4 => $reviews->where('rating', 4)->count(),
                3 => $reviews->where('rating', 3)->count(),
                2 => $reviews->where('rating', 2)->count(),
                1 => $reviews->where('rating', 1)->count(),
            ],
            'with_images' => $reviews->whereNotNull('images')->count(),
            'with_videos' => $reviews->whereNotNull('videos')->count(),
            'verified_purchases' => $reviews->where('is_verified_purchase', true)->count(),
        ];

        return $stats;
    }
}
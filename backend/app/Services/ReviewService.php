<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * ReviewService
 *
 * Handles business logic for product reviews
 * - Creating and updating reviews
 * - Media upload (images/videos)
 * - Review validation and eligibility checks
 */
class ReviewService
{
    private const MAX_IMAGE_SIZE = 2048; // 2MB
    private const MAX_VIDEO_SIZE = 10240; // 10MB
    private const MAX_IMAGES = 5;
    private const MAX_VIDEOS = 2;
    private const ALLOWED_IMAGE_TYPES = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    private const ALLOWED_VIDEO_TYPES = ['mp4', 'mov', 'avi', 'webm'];

    /**
     * Check if user can review a product from a specific order
     */
    public function canUserReviewProduct(User $user, Product $product, Order $order): array
    {
        // Check if order belongs to user
        if ($order->user_id !== $user->id) {
            return [
                'can_review' => false,
                'reason' => 'Order does not belong to this user',
            ];
        }

        // Check if order is completed
        if ($order->status !== Order::STATUS_COMPLETED) {
            return [
                'can_review' => false,
                'reason' => 'Order must be completed before reviewing',
            ];
        }

        // Check if product is in the order
        $orderDetail = $order->orderDetails()
            ->where('product_id', $product->id)
            ->first();

        if (!$orderDetail) {
            return [
                'can_review' => false,
                'reason' => 'Product not found in this order',
            ];
        }

        // Check if user already reviewed this product for this order
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
     * Get all completed orders containing a product that user can review
     */
    public function getUserEligibleOrdersForProduct(User $user, Product $product)
    {
        return Order::where('user_id', $user->id)
            ->where('status', Order::STATUS_COMPLETED)
            ->whereHas('orderDetails', function ($query) use ($product) {
                $query->where('product_id', $product->id);
            })
            ->whereDoesntHave('reviews', function ($query) use ($product) {
                $query->where('product_id', $product->id);
            })
            ->with('orderDetails')
            ->get();
    }

    /**
     * Create a new review
     */
    public function createReview(array $data, ?array $images = null, ?array $videos = null): ProductReview
    {
        // Upload media files
        $imagePaths = $images ? $this->uploadImages($images) : [];
        $videoPaths = $videos ? $this->uploadVideos($videos) : [];

        // Create review
        $review = ProductReview::create([
            'product_id' => $data['product_id'],
            'user_id' => $data['user_id'],
            'order_id' => $data['order_id'],
            'order_detail_id' => $data['order_detail_id'] ?? null,
            'rating' => $data['rating'],
            'title' => $data['title'] ?? null,
            'comment' => $data['comment'],
            'images' => !empty($imagePaths) ? $imagePaths : null,
            'videos' => !empty($videoPaths) ? $videoPaths : null,
            'status' => ProductReview::STATUS_APPROVED, // Auto-approve for now
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

        // Handle images
        if ($newImages !== null) {
            // Delete old images if replacing
            if ($review->images) {
                $this->deleteFiles($review->images);
            }
            $updateData['images'] = !empty($newImages) ? $this->uploadImages($newImages) : null;
        }

        // Handle videos
        if ($newVideos !== null) {
            // Delete old videos if replacing
            if ($review->videos) {
                $this->deleteFiles($review->videos);
            }
            $updateData['videos'] = !empty($newVideos) ? $this->uploadVideos($newVideos) : null;
        }

        $review->update($updateData);

        return $review->fresh(['user', 'product', 'order']);
    }

    /**
     * Delete a review and its media files
     */
    public function deleteReview(ProductReview $review): bool
    {
        // Delete media files
        if ($review->images) {
            $this->deleteFiles($review->images);
        }

        if ($review->videos) {
            $this->deleteFiles($review->videos);
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
     * Upload review videos
     */
    private function uploadVideos(array $videos): array
    {
        $paths = [];

        foreach (array_slice($videos, 0, self::MAX_VIDEOS) as $video) {
            if ($video instanceof UploadedFile && $video->isValid()) {
                $extension = $video->getClientOriginalExtension();

                if (!in_array(strtolower($extension), self::ALLOWED_VIDEO_TYPES)) {
                    continue;
                }

                if ($video->getSize() > self::MAX_VIDEO_SIZE * 1024) {
                    continue;
                }

                $filename = 'review_' . Str::random(20) . '_' . time() . '.' . $extension;
                $path = $video->storeAs('reviews/videos', $filename, 'public');
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

    /**
     * Mark review as helpful
     */
    public function markAsHelpful(ProductReview $review): void
    {
        $review->incrementHelpful();
    }

    /**
     * Mark review as not helpful
     */
    public function markAsNotHelpful(ProductReview $review): void
    {
        $review->incrementNotHelpful();
    }
}

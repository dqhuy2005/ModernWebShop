<?php

namespace App\Observers;

use App\Models\ProductReview;
use App\Services\RedisService;
use Illuminate\Support\Facades\Log;

/**
 * ProductReview Observer
 *
 * Handles cache invalidation when product reviews are modified
 * Ensures review data consistency between database and cache
 */
class ProductReviewObserver
{
    protected RedisService $redis;

    public function __construct(RedisService $redis)
    {
        $this->redis = $redis;
    }

    public function created(ProductReview $review): void
    {
        $this->clearReviewCaches($review, 'created');
    }

    public function updated(ProductReview $review): void
    {
        $this->clearReviewCaches($review, 'updated');
    }

    public function deleted(ProductReview $review): void
    {
        $this->clearReviewCaches($review, 'deleted');
    }

    public function restored(ProductReview $review): void
    {
        $this->clearReviewCaches($review, 'restored');
    }

    public function forceDeleted(ProductReview $review): void
    {
        $this->clearReviewCaches($review, 'force_deleted');
    }

    protected function clearReviewCaches(ProductReview $review, string $event): void
    {
        try {
            $productId = $review->product_id;

            $this->redis->deleteByPattern("product_reviews_{$productId}_page_*");
            $this->redis->forget("product_review_stats_{$productId}");

            Log::info('ProductReviewObserver: Review caches cleared', [
                'event' => $event,
                'review_id' => $review->id,
                'product_id' => $productId,
                'rating' => $review->rating,
                'status' => $review->status,
            ]);
        } catch (\Exception $e) {
            Log::error('ProductReviewObserver: Error clearing caches', [
                'event' => $event,
                'review_id' => $review->id,
                'product_id' => $review->product_id,
                'error' => $e->getMessage()
            ]);
        }
    }
}

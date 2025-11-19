<?php

namespace App\Observers;

use App\Models\ProductReview;
use App\Services\Cache\RedisService;
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

    /**
     * Handle the ProductReview "created" event.
     */
    public function created(ProductReview $review): void
    {
        $this->clearReviewCaches($review, 'created');
    }

    /**
     * Handle the ProductReview "updated" event.
     */
    public function updated(ProductReview $review): void
    {
        $this->clearReviewCaches($review, 'updated');
    }

    /**
     * Handle the ProductReview "deleted" event.
     */
    public function deleted(ProductReview $review): void
    {
        $this->clearReviewCaches($review, 'deleted');
    }

    /**
     * Handle the ProductReview "restored" event.
     */
    public function restored(ProductReview $review): void
    {
        $this->clearReviewCaches($review, 'restored');
    }

    /**
     * Handle the ProductReview "force deleted" event.
     */
    public function forceDeleted(ProductReview $review): void
    {
        $this->clearReviewCaches($review, 'force_deleted');
    }

    /**
     * Clear all review-related caches for a product
     */
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

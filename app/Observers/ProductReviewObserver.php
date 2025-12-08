<?php

namespace App\Observers;

use App\Mail\ReviewApprovedNotification;
use App\Models\ProductReview;
use App\Services\impl\RedisService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * ProductReview Observer
 *
 * Handles cache invalidation when product reviews are modified
 * Ensures review data consistency between database and cache
 * Sends email notification when review is approved
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

        // Send email notification when review status changes to approved
        if ($review->isDirty('status') && $review->status === ProductReview::STATUS_APPROVED) {
            $this->sendApprovalEmail($review);
        }
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

    protected function sendApprovalEmail(ProductReview $review): void
    {
        try {
            $review->load(['user', 'product', 'order']);

            if ($review->user && $review->user->email) {
                Mail::to($review->user->email)
                    ->send(new ReviewApprovedNotification($review));

                Log::info('ProductReviewObserver: Approval email sent', [
                    'review_id' => $review->id,
                    'user_id' => $review->user_id,
                    'product_id' => $review->product_id,
                    'email' => $review->user->email,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('ProductReviewObserver: Error sending approval email', [
                'review_id' => $review->id,
                'user_id' => $review->user_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}

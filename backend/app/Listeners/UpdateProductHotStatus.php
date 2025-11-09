<?php

namespace App\Listeners;

use App\Events\ProductViewed;
use App\Models\ProductView;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateProductHotStatus implements ShouldQueue
{
    use InteractsWithQueue;

    public $tries = 3;

    public $backoff = [10, 30, 60];

    public function __construct()
    {
        //
    }

    public function handle(ProductViewed $event): void
    {
        try {
            $product = $event->product;
            $productId = $product->id;

            ProductView::create([
                'product_id' => $productId,
                'ip_address' => $event->ipAddress,
                'user_agent' => $event->userAgent,
                'user_id' => $event->userId,
                'viewed_at' => now(),
            ]);

            $product->increment('views');

            $recentViews = $this->getRecentViewCount($productId);

            $this->updateHotStatus($product, $recentViews);

            Cache::forget("product_{$productId}");
            Cache::forget("hot_products");

        } catch (\Exception $e) {
            Log::error('Failed to update product hot status', [
                'product_id' => $event->product->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Re-throw để queue có thể retry
            throw $e;
        }
    }

    /**
     * Đếm số views trong 7 ngày gần nhất
     */
    private function getRecentViewCount(int $productId): int
    {
        return Cache::remember(
            "product_{$productId}_recent_views",
            now()->addMinutes(5),
            function () use ($productId) {
                return ProductView::forProduct($productId)
                    ->recent(7)
                    ->count();
            }
        );
    }

    /**
     * Update is_hot status dựa trên rule
     * >= 100 views in 7 days: is_hot = true
     * < 100 views in 7 days: is_hot = false
     */
    private function updateHotStatus($product, int $recentViews): void
    {
        $shouldBeHot = null;

        if ($recentViews >= 100) {
            $shouldBeHot = true;
        } else {
            $shouldBeHot = false;
        }

        if ($shouldBeHot !== null) {
            $product->update(['is_hot' => $shouldBeHot]);

            Log::info('Product hot status updated', [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'recent_views' => $recentViews,
                'is_hot' => $product->is_hot,
            ]);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(ProductViewed $event, \Throwable $exception): void
    {
        Log::error('UpdateProductHotStatus job failed permanently', [
            'product_id' => $event->product->id,
            'error' => $exception->getMessage(),
        ]);
    }
}

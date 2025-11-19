<?php

namespace App\Observers;

use App\Models\Order;
use App\Models\CacheKeyManager;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Order Observer
 *
 * Handles cache invalidation when orders are completed
 * Completed orders affect best sellers and top products
 */
class OrderObserver
{
    /**
     * Handle the Order "updated" event.
     *
     * Only clear caches when order status changes to "completed"
     * because completed orders affect best sellers statistics
     */
    public function updated(Order $order): void
    {
        // Check if status was changed to completed
        if ($order->isDirty('status') && $order->status === Order::STATUS_COMPLETED) {
            $this->clearBestSellerCaches($order, 'completed');
        }
    }

    /**
     * Handle the Order "created" event.
     *
     * New orders might affect "recently ordered" or trending products
     * but typically don't affect cached homepage until completed
     */
    public function created(Order $order): void
    {
        // Optional: clear caches immediately if you want real-time updates
        // For better performance, only clear on completion
        // Uncomment below if you need immediate cache refresh

        // $this->clearBestSellerCaches($order, 'created');
    }

    /**
     * Clear best seller related caches
     *
     * When orders are completed, the best selling products might change
     */
    protected function clearBestSellerCaches(Order $order, string $event): void
    {
        try {
            // Only clear best seller caches, not all homepage caches
            // This is more efficient than clearing everything
            Cache::forget(CacheKeyManager::HOME_TOP_SELLING);

            // Optional: also clear hot products as they might be affected by sales
            Cache::forget(CacheKeyManager::HOME_HOT_DEALS);
            Cache::forget(CacheKeyManager::HOME_CATEGORIES_WITH_PRODUCTS);

            Log::info('OrderObserver: Best seller caches cleared', [
                'event' => $event,
                'order_id' => $order->id,
                'order_status' => $order->status,
                'total_items' => $order->total_items
            ]);
        } catch (\Exception $e) {
            Log::error('OrderObserver: Error clearing caches', [
                'event' => $event,
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}

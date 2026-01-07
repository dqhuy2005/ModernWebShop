<?php

namespace App\Observers;

use App\Models\Order;
use App\Models\CacheKeyManager;
use App\Services\impl\RedisService;
use App\Services\impl\ProductSalesCache;
use Illuminate\Support\Facades\Log;

class OrderObserver
{
    protected RedisService $redis;
    protected ProductSalesCache $salesCache;

    public function __construct(RedisService $redis, ProductSalesCache $salesCache)
    {
        $this->redis = $redis;
        $this->salesCache = $salesCache;
    }

    public function updated(Order $order): void
    {
        if ($order->isDirty('status') && $order->status === Order::STATUS_COMPLETED) {
            $this->clearBestSellerCaches($order, 'completed');

            // Invalidate sales cache
            $this->salesCache->invalidate();
        }
    }

    public function created(Order $order): void
    {
        // Optional: clear caches immediately if you want real-time updates
        // For better performance, only clear on completion
        // Uncomment below if you need immediate cache refresh

        // $this->clearBestSellerCaches($order, 'created');
    }

    protected function clearBestSellerCaches(Order $order, string $event): void
    {
        try {
            $this->redis->forget([
                CacheKeyManager::HOME_TOP_SELLING,
                CacheKeyManager::HOME_HOT_DEALS,
                CacheKeyManager::HOME_CATEGORIES_WITH_PRODUCTS
            ]);

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

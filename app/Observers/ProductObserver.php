<?php

namespace App\Observers;

use App\Models\Product;
use App\Models\CacheKeyManager;
use App\Services\RedisService;
use Illuminate\Support\Facades\Log;

class ProductObserver
{
    protected RedisService $redis;

    public function __construct(RedisService $redis)
    {
        $this->redis = $redis;
    }

    public function created(Product $product): void
    {
        $this->clearProductCaches($product, 'created');
    }

    public function updated(Product $product): void
    {
        $this->clearProductCaches($product, 'updated');

        $this->redis->forget(CacheKeyManager::product($product->id));
    }

    public function deleted(Product $product): void
    {
        $this->clearProductCaches($product, 'deleted');

        $this->redis->forget(CacheKeyManager::product($product->id));
    }

    public function restored(Product $product): void
    {
        $this->clearProductCaches($product, 'restored');
    }

    public function forceDeleted(Product $product): void
    {
        $this->clearProductCaches($product, 'force_deleted');

        $this->redis->forget(CacheKeyManager::product($product->id));
    }

    protected function clearProductCaches(Product $product, string $event): void
    {
        try {
            $keys = CacheKeyManager::productKeys();

            $this->redis->forget($keys);

            $this->redis->forget("product_detail_{$product->slug}");
            $this->redis->forget("product_view_stats_{$product->id}");
            $this->redis->deleteByPattern("product_reviews_{$product->id}_page_*");
            $this->redis->forget("product_review_stats_{$product->id}");
            $this->redis->forget("related_products_{$product->id}");

            if ($product->category_id) {
                $this->redis->forget(CacheKeyManager::category($product->category_id));
            }

            Log::info('ProductObserver: Caches cleared', [
                'event' => $event,
                'product_id' => $product->id,
                'product_name' => $product->name,
                'category_id' => $product->category_id,
                'cleared_keys' => count($keys)
            ]);
        } catch (\Exception $e) {
            Log::error('ProductObserver: Error clearing caches', [
                'event' => $event,
                'product_id' => $product->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}

<?php

namespace App\Observers;

use App\Models\Product;
use App\Services\Cache\CacheKeyManager;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Product Observer
 *
 * Handles cache invalidation when products are modified
 * Ensures data consistency between database and cache
 */
class ProductObserver
{
    /**
     * Handle the Product "created" event.
     */
    public function created(Product $product): void
    {
        $this->clearProductCaches($product, 'created');
    }

    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
    {
        $this->clearProductCaches($product, 'updated');

        // Clear specific product cache if exists
        Cache::forget(CacheKeyManager::product($product->id));
    }

    /**
     * Handle the Product "deleted" event.
     */
    public function deleted(Product $product): void
    {
        $this->clearProductCaches($product, 'deleted');

        // Clear specific product cache
        Cache::forget(CacheKeyManager::product($product->id));
    }

    /**
     * Handle the Product "restored" event.
     */
    public function restored(Product $product): void
    {
        $this->clearProductCaches($product, 'restored');
    }

    /**
     * Handle the Product "force deleted" event.
     */
    public function forceDeleted(Product $product): void
    {
        $this->clearProductCaches($product, 'force_deleted');

        // Clear specific product cache
        Cache::forget(CacheKeyManager::product($product->id));
    }

    /**
     * Clear all product-related caches
     */
    protected function clearProductCaches(Product $product, string $event): void
    {
        try {
            $keys = CacheKeyManager::productKeys();

            foreach ($keys as $key) {
                Cache::forget($key);
            }

            // Also clear category-specific cache if product belongs to a category
            if ($product->category_id) {
                Cache::forget(CacheKeyManager::category($product->category_id));
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

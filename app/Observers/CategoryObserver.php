<?php

namespace App\Observers;

use App\Models\Category;
use App\Models\CacheKeyManager;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Category Observer
 *
 * Handles cache invalidation when categories are modified
 * Ensures data consistency between database and cache
 */
class CategoryObserver
{
    /**
     * Handle the Category "created" event.
     */
    public function created(Category $category): void
    {
        $this->clearCategoryCaches($category, 'created');
    }

    /**
     * Handle the Category "updated" event.
     */
    public function updated(Category $category): void
    {
        $this->clearCategoryCaches($category, 'updated');

        // Clear specific category caches
        Cache::forget(CacheKeyManager::category($category->id));
        Cache::forget(CacheKeyManager::categoryBySlug($category->slug));
    }

    /**
     * Handle the Category "deleted" event.
     */
    public function deleted(Category $category): void
    {
        $this->clearCategoryCaches($category, 'deleted');

        // Clear specific category caches
        Cache::forget(CacheKeyManager::category($category->id));
        Cache::forget(CacheKeyManager::categoryBySlug($category->slug));
    }

    /**
     * Handle the Category "restored" event.
     */
    public function restored(Category $category): void
    {
        $this->clearCategoryCaches($category, 'restored');
    }

    /**
     * Handle the Category "force deleted" event.
     */
    public function forceDeleted(Category $category): void
    {
        $this->clearCategoryCaches($category, 'force_deleted');

        // Clear specific category caches
        Cache::forget(CacheKeyManager::category($category->id));
        Cache::forget(CacheKeyManager::categoryBySlug($category->slug));
    }

    /**
     * Clear all category-related caches
     */
    protected function clearCategoryCaches(Category $category, string $event): void
    {
        try {
            $keys = CacheKeyManager::categoryKeys();

            foreach ($keys as $key) {
                Cache::forget($key);
            }

            Log::info('CategoryObserver: Caches cleared', [
                'event' => $event,
                'category_id' => $category->id,
                'category_name' => $category->name,
                'cleared_keys' => count($keys)
            ]);
        } catch (\Exception $e) {
            Log::error('CategoryObserver: Error clearing caches', [
                'event' => $event,
                'category_id' => $category->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}

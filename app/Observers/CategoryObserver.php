<?php

namespace App\Observers;

use App\Models\Category;
use App\Models\CacheKeyManager;
use App\Services\impl\RedisService;
use Illuminate\Support\Facades\Log;

class CategoryObserver
{
    protected RedisService $redis;

    public function __construct(RedisService $redis)
    {
        $this->redis = $redis;
    }

    public function created(Category $category): void
    {
        $this->clearCategoryCaches($category, 'created');
    }

    public function updated(Category $category): void
    {
        $this->clearCategoryCaches($category, 'updated');

        $this->redis->forget(CacheKeyManager::category($category->id));
        $this->redis->forget(CacheKeyManager::categoryBySlug($category->slug));
    }

    public function deleted(Category $category): void
    {
        $this->clearCategoryCaches($category, 'deleted');

        $this->redis->forget(CacheKeyManager::category($category->id));
        $this->redis->forget(CacheKeyManager::categoryBySlug($category->slug));
    }

    public function restored(Category $category): void
    {
        $this->clearCategoryCaches($category, 'restored');
    }

    public function forceDeleted(Category $category): void
    {
        $this->clearCategoryCaches($category, 'force_deleted');

        $this->redis->forget(CacheKeyManager::category($category->id));
        $this->redis->forget(CacheKeyManager::categoryBySlug($category->slug));
    }

    protected function clearCategoryCaches(Category $category, string $event): void
    {
        try {
            $keys = CacheKeyManager::categoryKeys();

            $this->redis->forget($keys);

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

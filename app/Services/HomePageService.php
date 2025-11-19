<?php

namespace App\Services;

use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use App\Models\CacheKeyManager;
use App\Services\Cache\RedisService;
use Illuminate\Support\Facades\Log;

/**
 * Home Page Service
 *
 * Handles all homepage data with Redis caching strategy
 * Implements cache-aside pattern with automatic cache warming
 */
class HomePageService
{
    protected ProductRepository $productRepository;
    protected CategoryRepository $categoryRepository;
    protected RedisService $redis;

    public function __construct(
        ProductRepository $productRepository,
        CategoryRepository $categoryRepository,
        RedisService $redis
    ) {
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
        $this->redis = $redis;
    }

    /**
     * Get all homepage data (cached)
     *
     * Returns all sections needed for homepage in one call
     * Uses medium TTL (30 minutes) as default
     *
     * @return array
     */
    public function getHomePageData(): array
    {
        try {
            return [
                'featuredCategories' => $this->getFeaturedCategories(),
                'newProducts' => $this->getNewProducts(),
                'categories' => $this->getCategoriesWithHotProducts(),
                'topSellingProducts' => $this->getTopSellingProducts(),
                'hotDeals' => $this->getHotDeals(),
            ];
        } catch (\Exception $e) {
            Log::error('HomePageService: Error fetching homepage data', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->getHomePageDataFallback();
        }
    }

    /**
     * Get featured categories (cached in Redis)
     * TTL: 1 hour
     */
    public function getFeaturedCategories(int $limit = 3)
    {
        return $this->redis->remember(
            CacheKeyManager::HOME_FEATURED_CATEGORIES,
            CacheKeyManager::TTL_LONG,
            fn() => $this->categoryRepository->getFeaturedCategories($limit)
        );
    }

    /**
     * Get new products (cached in Redis)
     * TTL: 5 minutes
     */
    public function getNewProducts(int $limit = 8)
    {
        return $this->redis->remember(
            CacheKeyManager::HOME_NEW_PRODUCTS,
            CacheKeyManager::TTL_SHORT,
            fn() => $this->productRepository->getNewProducts($limit)
        );
    }

    /**
     * Get categories with hot products (cached in Redis)
     * TTL: 30 minutes
     */
    public function getCategoriesWithHotProducts(int $categoryLimit = 5, int $productLimit = 15)
    {
        return $this->redis->remember(
            CacheKeyManager::HOME_CATEGORIES_WITH_PRODUCTS,
            CacheKeyManager::TTL_MEDIUM,
            fn() => $this->categoryRepository->getCategoriesWithHotProducts($categoryLimit, $productLimit)
        );
    }

    /**
     * Get top selling products (cached in Redis)
     * TTL: 30 minutes
     */
    public function getTopSellingProducts(int $limit = 12)
    {
        $products = $this->redis->remember(
            CacheKeyManager::HOME_TOP_SELLING,
            CacheKeyManager::TTL_MEDIUM,
            fn() => $this->productRepository->getTopSellingProducts($limit)
        );

        return $products->chunk(6);
    }

    /**
     * Get hot deals (cached in Redis)
     * TTL: 30 minutes
     */
    public function getHotDeals(int $limit = 8)
    {
        return $this->redis->remember(
            CacheKeyManager::HOME_HOT_DEALS,
            CacheKeyManager::TTL_MEDIUM,
            fn() => $this->productRepository->getHotDeals($limit)
        );
    }

    /**
     * Clear all homepage caches from Redis
     */
    public function clearHomePageCache(): void
    {
        try {
            $keys = CacheKeyManager::homePageKeys();

            foreach ($keys as $key) {
                $this->redis->forget($key);
            }

            Log::info('HomePageService: Redis cache cleared successfully', [
                'keys' => $keys,
                'count' => count($keys)
            ]);
        } catch (\Exception $e) {
            Log::error('HomePageService: Error clearing Redis cache', [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Clear homepage cache by pattern (more efficient)
     * Deletes all keys matching "homepage:*"
     */
    public function clearHomePageCacheByPattern(): int
    {
        try {
            $deleted = $this->redis->deleteByPattern('homepage:*');

            Log::info('HomePageService: Redis cache cleared by pattern', [
                'pattern' => 'homepage:*',
                'deleted' => $deleted
            ]);

            return $deleted;
        } catch (\Exception $e) {
            Log::error('HomePageService: Error clearing cache by pattern', [
                'error' => $e->getMessage()
            ]);

            return 0;
        }
    }

    /**
     * Warm up all homepage caches
     *
     * Pre-loads all homepage data into cache
     * Useful after cache clear or on deployment
     */
    public function warmUpCache(): void
    {
        try {
            Log::info('HomePageService: Starting cache warm-up');

            $this->getHomePageData();

            Log::info('HomePageService: Cache warm-up completed successfully');
        } catch (\Exception $e) {
            Log::error('HomePageService: Cache warm-up failed', [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Fallback method when cache fails
     * Returns fresh data from database
     */
    protected function getHomePageDataFallback(): array
    {
        return [
            'featuredCategories' => $this->categoryRepository->getFeaturedCategories(3),
            'newProducts' => $this->productRepository->getNewProducts(8),
            'categories' => $this->categoryRepository->getCategoriesWithHotProducts(5, 15),
            'topSellingProducts' => $this->productRepository->getTopSellingProducts(12)->chunk(6),
            'hotDeals' => $this->productRepository->getHotDeals(8),
        ];
    }

    /**
     * Get cache statistics from Redis
     * Useful for monitoring and debugging
     */
    public function getCacheStats(): array
    {
        $keys = CacheKeyManager::homePageKeys();
        $stats = [
            'keys' => [],
            'redis_server' => []
        ];

        foreach ($keys as $key) {
            $exists = $this->redis->has($key);
            $stats['keys'][$key] = [
                'status' => $exists ? 'HIT' : 'MISS',
                'ttl' => $exists ? $this->redis->ttl($key) : -2
            ];
        }

        $stats['redis_server'] = $this->redis->stats();

        $totalKeys = count($keys);
        $cachedKeys = count(array_filter($stats['keys'], fn($k) => $k['status'] === 'HIT'));
        $stats['summary'] = [
            'total_keys' => $totalKeys,
            'cached_keys' => $cachedKeys,
            'cache_coverage' => $totalKeys > 0 ? round(($cachedKeys / $totalKeys) * 100, 2) : 0
        ];

        return $stats;
    }

    /**
     * Check Redis connection health
     *
     * @return bool
     */
    public function isRedisHealthy(): bool
    {
        return $this->redis->ping();
    }
}

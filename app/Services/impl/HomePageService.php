<?php

namespace App\Services\impl;

use App\Repository\impl\ProductRepository;
use App\Repository\impl\CategoryRepository;
use App\Models\CacheKeyManager;
use App\Services\impl\RedisService;
use Illuminate\Support\Facades\Log;
use App\Services\IHomePageService;

class HomePageService implements IHomePageService
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

    public function getFeaturedCategories(int $limit = 3)
    {
        try {
            return $this->redis->rememberWithWarming(
                CacheKeyManager::HOME_FEATURED_CATEGORIES,
                CacheKeyManager::TTL_LONG,
                fn() => $this->categoryRepository->getFeaturedCategories($limit),
                300
            );
        } catch (\Exception $e) {
            Log::error('HomePageService: Error fetching featured categories', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }

    }

    public function getNewProducts(int $limit = 8)
    {
        return $this->redis->rememberWithWarming(
            CacheKeyManager::HOME_NEW_PRODUCTS,
            CacheKeyManager::TTL_SHORT,
            fn() => $this->productRepository->getNewProducts($limit),
            180
        );
    }

    public function getCategoriesWithHotProducts(int $categoryLimit = 5, int $productLimit = 15)
    {
        return $this->redis->rememberWithWarming(
            CacheKeyManager::HOME_CATEGORIES_WITH_PRODUCTS,
            CacheKeyManager::TTL_MEDIUM,
            fn() => $this->categoryRepository->getCategoriesWithHotProducts($categoryLimit, $productLimit),
            300
        );
    }

    public function getTopSellingProducts(int $limit = 12)
    {
        $products = $this->redis->rememberWithWarming(
            CacheKeyManager::HOME_TOP_SELLING,
            CacheKeyManager::TTL_MEDIUM,
            fn() => $this->productRepository->getTopSellingProducts($limit),
            300
        );

        return $products->chunk(6);
    }

    public function getHotDeals(int $limit = 8)
    {
        return $this->redis->rememberWithWarming(
            CacheKeyManager::HOME_HOT_DEALS,
            CacheKeyManager::TTL_MEDIUM,
            fn() => $this->productRepository->getHotDeals($limit),
            300
        );
    }

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

    public function getNavigationCategories()
    {
        return $this->redis->rememberWithWarming(
            'navigation:categories',
            CacheKeyManager::TTL_LONG,
            fn() => $this->categoryRepository->getNavigationCategories(),
            600
        );
    }

    public function getDisplayCategories()
    {
        return $this->redis->rememberWithWarming(
            'display:categories',
            CacheKeyManager::TTL_MEDIUM,
            fn() => $this->categoryRepository->getDisplayCategories(),
            300
        );
    }
}

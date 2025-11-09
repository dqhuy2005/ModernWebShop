<?php

namespace App\Services;

use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use App\Services\Cache\CacheKeyManager;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Home Page Service
 *
 * Handles all homepage data with caching strategy
 * Implements cache-aside pattern with automatic cache warming
 */
class HomePageService
{
    protected ProductRepository $productRepository;
    protected CategoryRepository $categoryRepository;

    public function __construct(
        ProductRepository $productRepository,
        CategoryRepository $categoryRepository
    ) {
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
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

            // Fallback to non-cached data
            return $this->getHomePageDataFallback();
        }
    }

    /**
     * Get featured categories (cached)
     * TTL: 1 hour
     */
    public function getFeaturedCategories(int $limit = 3)
    {
        return Cache::remember(
            CacheKeyManager::HOME_FEATURED_CATEGORIES,
            CacheKeyManager::TTL_LONG,
            fn() => $this->categoryRepository->getFeaturedCategories($limit)
        );
    }

    /**
     * Get new products (cached)
     */
    public function getNewProducts(int $limit = 8)
    {
        return Cache::remember(
            CacheKeyManager::HOME_NEW_PRODUCTS,
            CacheKeyManager::TTL_SHORT,
            fn() => $this->productRepository->getNewProducts($limit)
        );
    }

    /**
     * Get categories with hot products (cached)
     */
    public function getCategoriesWithHotProducts(int $categoryLimit = 5, int $productLimit = 15)
    {
        return Cache::remember(
            CacheKeyManager::HOME_CATEGORIES_WITH_PRODUCTS,
            CacheKeyManager::TTL_MEDIUM,
            fn() => $this->categoryRepository->getCategoriesWithHotProducts($categoryLimit, $productLimit)
        );
    }

    /**
     * Get top selling products (cached)
     */
    public function getTopSellingProducts(int $limit = 12)
    {
        $products = Cache::remember(
            CacheKeyManager::HOME_TOP_SELLING,
            CacheKeyManager::TTL_MEDIUM,
            fn() => $this->productRepository->getTopSellingProducts($limit)
        );

        // Chunk for display (2 chunks of 6 products each)
        return $products->chunk(6);
    }

    /**
     * Get hot deals (cached)
     */
    public function getHotDeals(int $limit = 8)
    {
        return Cache::remember(
            CacheKeyManager::HOME_HOT_DEALS,
            CacheKeyManager::TTL_MEDIUM,
            fn() => $this->productRepository->getHotDeals($limit)
        );
    }

    /**
     * Clear all homepage caches
     */
    public function clearHomePageCache(): void
    {
        try {
            $keys = CacheKeyManager::homePageKeys();

            foreach ($keys as $key) {
                Cache::forget($key);
            }

            Log::info('HomePageService: Cache cleared successfully', [
                'keys' => $keys
            ]);
        } catch (\Exception $e) {
            Log::error('HomePageService: Error clearing cache', [
                'error' => $e->getMessage()
            ]);
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

            // Load all data which will populate the cache
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
     * Get cache statistics
     * Useful for monitoring and debugging
     */
    public function getCacheStats(): array
    {
        $keys = CacheKeyManager::homePageKeys();
        $stats = [];

        foreach ($keys as $key) {
            $stats[$key] = Cache::has($key) ? 'HIT' : 'MISS';
        }

        return $stats;
    }
}

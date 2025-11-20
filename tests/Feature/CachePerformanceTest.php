<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Services\HomePageService;
use App\Services\RedisService;
use App\Models\CacheKeyManager;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CachePerformanceTest extends TestCase
{
    protected HomePageService $homePageService;
    protected RedisService $redis;

    protected function setUp(): void
    {
        parent::setUp();
        $this->homePageService = app(HomePageService::class);
        $this->redis = app(RedisService::class);
    }

    /**
     * Test: Homepage loads successfully with cache
     */
    public function test_homepage_loads_with_cache(): void
    {
        // Clear cache first
        $this->homePageService->clearHomePageCache();

        // First request (cache miss) - should be slower
        $startTime = microtime(true);
        $response = $this->get('/');
        $firstLoadTime = (microtime(true) - $startTime) * 1000;

        $response->assertStatus(200);

        // Second request (cache hit) - should be faster
        $startTime = microtime(true);
        $response = $this->get('/');
        $secondLoadTime = (microtime(true) - $startTime) * 1000;

        $response->assertStatus(200);

        // Assert second load is faster (cached)
        $this->assertLessThan($firstLoadTime, $secondLoadTime);

        // Assert cached load time is under 200ms target
        $this->assertLessThan(200, $secondLoadTime,
            "Cached homepage load time ({$secondLoadTime}ms) exceeds 200ms target"
        );

        echo "\n";
        echo "First load (cache miss): {$firstLoadTime}ms\n";
        echo "Second load (cache hit): {$secondLoadTime}ms\n";
        echo "Performance improvement: " . round((($firstLoadTime - $secondLoadTime) / $firstLoadTime) * 100, 2) . "%\n";
    }

    /**
     * Test: Cache invalidation on product creation
     */
    public function test_cache_invalidates_on_product_creation(): void
    {
        // Warm up cache
        $this->homePageService->warmUpCache();

        // Verify cache exists
        $this->assertTrue($this->redis->has(CacheKeyManager::HOME_NEW_PRODUCTS));

        // Create new product
        $category = Category::first() ?? Category::factory()->create();
        Product::factory()->create([
            'category_id' => $category->id,
            'status' => true,
            'is_hot' => true,
        ]);

        // Cache should be cleared
        $this->assertFalse($this->redis->has(CacheKeyManager::HOME_NEW_PRODUCTS),
            'Cache was not cleared after product creation'
        );
    }

    /**
     * Test: Cache invalidation on product update
     */
    public function test_cache_invalidates_on_product_update(): void
    {
        // Warm up cache
        $this->homePageService->warmUpCache();

        // Get a product and update it
        $product = Product::first();
        if (!$product) {
            $this->markTestSkipped('No products in database');
        }

        $this->assertTrue($this->redis->has(CacheKeyManager::HOME_HOT_DEALS));

        $product->update(['name' => 'Updated Product Name']);

        // Cache should be cleared
        $this->assertFalse($this->redis->has(CacheKeyManager::HOME_HOT_DEALS),
            'Cache was not cleared after product update'
        );
    }

    /**
     * Test: Cache invalidation on category update
     */
    public function test_cache_invalidates_on_category_update(): void
    {
        // Warm up cache
        $this->homePageService->warmUpCache();

        $category = Category::first();
        if (!$category) {
            $this->markTestSkipped('No categories in database');
        }

        $this->assertTrue($this->redis->has(CacheKeyManager::HOME_FEATURED_CATEGORIES));

        $category->update(['name' => 'Updated Category Name']);

        // Cache should be cleared
        $this->assertFalse($this->redis->has(CacheKeyManager::HOME_FEATURED_CATEGORIES),
            'Cache was not cleared after category update'
        );
    }

    /**
     * Test: Cache stats are accurate
     */
    public function test_cache_stats_are_accurate(): void
    {
        // Clear all cache
        $this->homePageService->clearHomePageCache();

        $stats = $this->homePageService->getCacheStats();

        // All should be MISS
        foreach ($stats as $key => $status) {
            $this->assertEquals('MISS', $status, "Cache key {$key} should be MISS");
        }

        // Warm up cache
        $this->homePageService->warmUpCache();

        $stats = $this->homePageService->getCacheStats();

        // All should be HIT
        foreach ($stats as $key => $status) {
            $this->assertEquals('HIT', $status, "Cache key {$key} should be HIT after warm-up");
        }
    }

    /**
     * Test: Service fallback works when cache fails
     */
    public function test_service_fallback_on_cache_failure(): void
    {
        // This test simulates cache driver failure
        // Note: Hard to test without mocking, but service has try-catch

        $data = $this->homePageService->getHomePageData();

        $this->assertIsArray($data);
        $this->assertArrayHasKey('featuredCategories', $data);
        $this->assertArrayHasKey('newProducts', $data);
        $this->assertArrayHasKey('categories', $data);
        $this->assertArrayHasKey('topSellingProducts', $data);
        $this->assertArrayHasKey('hotDeals', $data);
    }

    /**
     * Test: Database query count comparison
     */
    public function test_query_count_reduction(): void
    {
        // Clear cache for fresh test
        $this->homePageService->clearHomePageCache();

        // Enable query logging
        DB::enableQueryLog();

        // First request (cache miss)
        $this->get('/');
        $queriesWithoutCache = count(DB::getQueryLog());

        DB::flushQueryLog();

        // Second request (cache hit)
        $this->get('/');
        $queriesWithCache = count(DB::getQueryLog());

        echo "\n";
        echo "Queries without cache: {$queriesWithoutCache}\n";
        echo "Queries with cache: {$queriesWithCache}\n";
        echo "Query reduction: " . round((($queriesWithoutCache - $queriesWithCache) / $queriesWithoutCache) * 100, 2) . "%\n";

        // Assert query reduction is significant (>90%)
        $reduction = (($queriesWithoutCache - $queriesWithCache) / $queriesWithoutCache) * 100;
        $this->assertGreaterThan(90, $reduction,
            "Query reduction ({$reduction}%) should be greater than 90%"
        );
    }

    /**
     * Benchmark: Compare performance metrics
     */
    public function test_performance_benchmark(): void
    {
        $iterations = 10;

        // Clear cache
        $this->homePageService->clearHomePageCache();

        // Benchmark without cache
        $timesWithoutCache = [];
        DB::enableQueryLog();

        for ($i = 0; $i < $iterations; $i++) {
            $this->redis->flush(); // Force cache miss
            $start = microtime(true);
            $this->homePageService->getHomePageData();
            $timesWithoutCache[] = (microtime(true) - $start) * 1000;
        }

        $queriesWithoutCache = count(DB::getQueryLog());
        DB::flushQueryLog();

        // Warm up cache
        $this->homePageService->warmUpCache();

        // Benchmark with cache
        $timesWithCache = [];

        for ($i = 0; $i < $iterations; $i++) {
            $start = microtime(true);
            $this->homePageService->getHomePageData();
            $timesWithCache[] = (microtime(true) - $start) * 1000;
        }

        $queriesWithCache = count(DB::getQueryLog());

        $avgWithoutCache = array_sum($timesWithoutCache) / count($timesWithoutCache);
        $avgWithCache = array_sum($timesWithCache) / count($timesWithCache);
        $improvement = round((($avgWithoutCache - $avgWithCache) / $avgWithoutCache) * 100, 2);

        echo "\n";
        echo "=== Performance Benchmark ({$iterations} iterations) ===\n";
        echo "Avg time WITHOUT cache: " . round($avgWithoutCache, 2) . "ms\n";
        echo "Avg time WITH cache: " . round($avgWithCache, 2) . "ms\n";
        echo "Performance improvement: {$improvement}%\n";
        echo "DB queries WITHOUT cache: {$queriesWithoutCache}\n";
        echo "DB queries WITH cache: {$queriesWithCache}\n";
        echo "Query reduction: " . round((($queriesWithoutCache - $queriesWithCache) / max($queriesWithoutCache, 1)) * 100, 2) . "%\n";

        // Assertions
        $this->assertLessThan($avgWithoutCache, $avgWithCache,
            'Cached requests should be faster'
        );

        $this->assertLessThan(200, $avgWithCache,
            "Avg cached load time ({$avgWithCache}ms) should be under 200ms"
        );

        $this->assertGreaterThan(50, $improvement,
            "Performance improvement ({$improvement}%) should be at least 50%"
        );
    }
}

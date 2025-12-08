<?php

namespace Tests\Performance;

use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use App\Models\ProductReview;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductPerformanceTest extends BasePerformanceTest
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Set specific thresholds for product tests
        $this->maxQueryCount = 20;
        $this->maxTotalTime = 500.0;
        $this->slowQueryThreshold = 50;
    }

    /**
     * Test product listing page performance
     */
    public function test_product_listing_performance(): void
    {
        // Arrange: Create test data
        $category = Category::factory()->create();
        Product::factory()->count(50)->create([
            'category_id' => $category->id,
            'status' => true
        ]);

        // Reset query monitor after setup
        $this->queries = [];
        $this->totalQueryTime = 0;

        // Act: Load products with relationships
        $products = Product::with(['category', 'images'])
            ->where('status', true)
            ->paginate(20);

        // Assert: Check performance
        $this->assertMaxQueryCount(5, 'Product listing should use eager loading');
        $this->assertMaxQueryTime(200.0, 'Product listing should load quickly');
        $this->assertNoNPlusOne('Product listing should not have N+1 problems');

        $this->assertCount(20, $products);
    }

    /**
     * Test product detail page performance
     */
    public function test_product_detail_performance(): void
    {
        // Arrange
        $product = Product::factory()
            ->has(ProductReview::factory()->count(10), 'reviews')
            ->create();

        // Reset query monitoring after setup
        $this->resetQueryMonitoring();

        // Act: Simulate product detail loading
        $loadedProduct = Product::with(['category', 'images', 'reviews.user'])
            ->where('slug', $product->slug)
            ->firstOrFail();

        // Assert
        $this->assertMaxQueryCount(5, 'Product detail should use 5 queries max (product, category, images, reviews, users)');
        $this->assertMaxQueryTime(300.0);
        $this->assertNotNull($loadedProduct);
    }

    /**
     * Test product search performance
     */
    public function test_product_search_performance(): void
    {
        // Arrange
        Product::factory()->count(100)->create();
        // Create a product with "test" in the name to ensure search finds results
        Product::factory()->create(['name' => 'Test Product']);

        // Reset query monitoring after setup
        $this->resetQueryMonitoring();

        // Act: Search products
        $startQueries = count($this->queries);
        $results = Product::where('name', 'LIKE', '%test%')
            ->with('category')
            ->limit(20)
            ->get();
        $endQueries = count($this->queries);

        // Assert
        $this->assertEquals(2, $endQueries - $startQueries, 'Should use exactly 2 queries with eager loading');
        $this->assertMaxQueryTime(100.0, 'Search should be fast');
    }

    /**
     * Test related products query optimization
     */
    public function test_related_products_optimization(): void
    {
        // Arrange
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);
        Product::factory()->count(20)->create(['category_id' => $category->id]);

        // Reset query monitoring after setup
        $this->resetQueryMonitoring();

        // Act
        $related = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->with('images')
            ->limit(8)
            ->get();

        // Assert
        $this->assertMaxQueryCount(2, 'Should use 2 queries (products + images)');
        $this->assertCount(8, $related);
    }

    /**
     * Test N+1 problem detection
     */
    public function test_detect_n_plus_one_in_reviews(): void
    {
        // Arrange
        $product = Product::factory()
            ->has(ProductReview::factory()->count(10)->approved(), 'reviews')
            ->create();

        // Reset query monitoring after setup
        $this->resetQueryMonitoring();

        // Act: Bad implementation (N+1)
        $reviews = $product->reviews()->where('status', 'approved')->get();
        $userNames = [];
        foreach ($reviews as $review) {
            $userNames[] = $review->user->fullname; // This triggers N+1
        }

        // Assert
        $this->assertNotEmpty($reviews, 'Should have reviews');
        $this->assertGreaterThan(5, count($this->queries), 'Should have many queries due to N+1 (1 for reviews + 1 per review for user)');
    }

    /**
     * Test optimized reviews (no N+1)
     */
    public function test_optimized_reviews_no_n_plus_one(): void
    {
        // Arrange
        $product = Product::factory()
            ->has(ProductReview::factory()->count(10), 'reviews')
            ->create();

        // Reset query monitoring after setup
        $this->resetQueryMonitoring();

        // Act: Good implementation (eager loading)
        $reviews = $product->approvedReviews()->with('user')->get();
        foreach ($reviews as $review) {
            $userName = $review->user->fullname; // No additional query
        }

        // Assert
        $this->assertMaxQueryCount(2, 'Should use only 2 queries with eager loading');
        $this->assertNoNPlusOne('Should not have N+1 problem');
    }

    /**
     * Test performance comparison between implementations
     */
    public function test_compare_product_loading_methods(): void
    {
        // Arrange
        Category::factory()->count(5)->create();
        Product::factory()->count(30)->create();

        // Compare two implementations
        $comparison = $this->comparePerformance(
            // Implementation 1: Without eager loading
            function () {
                $products = Product::limit(20)->get();
                foreach ($products as $product) {
                    $categoryName = $product->category->name; // N+1
                }
                return $products;
            },
            // Implementation 2: With eager loading
            function () {
                $products = Product::with('category')->limit(20)->get();
                foreach ($products as $product) {
                    $categoryName = $product->category->name;
                }
                return $products;
            }
        );

        // Assert that eager loading is better
        $this->assertLessThan(
            $comparison['implementation_1']['query_count'],
            $comparison['implementation_2']['query_count'],
            'Eager loading should use fewer queries'
        );

        echo "\nPerformance Comparison:\n";
        echo sprintf(
            "  Without eager loading: %d queries, %.2fms\n",
            $comparison['implementation_1']['query_count'],
            $comparison['implementation_1']['query_time']
        );
        echo sprintf(
            "  With eager loading: %d queries, %.2fms\n",
            $comparison['implementation_2']['query_count'],
            $comparison['implementation_2']['query_time']
        );
        echo sprintf(
            "  Improvement: %d fewer queries, %.2fms faster\n",
            abs($comparison['query_count_diff']),
            abs($comparison['query_time_diff'])
        );
    }

    /**
     * Test hot products query performance
     */
    public function test_hot_products_performance(): void
    {
        // Arrange
        Product::factory()->count(100)->create(['is_hot' => true]);

        // Reset query monitoring after setup
        $this->resetQueryMonitoring();

        // Act
        $measure = $this->measureExecutionTime(function () {
            return Product::where('is_hot', true)
                ->where('status', true)
                ->with('category')
                ->orderBy('views', 'desc')
                ->paginate(20);
        });

        // Assert
        $this->assertLessThanOrEqual(10, $measure['query_count'], 'Should use reasonable number of queries');
        $this->assertLessThan(200.0, $measure['query_time'], 'Should execute quickly');

        echo sprintf(
            "\nHot Products Performance: %d queries in %.2fms\n",
            $measure['query_count'],
            $measure['query_time']
        );
    }

    /**
     * Test performance assertions helper
     */
    public function test_product_index_with_performance_check(): void
    {
        // Arrange
        Product::factory()->count(50)->create();

        // Reset query monitoring after setup
        $this->resetQueryMonitoring();

        // Act
        $products = Product::with(['category', 'images'])
            ->where('status', true)
            ->paginate(20);

        // Assert performance automatically
        $this->assertPerformance();

        $this->assertNotEmpty($products);
    }

    /**
     * Test query time measurement for specific operation
     */
    public function test_measure_specific_query_time(): void
    {
        // Arrange
        Product::factory()->count(100)->create();

        // Reset query monitoring after setup
        $this->resetQueryMonitoring();

        // Act & Measure
        $measure = $this->measureExecutionTime(function () {
            return Product::whereHas('category', function ($query) {
                $query->where('status', true);
            })->count();
        });

        // Assert
        $this->assertLessThan(100.0, $measure['query_time'], 'Count query should be fast');
        $this->assertLessThanOrEqual(2, $measure['query_count'], 'Should use minimal queries');

        echo sprintf(
            "\nQuery measurement: %.2fms with %d queries\n",
            $measure['query_time'],
            $measure['query_count']
        );
    }

    /**
     * Generate performance report after tests
     */
    public function test_generate_performance_report(): void
    {
        // Arrange
        Product::factory()->count(20)->create();

        // Reset query monitoring after setup
        $this->resetQueryMonitoring();

        // Act
        Product::with('category')->get();

        // Generate report
        $reportPath = $this->generateQueryReport();

        // Assert
        $this->assertFileExists($reportPath);
        $this->assertStringContainsString('Database Query Performance Report', file_get_contents($reportPath));

        echo "\nPerformance report generated: {$reportPath}\n";
    }
}

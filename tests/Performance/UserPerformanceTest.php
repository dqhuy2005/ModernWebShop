<?php

namespace Tests\Performance;

use App\Models\User;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserPerformanceTest extends BasePerformanceTest
{
    use RefreshDatabase;

    /**
     * Test user listing with orders (N+1 detection example)
     */
    public function test_user_list_with_orders_detects_n_plus_one(): void
    {
        // Arrange: Create users with orders
        User::factory()
            ->count(10)
            ->has(Order::factory()->count(3))
            ->create();

        // Reset query monitoring after setup
        $this->resetQueryMonitoring();

        // Act: Load users without eager loading (BAD)
        $users = User::all();
        foreach ($users as $user) {
            $orderCount = $user->orders->count(); // This triggers N+1
        }

        // Assert: Should detect N+1 problem
        $nPlusOnes = $this->detectNPlusOne();
        $this->assertNotEmpty($nPlusOnes, 'Should detect N+1 problem');
        $this->assertGreaterThan(10, count($this->queries), 'Should have many queries');
    }

    /**
     * Test optimized user listing (no N+1)
     */
    public function test_user_list_optimized_no_n_plus_one(): void
    {
        // Arrange
        User::factory()
            ->count(10)
            ->has(Order::factory()->count(3))
            ->create();

        // Reset query monitoring after setup
        $this->resetQueryMonitoring();

        // Act: Load users with eager loading (GOOD)
        $users = User::with('orders')->get();
        foreach ($users as $user) {
            $orderCount = $user->orders->count(); // No additional query
        }

        // Assert: Should not have N+1
        $this->assertMaxQueryCount(2, 'Should only use 2 queries with eager loading');
        $this->assertNoNPlusOne('Should not have N+1 problem');
    }

    /**
     * Test query execution time measurement
     */
    public function test_measure_user_query_performance(): void
    {
        // Arrange
        User::factory()->count(100)->create();

        // Reset query monitoring after setup
        $this->resetQueryMonitoring();

        // Act & Measure
        $measure = $this->measureExecutionTime(function () {
            return User::where('email', 'LIKE', '%@example.com')->count();
        });

        // Assert
        $this->assertLessThan(50.0, $measure['query_time'], 'Count query should be fast');
        $this->assertEquals(1, $measure['query_count'], 'Should use only 1 query');

        echo sprintf(
            "\n✓ User count query: %.2fms with %d query\n",
            $measure['query_time'],
            $measure['query_count']
        );
    }

    /**
     * Test performance comparison
     */
    public function test_compare_user_loading_strategies(): void
    {
        // Arrange
        User::factory()
            ->count(20)
            ->has(Order::factory()->count(5))
            ->create();

        // Compare implementations
        $comparison = $this->comparePerformance(
            // Strategy 1: Without eager loading
            function () {
                $users = User::limit(10)->get();
                $totalOrders = 0;
                foreach ($users as $user) {
                    $totalOrders += $user->orders->count(); // N+1
                }
                return $totalOrders;
            },
            // Strategy 2: With eager loading
            function () {
                $users = User::withCount('orders')->limit(10)->get();
                $totalOrders = 0;
                foreach ($users as $user) {
                    $totalOrders += $user->orders_count; // No extra query
                }
                return $totalOrders;
            }
        );

        // Assert
        $this->assertLessThan(
            $comparison['implementation_1']['query_count'],
            $comparison['implementation_2']['query_count'],
            'Eager loading should use fewer queries'
        );

        // Display comparison
        echo "\n";
        echo "Performance Comparison:\n";
        echo sprintf(
            "  Strategy 1 (N+1): %d queries in %.2fms\n",
            $comparison['implementation_1']['query_count'],
            $comparison['implementation_1']['query_time']
        );
        echo sprintf(
            "  Strategy 2 (Optimized): %d queries in %.2fms\n",
            $comparison['implementation_2']['query_count'],
            $comparison['implementation_2']['query_time']
        );
        echo sprintf(
            "  Improvement: %d fewer queries (%.1f%% reduction)\n",
            $comparison['query_count_diff'],
            ($comparison['query_count_diff'] / $comparison['implementation_1']['query_count']) * 100
        );
    }

    /**
     * Example test with custom thresholds
     */
    public function test_strict_user_query_limits(): void
    {
        // Set stricter limits for this test
        $this->maxQueryCount = 5;
        $this->maxTotalTime = 200.0;
        $this->slowQueryThreshold = 30;

        // Arrange
        User::factory()->count(50)->create();

        // Reset query monitoring after setup
        $this->resetQueryMonitoring();

        // Act
        $users = User::select('id', 'fullname', 'email')
            ->limit(10)
            ->get();

        // Assert with strict limits
        $this->assertPerformance();
        $this->assertCount(10, $users);
    }
}

<?php

namespace Tests\Performance;

use Tests\TestCase;
use Tests\Traits\DatabaseQueryMonitor;

abstract class BasePerformanceTest extends TestCase
{
    use DatabaseQueryMonitor;

    /**
     * Performance thresholds
     */
    protected int $maxQueryCount = 50;
    protected float $maxTotalTime = 1000.0; // milliseconds
    protected int $slowQueryThreshold = 100; // milliseconds

    /**
     * Setup the test environment
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRequiredData();
        $this->setUpQueryMonitoring();
        $this->setQueryThreshold($this->slowQueryThreshold);
    }

    /**
     * Seed required data for tests
     */
    protected function seedRequiredData(): void
    {
        // Seed roles first (required for users)
        $this->seed(\Database\Seeders\RoleSeeder::class);
    }

    /**
     * Clean up the test environment
     */
    protected function tearDown(): void
    {
        $this->tearDownQueryMonitoring();
        parent::tearDown();
    }

    /**
     * Run performance assertions after test
     */
    protected function assertPerformance(): void
    {
        // Assert query count doesn't exceed maximum
        $this->assertMaxQueryCount(
            $this->maxQueryCount,
            sprintf(
                "Query count exceeded limit. Expected max %d but got %d queries.",
                $this->maxQueryCount,
                count($this->queries)
            )
        );

        // Assert total time doesn't exceed maximum
        $this->assertMaxQueryTime(
            $this->maxTotalTime,
            sprintf(
                "Total query time exceeded limit. Expected max %.2fms but took %.2fms.",
                $this->maxTotalTime,
                $this->totalQueryTime
            )
        );

        // Warn about slow queries but don't fail
        if ($this->queryStats['slow'] > 0) {
            fwrite(STDERR, sprintf(
                "\n⚠️  Warning: Found %d slow queries (>%dms). Consider optimization.\n",
                $this->queryStats['slow'],
                $this->queryThreshold
            ));
        }

        // Detect N+1 problems
        $nPlusOnes = $this->detectNPlusOne();
        if (!empty($nPlusOnes)) {
            fwrite(STDERR, sprintf(
                "\n⚠️  Warning: Detected %d potential N+1 query problems.\n",
                count($nPlusOnes)
            ));
        }
    }

    /**
     * Reset query monitoring (useful after setup data)
     */
    protected function resetQueryMonitoring(): void
    {
        $this->queries = [];
        $this->totalQueryTime = 0;
    }

    /**
     * Helper to measure execution time of a callable
     */
    protected function measureExecutionTime(callable $callback): array
    {
        $startTime = microtime(true);
        $queriesBefore = count($this->queries);

        $result = $callback();

        $endTime = microtime(true);
        $queriesAfter = count($this->queries);

        return [
            'result' => $result,
            'execution_time' => ($endTime - $startTime) * 1000, // milliseconds
            'query_count' => $queriesAfter - $queriesBefore,
            'query_time' => array_sum(array_column(array_slice($this->queries, $queriesBefore), 'time')),
        ];
    }

    /**
     * Compare performance between two implementations
     */
    protected function comparePerformance(callable $implementation1, callable $implementation2): array
    {
        // Measure first implementation
        $this->queries = [];
        $this->totalQueryTime = 0;
        $result1 = $this->measureExecutionTime($implementation1);

        // Measure second implementation
        $this->queries = [];
        $this->totalQueryTime = 0;
        $result2 = $this->measureExecutionTime($implementation2);

        return [
            'implementation_1' => $result1,
            'implementation_2' => $result2,
            'execution_time_diff' => $result2['execution_time'] - $result1['execution_time'],
            'query_count_diff' => $result2['query_count'] - $result1['query_count'],
            'query_time_diff' => $result2['query_time'] - $result1['query_time'],
        ];
    }
}

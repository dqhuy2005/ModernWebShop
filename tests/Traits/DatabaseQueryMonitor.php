<?php

namespace Tests\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait DatabaseQueryMonitor
{
    protected array $queries = [];
    protected float $totalQueryTime = 0;
    protected int $queryThreshold = 100; // milliseconds
    protected bool $logQueries = true;
    protected array $queryStats = [];

    /**
     * Setup query monitoring before each test
     */
    protected function setUpQueryMonitoring(): void
    {
        $this->queries = [];
        $this->totalQueryTime = 0;
        $this->queryStats = [
            'total' => 0,
            'slow' => 0,
            'slowest' => null,
            'fastest' => null,
        ];

        DB::enableQueryLog();

        // Listen to all database queries
        DB::listen(function ($query) {
            $executionTime = $query->time; // milliseconds
            $this->totalQueryTime += $executionTime;

            $queryData = [
                'sql' => $query->sql,
                'bindings' => $query->bindings,
                'time' => $executionTime,
                'formatted_sql' => $this->formatQuery($query->sql, $query->bindings),
            ];

            $this->queries[] = $queryData;
            $this->queryStats['total']++;

            // Track slow queries
            if ($executionTime > $this->queryThreshold) {
                $this->queryStats['slow']++;
            }

            // Track slowest query
            if (!$this->queryStats['slowest'] || $executionTime > $this->queryStats['slowest']['time']) {
                $this->queryStats['slowest'] = $queryData;
            }

            // Track fastest query
            if (!$this->queryStats['fastest'] || $executionTime < $this->queryStats['fastest']['time']) {
                $this->queryStats['fastest'] = $queryData;
            }

            if ($this->logQueries && config('testing.log_queries', false)) {
                $this->logQuery($queryData);
            }
        });
    }

    /**
     * Tear down query monitoring after each test
     */
    protected function tearDownQueryMonitoring(): void
    {
        if (config('testing.show_query_summary', true)) {
            $this->displayQuerySummary();
        }

        DB::flushQueryLog();
    }    /**
     * Format SQL query with bindings
     */
    protected function formatQuery(string $sql, array $bindings): string
    {
        $formattedSql = $sql;
        foreach ($bindings as $binding) {
            $value = is_numeric($binding) ? $binding : "'{$binding}'";
            $formattedSql = preg_replace('/\?/', $value, $formattedSql, 1);
        }
        return $formattedSql;
    }

    /**
     * Log individual query
     */
    protected function logQuery(array $queryData): void
    {
        $message = sprintf(
            "[%s] Query executed in %.2fms:\n%s",
            now()->format('Y-m-d H:i:s.u'),
            $queryData['time'],
            $queryData['formatted_sql']
        );

        if ($queryData['time'] > $this->queryThreshold) {
            Log::channel('query-performance')->warning('SLOW QUERY: ' . $message);
        } else {
            Log::channel('query-performance')->info($message);
        }
    }

    /**
     * Display query summary after test
     */
    protected function displayQuerySummary(): void
    {
        if (empty($this->queries)) {
            return;
        }

        echo "\n\n";
        echo "╔══════════════════════════════════════════════════════════════╗\n";
        echo "║           DATABASE QUERY PERFORMANCE SUMMARY                ║\n";
        echo "╚══════════════════════════════════════════════════════════════╝\n";
        echo sprintf("  Total Queries: %d\n", $this->queryStats['total']);
        echo sprintf("  Total Time: %.2fms\n", $this->totalQueryTime);
        echo sprintf("  Average Time: %.2fms\n", $this->queryStats['total'] > 0 ? $this->totalQueryTime / $this->queryStats['total'] : 0);
        echo sprintf("  Slow Queries (>%dms): %d\n", $this->queryThreshold, $this->queryStats['slow']);

        if ($this->queryStats['slowest']) {
            echo "\n  🐌 Slowest Query (%.2fms):\n", $this->queryStats['slowest']['time'];
            echo "  " . $this->truncateQuery($this->queryStats['slowest']['formatted_sql']) . "\n";
        }

        if ($this->queryStats['fastest']) {
            echo sprintf("\n  ⚡ Fastest Query (%.2fms):\n", $this->queryStats['fastest']['time']);
            echo "  " . $this->truncateQuery($this->queryStats['fastest']['formatted_sql']) . "\n";
        }

        echo "\n" . str_repeat("─", 64) . "\n\n";
    }

    /**
     * Truncate long queries for display
     */
    protected function truncateQuery(string $query, int $length = 100): string
    {
        $query = preg_replace('/\s+/', ' ', trim($query));
        return strlen($query) > $length ? substr($query, 0, $length) . '...' : $query;
    }

    /**
     * Get all executed queries
     */
    protected function getExecutedQueries(): array
    {
        return $this->queries;
    }

    /**
     * Get total query execution time
     */
    protected function getTotalQueryTime(): float
    {
        return $this->totalQueryTime;
    }

    /**
     * Get query statistics
     */
    protected function getQueryStats(): array
    {
        return $this->queryStats;
    }

    /**
     * Set query time threshold for slow query detection
     */
    protected function setQueryThreshold(int $milliseconds): void
    {
        $this->queryThreshold = $milliseconds;
    }

    /**
     * Enable/disable query logging
     */
    protected function setQueryLogging(bool $enabled): void
    {
        $this->logQueries = $enabled;
    }

    /**
     * Assert that number of queries doesn't exceed limit
     */
    protected function assertQueryCount(int $expected, string $message = ''): void
    {
        $actual = count($this->queries);
        $this->assertEquals(
            $expected,
            $actual,
            $message ?: "Expected {$expected} queries but {$actual} were executed."
        );
    }

    /**
     * Assert that number of queries is less than or equal to limit
     */
    protected function assertMaxQueryCount(int $max, string $message = ''): void
    {
        $actual = count($this->queries);
        $this->assertLessThanOrEqual(
            $max,
            $actual,
            $message ?: "Expected maximum {$max} queries but {$actual} were executed."
        );
    }

    /**
     * Assert that total query time doesn't exceed limit
     */
    protected function assertMaxQueryTime(float $maxMilliseconds, string $message = ''): void
    {
        $this->assertLessThanOrEqual(
            $maxMilliseconds,
            $this->totalQueryTime,
            $message ?: sprintf(
                "Expected maximum %.2fms but queries took %.2fms.",
                $maxMilliseconds,
                $this->totalQueryTime
            )
        );
    }

    /**
     * Assert that no slow queries were executed
     */
    protected function assertNoSlowQueries(int $threshold = null, string $message = ''): void
    {
        $threshold = $threshold ?? $this->queryThreshold;
        $slowQueries = array_filter($this->queries, fn($q) => $q['time'] > $threshold);

        $this->assertEmpty(
            $slowQueries,
            $message ?: sprintf(
                "Expected no queries slower than %dms but %d were found.",
                $threshold,
                count($slowQueries)
            )
        );
    }

    /**
     * Assert that specific query was executed
     */
    protected function assertQueryExecuted(string $sqlPattern, string $message = ''): void
    {
        $found = false;
        foreach ($this->queries as $query) {
            if (preg_match($sqlPattern, $query['sql'])) {
                $found = true;
                break;
            }
        }

        $this->assertTrue(
            $found,
            $message ?: "Expected query matching pattern '{$sqlPattern}' was not executed."
        );
    }

    /**
     * Assert that specific query was NOT executed
     */
    protected function assertQueryNotExecuted(string $sqlPattern, string $message = ''): void
    {
        foreach ($this->queries as $query) {
            $this->assertNotMatchesRegularExpression(
                $sqlPattern,
                $query['sql'],
                $message ?: "Query matching pattern '{$sqlPattern}' should not have been executed."
            );
        }
    }

    /**
     * Detect N+1 query problems
     */
    protected function detectNPlusOne(): array
    {
        $suspiciousQueries = [];
        $queryPatterns = [];

        foreach ($this->queries as $index => $query) {
            // Normalize query to detect patterns
            $pattern = preg_replace('/\d+/', '?', $query['sql']);

            if (!isset($queryPatterns[$pattern])) {
                $queryPatterns[$pattern] = [
                    'count' => 0,
                    'total_time' => 0,
                    'queries' => [],
                ];
            }

            $queryPatterns[$pattern]['count']++;
            $queryPatterns[$pattern]['total_time'] += $query['time'];
            $queryPatterns[$pattern]['queries'][] = $index;
        }

        // Find patterns that executed multiple times (potential N+1)
        foreach ($queryPatterns as $pattern => $data) {
            if ($data['count'] > 3) { // Threshold for suspicious repetition
                $suspiciousQueries[] = [
                    'pattern' => $pattern,
                    'count' => $data['count'],
                    'total_time' => $data['total_time'],
                    'average_time' => $data['total_time'] / $data['count'],
                ];
            }
        }

        return $suspiciousQueries;
    }

    /**
     * Assert that no N+1 queries are present
     */
    protected function assertNoNPlusOne(string $message = ''): void
    {
        $nPlusOnes = $this->detectNPlusOne();

        $this->assertEmpty(
            $nPlusOnes,
            $message ?: sprintf(
                "Detected potential N+1 query problems:\n%s",
                json_encode($nPlusOnes, JSON_PRETTY_PRINT)
            )
        );
    }

    /**
     * Generate detailed query report
     */
    protected function generateQueryReport(string $filename = null): string
    {
        $filename = $filename ?? storage_path('logs/query-report-' . now()->format('Y-m-d-His') . '.txt');

        $report = "Database Query Performance Report\n";
        $report .= "Generated: " . now()->toDateTimeString() . "\n";
        $report .= str_repeat("=", 80) . "\n\n";

        $report .= "Summary:\n";
        $report .= sprintf("  Total Queries: %d\n", $this->queryStats['total']);
        $report .= sprintf("  Total Time: %.2fms\n", $this->totalQueryTime);
        $report .= sprintf("  Average Time: %.2fms\n", $this->queryStats['total'] > 0 ? $this->totalQueryTime / $this->queryStats['total'] : 0);
        $report .= sprintf("  Slow Queries: %d\n\n", $this->queryStats['slow']);

        $report .= str_repeat("-", 80) . "\n\n";
        $report .= "All Queries:\n\n";

        foreach ($this->queries as $index => $query) {
            $report .= sprintf("[%d] Time: %.2fms %s\n", $index + 1, $query['time'], $query['time'] > $this->queryThreshold ? '⚠️  SLOW' : '');
            $report .= "SQL: " . $query['formatted_sql'] . "\n";
            $report .= "Bindings: " . json_encode($query['bindings']) . "\n\n";
        }

        // N+1 Detection
        $nPlusOnes = $this->detectNPlusOne();
        if (!empty($nPlusOnes)) {
            $report .= str_repeat("-", 80) . "\n";
            $report .= "⚠️  Potential N+1 Query Problems Detected:\n\n";
            foreach ($nPlusOnes as $problem) {
                $report .= sprintf("  Pattern executed %d times (%.2fms total, %.2fms avg):\n",
                    $problem['count'],
                    $problem['total_time'],
                    $problem['average_time']
                );
                $report .= "  " . $this->truncateQuery($problem['pattern'], 120) . "\n\n";
            }
        }

        file_put_contents($filename, $report);

        return $filename;
    }
}

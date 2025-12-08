<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Query Logging Configuration
    |--------------------------------------------------------------------------
    |
    | Enable or disable query logging during tests. When enabled, all database
    | queries will be logged to the specified channel with execution times.
    |
    */

    'log_queries' => env('TEST_LOG_QUERIES', false),

    /*
    |--------------------------------------------------------------------------
    | Show Query Summary
    |--------------------------------------------------------------------------
    |
    | Display a summary of all queries executed during each test, including
    | total count, execution time, and slowest queries.
    |
    */

    'show_query_summary' => env('TEST_SHOW_QUERY_SUMMARY', true),

    /*
    |--------------------------------------------------------------------------
    | Query Performance Thresholds
    |--------------------------------------------------------------------------
    |
    | Define thresholds for query performance testing. Tests can use these
    | default values or override them individually.
    |
    */

    'thresholds' => [
        'slow_query_time' => env('TEST_SLOW_QUERY_THRESHOLD', 100), // milliseconds
        'max_query_count' => env('TEST_MAX_QUERY_COUNT', 50),
        'max_total_time' => env('TEST_MAX_TOTAL_TIME', 1000), // milliseconds
    ],

    /*
    |--------------------------------------------------------------------------
    | Query Report Configuration
    |--------------------------------------------------------------------------
    |
    | Configure automatic query report generation after test suites.
    |
    */

    'reports' => [
        'enabled' => env('TEST_GENERATE_REPORTS', false),
        'path' => storage_path('logs/query-reports'),
        'format' => 'txt', // txt, json, html
    ],

    /*
    |--------------------------------------------------------------------------
    | N+1 Detection
    |--------------------------------------------------------------------------
    |
    | Configure N+1 query detection sensitivity. A pattern repeated more than
    | this threshold will be flagged as a potential N+1 problem.
    |
    */

    'n_plus_one_threshold' => env('TEST_N_PLUS_ONE_THRESHOLD', 3),

    /*
    |--------------------------------------------------------------------------
    | Performance Comparison
    |--------------------------------------------------------------------------
    |
    | Enable storage of performance metrics for comparison between test runs.
    |
    */

    'comparison' => [
        'enabled' => env('TEST_ENABLE_COMPARISON', false),
        'storage_path' => storage_path('testing/performance-history.json'),
    ],

];

<?php

namespace App\Services\impl;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Cache service for product sales data to avoid expensive subquery joins
 */
class ProductSalesCache
{
    private const CACHE_KEY = 'product_sales_ranking';
    private const CACHE_TTL = 1800; // 30 minutes
    private const WARM_UP_THRESHOLD = 300; // 5 minutes before expiry

    /**
     * Get cached product sales data or fetch from database
     * Returns array: [product_id => total_sold]
     */
    public function getSalesRanking(): array
    {
        try {
            return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
                return $this->fetchSalesData();
            });
        } catch (\Exception $e) {
            Log::error('ProductSalesCache: Error fetching sales ranking', [
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Fetch sales data from database
     */
    private function fetchSalesData(): array
    {
        $results = DB::table('order_details')
            ->join('orders', function ($join) {
                $join->on('order_details.order_id', '=', 'orders.id')
                    ->where('orders.status', '=', 'completed');
            })
            ->select('order_details.product_id', DB::raw('SUM(order_details.quantity) as total_sold'))
            ->groupBy('order_details.product_id')
            ->get();

        $ranking = [];
        foreach ($results as $row) {
            $ranking[$row->product_id] = (int) $row->total_sold;
        }

        return $ranking;
    }

    /**
     * Invalidate cache (call when order status changes to completed)
     */
    public function invalidate(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Warm up cache in background
     */
    public function warmUp(): void
    {
        $this->getSalesRanking();
    }
}

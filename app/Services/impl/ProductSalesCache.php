<?php

namespace App\Services\impl;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductSalesCache
{
    private const CACHE_KEY = 'product_sales_ranking';
    private const CACHE_TTL = 1800;
    private const WARM_UP_THRESHOLD = 300;

    protected RedisService $redis;

    public function __construct(RedisService $redis)
    {
        $this->redis = $redis;
    }

    public function getSalesRanking(): array
    {
        try {
            return $this->redis->remember(self::CACHE_KEY, self::CACHE_TTL, function () {
                return $this->fetchSalesData();
            });
        } catch (\Exception $e) {
            Log::error('ProductSalesCache: Error fetching sales ranking', [
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

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

    public function invalidate(): void
    {
        $this->redis->forget(self::CACHE_KEY);
    }

    public function warmUp(): void
    {
        $this->getSalesRanking();
    }
}

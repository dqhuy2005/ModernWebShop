<?php

namespace App\Repository\impl;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Repository\IDashboardRepository;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Category;

class DashboardRepository implements IDashboardRepository
{
    public function getOverviewStatistics(Carbon $today, Carbon $thisMonth, Carbon $thisYear): array
    {
        $orderStats = Order::selectRaw("
            SUM(CASE WHEN status = 'completed' THEN total_amount ELSE 0 END) as total_revenue,
            SUM(CASE WHEN status = 'completed' AND DATE(created_at) = ? THEN total_amount ELSE 0 END) as today_revenue,
            SUM(CASE WHEN status = 'completed' AND created_at >= ? THEN total_amount ELSE 0 END) as month_revenue,
            SUM(CASE WHEN status = 'completed' AND created_at >= ? THEN total_amount ELSE 0 END) as year_revenue,
            COUNT(*) as total_orders,
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_orders,
            SUM(CASE WHEN status IN ('confirmed', 'processing') THEN 1 ELSE 0 END) as processing_orders,
            SUM(CASE WHEN status IN ('shipping', 'shipped') THEN 1 ELSE 0 END) as shipping_orders,
            SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_orders,
            SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_orders,
            SUM(CASE WHEN DATE(created_at) = ? THEN 1 ELSE 0 END) as today_orders,
            SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as month_orders
        ", [$today, $thisMonth, $thisYear, $today, $thisMonth])
            ->first();

        $productStats = Product::selectRaw("
            COUNT(*) as total_products,
            SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as active_products,
            SUM(CASE WHEN status = 0 THEN 1 ELSE 0 END) as inactive_products
        ")->first();

        $userStats = User::selectRaw("
            COUNT(*) as total_users,
            SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as active_users,
            SUM(CASE WHEN DATE(created_at) = ? THEN 1 ELSE 0 END) as new_users_today,
            SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as new_users_month
        ", [$today, $thisMonth])
            ->whereDoesntHave('role', fn($q) => $q->where('slug', 'admin'))
            ->first();

        $categoryStats = Category::selectRaw("
            COUNT(*) as total_categories,
            SUM(CASE WHEN deleted_at IS NULL THEN 1 ELSE 0 END) as active_categories
        ")->withTrashed()->first();

        return [
            'total_revenue' => $orderStats->total_revenue ?? 0,
            'today_revenue' => $orderStats->today_revenue ?? 0,
            'month_revenue' => $orderStats->month_revenue ?? 0,
            'year_revenue' => $orderStats->year_revenue ?? 0,
            'total_orders' => $orderStats->total_orders ?? 0,
            'pending_orders' => $orderStats->pending_orders ?? 0,
            'processing_orders' => $orderStats->processing_orders ?? 0,
            'shipping_orders' => $orderStats->shipping_orders ?? 0,
            'completed_orders' => $orderStats->completed_orders ?? 0,
            'cancelled_orders' => $orderStats->cancelled_orders ?? 0,
            'today_orders' => $orderStats->today_orders ?? 0,
            'month_orders' => $orderStats->month_orders ?? 0,
            'total_products' => $productStats->total_products ?? 0,
            'active_products' => $productStats->active_products ?? 0,
            'inactive_products' => $productStats->inactive_products ?? 0,
            'low_stock_products' => 0,
            'total_users' => $userStats->total_users ?? 0,
            'active_users' => $userStats->active_users ?? 0,
            'new_users_today' => $userStats->new_users_today ?? 0,
            'new_users_month' => $userStats->new_users_month ?? 0,
            'total_categories' => $categoryStats->total_categories ?? 0,
            'active_categories' => $categoryStats->active_categories ?? 0,
        ];
    }

    /**
     * Get revenue data by year
     */
    public function getRevenueByYear(int $year): Collection
    {
        $startDate = Carbon::create($year, 1, 1)->startOfYear();
        $endDate = Carbon::create($year, 12, 31)->endOfYear();

        return Order::where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('MONTH(created_at) as month, SUM(total_amount) as revenue')
            ->groupBy('month')
            ->pluck('revenue', 'month');
    }

    /**
     * Get revenue data by quarter
     */
    public function getRevenueByQuarter(int $year, int $quarter): Collection
    {
        $startMonth = ($quarter - 1) * 3 + 1;
        $endMonth = $quarter * 3;

        $startDate = Carbon::create($year, $startMonth, 1)->startOfMonth();
        $endDate = Carbon::create($year, $endMonth, 1)->endOfMonth();

        return Order::where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('MONTH(created_at) as month, SUM(total_amount) as revenue')
            ->groupBy('month')
            ->pluck('revenue', 'month');
    }

    /**
     * Get revenue data by month (weekly breakdown)
     */
    public function getRevenueByMonth(int $year, int $month): array
    {
        $startOfMonth = Carbon::create($year, $month, 1)->startOfMonth();
        $endOfMonth = Carbon::create($year, $month, 1)->endOfMonth();

        $weeklyRevenue = [];
        $currentDate = $startOfMonth->copy();
        $weekNumber = 1;

        while ($currentDate->lte($endOfMonth)) {
            $weekStart = $currentDate->copy()->startOfWeek();
            if ($weekStart->lt($startOfMonth)) {
                $weekStart = $startOfMonth->copy();
            }

            $weekEnd = $currentDate->copy()->endOfWeek();
            if ($weekEnd->gt($endOfMonth)) {
                $weekEnd = $endOfMonth->copy();
            }

            $revenue = Order::where('status', 'completed')
                ->whereBetween('created_at', [$weekStart, $weekEnd])
                ->sum('total_amount');

            $weeklyRevenue[$weekNumber] = $revenue;

            $currentDate->addWeek();
            $weekNumber++;
        }

        return $weeklyRevenue;
    }

    /**
     * Get category sales data
     */
    public function getCategorySalesData(Carbon $startDate, Carbon $endDate): Collection
    {
        return DB::table('order_details')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->join('products', 'order_details.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select(
                'categories.id',
                'categories.name',
                DB::raw('SUM(order_details.quantity) as total_quantity'),
                DB::raw('SUM(order_details.total_price) as total_revenue')
            )
            ->where('orders.status', 'completed')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->whereNull('orders.deleted_at')
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total_revenue')
            ->limit(10)
            ->get();
    }

    /**
     * Get top selling products
     */
    public function getTopSellingProducts(int $limit, ?Carbon $startDate = null): Collection
    {
        $query = DB::table('order_details')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->join('products', 'order_details.product_id', '=', 'products.id')
            ->select(
                'products.id',
                'products.name',
                'products.price',
                DB::raw('SUM(order_details.quantity) as total_sold'),
                DB::raw('SUM(order_details.total_price) as total_revenue')
            )
            ->where('orders.status', 'completed')
            ->whereNull('orders.deleted_at');

        if ($startDate) {
            $query->where('orders.created_at', '>=', $startDate);
        }

        return $query->groupBy('products.id', 'products.name', 'products.price')
            ->orderByDesc('total_sold')
            ->limit($limit)
            ->get();
    }
}

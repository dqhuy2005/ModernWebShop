<?php

namespace App\Http\Controllers\CMS;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display dashboard with statistics and charts
     */
    public function index(Request $request)
    {
        $stats = $this->getOverviewStatistics();

        $revenueChartData = $this->getRevenueChartData($request);
        $categoryChartData = $this->getCategoryChartData($request);

        $availableYears = range(date('Y'), 2010);

        $revenueFilter = [
            'type' => $request->get('revenue_type', 'month'),
            'year' => $request->get('revenue_year', date('Y')),
            'month' => $request->get('revenue_month', date('m')),
            'quarter' => $request->get('revenue_quarter', ceil(date('m') / 3))
        ];

        $categoryFilter = [
            'type' => $request->get('category_type', 'month'),
            'year' => $request->get('category_year', date('Y')),
            'month' => $request->get('category_month', date('m'))
        ];

        if ($request->ajax()) {
            return response()->json([
                'revenueChart' => $revenueChartData,
                'categoryChart' => $categoryChartData
            ]);
        }

        return view('admin.dashboard.index', compact(
            'stats',
            'revenueChartData',
            'categoryChartData',
            'availableYears',
            'revenueFilter',
            'categoryFilter'
        ));
    }

    /**
     * Get overview statistics for cards
     */
    protected function getOverviewStatistics()
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();
        $thisYear = Carbon::now()->startOfYear();

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
            // Revenue
            'total_revenue' => $orderStats->total_revenue ?? 0,
            'today_revenue' => $orderStats->today_revenue ?? 0,
            'month_revenue' => $orderStats->month_revenue ?? 0,
            'year_revenue' => $orderStats->year_revenue ?? 0,

            // Orders
            'total_orders' => $orderStats->total_orders ?? 0,
            'pending_orders' => $orderStats->pending_orders ?? 0,
            'processing_orders' => $orderStats->processing_orders ?? 0,
            'shipping_orders' => $orderStats->shipping_orders ?? 0,
            'completed_orders' => $orderStats->completed_orders ?? 0,
            'cancelled_orders' => $orderStats->cancelled_orders ?? 0,
            'today_orders' => $orderStats->today_orders ?? 0,
            'month_orders' => $orderStats->month_orders ?? 0,

            // Products
            'total_products' => $productStats->total_products ?? 0,
            'active_products' => $productStats->active_products ?? 0,
            'inactive_products' => $productStats->inactive_products ?? 0,
            'low_stock_products' => 0,

            // Users
            'total_users' => $userStats->total_users ?? 0,
            'active_users' => $userStats->active_users ?? 0,
            'new_users_today' => $userStats->new_users_today ?? 0,
            'new_users_month' => $userStats->new_users_month ?? 0,

            // Categories
            'total_categories' => $categoryStats->total_categories ?? 0,
            'active_categories' => $categoryStats->active_categories ?? 0,
        ];
    }

    protected function getRevenueChartData(Request $request)
    {
        $type = $request->get('revenue_type', 'month');
        $year = $request->get('revenue_year', date('Y'));
        $month = $request->get('revenue_month', date('m'));
        $quarter = $request->get('revenue_quarter', ceil(date('m') / 3));

        $labels = [];
        $data = [];

        switch ($type) {
            case 'year':
                $startDate = Carbon::create($year, 1, 1)->startOfYear();
                $endDate = Carbon::create($year, 12, 31)->endOfYear();

                $revenues = Order::where('status', 'completed')
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->selectRaw('MONTH(created_at) as month, SUM(total_amount) as revenue')
                    ->groupBy('month')
                    ->pluck('revenue', 'month');

                for ($m = 1; $m <= 12; $m++) {
                    $labels[] = 'Month ' . $m;
                    $data[] = $revenues->get($m, 0);
                }
                break;

            case 'quarter':
                $startMonth = ($quarter - 1) * 3 + 1;
                $endMonth = $quarter * 3;

                $startDate = Carbon::create($year, $startMonth, 1)->startOfMonth();
                $endDate = Carbon::create($year, $endMonth, 1)->endOfMonth();

                $revenues = Order::where('status', 'completed')
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->selectRaw('MONTH(created_at) as month, SUM(total_amount) as revenue')
                    ->groupBy('month')
                    ->pluck('revenue', 'month');

                for ($m = $startMonth; $m <= $endMonth; $m++) {
                    $labels[] = 'Month ' . $m;
                    $data[] = $revenues->get($m, 0);
                }
                break;

            case 'month':
                $startOfMonth = Carbon::create($year, $month, 1)->startOfMonth();
                $endOfMonth = Carbon::create($year, $month, 1)->endOfMonth();

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

                    $labels[] = 'Week ' . $weekNumber;
                    $data[] = $revenue;

                    $currentDate->addWeek();
                    $weekNumber++;
                }
                break;
        }

        return [
            'labels' => $labels,
            'data' => $data,
            'type' => $type,
            'year' => $year,
            'month' => $month,
            'quarter' => $quarter
        ];
    }

    /**
     * Get category sales chart data (Pie Chart)
     * Filter by: week, month, year
     */
    protected function getCategoryChartData(Request $request)
    {
        $type = $request->get('category_type', 'month');
        $year = $request->get('category_year', date('Y'));
        $month = $request->get('category_month', date('m'));

        $startDate = null;
        $endDate = null;

        switch ($type) {
            case 'year':
                $startDate = Carbon::create($year, 1, 1)->startOfYear();
                $endDate = Carbon::create($year, 12, 31)->endOfYear();
                break;

            case 'month':
                $startDate = Carbon::create($year, $month, 1)->startOfMonth();
                $endDate = Carbon::create($year, $month, 1)->endOfMonth();
                break;
        }

        $categorySales = DB::table('order_details')
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

        $labels = [];
        $data = [];
        $quantities = [];
        $colors = [
            '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF',
            '#FF9F40', '#FF6384', '#C9CBCF', '#4BC0C0', '#FF6384'
        ];

        foreach ($categorySales as $index => $category) {
            $labels[] = $category->name;
            $data[] = $category->total_revenue;
            $quantities[] = $category->total_quantity;
        }

        return [
            'labels' => $labels,
            'data' => $data,
            'quantities' => $quantities,
            'colors' => array_slice($colors, 0, count($labels)),
            'type' => $type,
            'year' => $year,
            'month' => $month,
        ];
    }

    /**
     * Get recent orders for dashboard
     */
    public function getRecentOrders(Request $request)
    {
        $limit = $request->get('limit', 10);

        $orders = Order::with('user:id,fullname,email')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        return response()->json($orders);
    }

    /**
     * Get top selling products
     */
    public function getTopProducts(Request $request)
    {
        $limit = $request->get('limit', 10);
        $period = $request->get('period', 'month');

        $startDate = match($period) {
            'week' => Carbon::now()->startOfWeek(),
            'month' => Carbon::now()->startOfMonth(),
            'year' => Carbon::now()->startOfYear(),
            default => null
        };

        $query = DB::table('order_details')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->join('products', 'order_details.product_id', '=', 'products.id')
            ->select(
                'products.id',
                'products.name',
                'products.price',
                'products.image',
                DB::raw('SUM(order_details.quantity) as total_sold'),
                DB::raw('SUM(order_details.total_price) as total_revenue')
            )
            ->where('orders.status', 'completed')
            ->whereNull('orders.deleted_at');

        if ($startDate) {
            $query->where('orders.created_at', '>=', $startDate);
        }

        $products = $query->groupBy('products.id', 'products.name', 'products.price', 'products.image')
            ->orderByDesc('total_sold')
            ->limit($limit)
            ->get();

        return response()->json($products);
    }
}

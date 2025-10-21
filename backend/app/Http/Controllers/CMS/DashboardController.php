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
        // Get overview statistics
        $stats = $this->getOverviewStatistics();

        // Get chart data based on filters
        $revenueChartData = $this->getRevenueChartData($request);
        $categoryChartData = $this->getCategoryChartData($request);

        // Get available years (from 2010 to current year)
        $availableYears = range(date('Y'), 2010);

        // Get current filters
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

        return [
            // Revenue statistics
            'total_revenue' => Order::where('status', 'completed')
                ->sum('total_amount'),
            'today_revenue' => Order::where('status', 'completed')
                ->whereDate('created_at', $today)
                ->sum('total_amount'),
            'month_revenue' => Order::where('status', 'completed')
                ->where('created_at', '>=', $thisMonth)
                ->sum('total_amount'),
            'year_revenue' => Order::where('status', 'completed')
                ->where('created_at', '>=', $thisYear)
                ->sum('total_amount'),

            // Order statistics
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'processing_orders' => Order::whereIn('status', ['confirmed', 'processing'])->count(),
            'shipping_orders' => Order::whereIn('status', ['shipping', 'shipped'])->count(),
            'completed_orders' => Order::where('status', 'completed')->count(),
            'cancelled_orders' => Order::where('status', 'cancelled')->count(),
            'today_orders' => Order::whereDate('created_at', $today)->count(),
            'month_orders' => Order::where('created_at', '>=', $thisMonth)->count(),

            // Product statistics
            'total_products' => Product::count(),
            'active_products' => Product::where('status', true)->count(),
            'inactive_products' => Product::where('status', false)->count(),
            'low_stock_products' => 0,

            // User statistics
            'total_users' => User::whereDoesntHave('role', fn($q) => $q->where('slug', 'admin'))->count(),
            'active_users' => User::where('status', true)
                ->whereDoesntHave('role', fn($q) => $q->where('slug', 'admin'))
                ->count(),
            'new_users_today' => User::whereDate('created_at', $today)
                ->whereDoesntHave('role', fn($q) => $q->where('slug', 'admin'))
                ->count(),
            'new_users_month' => User::where('created_at', '>=', $thisMonth)
                ->whereDoesntHave('role', fn($q) => $q->where('slug', 'admin'))
                ->count(),

            // Category statistics
            'total_categories' => Category::count(),
            'active_categories' => Category::whereNotNull('deleted_at')->count(),
        ];
    }

    /**
     * Get revenue chart data (Line Chart)
     * Filter by: year, month, quarter, week
     */
    protected function getRevenueChartData(Request $request)
    {
        $type = $request->get('revenue_type', 'month'); // year, month, quarter, week
        $year = $request->get('revenue_year', date('Y'));
        $month = $request->get('revenue_month', date('m'));
        $quarter = $request->get('revenue_quarter', ceil(date('m') / 3));

        $labels = [];
        $data = [];

        switch ($type) {
            case 'year':
                // Show 12 months of selected year
                for ($m = 1; $m <= 12; $m++) {
                    $startDate = Carbon::create($year, $m, 1)->startOfMonth();
                    $endDate = Carbon::create($year, $m, 1)->endOfMonth();

                    $revenue = Order::where('status', 'completed')
                        ->whereBetween('created_at', [$startDate, $endDate])
                        ->sum('total_amount');

                    $labels[] = 'Tháng ' . $m;
                    $data[] = $revenue;
                }
                break;

            case 'quarter':
                // Show months in selected quarter
                $startMonth = ($quarter - 1) * 3 + 1;
                $endMonth = $quarter * 3;

                for ($m = $startMonth; $m <= $endMonth; $m++) {
                    $startDate = Carbon::create($year, $m, 1)->startOfMonth();
                    $endDate = Carbon::create($year, $m, 1)->endOfMonth();

                    $revenue = Order::where('status', 'completed')
                        ->whereBetween('created_at', [$startDate, $endDate])
                        ->sum('total_amount');

                    $labels[] = 'Tháng ' . $m;
                    $data[] = $revenue;
                }
                break;

            case 'month':
                // Show weeks in selected month
                $startOfMonth = Carbon::create($year, $month, 1)->startOfMonth();
                $endOfMonth = Carbon::create($year, $month, 1)->endOfMonth();

                $currentDate = $startOfMonth->copy();
                $weekNumber = 1;

                while ($currentDate->lte($endOfMonth)) {
                    // Start of week (Monday) or start of month if first week
                    $weekStart = $currentDate->copy()->startOfWeek();
                    if ($weekStart->lt($startOfMonth)) {
                        $weekStart = $startOfMonth->copy();
                    }

                    // End of week (Sunday) or end of month if last week
                    $weekEnd = $currentDate->copy()->endOfWeek();
                    if ($weekEnd->gt($endOfMonth)) {
                        $weekEnd = $endOfMonth->copy();
                    }

                    $revenue = Order::where('status', 'completed')
                        ->whereBetween('created_at', [$weekStart, $weekEnd])
                        ->sum('total_amount');

                    $labels[] = 'Tuần ' . $weekNumber;
                    $data[] = $revenue;

                    // Move to next week
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
        $type = $request->get('category_type', 'month'); // week, month, year
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

        // Get category sales statistics
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
            ->limit(10) // Top 10 categories
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
        $period = $request->get('period', 'month'); // week, month, year, all

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

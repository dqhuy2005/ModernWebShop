<?php

namespace App\Http\Controllers\CMS;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Repository\impl\DashboardRepository;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Carbon\Carbon;

class DashboardController extends Controller
{
    protected DashboardRepository $dashboardRepository;

    public function __construct(DashboardRepository $dashboardRepository)
    {
        $this->dashboardRepository = $dashboardRepository;
    }

    /**
     * Display dashboard with statistics and charts
     */
    public function index(Request $request): View|JsonResponse
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
    protected function getOverviewStatistics(): array
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();
        $thisYear = Carbon::now()->startOfYear();

        return $this->dashboardRepository->getOverviewStatistics($today, $thisMonth, $thisYear);
    }

    /**
     * Get revenue chart data
     */
    protected function getRevenueChartData(Request $request): array
    {
        $type = $request->get('revenue_type', 'month');
        $year = $request->get('revenue_year', date('Y'));
        $month = $request->get('revenue_month', date('m'));
        $quarter = $request->get('revenue_quarter', ceil(date('m') / 3));

        $labels = [];
        $data = [];

        switch ($type) {
            case 'year':
                $revenues = $this->dashboardRepository->getRevenueByYear($year);

                for ($m = 1; $m <= 12; $m++) {
                    $labels[] = 'Month ' . $m;
                    $data[] = $revenues->get($m, 0);
                }
                break;

            case 'quarter':
                $startMonth = ($quarter - 1) * 3 + 1;
                $endMonth = $quarter * 3;

                $revenues = $this->dashboardRepository->getRevenueByQuarter($year, $quarter);

                for ($m = $startMonth; $m <= $endMonth; $m++) {
                    $labels[] = 'Month ' . $m;
                    $data[] = $revenues->get($m, 0);
                }
                break;

            case 'month':
                $weeklyRevenue = $this->dashboardRepository->getRevenueByMonth($year, $month);

                foreach ($weeklyRevenue as $weekNumber => $revenue) {
                    $labels[] = 'Week ' . $weekNumber;
                    $data[] = $revenue;
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
     */
    protected function getCategoryChartData(Request $request): array
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

        $categorySales = $this->dashboardRepository->getCategorySalesData($startDate, $endDate);

        $labels = [];
        $data = [];
        $quantities = [];
        $colors = [
            '#FF6384',
            '#36A2EB',
            '#FFCE56',
            '#4BC0C0',
            '#9966FF',
            '#FF9F40',
            '#FF6384',
            '#C9CBCF',
            '#4BC0C0',
            '#FF6384'
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
    public function getRecentOrders(Request $request): JsonResponse
    {
        $limit = $request->get('limit', 10);

        $orders = Order::select('id', 'user_id', 'total_amount', 'total_items', 'status', 'created_at')
            ->with('user:id,fullname,email')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        return response()->json($orders);
    }

    /**
     * Get top selling products
     */
    public function getTopProducts(Request $request): JsonResponse
    {
        $limit = $request->get('limit', 10);
        $period = $request->get('period', 'month');

        $startDate = match ($period) {
            'week' => Carbon::now()->startOfWeek(),
            'month' => Carbon::now()->startOfMonth(),
            'year' => Carbon::now()->startOfYear(),
            default => null
        };

        $products = $this->dashboardRepository->getTopSellingProducts($limit, $startDate);

        return response()->json($products);
    }
}

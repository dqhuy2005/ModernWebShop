<?php

namespace App\Repository;

use Illuminate\Support\Collection;
use Carbon\Carbon;

interface IDashboardRepository
{
    public function getOverviewStatistics(Carbon $today, Carbon $thisMonth, Carbon $thisYear): array;
    
    public function getRevenueByYear(int $year): Collection;
    
    public function getRevenueByQuarter(int $year, int $quarter): Collection;
    
    public function getRevenueByMonth(int $year, int $month): array;
    
    public function getCategorySalesData(Carbon $startDate, Carbon $endDate): Collection;
    
    public function getTopSellingProducts(int $limit, ?Carbon $startDate = null): Collection;
}

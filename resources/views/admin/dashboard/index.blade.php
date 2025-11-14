@extends('layouts.admin.app')

@section('title', 'Dashboard - Admin Panel')

@section('content')
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0">
                    <i class="fas fa-chart-line me-2"></i>Dashboard
                </h1>
            </div>
            <div>
                <span class="text-muted">
                    <i class="fas fa-calendar me-1"></i>
                    {{ now()->format('d/m/Y H:i') }}
                </span>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card stats-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-2 text-uppercase small">Total Revenue</p>
                            <h3 class="mb-0 fw-bold">{{ number_format($stats['total_revenue']) }} ₫</h3>
                            <small class="text-success">
                                <i class="fas fa-arrow-up"></i>
                                All Completed Orders
                            </small>
                        </div>
                        <div class="stats-icon bg-primary-soft">
                            <i class="fas fa-dollar-sign text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stats-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-2 text-uppercase small">Today's Revenue</p>
                            <h3 class="mb-0 fw-bold">{{ number_format($stats['today_revenue']) }} ₫</h3>
                            <small class="text-info">
                                <i class="fas fa-calendar-day"></i>
                                {{ now()->format('d/m/Y') }}
                            </small>
                        </div>
                        <div class="stats-icon bg-info-soft">
                            <i class="fas fa-chart-line text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stats-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-2 text-uppercase small">This Month Revenue</p>
                            <h3 class="mb-0 fw-bold">{{ number_format($stats['month_revenue']) }} ₫</h3>
                            <small class="text-success">
                                <i class="fas fa-calendar-alt"></i>
                                {{ now()->format('F Y') }}
                            </small>
                        </div>
                        <div class="stats-icon bg-success-soft">
                            <i class="fas fa-wallet text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stats-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-2 text-uppercase small">This Year Revenue</p>
                            <h3 class="mb-0 fw-bold">{{ number_format($stats['year_revenue']) }} ₫</h3>
                            <small class="text-warning">
                                <i class="fas fa-calendar"></i>
                                Year {{ now()->format('Y') }}
                            </small>
                        </div>
                        <div class="stats-icon bg-warning-soft">
                            <i class="fas fa-coins text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card stats-card-sm border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="stats-icon-sm bg-primary-soft mx-auto mb-2">
                        <i class="fas fa-shopping-cart text-primary"></i>
                    </div>
                    <h4 class="mb-1 fw-bold">{{ $stats['total_orders'] }}</h4>
                    <p class="text-muted small mb-0">Total Orders</p>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 col-6">
            <div class="card stats-card-sm border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="stats-icon-sm bg-warning-soft mx-auto mb-2">
                        <i class="fas fa-clock text-warning"></i>
                    </div>
                    <h4 class="mb-1 fw-bold">{{ $stats['pending_orders'] }}</h4>
                    <p class="text-muted small mb-0">Pending</p>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 col-6">
            <div class="card stats-card-sm border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="stats-icon-sm bg-info-soft mx-auto mb-2">
                        <i class="fas fa-cog text-info"></i>
                    </div>
                    <h4 class="mb-1 fw-bold">{{ $stats['processing_orders'] }}</h4>
                    <p class="text-muted small mb-0">Processing</p>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 col-6">
            <div class="card stats-card-sm border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="stats-icon-sm bg-primary-soft mx-auto mb-2">
                        <i class="fas fa-truck text-primary"></i>
                    </div>
                    <h4 class="mb-1 fw-bold">{{ $stats['shipping_orders'] }}</h4>
                    <p class="text-muted small mb-0">Shipping</p>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 col-6">
            <div class="card stats-card-sm border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="stats-icon-sm bg-success-soft mx-auto mb-2">
                        <i class="fas fa-check-circle text-success"></i>
                    </div>
                    <h4 class="mb-1 fw-bold">{{ $stats['completed_orders'] }}</h4>
                    <p class="text-muted small mb-0">Completed</p>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 col-6">
            <div class="card stats-card-sm border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="stats-icon-sm bg-danger-soft mx-auto mb-2">
                        <i class="fas fa-times-circle text-danger"></i>
                    </div>
                    <h4 class="mb-1 fw-bold">{{ $stats['cancelled_orders'] }}</h4>
                    <p class="text-muted small mb-0">Cancelled</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon bg-primary-soft me-3">
                            <i class="fas fa-box text-primary"></i>
                        </div>
                        <div>
                            <h5 class="mb-0">{{ $stats['total_products'] }}</h5>
                            <small class="text-muted">Products ({{ $stats['active_products'] }} active)</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon bg-success-soft me-3">
                            <i class="fas fa-users text-success"></i>
                        </div>
                        <div>
                            <h5 class="mb-0">{{ $stats['total_users'] }}</h5>
                            <small class="text-muted">Users ({{ $stats['new_users_month'] }} new this month)</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon bg-info-soft me-3">
                            <i class="fas fa-list text-info"></i>
                        </div>
                        <div>
                            <h5 class="mb-0">{{ $stats['total_categories'] }}</h5>
                            <small class="text-muted">Categories ({{ $stats['active_categories'] }} active)</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon bg-warning-soft me-3">
                            <i class="fas fa-shopping-bag text-warning"></i>
                        </div>
                        <div>
                            <h5 class="mb-0">{{ $stats['today_orders'] }}</h5>
                            <small class="text-muted">Today's Orders</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-3 pb-0">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-line me-2 text-primary"></i>
                            Revenue Chart
                        </h5>
                    </div>

                    <div class="row g-2">
                        <div class="col-md-3">
                            <select id="revenueType" class="form-select form-select-sm">
                                <option value="year" {{ $revenueFilter['type'] == 'year' ? 'selected' : '' }}>By Year</option>
                                <option value="quarter" {{ $revenueFilter['type'] == 'quarter' ? 'selected' : '' }}>By Quarter</option>
                                <option value="month" {{ $revenueFilter['type'] == 'month' ? 'selected' : '' }}>By Month</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select id="revenueYear" class="form-select form-select-sm">
                                @foreach($availableYears as $year)
                                    <option value="{{ $year }}" {{ $revenueFilter['year'] == $year ? 'selected' : '' }}>
                                        Year {{ $year }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3" id="revenueQuarterContainer" style="display: none;">
                            <select id="revenueQuarter" class="form-select form-select-sm">
                                <option value="1" {{ $revenueFilter['quarter'] == 1 ? 'selected' : '' }}>Q1</option>
                                <option value="2" {{ $revenueFilter['quarter'] == 2 ? 'selected' : '' }}>Q2</option>
                                <option value="3" {{ $revenueFilter['quarter'] == 3 ? 'selected' : '' }}>Q3</option>
                                <option value="4" {{ $revenueFilter['quarter'] == 4 ? 'selected' : '' }}>Q4</option>
                            </select>
                        </div>
                        <div class="col-md-3" id="revenueMonthContainer" style="display: none;">
                            <select id="revenueMonth" class="form-select form-select-sm">
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ $revenueFilter['month'] == $m ? 'selected' : '' }}>
                                        Month {{ $m }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" height="80"></canvas>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-3 pb-0">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-pie me-2 text-success"></i>
                            Top Categories
                        </h5>
                    </div>

                    <div class="row g-2">
                        <div class="col-md-4">
                            <select id="categoryType" class="form-select form-select-sm">
                                <option value="year" {{ $categoryFilter['type'] == 'year' ? 'selected' : '' }}>Year</option>
                                <option value="month" {{ $categoryFilter['type'] == 'month' ? 'selected' : '' }}>Month</option>
                                <option value="week" {{ $categoryFilter['type'] == 'week' ? 'selected' : '' }}>Week</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select id="categoryYear" class="form-select form-select-sm">
                                @foreach($availableYears as $year)
                                    <option value="{{ $year }}" {{ $categoryFilter['year'] == $year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4" id="categoryMonthContainer" style="display: none;">
                            <select id="categoryMonth" class="form-select form-select-sm">
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ $categoryFilter['month'] == $m ? 'selected' : '' }}>
                                        M{{ $m }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="categoryChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .stats-card {
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }

    .stats-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }

    .stats-icon-sm {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }

    .stats-card-sm {
        transition: transform 0.2s;
    }

    .stats-card-sm:hover {
        transform: translateY(-3px);
    }

    .bg-primary-soft { background-color: rgba(13, 110, 253, 0.1); }
    .bg-success-soft { background-color: rgba(25, 135, 84, 0.1); }
    .bg-warning-soft { background-color: rgba(255, 193, 7, 0.1); }
    .bg-info-soft { background-color: rgba(13, 202, 240, 0.1); }
    .bg-danger-soft { background-color: rgba(220, 53, 69, 0.1); }

    .card-header {
        padding: 1.25rem;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    let revenueChart = null;
    let categoryChart = null;

    $(document).ready(function() {
        initRevenueChart(@json($revenueChartData));
        initCategoryChart(@json($categoryChartData));

        setupRevenueFilters();
        setupCategoryFilters();

        updateRevenueFilterVisibility();
        updateCategoryFilterVisibility();
    });

    function initRevenueChart(data) {
        const ctx = $('#revenueChart')[0].getContext('2d');

        if (revenueChart) {
            revenueChart.destroy();
        }

        revenueChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Revenue (VND)',
                    data: data.data,
                    borderColor: 'rgb(13, 110, 253)',
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    pointBackgroundColor: 'rgb(13, 110, 253)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += new Intl.NumberFormat('vi-VN').format(context.parsed.y) + ' ₫';
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return new Intl.NumberFormat('vi-VN', {
                                    notation: 'compact',
                                    compactDisplay: 'short'
                                }).format(value) + ' ₫';
                            }
                        }
                    }
                }
            }
        });
    }

    function initCategoryChart(data) {
        const ctx = $('#categoryChart')[0].getContext('2d');

        if (categoryChart) {
            categoryChart.destroy();
        }

        categoryChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: data.labels,
                datasets: [{
                    data: data.data,
                    backgroundColor: data.colors,
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            font: {
                                size: 11
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = new Intl.NumberFormat('vi-VN').format(context.parsed);
                                const quantity = data.quantities[context.dataIndex];
                                return [
                                    label,
                                    'Revenue: ' + value + ' ₫',
                                    'Quantity: ' + quantity + ' products'
                                ];
                            }
                        }
                    }
                }
            }
        });
    }

    function setupRevenueFilters() {
        $('#revenueType, #revenueYear, #revenueQuarter, #revenueMonth').on('change', function() {
            updateRevenueFilterVisibility();
            loadRevenueChart();
        });
    }

    function setupCategoryFilters() {
        $('#categoryType, #categoryYear, #categoryMonth').on('change', function() {
            updateCategoryFilterVisibility();
            loadCategoryChart();
        });
    }

    function updateRevenueFilterVisibility() {
        const type = $('#revenueType').val();

        $('#revenueQuarterContainer').hide();
        $('#revenueMonthContainer').hide();

        if (type === 'quarter') {
            $('#revenueQuarterContainer').show();
        } else if (type === 'month' || type === 'week') {
            $('#revenueMonthContainer').show();
        }
    }

    function updateCategoryFilterVisibility() {
        const type = $('#categoryType').val();

        $('#categoryMonthContainer').hide();

        if (type === 'month' || type === 'week') {
            $('#categoryMonthContainer').show();
        }
    }

    function loadRevenueChart() {
        const params = {
            revenue_type: $('#revenueType').val(),
            revenue_year: $('#revenueYear').val(),
            revenue_quarter: $('#revenueQuarter').val(),
            revenue_month: $('#revenueMonth').val()
        };

        $.ajax({
            url: '{{ route('admin.dashboard.index') }}',
            type: 'GET',
            data: params,
            success: function(response) {
                if (response.revenueChart) {
                    initRevenueChart(response.revenueChart);
                }
            },
            error: function(xhr) {
                console.error('Failed to load revenue chart:', xhr);
                toastr.error('Failed to load revenue chart');
            }
        });
    }

    function loadCategoryChart() {
        const params = {
            category_type: $('#categoryType').val(),
            category_year: $('#categoryYear').val(),
            category_month: $('#categoryMonth').val()
        };

        $.ajax({
            url: '{{ route('admin.dashboard.index') }}',
            type: 'GET',
            data: params,
            success: function(response) {
                if (response.categoryChart) {
                    initCategoryChart(response.categoryChart);
                }
            },
            error: function(xhr) {
                console.error('Failed to load category chart:', xhr);
                toastr.error('Failed to load category chart');
            }
        });
    }
</script>
@endpush

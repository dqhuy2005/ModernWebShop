@extends('layouts.admin.app')

@section('title', 'Dashboard - Admin Panel')

@section('content')
<div class="page-header">
    <h1 class="h3 mb-0">Dashboard</h1>
</div>

<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card border-start border-primary border-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small">Tổng doanh thu</div>
                        <div class="h4 mb-0">125.000.000₫</div>
                        <small class="text-success">
                            <i class="fas fa-arrow-up"></i> 12% so với tháng trước
                        </small>
                    </div>
                    <div class="text-primary">
                        <i class="fas fa-dollar-sign fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card border-start border-success border-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small">Đơn hàng mới</div>
                        <div class="h4 mb-0">342</div>
                        <small class="text-success">
                            <i class="fas fa-arrow-up"></i> 8% so với tháng trước
                        </small>
                    </div>
                    <div class="text-success">
                        <i class="fas fa-shopping-cart fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card border-start border-info border-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small">Khách hàng</div>
                        <div class="h4 mb-0">1.234</div>
                        <small class="text-success">
                            <i class="fas fa-arrow-up"></i> 5% so với tháng trước
                        </small>
                    </div>
                    <div class="text-info">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card border-start border-warning border-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small">Sản phẩm</div>
                        <div class="h4 mb-0">567</div>
                        <small class="text-muted">Tổng số sản phẩm</small>
                    </div>
                    <div class="text-warning">
                        <i class="fas fa-box fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-8 mb-4">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-chart-line me-1"></i> Doanh thu theo tháng
            </div>
            <div class="card-body">
                <canvas id="revenueChart" height="100"></canvas>
            </div>
        </div>
    </div>

    <div class="col-xl-4 mb-4">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-chart-pie me-1"></i> Sản phẩm theo danh mục
            </div>
            <div class="card-body">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-table me-1"></i> Đơn hàng gần đây</span>
                <a href="#" class="btn btn-sm btn-primary">
                    Xem tất cả
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Mã đơn</th>
                                <th>Khách hàng</th>
                                <th>Sản phẩm</th>
                                <th>Tổng tiền</th>
                                <th>Trạng thái</th>
                                <th>Ngày đặt</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @for($i = 1; $i <= 5; $i++)
                            <tr>
                                <td>#ORD-{{ str_pad($i, 5, '0', STR_PAD_LEFT) }}</td>
                                <td>Khách hàng {{ $i }}</td>
                                <td>{{ rand(1, 5) }} sản phẩm</td>
                                <td>{{ number_format(rand(100000, 5000000)) }}₫</td>
                                <td>
                                    <span class="badge bg-{{ ['warning', 'info', 'success'][array_rand(['warning', 'info', 'success'])] }}">
                                        {{ ['Chờ xử lý', 'Đang giao', 'Hoàn thành'][array_rand(['Chờ xử lý', 'Đang giao', 'Hoàn thành'])] }}
                                    </span>
                                </td>
                                <td>{{ now()->subDays($i)->format('d/m/Y') }}</td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Revenue Chart
        const revenueCtx = $('#revenueChart')[0].getContext('2d');
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: ['T1', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'T8', 'T9', 'T10', 'T11', 'T12'],
                datasets: [{
                    label: 'Doanh thu (triệu đồng)',
                    data: [12, 19, 15, 25, 22, 30, 28, 32, 35, 40, 38, 45],
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                }
            }
        });

        // Category Chart
        const categoryCtx = $('#categoryChart')[0].getContext('2d');
        new Chart(categoryCtx, {
            type: 'doughnut',
            data: {
                labels: ['Điện tử', 'Thời trang', 'Gia dụng', 'Sách', 'Khác'],
                datasets: [{
                    data: [30, 25, 20, 15, 10],
                    backgroundColor: [
                        'rgb(54, 162, 235)',
                        'rgb(255, 99, 132)',
                        'rgb(255, 205, 86)',
                        'rgb(75, 192, 192)',
                    'rgb(153, 102, 255)'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                }
            }
        }
    });
    });
</script>
@endpush

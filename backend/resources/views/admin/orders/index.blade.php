@extends('layouts.admin.app')

@section('title', 'Quản lý đơn hàng - Admin Panel')

@section('content')
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0">
                    <i class="fas fa-shopping-cart me-2"></i>Quản lý đơn hàng
                </h1>
                <p class="text-muted mb-0">Danh sách tất cả đơn hàng trong hệ thống</p>
            </div>
        </div>
    </div>

    {{-- Filter & Search --}}
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('admin.orders.index') }}" method="GET" id="searchForm">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="position-relative">
                            <i class="fas fa-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                            <input type="text" name="search" class="form-control ps-5"
                                placeholder="Tìm theo mã đơn hàng hoặc tên khách hàng..."
                                value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <select name="status" class="form-select" onchange="this.form.submit()">
                            <option value="">-- Tất cả trạng thái --</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                            <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Đã xác nhận</option>
                            <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Đang xử lý</option>
                            <option value="shipping" {{ request('status') === 'shipping' ? 'selected' : '' }}>Đang giao</option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter me-2"></i>Lọc
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Orders Table --}}
    <div class="card">
        <div class="card-body p-0">
            @if ($orders->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="10%">Mã ĐH</th>
                                <th width="20%">Khách hàng</th>
                                <th width="15%" class="text-center">Trạng thái</th>
                                <th width="15%" class="text-end">Tổng tiền</th>
                                <th width="10%" class="text-center">SL sản phẩm</th>
                                <th width="15%">Ngày tạo</th>
                                <th width="15%" class="text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $order)
                                <tr>
                                    <td class="align-middle">
                                        <strong>#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</strong>
                                    </td>
                                    <td class="align-middle">
                                        <div>
                                            <p class="mb-0 fw-bold">{{ $order->user->name ?? 'N/A' }}</p>
                                            @if ($order->user && $order->user->email)
                                                <small class="text-muted">{{ $order->user->email }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <span class="badge bg-{{ $order->status_badge_color }} px-3 py-2">
                                            {{ $order->status_label }}
                                        </span>
                                    </td>
                                    <td class="text-end align-middle">
                                        <strong class="text-success">{{ $order->formatted_total_amount }}</strong>
                                    </td>
                                    <td class="text-center align-middle">
                                        <span class="badge bg-light text-dark">{{ $order->total_items }}</span>
                                    </td>
                                    <td class="align-middle">
                                        <small>{{ $order->created_at->format('d/m/Y') }}</small><br>
                                        <small class="text-muted">{{ $order->created_at->format('H:i') }}</small>
                                    </td>
                                    <td class="text-center align-middle">
                                        <a href="{{ route('admin.orders.show', $order->id) }}"
                                            class="btn btn-sm btn-info" title="Xem chi tiết">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">
                                Hiển thị {{ $orders->firstItem() }} - {{ $orders->lastItem() }}
                                trong tổng số {{ $orders->total() }} đơn hàng
                            </small>
                        </div>
                        <div>
                            {{ $orders->links() }}
                        </div>
                    </div>
                </div>
            @else
                <div class="p-5 text-center text-muted">
                    <i class="fas fa-inbox fa-3x mb-3"></i>
                    <p class="mb-0">Không tìm thấy đơn hàng nào.</p>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .table-hover tbody tr:hover {
            background-color: rgba(0, 123, 255, 0.05);
        }
    </style>
@endpush

@extends('layouts.admin.app')

@section('title', 'Chi tiết đơn hàng #' . $order->id . ' - Admin Panel')

@section('content')
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0">
                    <i class="fas fa-file-invoice me-2"></i>Chi tiết đơn hàng #{{ $order->id }}
                </h1>
            </div>
            <div>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Quay lại
                </a>
            </div>
        </div>
    </div>

    {{-- Warning Messages --}}
    @if (isset($warnings) && count($warnings) > 0)
        @foreach ($warnings as $warning)
            <div class="alert alert-{{ $warning['type'] }} alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>{{ $warning['message'] }}</strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endforeach
    @endif

    <div class="row">
        {{-- Thông tin đơn hàng --}}
        <div class="col-lg-8">
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-box me-2"></i>Thông tin đơn hàng
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <p class="mb-1 text-muted">
                                <i class="fas fa-hashtag me-2"></i>Mã đơn hàng:
                            </p>
                            <p class="fw-bold">#ORD-{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <p class="mb-1 text-muted">
                                <i class="fas fa-info-circle me-2"></i>Trạng thái:
                            </p>
                            <p>
                                <span class="badge bg-{{ $order->status_badge_color }} fs-6 px-3 py-2">
                                    {{ $order->status_label }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <p class="mb-1 text-muted">
                                <i class="fas fa-calendar me-2"></i>Ngày tạo:
                            </p>
                            <p class="fw-bold">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <p class="mb-1 text-muted">
                                <i class="fas fa-user me-2"></i>Người tạo:
                            </p>
                            <p class="fw-bold">{{ $order->user->name ?? 'N/A' }}</p>
                            @if ($order->user && $order->user->email)
                                <p class="text-muted small">{{ $order->user->email }}</p>
                            @endif
                        </div>
                        @if ($order->address)
                            <div class="col-12 mb-3">
                                <p class="mb-1 text-muted">
                                    <i class="fas fa-map-marker-alt me-2"></i>Địa chỉ giao hàng:
                                </p>
                                <p class="fw-bold">{{ $order->address }}</p>
                            </div>
                        @endif
                        @if ($order->note)
                            <div class="col-12">
                                <p class="mb-1 text-muted">
                                    <i class="fas fa-sticky-note me-2"></i>Ghi chú:
                                </p>
                                <p class="fst-italic">{{ $order->note }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Danh sách sản phẩm --}}
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-shopping-cart me-2"></i>Danh sách sản phẩm
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if ($order->orderDetails && $order->orderDetails->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%" class="text-center">#</th>
                                        <th width="35%">Sản phẩm</th>
                                        <th width="20%" class="text-end">Đơn giá</th>
                                        <th width="15%" class="text-center">Số lượng</th>
                                        <th width="25%" class="text-end">Thành tiền</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($order->orderDetails as $index => $item)
                                        <tr>
                                            <td class="text-center align-middle">{{ $index + 1 }}</td>
                                            <td class="align-middle">
                                                <div class="d-flex align-items-center">
                                                    @if ($item->product && $item->product->image)
                                                        <img src="{{ asset('storage/' . $item->product->image) }}"
                                                            alt="{{ $item->product_name }}" class="rounded me-3"
                                                            style="width: 50px; height: 50px; object-fit: cover;">
                                                    @else
                                                        <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center"
                                                            style="width: 50px; height: 50px;">
                                                            <i class="fas fa-image text-muted"></i>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <p class="mb-0 fw-bold">{{ $item->product_name }}</p>
                                                        @if ($item->product_specifications)
                                                            <small class="text-muted">
                                                                @foreach ($item->product_specifications as $key => $value)
                                                                    <span class="badge bg-secondary me-1">{{ $key }}:
                                                                        {{ $value }}</span>
                                                                @endforeach
                                                            </small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-end align-middle">
                                                <span class="text-primary fw-bold">{{ $item->formatted_unit_price }} ₫</span>
                                            </td>
                                            <td class="text-center align-middle">
                                                <span class="badge bg-light text-dark fs-6 px-3">{{ $item->quantity }}</span>
                                            </td>
                                            <td class="text-end align-middle">
                                                <span class="text-success fw-bold fs-6">{{ $item->formatted_total_price }}
                                                    ₫</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-5 text-center text-muted">
                            <i class="fas fa-inbox fa-3x mb-3"></i>
                            <p class="mb-0">Đơn hàng này không có sản phẩm nào.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Tổng kết --}}
        <div class="col-lg-4">
            <div class="card shadow-sm sticky-top" style="top: 20px;">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-calculator me-2"></i>Tổng kết đơn hàng
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Tổng số lượng:</span>
                            <span class="fw-bold">{{ $order->total_items }} sản phẩm</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Tổng tiền hàng:</span>
                            <span class="fw-bold text-primary">{{ $order->formatted_total_amount }}</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 fw-bold">TỔNG CỘNG:</h5>
                            <h4 class="mb-0 fw-bold text-success">{{ $order->formatted_total_amount }}</h4>
                        </div>
                    </div>

                    @if (!$isIntegrityValid)
                        <div class="alert alert-danger mb-0">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <small>Dữ liệu đơn hàng có vấn đề! Vui lòng kiểm tra lại.</small>
                        </div>
                    @else
                        <div class="alert alert-success mb-0">
                            <i class="fas fa-check-circle me-2"></i>
                            <small>Dữ liệu đơn hàng hợp lệ</small>
                        </div>
                    @endif
                </div>

                <div class="card-footer bg-light">
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Trang này chỉ xem, không thể chỉnh sửa
                    </small>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .card {
            border: 1px solid #e9ecef;
        }

        .card-header {
            border-bottom: 2px solid rgba(0, 0, 0, 0.1);
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 123, 255, 0.05);
        }

        .badge {
            font-weight: 500;
        }

        .shadow-sm {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
        }

        @media (max-width: 991px) {
            .sticky-top {
                position: relative !important;
                top: 0 !important;
            }
        }
    </style>
@endpush

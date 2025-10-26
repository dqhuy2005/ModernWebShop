@extends('layouts.user.app')

@section('title', 'Chi tiết đơn hàng - ModernWebShop')

@section('content')
    <div class="purchase-detail-section py-5" style="background-color: #F8F9FA;">
        <div class="container">
            <div class="mb-4">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('purchase.index') }}">Đơn hàng của tôi</a></li>
                        <li class="breadcrumb-item active">Chi tiết đơn hàng</li>
                    </ol>
                </nav>
            </div>

            <div class="row">
                <div class="col-lg-8 mb-4">
                    <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div>
                                    <h5 class="fw-bold mb-1" style="color: #202732;">
                                        Đơn hàng #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}
                                    </h5>
                                    <small class="text-muted">
                                        <i class="far fa-clock me-1"></i>
                                        {{ $order->created_at->format('d/m/Y H:i') }}
                                    </small>
                                </div>
                                @php
                                    $statusConfig = [
                                        'pending' => ['badge' => 'warning', 'text' => 'Chờ xử lý'],
                                        'confirmed' => ['badge' => 'info', 'text' => 'Đã xác nhận'],
                                        'processing' => ['badge' => 'primary', 'text' => 'Đang xử lý'],
                                        'shipping' => ['badge' => 'secondary', 'text' => 'Đang giao'],
                                        'completed' => ['badge' => 'success', 'text' => 'Hoàn thành'],
                                        'cancelled' => ['badge' => 'danger', 'text' => 'Đã hủy'],
                                    ];
                                    $config = $statusConfig[$order->status] ?? [
                                        'badge' => 'secondary',
                                        'text' => $order->status,
                                    ];
                                @endphp
                                <span class="badge bg-{{ $config['badge'] }} fs-6">
                                    {{ $config['text'] }}
                                </span>
                            </div>

                            <h6 class="fw-bold mb-3" style="color: #202732;">Sản phẩm đã đặt</h6>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Sản phẩm</th>
                                            <th class="text-center">Số lượng</th>
                                            <th class="text-end">Đơn giá</th>
                                            <th class="text-end">Thành tiền</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($order->orderDetails as $detail)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        @if ($detail->product)
                                                            <img src="{{ asset('storage/' . $detail->product->image) }}"
                                                                alt="{{ $detail->product_name }}" class="rounded me-3"
                                                                style="width: 60px; height: 60px; object-fit: cover;">
                                                        @else
                                                            <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center"
                                                                style="width: 60px; height: 60px;">
                                                                <i class="fas fa-image text-muted"></i>
                                                            </div>
                                                        @endif
                                                        <div>
                                                            <h6 class="mb-0">
                                                                @if ($detail->product)
                                                                    <a href="{{ route('products.show', $detail->product->name) }}"
                                                                        class="text-dark text-decoration-none">
                                                                        {{ $detail->product_name }}
                                                                    </a>
                                                                @else
                                                                    {{ $detail->product_name }}
                                                                @endif
                                                            </h6>
                                                            @if ($detail->product && $detail->product->category)
                                                                <small
                                                                    class="text-muted">{{ $detail->product->category->name }}</small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center align-middle">
                                                    <span class="badge bg-secondary">{{ $detail->quantity }}</span>
                                                </td>
                                                <td class="text-end align-middle">
                                                    ₫{{ number_format($detail->unit_price) }}
                                                </td>
                                                <td class="text-end align-middle">
                                                    <strong
                                                        class="text-danger">₫{{ number_format($detail->total_price) }}</strong>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm border-0" style="border-radius: 12px;">
                        <div class="card-body p-4">
                            <h6 class="fw-bold mb-3" style="color: #202732;">
                                <i class="fas fa-shipping-fast me-2"></i>Thông tin giao hàng
                            </h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <p class="mb-1 text-muted small">Người nhận</p>
                                    <p class="mb-0 fw-semibold">{{ $order->user->fullname }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <p class="mb-1 text-muted small">Email</p>
                                    <p class="mb-0 fw-semibold">{{ $order->user->email }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <p class="mb-1 text-muted small">Số điện thoại</p>
                                    <p class="mb-0 fw-semibold">{{ $order->user->phone ?? 'Chưa cập nhật' }}</p>
                                </div>
                                <div class="col-12">
                                    <p class="mb-1 text-muted small">Địa chỉ giao hàng</p>
                                    <p class="mb-0 fw-semibold">{{ $order->address }}</p>
                                </div>
                                @if ($order->note)
                                    <div class="col-12 mt-3">
                                        <p class="mb-1 text-muted small">Ghi chú</p>
                                        <p class="mb-0 fst-italic">{{ $order->note }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card shadow-sm border-0 sticky-top" style="border-radius: 12px; top: 20px;">
                        <div class="card-body p-4">
                            <h6 class="fw-bold mb-4" style="color: #202732;">Tổng đơn hàng</h6>

                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Tạm tính:</span>
                                <span class="fw-semibold">₫{{ number_format($order->total_amount) }}</span>
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Phí vận chuyển:</span>
                                <span class="text-success fw-semibold">Miễn phí</span>
                            </div>

                            <hr class="my-3">

                            <div class="d-flex justify-content-between mb-4">
                                <span class="fw-bold fs-5" style="color: #202732;">Tổng cộng:</span>
                                <span class="fw-bold fs-5 text-danger">₫{{ number_format($order->total_amount) }}</span>
                            </div>

                            <div class="d-grid gap-2">
                                @if (in_array($order->status, ['pending', 'confirmed']))
                                    <button type="button" class="btn btn-danger" id="cancelOrderBtn" style="border: none;">
                                        <i class="fas fa-times me-2"></i>Hủy đơn hàng
                                    </button>
                                @endif
                                <a href="{{ route('purchase.index') }}" class="btn btn-secondary" style="border: none;">
                                    <i class="fas fa-arrow-left me-2"></i>Quay lại
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .breadcrumb-item a {
            color: #dc3545;
            text-decoration: none;
        }

        .breadcrumb-item a:hover {
            text-decoration: underline;
        }

        .breadcrumb-item.active {
            color: #6c757d;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#cancelOrderBtn').on('click', function() {
                if (!confirm('Bạn có chắc muốn hủy đơn hàng này?')) {
                    return;
                }

                const $btn = $(this);
                const originalText = $btn.html();
                $btn.prop('disabled', true);
                $btn.html('<i class="fas fa-spinner fa-spin me-2"></i>Đang xử lý...');

                $.ajax({
                    url: '/purchase/{{ $order->id }}/cancel',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            setTimeout(function() {
                                location.reload();
                            }, 1500);
                        }
                    },
                    error: function(xhr) {
                        $btn.prop('disabled', false);
                        $btn.html(originalText);

                        if (xhr.status === 400) {
                            toastr.error(xhr.responseJSON.message);
                        } else {
                            toastr.error('Có lỗi xảy ra. Vui lòng thử lại!');
                        }
                    }
                });
            });
        });
    </script>
@endpush

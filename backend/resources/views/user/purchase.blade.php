@extends('layouts.user.app')

@section('title', 'Đơn hàng của tôi - ModernWebShop')

@section('content')
    <div class="purchase-section py-5" style="background-color: #F8F9FA;">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 mb-4">
                    <div class="card shadow-sm border-0" style="border-radius: 12px;">
                        <div class="card-body p-4 text-center">
                            <div class="profile-avatar mb-3">
                                <img src="{{ Auth::user()->image ? asset('storage/' . Auth::user()->image) : asset('assets/imgs/default-avatar.png') }}"
                                    alt="Avatar" class="rounded-circle"
                                    style="width: 120px; height: 120px; object-fit: cover;">
                            </div>
                            <h5 class="fw-bold mb-1" style="color: #202732;">{{ Auth::user()->fullname }}</h5>
                            <p class="text-muted small mb-3">{{ Auth::user()->email }}</p>
                            <div class="d-grid gap-2">
                                <a href="{{ route('profile.index') }}" class="btn btn-sm btn-outline-secondary" style="border: none">
                                    <i class="fas fa-user me-2"></i>Thông tin cá nhân
                                </a>
                                <a href="{{ route('purchase.index') }}" class="btn btn-sm btn-danger active">
                                    <i class="fas fa-shopping-bag me-2"></i>Đơn hàng của tôi
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-9">
                    <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-4" style="color: #202732;">
                                Đơn hàng của tôi
                            </h5>

                            <form method="GET" action="{{ route('purchase.index') }}" class="mb-4">
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <div class="input-group">
                                            <span class="input-group-text bg-white">
                                                <i class="fas fa-search text-muted"></i>
                                            </span>
                                            <input type="text" class="form-control border-start-0" name="search"
                                                placeholder="Tìm kiếm theo mã đơn hàng hoặc tên sản phẩm"
                                                value="{{ $search ?? '' }}">
                                        </div>
                                    </div>
                                </div>
                            </form>

                            @if ($orders->count() > 0)
                                <div class="orders-list">
                                    @foreach ($orders as $order)
                                        <div class="order-card mb-3 border rounded p-3" data-order-id="{{ $order->id }}">
                                            <div
                                                class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                                                <div>
                                                    <h6 class="fw-bold mb-1">
                                                        Đơn hàng #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}
                                                    </h6>
                                                    <small class="text-muted">
                                                        <i class="far fa-clock me-1"></i>
                                                        {{ $order->created_at->format('d/m/Y H:i') }}
                                                    </small>
                                                </div>
                                                <div class="text-end">
                                                    @php
                                                        $statusConfig = [
                                                            'pending' => ['badge' => 'warning', 'text' => 'Chờ xử lý'],
                                                            'confirmed' => ['badge' => 'info', 'text' => 'Đã xác nhận'],
                                                            'processing' => [
                                                                'badge' => 'primary',
                                                                'text' => 'Đang xử lý',
                                                            ],
                                                            'shipping' => [
                                                                'badge' => 'secondary',
                                                                'text' => 'Đang giao',
                                                            ],
                                                            'completed' => [
                                                                'badge' => 'success',
                                                                'text' => 'Hoàn thành',
                                                            ],
                                                            'cancelled' => ['badge' => 'danger', 'text' => 'Đã hủy'],
                                                        ];
                                                        $config = $statusConfig[$order->status] ?? [
                                                            'badge' => 'secondary',
                                                            'text' => $order->status,
                                                        ];
                                                    @endphp
                                                    <span class="badge bg-{{ $config['badge'] }}">
                                                        {{ $config['text'] }}
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="order-items">
                                                @foreach ($order->orderDetails->take(2) as $detail)
                                                    <div class="d-flex align-items-center mb-2">
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
                                                        <div class="flex-grow-1">
                                                            <h6 class="mb-0">
                                                                @if ($detail->product)
                                                                    <a href="{{ route('products.show', $detail->product->slug) }}" class="text-dark text-decoration-none">
                                                                        {{ $detail->product_name }}
                                                                    </a>
                                                                @else
                                                                    {{ $detail->product_name }}
                                                                @endif
                                                            </h6>
                                                            <small class="text-muted">Số lượng:
                                                                {{ $detail->quantity }}</small>
                                                        </div>
                                                        <div class="text-end">
                                                            <span
                                                                class="text-danger fw-semibold">₫{{ number_format($detail->total_price) }}</span>
                                                        </div>
                                                    </div>
                                                @endforeach

                                                @if ($order->orderDetails->count() > 2)
                                                    <p class="text-muted small mb-0">
                                                        <i class="fas fa-ellipsis-h me-1"></i>
                                                        Và {{ $order->orderDetails->count() - 2 }} sản phẩm khác
                                                    </p>
                                                @endif
                                            </div>

                                            <div
                                                class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                                                <div>
                                                    <span class="text-muted">Tổng tiền:</span>
                                                    <strong class="text-danger fs-5 ms-2">
                                                        ₫{{ number_format($order->total_amount) }}
                                                    </strong>
                                                </div>
                                                <div class="d-flex gap-2">
                                                    <a href="{{ route('purchase.show', $order->id) }}"
                                                        class="btn btn-sm btn-outline-secondary">
                                                        <i class="fas fa-eye me-1"></i>Chi tiết
                                                    </a>
                                                    @if (in_array($order->status, ['pending', 'confirmed']))
                                                        <button type="button"
                                                            class="btn btn-sm btn-outline-danger cancel-order-btn"
                                                            data-order-id="{{ $order->id }}">
                                                            <i class="fas fa-times me-1"></i>Hủy đơn
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="d-flex justify-content-center mt-4">
                                    {{ $orders->links() }}
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-shopping-bag" style="font-size: 5rem; color: #E9ECEF;"></i>
                                    <h5 class="fw-bold mt-3" style="color: #202732;">Chưa có đơn hàng</h5>
                                    <p class="text-muted mb-4">Bạn chưa có đơn hàng nào</p>
                                    <a href="{{ route('home') }}" class="btn btn-danger">
                                        <i class="fas fa-shopping-bag me-2"></i>Mua sắm ngay
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .order-card {
            transition: all 0.3s ease;
            background-color: white;
        }

        .input-group-text {
            border-right: none;
        }

        .input-group .form-control {
            border-left: none;
        }

        .input-group .form-control:focus {
            border-color: #dee2e6;
            box-shadow: none;
        }

        .btn-danger.active {
            background-color: #dc3545;
            border-color: #dc3545;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            // Submit search form on Enter key
            $('input[name="search"]').on('keypress', function(e) {
                if (e.which === 13) { // Enter key
                    $(this).closest('form').submit();
                }
            });

            $('.cancel-order-btn').on('click', function() {
                const orderId = $(this).data('order-id');
                const $orderCard = $(`.order-card[data-order-id="${orderId}"]`);

                if (!confirm('Bạn có chắc muốn hủy đơn hàng này?')) {
                    return;
                }

                $.ajax({
                    url: `/purchase/${orderId}/cancel`,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);

                            $orderCard.find('.badge').removeClass().addClass('badge bg-danger')
                                .text('Đã hủy');

                            $orderCard.find('.cancel-order-btn').remove();
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 400) {
                            toastr.error(xhr.responseJSON.message);
                        } else if (xhr.status === 404) {
                            toastr.error('Không tìm thấy đơn hàng');
                        } else {
                            toastr.error('Có lỗi xảy ra. Vui lòng thử lại!');
                        }
                    }
                });
            });
        });
    </script>
@endpush

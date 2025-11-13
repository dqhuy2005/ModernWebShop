@extends('layouts.user.app')

@section('title', 'Đặt hàng thành công - ModernWebShop')

@section('content')
    <div class="checkout-success-section py-5" style="background-color: #F8F9FA;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
                        <div class="card-body p-5 text-center">
                            <div class="success-icon mb-4">
                                <i class="fas fa-check-circle text-success" style="font-size: 5rem;"></i>
                            </div>
                            <h2 class="fw-bold mb-3" style="color: #202732;">Đặt hàng thành công!</h2>
                            <p class="text-muted mb-4">
                                Cảm ơn bạn đã đặt hàng. Chúng tôi đã nhận được đơn hàng của bạn và sẽ xử lý trong thời
                                gian sớm nhất.
                            </p>

                            <div class="order-info p-4 bg-light rounded mb-4">
                                <div class="row">
                                    <div class="col-md-6 text-start mb-3">
                                        <small class="text-muted d-block mb-1">Mã đơn hàng</small>
                                        <strong class="text-danger">#{{ $order->id }}</strong>
                                    </div>
                                    <div class="col-md-6 text-start mb-3">
                                        <small class="text-muted d-block mb-1">Ngày đặt</small>
                                        <strong>{{ $order->created_at->format('d/m/Y H:i') }}</strong>
                                    </div>
                                    <div class="col-md-6 text-start mb-3">
                                        <small class="text-muted d-block mb-1">Tổng tiền</small>
                                        <strong class="text-danger">{{ number_format($order->total_amount) }}₫</strong>
                                    </div>
                                    <div class="col-md-6 text-start mb-3">
                                        <small class="text-muted d-block mb-1">Trạng thái</small>
                                        <span class="badge bg-warning">Chờ xử lý</span>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex gap-3 justify-content-center flex-wrap">
                                <a href="{{ route('home') }}" class="btn btn-danger" style="border-radius: 0px;">
                                    TRANG CHỦ
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm border-0" style="border-radius: 12px;" id="orderDetails">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-4" style="color: #202732;">
                                <i class="fas fa-box me-2"></i>Chi tiết đơn hàng
                            </h5>

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
                                                            <img src="{{ $detail->product->image_url }}"
                                                                alt="{{ $detail->product_name }}" class="rounded me-3"
                                                                style="width: 60px; height: 60px; object-fit: cover;">
                                                        @endif
                                                        <div>
                                                            <h6 class="mb-0">
                                                                @if ($detail->product)
                                                                    <a href="{{ route('products.show', $detail->product->slug) }}"
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
                                                    {{ number_format($detail->unit_price) }}₫
                                                </td>
                                                <td class="text-end align-middle">
                                                    <strong
                                                        class="text-danger">{{ number_format($detail->total_price) }}₫</strong>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3" class="text-end fw-bold">Tổng cộng:</td>
                                            <td class="text-end">
                                                <strong
                                                    class="text-danger fs-5">{{ number_format($order->total_amount) }}₫</strong>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <h6 class="fw-bold mb-3" style="color: #202732;">Thông tin giao hàng:</h6>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Họ tên:</strong> {{ $order->user->fullname }}</p>
                                    <p class="mb-1"><strong>Email:</strong> {{ $order->user->email }}</p>
                                    <p class="mb-1"><strong>Số điện thoại:</strong> {{ $order->user->phone }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Địa chỉ:</strong> {{ $order->address }}</p>

                                    @if ($order->note)
                                        <div class="mt-3">
                                            <h6 class="fw-bold mb-2" style="color: #202732;">Ghi chú</h6>
                                            <p class="text-muted">{{ $order->note }}</p>
                                        </div>
                                    @endif
                                </div>
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
        .success-icon {
            animation: scaleIn 0.5s ease-out;
        }

        @keyframes scaleIn {
            0% {
                transform: scale(0);
                opacity: 0;
            }

            50% {
                transform: scale(1.1);
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        .order-info {
            animation: fadeInUp 0.6s ease-out 0.2s both;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card {
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            $('a[href="#orderDetails"]').on('click', function(e) {
                e.preventDefault();
                $('html, body').animate({
                    scrollTop: $('#orderDetails').offset().top - 20
                }, 500);
            });
        });
    </script>
@endpush

@extends('layouts.user.app')

@section('title', 'Thanh toán - ModernWebShop')

@section('content')
    <div class="checkout-section py-5" style="background-color: #F8F9FA;">
        <div class="container">
            <div class="mb-4">
                <h2 class="fw-bold" style="color: #202732;">
                    <i class="fas fa-credit-card me-2"></i>Thanh toán
                </h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('cart.index') }}">Giỏ hàng</a></li>
                        <li class="breadcrumb-item active">Thanh toán</li>
                    </ol>
                </nav>
            </div>

            <form id="checkoutForm">
                @csrf
                <div class="row g-4">
                    <div class="col-lg-7">
                        <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
                            <div class="card-body p-4">
                                <h5 class="fw-bold mb-4" style="color: #202732;">
                                    <i class="fas fa-shipping-fast me-2"></i>Thông tin giao hàng
                                </h5>

                                <div class="mb-3">
                                    <label for="name" class="form-label fw-semibold">Họ và tên <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        value="{{ $user->fullname }}" required>
                                </div>

                                <div class="mb-3">
                                    <label for="phone" class="form-label fw-semibold">Số điện thoại <span
                                            class="text-danger">*</span></label>
                                    <input type="tel" class="form-control" id="phone" name="phone"
                                        value="{{ $user->phone ?? '' }}" required>
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label fw-semibold">Email</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        value="{{ $user->email }}" readonly>
                                </div>

                                <div class="mb-3">
                                    <label for="address" class="form-label fw-semibold">Địa chỉ giao hàng <span
                                            class="text-danger">*</span></label>
                                    <textarea class="form-control" id="address" name="address" rows="3" required
                                        placeholder="Số nhà, tên đường, phường/xã, quận/huyện, tỉnh/thành phố">{{ old('address', $user->address) }}</textarea>
                                </div>

                                <div class="mb-3">
                                    <label for="note" class="form-label fw-semibold">Ghi chú đơn hàng</label>
                                    <textarea class="form-control" id="note" name="note" rows="3"
                                        placeholder="Ghi chú về đơn hàng, ví dụ: thời gian hay chỉ dẫn địa điểm giao hàng chi tiết hơn."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-5">
                        <div class="card shadow-sm border-0 sticky-top" style="border-radius: 12px; top: 20px;">
                            <div class="card-body p-4">
                                <h5 class="fw-bold mb-4" style="color: #202732;">
                                    <i class="fas fa-clipboard-list me-2"></i>Đơn hàng của bạn
                                </h5>

                                <div class="order-items mb-3">
                                    @foreach ($cartItems as $item)
                                        @php
                                            $product = $item->product;
                                        @endphp
                                        <div class="d-flex justify-content-between align-items-start mb-3 pb-3 border-bottom">
                                            <div class="d-flex align-items-start">
                                                <img src="{{ asset('storage/' . $product->image) }}"
                                                    alt="{{ $product->name }}" class="rounded me-3"
                                                    style="width: 60px; height: 60px; object-fit: cover;">
                                                <div>
                                                    <h6 class="mb-1">
                                                        <a href="{{ route('products.show', $product->slug) }}" class="text-dark text-decoration-none">
                                                            {{ $product->name }}
                                                        </a>
                                                    </h6>
                                                    <small class="text-muted">Số lượng: {{ $item->quantity }}</small>
                                                </div>
                                            </div>
                                            <div class="text-end">
                                                <span class="fw-semibold text-danger">
                                                    ₫{{ number_format($item->price * $item->quantity) }}
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="order-summary">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Tạm tính:</span>
                                        <span class="fw-semibold">₫{{ number_format($total) }}</span>
                                    </div>

                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Phí vận chuyển:</span>
                                        <span class="text-success fw-semibold">Miễn phí</span>
                                    </div>

                                    <hr class="my-3">

                                    <div class="d-flex justify-content-between mb-4">
                                        <span class="fw-bold fs-5" style="color: #202732;">Tổng cộng:</span>
                                        <span class="fw-bold fs-5 text-danger">₫{{ number_format($total) }}</span>
                                    </div>

                                    <button type="submit" class="btn btn-danger w-100 py-3 fw-semibold"
                                        id="submitOrder">
                                       Đặt hàng
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .form-control:focus {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }

        .form-check-input:checked {
            background-color: #dc3545;
            border-color: #dc3545;
        }

        .form-check-input:focus {
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }

        .form-check {
            transition: all 0.3s ease;
        }

        .form-check:hover {
            background-color: #f8f9fa;
        }

        .order-items {
            max-height: 400px;
            overflow-y: auto;
        }

        .order-items::-webkit-scrollbar {
            width: 6px;
        }

        .order-items::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .order-items::-webkit-scrollbar-thumb {
            background: #dc3545;
            border-radius: 10px;
        }

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
            $('#checkoutForm').on('submit', function(e) {
                e.preventDefault();

                if (!this.checkValidity()) {
                    e.stopPropagation();
                    $(this).addClass('was-validated');
                    toastr.error('Vui lòng điền đầy đủ thông tin bắt buộc');
                    return;
                }

                const $submitBtn = $('#submitOrder');
                const originalText = $submitBtn.html();
                $submitBtn.prop('disabled', true);
                $submitBtn.html('<i class="fas fa-spinner fa-spin me-2"></i>Đang xử lý...');

                const formData = {
                    _token: '{{ csrf_token() }}',
                    name: $('#name').val(),
                    phone: $('#phone').val(),
                    address: $('#address').val(),
                    note: $('#note').val(),
                };

                $.ajax({
                    url: '{{ route('checkout.process') }}',
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);

                            $('#cart-count').text('0');

                            setTimeout(function() {
                                window.location.href = response.redirect_url;
                            }, 1000);
                        }
                    },
                    error: function(xhr) {
                        $submitBtn.prop('disabled', false);
                        $submitBtn.html(originalText);

                        if (xhr.status === 401) {
                            toastr.error('Vui lòng đăng nhập để tiếp tục');
                            setTimeout(function() {
                                window.location.href = '{{ route('login') }}';
                            }, 1500);
                        } else if (xhr.status === 400) {
                            toastr.error(xhr.responseJSON.message || 'Giỏ hàng của bạn đang trống');
                        } else {
                            toastr.error('Có lỗi xảy ra. Vui lòng thử lại!');
                        }
                    }
                });
            });

            $('#phone').on('input', function() {
                let value = $(this).val().replace(/[^0-9]/g, '');
                if (value.length > 11) {
                    value = value.substring(0, 11);
                }
                $(this).val(value);
            });
        });
    </script>
@endpush

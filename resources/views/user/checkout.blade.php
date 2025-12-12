@extends('layouts.user.app')

@section('title', 'Thanh toán')

@section('content')
    <div class="checkout-section py-4" style="background-color: #f5f5f5;">
        <div class="container">
            <div class="mb-4">
                <h4 class="fw-bold mb-3" style="color: #333;">
                    <i class="fas fa-shopping-bag me-2 text-danger"></i>Thanh toán
                </h4>
            </div>

            <form id="checkoutForm">
                @csrf
                <input type="hidden" id="selectedItemsInput" name="selected_items">

                <div class="row g-3">
                    <!-- Section 1: Selected Products -->
                    <div class="col-12">
                        <div class="card shadow-sm border-0 mb-3" style="border-radius: 4px;">
                            <div class="card-body p-0">
                                <div class="p-3 border-bottom" style="background-color: #fff;">
                                    <h6 class="mb-0 fw-semibold" style="color: #333;">
                                        <i class="fas fa-box me-2 text-danger"></i>Sản phẩm đã chọn
                                    </h6>
                                </div>
                                <div class="table-responsive">
                                    <table class="table mb-0">
                                        <thead style="background-color: #fafafa;">
                                            <tr>
                                                <th class="py-3 px-4"
                                                    style="width: 50%; font-size: 14px; font-weight: 500; color: #888;">Sản
                                                    Phẩm</th>
                                                <th class="py-3 text-center"
                                                    style="width: 15%; font-size: 14px; font-weight: 500; color: #888;">Đơn
                                                    Giá</th>
                                                <th class="py-3 text-center"
                                                    style="width: 15%; font-size: 14px; font-weight: 500; color: #888;">Số
                                                    Lượng</th>
                                                <th class="py-3 text-center"
                                                    style="width: 20%; font-size: 14px; font-weight: 500; color: #888;">
                                                    Thành Tiền</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($cartItems as $item)
                                                @php
                                                    $product = $item->product;
                                                @endphp
                                                <tr style="background-color: #fff; border-bottom: 1px solid #f0f0f0;">
                                                    <td class="py-3 px-4 align-middle">
                                                        <div class="d-flex align-items-center">
                                                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                                                                class="rounded me-3"
                                                                style="width: 80px; height: 80px; object-fit: cover; border: 1px solid #e0e0e0;">
                                                            <div>
                                                                <a href="{{ route('products.show', $product->slug) }}"
                                                                    class="text-dark text-decoration-none d-block mb-1"
                                                                    style="font-size: 14px; line-height: 1.4;">
                                                                    {{ $product->name }}
                                                                </a>
                                                                @if ($product->category)
                                                                    <small
                                                                        class="text-muted">{{ $product->category->name }}</small>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="text-center align-middle">
                                                        <span style="color: #333; font-size: 14px;">
                                                            {{ number_format($item->price) }}₫
                                                        </span>
                                                    </td>
                                                    <td class="text-center align-middle">
                                                        <span style="color: #666; font-size: 14px;">
                                                            {{ $item->quantity }}
                                                        </span>
                                                    </td>
                                                    <td class="text-center align-middle">
                                                        <span style="color: #ee4d2d; font-size: 16px; font-weight: 500;">
                                                            {{ number_format($item->price * $item->quantity) }}₫
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Main Content -->
                    <div class="col-lg-8">
                        <div class="card shadow-sm border-0" style="border-radius: 4px;">
                            <div class="card-body p-4">
                                <h6 class="fw-bold mb-3" style="color: #333;">
                                    <i class="fas fa-map-marker-alt me-2 text-danger"></i>Thông tin giao hàng
                                </h6>

                                <div class="mb-3">
                                    <label for="name" class="form-label"
                                        style="font-size: 14px; font-weight: 500; color: #333;">
                                        Họ và tên <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        value="{{ $user->fullname }}" required style="font-size: 14px; padding: 10px 12px;">
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="phone" class="form-label"
                                            style="font-size: 14px; font-weight: 500; color: #333;">
                                            Số điện thoại <span class="text-danger">*</span>
                                        </label>
                                        <input type="tel" class="form-control" id="phone" name="phone"
                                            value="{{ $user->phone ?? '' }}" required
                                            style="font-size: 14px; padding: 10px 12px;">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label"
                                            style="font-size: 14px; font-weight: 500; color: #333;">
                                            Email
                                        </label>
                                        <input type="email" class="form-control" id="email" name="email"
                                            value="{{ $user->email }}" readonly
                                            style="font-size: 14px; padding: 10px 12px; background-color: #f5f5f5;">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="address" class="form-label"
                                        style="font-size: 14px; font-weight: 500; color: #333;">
                                        Địa chỉ giao hàng <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control" id="address" name="address" rows="3" required
                                        placeholder="Số nhà, tên đường, phường/xã, quận/huyện, tỉnh/thành phố" style="font-size: 14px; padding: 10px 12px;">{{ old('address', $user->address) }}</textarea>
                                </div>

                                <div class="mb-0">
                                    <label for="note" class="form-label"
                                        style="font-size: 14px; font-weight: 500; color: #333;">
                                        Ghi chú đơn hàng
                                    </label>
                                    <textarea class="form-control" id="note" name="note" rows="2" placeholder="Ghi chú về đơn hàng..."
                                        style="font-size: 14px; padding: 10px 12px;"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Order Summary -->
                    <div class="col-lg-4">
                        <div class="card shadow-sm border-0 sticky-top" style="border-radius: 4px; top: 20px;">
                            <div class="card-body p-4">
                                <h6 class="fw-bold mb-4" style="color: #333;">
                                    <i class="fas fa-receipt me-2 text-danger"></i>Tổng tiền hàng
                                </h6>

                                <div class="mb-3 pb-3 border-bottom">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span style="color: #666; font-size: 14px;">Tổng tiền hàng:</span>
                                        <span style="color: #333; font-size: 14px; font-weight: 500;">
                                            {{ number_format($total) }}₫
                                        </span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span style="color: #666; font-size: 14px;">Tổng tiền phí vận chuyển:</span>
                                        <span style="color: #26aa99; font-size: 14px; font-weight: 500;">
                                            Miễn phí
                                        </span>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span style="color: #333; font-size: 16px; font-weight: 500;">Tổng thanh
                                            toán:</span>
                                        <span style="color: #ee4d2d; font-size: 24px; font-weight: 500;">
                                            {{ number_format($grandTotal) }}₫
                                        </span>
                                    </div>
                                    <div class="text-end mt-1">
                                        <small class="text-muted" style="font-size: 12px;">(Đã bao gồm thuế)</small>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-danger w-100 py-3 fw-semibold" id="submitOrder"
                                    style="background: #ee4d2d; border-color: #ee4d2d; font-size: 14px;">
                                    <i class="fas fa-check-circle me-2"></i>Đặt hàng
                                </button>
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
        .table {
            font-size: 14px;
        }

        .table tbody tr:hover {
            background-color: #fafafa !important;
        }

        .form-control {
            border: 1px solid #d9d9d9;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            border-color: #ee4d2d;
            box-shadow: 0 0 0 0.2rem rgba(238, 77, 45, 0.1);
        }

        .form-control:disabled,
        .form-control:read-only {
            background-color: #f5f5f5;
            opacity: 0.8;
        }

        .btn-danger {
            transition: all 0.3s ease;
        }

        .btn-danger:hover {
            background: #d73211 !important;
            border-color: #d73211 !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(238, 77, 45, 0.3);
        }

        .btn-danger:active {
            transform: translateY(0);
        }

        .sticky-top {
            top: 20px;
        }

        @media (max-width: 991px) {
            .sticky-top {
                position: relative !important;
                top: 0 !important;
            }
        }

        /* Smooth animations */
        .card {
            transition: box-shadow 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08) !important;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            // Retrieve selected items from sessionStorage
            const selectedItems = JSON.parse(sessionStorage.getItem('selectedCartItems') || '[]');

            // If we have selected items, populate the hidden input
            if (selectedItems.length > 0) {
                $('#selectedItemsInput').val(JSON.stringify(selectedItems));
            }

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
                    selected_items: selectedItems.length > 0 ? selectedItems : null
                };

                $.ajax({
                    url: '{{ route('checkout.process') }}',
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);

                            // Clear selected items from sessionStorage
                            sessionStorage.removeItem('selectedCartItems');

                            // Update cart count
                            if (response.cart_count !== undefined) {
                                $('#cart-count').text(response.cart_count);
                            } else {
                                $('#cart-count').text('0');
                            }

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
                            toastr.error(xhr.responseJSON.message ||
                                'Giỏ hàng của bạn đang trống');
                        } else {
                            toastr.error('Có lỗi xảy ra. Vui lòng thử lại!');
                        }
                    }
                });
            });

            // Phone number validation
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

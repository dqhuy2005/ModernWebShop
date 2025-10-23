@extends('layouts.user.app')

@section('title', 'Giỏ hàng - ModernWebShop')

@section('content')
    <div class="cart-section py-5" style="background-color: #F8F9FA;">
        <div class="container">
            <div class="mb-4">
                <h2 class="fw-bold" style="color: #202732;">
                    <i class="fas fa-shopping-cart me-2"></i>Giỏ hàng của bạn
                </h2>
            </div>

            @if (isset($cartItems) && $cartItems->count() > 0)
                <div class="row g-4">
                    <div class="col-lg-8">
                        <div class="card shadow-sm border-0" style="border-radius: 12px;">
                            <div class="card-body p-4">
                                <div class="table-responsive">
                                    <table class="table align-middle">
                                        <thead class="border-bottom">
                                            <tr>
                                                <th scope="col" class="fw-semibold">Sản phẩm</th>
                                                <th scope="col" class="fw-semibold text-center">Đơn giá</th>
                                                <th scope="col" class="fw-semibold text-center">Số lượng</th>
                                                <th scope="col" class="fw-semibold text-end">Tổng</th>
                                                <th scope="col" class="text-center"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($cartItems as $item)
                                                @php
                                                    $product = Auth::check() ? $item->product : (object) $item;
                                                    $itemPrice = Auth::check() ? $item->price : $item['price'];
                                                    $itemQuantity = Auth::check() ? $item->quantity : $item['quantity'];
                                                    $itemId = Auth::check() ? $item->id : $item['product_id'];
                                                @endphp
                                                <tr class="cart-item" data-cart-id="{{ $itemId }}">
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <img src="{{ asset('storage/' . (Auth::check() ? $product->image : $item['image'])) }}"
                                                                alt="{{ Auth::check() ? $product->name : $item['name'] }}"
                                                                class="rounded me-3"
                                                                style="width: 80px; height: 80px; object-fit: cover;">
                                                            <div>
                                                                <h6 class="mb-1 fw-semibold">
                                                                    {{ Auth::check() ? $product->name : $item['name'] }}
                                                                </h6>
                                                                @if (Auth::check() && $product->category)
                                                                    <small
                                                                        class="text-muted">{{ $product->category->name }}</small>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        <span
                                                            class="text-danger fw-semibold">₫{{ number_format($itemPrice) }}</span>
                                                    </td>
                                                    <td>
                                                        <div class="input-group input-group-sm mx-auto"
                                                            style="max-width: 120px;">
                                                            <button class="btn btn-outline-secondary qty-btn" type="button"
                                                                onclick="updateQuantity({{ $itemId }}, -1)">
                                                                <i class="fas fa-minus"></i>
                                                            </button>
                                                            <input type="number"
                                                                class="form-control text-center quantity-input"
                                                                value="{{ $itemQuantity }}" min="1" max="999"
                                                                data-cart-id="{{ $itemId }}"
                                                                data-original-value="{{ $itemQuantity }}"
                                                                onchange="updateQuantityInput({{ $itemId }}, this.value)">
                                                            <button class="btn btn-outline-secondary qty-btn" type="button"
                                                                onclick="updateQuantity({{ $itemId }}, 1)">
                                                                <i class="fas fa-plus"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                    <td class="text-end">
                                                        <span class="fw-bold item-total" style="color: #202732;">
                                                            ₫{{ number_format($itemPrice * $itemQuantity) }}
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        <button class="btn btn-sm btn-outline-danger"
                                                            onclick="removeFromCart({{ $itemId }})" title="Xóa">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                                    <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-arrow-left me-2"></i>Tiếp tục mua sắm
                                    </a>
                                    <form action="{{ route('cart.clear') }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger"
                                            onclick="return confirm('Bạn có chắc muốn xóa toàn bộ giỏ hàng?')">
                                            <i class="fas fa-trash me-2"></i>Xóa tất cả
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card shadow-sm border-0 sticky-top" style="border-radius: 12px; top: 20px;">
                            <div class="card-body p-4">
                                <h5 class="fw-bold mb-4" style="color: #202732;">Tổng đơn hàng</h5>

                                <div class="d-flex justify-content-between mb-3">
                                    <span class="text-muted">Tạm tính:</span>
                                    <span class="fw-semibold" id="subtotal">₫{{ number_format($total) }}</span>
                                </div>

                                <div class="d-flex justify-content-between mb-3">
                                    <span class="text-muted">Phí vận chuyển:</span>
                                    <span class="text-success fw-semibold">Miễn phí</span>
                                </div>

                                <hr>

                                <div class="d-flex justify-content-between mb-4">
                                    <span class="fw-bold fs-5" style="color: #202732;">Tổng cộng:</span>
                                    <span class="fw-bold fs-5 text-danger"
                                        id="total">₫{{ number_format($total) }}</span>
                                </div>

                                <a href="#" class="btn btn-danger w-100 py-2 fw-semibold mb-3">
                                    <i class="fas fa-credit-card me-2"></i>Thanh toán
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <div class="card shadow-sm border-0 mx-auto" style="max-width: 500px; border-radius: 12px;">
                        <div class="card-body p-5">
                            <div class="mb-4">
                                <i class="fas fa-shopping-cart" style="font-size: 5rem; color: #E9ECEF;"></i>
                            </div>
                            <h4 class="fw-bold mb-3" style="color: #202732;">Giỏ hàng trống</h4>
                            <p class="text-muted mb-4">Bạn chưa có sản phẩm nào trong giỏ hàng</p>
                            <a href="{{ route('home') }}" class="btn btn-danger px-4">
                                <i class="fas fa-shopping-bag me-2"></i>Tiếp tục mua sắm
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .cart-item {
            transition: background-color 0.3s ease;
        }

        .quantity-input {
            border-left: none;
            border-right: none;
            font-weight: 600;
        }

        .quantity-input:focus {
            box-shadow: none;
            border-color: #dee2e6;
        }

        .qty-btn {
            border-color: #dee2e6;
            transition: all 0.3s ease;
        }

        .qty-btn:hover:not(:disabled) {
            background-color: #dc3545;
            border-color: #dc3545;
            color: white;
        }

        .qty-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .quantity-input:disabled {
            background-color: #f8f9fa;
            opacity: 0.7;
        }

        .item-total {
            transition: all 0.3s ease;
        }

        .cart-item:hover {
            background-color: rgba(0, 0, 0, 0.02);
        }

        /* Loading animation for buttons */
        .qty-btn.loading {
            position: relative;
            color: transparent;
        }

        .qty-btn.loading::after {
            content: "";
            position: absolute;
            width: 12px;
            height: 12px;
            top: 50%;
            left: 50%;
            margin-left: -6px;
            margin-top: -6px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid #dc3545;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Pulse animation for updated totals */
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .total-updated {
            animation: pulse 0.5s ease;
            color: #28a745 !important;
        }
    </style>
@endpush

@push('scripts')
    <script>
        let quantityUpdateTimer;

        function updateQuantity(cartId, change) {
            const input = document.querySelector(`input[data-cart-id="${cartId}"]`);
            let newQuantity = parseInt(input.value) + change;

            if (newQuantity < 1) {
                if (confirm('Bạn có muốn xóa sản phẩm này khỏi giỏ hàng?')) {
                    removeFromCart(cartId);
                }
                return;
            }

            updateCartQuantity(cartId, newQuantity);
        }

        function updateQuantityInput(cartId, quantity) {
            clearTimeout(quantityUpdateTimer);

            quantity = parseInt(quantity);

            if (isNaN(quantity) || quantity < 1) {
                toastr.error('Số lượng phải là số nguyên dương');
                const $input = $(`input[data-cart-id="${cartId}"]`);
                $input.val($input.attr('data-original-value') || 1);
                return;
            }

            if (quantity > 999) {
                toastr.warning('Số lượng tối đa là 999');
                $(`input[data-cart-id="${cartId}"]`).val(999);
                quantity = 999;
            }

            quantityUpdateTimer = setTimeout(function() {
                updateCartQuantity(cartId, quantity);
            }, 500);
        }

        function updateCartQuantity(cartId, quantity) {
            const $row = $(`.cart-item[data-cart-id="${cartId}"]`);
            const $input = $(`input[data-cart-id="${cartId}"]`);
            const $buttons = $row.find('.qty-btn');

            $buttons.prop('disabled', true);
            $input.prop('disabled', true);

            const $itemTotal = $row.find('.item-total');
            const originalTotalHtml = $itemTotal.html();
            $itemTotal.html('<i class="fas fa-spinner fa-spin text-muted"></i>');

            $.ajax({
                url: '{{ route('cart.update') }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    cart_id: cartId,
                    quantity: quantity
                },
                success: function(response) {
                    if (response.success) {
                        $input.val(quantity);
                        $input.attr('data-original-value', quantity);

                        const priceText = $row.find('.text-danger.fw-semibold').first().text();
                        const price = parseInt(priceText.replace(/[₫,]/g, ''));

                        const itemTotal = price * quantity;
                        $row.find('.item-total').text('₫' + itemTotal.toLocaleString('vi-VN'));

                        updateCartTotals();

                        toastr.success(response.message);
                    }
                },
                error: function(xhr) {
                    const originalValue = $input.attr('data-original-value') || 1;
                    $input.val(originalValue);
                    $itemTotal.html(originalTotalHtml);

                    toastr.error('Có lỗi xảy ra. Vui lòng thử lại!');
                },
                complete: function() {
                    $buttons.prop('disabled', false);
                    $input.prop('disabled', false);
                }
            });
        }

        function updateCartTotals() {
            let subtotal = 0;

            $('.cart-item').each(function() {
                const $row = $(this);
                const priceText = $row.find('.text-danger.fw-semibold').first().text();
                const price = parseInt(priceText.replace(/[₫,]/g, ''));
                const quantity = parseInt($row.find('.quantity-input').val());
                subtotal += price * quantity;
            });

            const $subtotal = $('#subtotal');
            const $total = $('#total');

            $subtotal.text('₫' + subtotal.toLocaleString('vi-VN'));
            $total.text('₫' + subtotal.toLocaleString('vi-VN'));
        }

        function removeFromCart(cartId) {
            if (!confirm('Bạn có chắc muốn xóa sản phẩm này?')) {
                return;
            }

            $.ajax({
                url: '{{ route('cart.remove') }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    cart_id: cartId
                },
                success: function(response) {
                    if (response.success) {
                        $(`.cart-item[data-cart-id="${cartId}"]`).fadeOut(300, function() {
                            $(this).remove();

                            $('#cart-count').text(response.cart_count);

                            if (response.cart_count === 0) {
                                location.reload();
                            } else {
                                $('#subtotal').text('₫' + response.total);
                                $('#total').text('₫' + response.total);
                            }
                        });

                        toastr.success(response.message);
                    }
                },
                error: function() {
                    toastr.error('Có lỗi xảy ra. Vui lòng thử lại!');
                }
            });
        }
    </script>
@endpush

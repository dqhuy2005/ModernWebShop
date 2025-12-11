@extends('layouts.user.app')

@section('title', 'Giỏ hàng - ModernWebShop')

@section('content')
    <div class="cart-section py-4" style="background-color: #f5f5f5;">
        <div class="container">
            @if (isset($cartItems) && $cartItems->count() > 0)
                <div class="row">
                    <div class="col-12">
                        <div class="card shadow-sm border-0 mb-3" style="border-radius: 4px;">
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table cart-table mb-0">
                                        <thead style="background-color: #fff; border-bottom: 1px solid #e0e0e0;">
                                            <tr>
                                                <th scope="col" class="py-3 px-4" style="width: 5%;">
                                                    <input type="checkbox" id="select-all" class="form-check-input">
                                                </th>
                                                <th scope="col" class="py-3" style="width: 40%;">Sản Phẩm</th>
                                                <th scope="col" class="py-3 text-center" style="width: 15%;">Đơn Giá</th>
                                                <th scope="col" class="py-3 text-center" style="width: 15%;">Số Lượng
                                                </th>
                                                <th scope="col" class="py-3 text-center" style="width: 15%;">Số Tiền</th>
                                                <th scope="col" class="py-3 text-center" style="width: 10%;">Thao Tác
                                                </th>
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
                                                <tr class="cart-item" data-cart-id="{{ $itemId }}"
                                                    style="background-color: #fff;">
                                                    <td class="px-4 align-middle">
                                                        <input type="checkbox" class="form-check-input item-checkbox"
                                                            data-cart-id="{{ $itemId }}"
                                                            data-price="{{ $itemPrice }}"
                                                            data-quantity="{{ $itemQuantity }}">
                                                    </td>
                                                    <td class="py-3 align-middle">
                                                        <div class="d-flex align-items-center">
                                                            <img src="{{ Auth::check() ? $product->image_url : asset('storage/' . $item['image']) }}"
                                                                alt="{{ Auth::check() ? $product->name : $item['name'] }}"
                                                                class="rounded me-3"
                                                                style="width: 80px; height: 80px; object-fit: cover; border: 1px solid #e0e0e0;">
                                                            <div>
                                                                <a href="{{ route('products.show', Auth::check() ? $product->slug : $item['slug']) }}"
                                                                    class="text-dark text-decoration-none d-block mb-1"
                                                                    style="font-size: 14px; line-height: 1.4;">
                                                                    {{ Auth::check() ? $product->name : $item['name'] }}
                                                                </a>
                                                                @if (Auth::check() && $product->category)
                                                                    <small
                                                                        class="text-muted">{{ $product->category->name }}</small>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="text-center align-middle">
                                                        <span class="item-price" style="color: #333; font-size: 14px;">
                                                            {{ number_format($itemPrice) }}₫
                                                        </span>
                                                    </td>
                                                    <td class="align-middle">
                                                        <div class="d-flex justify-content-center align-items-center">
                                                            <div class="input-group" style="max-width: 100px;">
                                                                <button class="btn btn-outline-secondary qty-btn px-2 py-1"
                                                                    type="button"
                                                                    onclick="updateQuantity({{ $itemId }}, -1)"
                                                                    style="border: 1px solid #d9d9d9; font-size: 12px;">
                                                                    <i class="fas fa-minus" style="font-size: 10px;"></i>
                                                                </button>
                                                                <input type="number"
                                                                    class="form-control text-center quantity-input px-1 py-1"
                                                                    value="{{ $itemQuantity }}" min="1"
                                                                    max="999" data-cart-id="{{ $itemId }}"
                                                                    data-original-value="{{ $itemQuantity }}"
                                                                    onchange="updateQuantityInput({{ $itemId }}, this.value)"
                                                                    style="border: 1px solid #d9d9d9; border-left: none; border-right: none; font-size: 14px;">
                                                                <button class="btn btn-outline-secondary qty-btn px-2 py-1"
                                                                    type="button"
                                                                    onclick="updateQuantity({{ $itemId }}, 1)"
                                                                    style="border: 1px solid #d9d9d9; font-size: 12px;">
                                                                    <i class="fas fa-plus" style="font-size: 10px;"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="text-center align-middle">
                                                        <span class="item-total"
                                                            style="color: #ee4d2d; font-size: 16px; font-weight: 500;">
                                                            {{ number_format($itemPrice * $itemQuantity) }}₫
                                                        </span>
                                                    </td>
                                                    <td class="text-center align-middle">
                                                        <button class="btn btn-link text-danger p-0"
                                                            onclick="removeFromCart({{ $itemId }})" title="Xóa"
                                                            style="font-size: 14px; text-decoration: none;">
                                                            Xóa
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Checkout Bar -->
                <div class="checkout-bar bg-white shadow-lg mt-3"
                    style="position: sticky; bottom: 0; z-index: 50; border-top: 1px solid #e0e0e0; border-radius: 4px;">
                    <div class="container">
                        <div class="row align-items-center py-3">
                            <div class="col-auto">
                                <div class="form-check">
                                    <input type="checkbox" id="select-all-bottom" class="form-check-input">
                                    <label class="form-check-label" for="select-all-bottom">
                                        Chọn Tất Cả (<span id="total-items">{{ $cartItems->count() }}</span>)
                                    </label>
                                </div>
                            </div>
                            <div class="col-auto">
                                <button class="btn btn-link text-danger p-0" onclick="deleteSelected()"
                                    style="font-size: 14px;">
                                    Xóa
                                </button>
                            </div>
                            <div class="col-auto ms-auto d-flex align-items-center">
                                <div class="me-4">
                                    <span class="text-muted" style="font-size: 14px;">Tổng thanh toán (<span
                                            id="selected-count">0</span> Sản phẩm):</span>
                                    <span class="ms-2" style="color: #ee4d2d; font-size: 24px; font-weight: 500;"
                                        id="selected-total">0₫</span>
                                </div>
                                <a href="#" class="btn btn-danger px-5 py-2 disabled" id="checkout-btn"
                                    style="background: #ee4d2d; border-color: #ee4d2d; font-size: 14px; font-weight: 500; opacity: 0.6;"
                                    onclick="proceedToCheckout(event)">Mua Hàng</a>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <div class="card shadow-sm border-0 mx-auto" style="max-width: 500px; border-radius: 4px;">
                        <div class="card-body p-5">
                            <div class="mb-4">
                                <i class="fas fa-shopping-cart" style="font-size: 5rem; color: #E9ECEF;"></i>
                            </div>
                            <h5 class="mb-3" style="color: #333;">Giỏ hàng của bạn còn trống</h5>
                            <p class="text-muted mb-4" style="font-size: 14px;">Hãy chọn thêm sản phẩm để mua sắm nhé</p>
                            <a href="{{ route('home') }}" class="btn btn-danger px-4">Mua Ngay</a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .cart-table {
            font-size: 14px;
        }

        .cart-table thead th {
            font-weight: 500;
            color: #888;
            font-size: 13px;
            text-transform: capitalize;
        }

        .cart-item {
            border-bottom: 1px solid #f0f0f0;
            transition: background-color 0.2s ease;
        }

        .cart-item:hover {
            background-color: #fafafa !important;
        }

        .quantity-input {
            border: 1px solid #d9d9d9;
            font-weight: 500;
            max-width: 50px;
        }

        .quantity-input:focus {
            box-shadow: none;
            border-color: #d9d9d9;
        }

        .qty-btn {
            background-color: #fff;
            border: 1px solid #d9d9d9;
            color: #666;
            transition: all 0.2s ease;
            height: 30px;
            width: 30px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .qty-btn:hover:not(:disabled) {
            background-color: #f5f5f5;
            border-color: #bbb;
            color: #333;
        }

        .qty-btn:disabled {
            opacity: 0.4;
            cursor: not-allowed;
        }

        .quantity-input:disabled {
            background-color: #f5f5f5;
            opacity: 0.6;
        }

        .item-total {
            transition: color 0.3s ease;
        }

        /* Checkout bar styles */
        .checkout-bar {
            position: sticky;
            bottom: 0;
            margin-top: 1rem;
            margin-bottom: 1rem;
            transition: box-shadow 0.3s ease;
        }

        .checkout-bar:hover {
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1) !important;
        }

        /* Loading animation */
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
            border-top: 2px solid #ee4d2d;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Pulse animation for updated totals */
        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }
        }

        .total-updated {
            animation: pulse 0.4s ease;
        }

        /* Checkbox styles */
        .form-check-input {
            width: 18px;
            height: 18px;
            border: 2px solid #d9d9d9;
            cursor: pointer;
        }

        .form-check-input:checked {
            background-color: #ee4d2d;
            border-color: #ee4d2d;
        }

        .form-check-input:focus {
            box-shadow: 0 0 0 0.2rem rgba(238, 77, 45, 0.25);
        }

        /* Responsive adjustments */
        @media (max-width: 1024px) {
            .cart-table {
                font-size: 13px;
            }

            .checkout-bar .col-auto {
                font-size: 13px;
            }

            #selected-total {
                font-size: 20px !important;
            }
        }

        @media (max-width: 768px) {
            .cart-table thead th {
                font-size: 12px;
                padding: 12px 8px !important;
            }

            .cart-item td {
                padding: 12px 8px !important;
            }

            .cart-item img {
                width: 60px !important;
                height: 60px !important;
            }

            .checkout-bar {
                padding: 10px 0 !important;
            }

            .checkout-bar .row {
                flex-wrap: wrap;
            }

            .checkout-bar .col-auto {
                font-size: 12px;
            }

            #selected-total {
                font-size: 18px !important;
            }

            #checkout-btn {
                padding: 8px 20px !important;
                font-size: 13px !important;
            }
        }

        /* Empty cart animation */
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
            animation: fadeInUp 0.5s ease-out;
        }
    </style>
@endpush

@push('scripts')
    <script>
        let quantityUpdateTimer;

        $(document).ready(function() {
            // Select all functionality
            $('#select-all, #select-all-bottom').on('change', function() {
                const isChecked = $(this).prop('checked');
                $('.item-checkbox').prop('checked', isChecked);
                $('#select-all, #select-all-bottom').prop('checked', isChecked);
                updateSelectedTotal();
            });

            // Individual checkbox change
            $('.item-checkbox').on('change', function() {
                updateSelectAllState();
                updateSelectedTotal();
            });

            // Initial state
            updateSelectAllState();
            updateSelectedTotal();
        });

        function updateSelectAllState() {
            const totalCheckboxes = $('.item-checkbox').length;
            const checkedCheckboxes = $('.item-checkbox:checked').length;
            const selectAllChecked = totalCheckboxes === checkedCheckboxes;

            $('#select-all, #select-all-bottom').prop('checked', selectAllChecked);
        }

        function updateSelectedTotal() {
            let selectedCount = 0;
            let selectedTotal = 0;

            $('.item-checkbox:checked').each(function() {
                const price = parseInt($(this).data('price'));
                const quantity = parseInt($(this).data('quantity'));
                selectedCount++;
                selectedTotal += price * quantity;
            });

            $('#selected-count').text(selectedCount);
            $('#selected-total').text(selectedTotal.toLocaleString('vi-VN') + '₫');

            // Enable/disable checkout button
            if (selectedCount > 0) {
                $('#checkout-btn').removeClass('disabled').css('opacity', '1');
            } else {
                $('#checkout-btn').addClass('disabled').css('opacity', '0.6');
            }
        }

        function updateQuantity(cartId, change) {
            const $input = $(`input[data-cart-id="${cartId}"]`);
            let newQuantity = parseInt($input.val()) + change;

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
            const $input = $(`input.quantity-input[data-cart-id="${cartId}"]`);
            const $buttons = $row.find('.qty-btn');
            const $checkbox = $(`.item-checkbox[data-cart-id="${cartId}"]`);

            $buttons.prop('disabled', true).addClass('loading');
            $input.prop('disabled', true);

            const $itemTotal = $row.find('.item-total');
            const originalTotalHtml = $itemTotal.html();
            $itemTotal.html('<i class="fas fa-spinner fa-spin"></i>');

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

                        // Update checkbox data
                        $checkbox.data('quantity', quantity);
                        $checkbox.attr('data-quantity', quantity);

                        const price = parseInt($checkbox.data('price'));
                        const itemTotal = price * quantity;

                        $itemTotal.text(itemTotal.toLocaleString('vi-VN') + '₫');
                        $itemTotal.addClass('total-updated');

                        setTimeout(function() {
                            $itemTotal.removeClass('total-updated');
                        }, 500);

                        updateSelectedTotal();
                        toastr.success(response.message || 'Đã cập nhật số lượng');
                    }
                },
                error: function(xhr) {
                    const originalValue = $input.attr('data-original-value') || 1;
                    $input.val(originalValue);
                    $itemTotal.html(originalTotalHtml);

                    const errorMsg = xhr.responseJSON?.message || 'Có lỗi xảy ra. Vui lòng thử lại!';
                    toastr.error(errorMsg);
                },
                complete: function() {
                    $buttons.prop('disabled', false).removeClass('loading');
                    $input.prop('disabled', false);
                }
            });
        }

        function removeFromCart(cartId) {
            if (!confirm('Bạn có chắc muốn xóa sản phẩm này?')) {
                return;
            }

            const $row = $(`.cart-item[data-cart-id="${cartId}"]`);

            $row.css('opacity', '0.5');

            $.ajax({
                url: '{{ route('cart.remove') }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    cart_id: cartId
                },
                success: function(response) {
                    if (response.success) {
                        $row.fadeOut(300, function() {
                            $(this).remove();

                            // Update cart count in header
                            $('#cart-count').text(response.cart_count);
                            $('#total-items').text($('.cart-item').length);

                            if (response.cart_count === 0 || $('.cart-item').length === 0) {
                                location.reload();
                            } else {
                                updateSelectAllState();
                                updateSelectedTotal();
                            }
                        });

                        toastr.success(response.message || 'Đã xóa sản phẩm khỏi giỏ hàng');
                    }
                },
                error: function(xhr) {
                    $row.css('opacity', '1');
                    const errorMsg = xhr.responseJSON?.message || 'Có lỗi xảy ra. Vui lòng thử lại!';
                    toastr.error(errorMsg);
                }
            });
        }

        function deleteSelected() {
            const selectedItems = [];
            $('.item-checkbox:checked').each(function() {
                selectedItems.push($(this).data('cart-id'));
            });

            if (selectedItems.length === 0) {
                toastr.warning('Vui lòng chọn sản phẩm cần xóa');
                return;
            }

            if (!confirm(`Bạn có chắc muốn xóa ${selectedItems.length} sản phẩm đã chọn?`)) {
                return;
            }

            let completed = 0;
            selectedItems.forEach(function(cartId) {
                $.ajax({
                    url: '{{ route('cart.remove') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        cart_id: cartId
                    },
                    success: function(response) {
                        completed++;
                        $(`.cart-item[data-cart-id="${cartId}"]`).fadeOut(300, function() {
                            $(this).remove();

                            if (completed === selectedItems.length) {
                                $('#cart-count').text(response.cart_count);
                                $('#total-items').text($('.cart-item').length);

                                if (response.cart_count === 0 || $('.cart-item').length === 0) {
                                    location.reload();
                                } else {
                                    updateSelectAllState();
                                    updateSelectedTotal();
                                    toastr.success(`Đã xóa ${selectedItems.length} sản phẩm`);
                                }
                            }
                        });
                    }
                });
            });
        }

        function proceedToCheckout(event) {
            event.preventDefault();

            const selectedItems = [];
            $('.item-checkbox:checked').each(function() {
                selectedItems.push($(this).data('cart-id'));
            });

            if (selectedItems.length === 0) {
                toastr.warning('Vui lòng chọn sản phẩm để thanh toán');
                return;
            }

            sessionStorage.setItem('selectedCartItems', JSON.stringify(selectedItems));

            const url = '{{ route('checkout.index') }}' + '?selected_items=' + encodeURIComponent(JSON.stringify(
                selectedItems));

            window.location.href = url;
        }
    </script>
@endpush

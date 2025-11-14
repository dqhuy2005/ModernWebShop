@extends('layouts.admin.app')

@section('title', 'Edit Order #' . $order->id . ' - Admin Panel')

@section('content')
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0">
                    <i class="fas fa-edit me-2"></i>Edit Order #{{ $order->id }}
                </h1>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-info">
                    <i class="fas fa-eye me-2"></i>View
                </a>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back
                </a>
            </div>
        </div>
    </div>

    @if (!$order->verifyIntegrity())
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Warning:</strong> Order total doesn't match calculated total. Editing will recalculate based on
            current product prices.
        </div>
    @endif

    <form action="{{ route('admin.orders.update', $order->id) }}" method="POST" id="orderForm">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="customer_search" class="form-label fw-bold">
                                Customer <span class="text-danger">*</span>
                            </label>
                            <div class="position-relative">
                                <input type="text" class="form-control @error('user_id') is-invalid @enderror"
                                    id="customer_search" placeholder="Type to search customer by name or email..."
                                    autocomplete="off" value="{{ old('customer_name', $order->user->fullname ?? '') }}">
                                <input type="hidden" id="user_id" name="user_id"
                                    value="{{ old('user_id', $order->user_id) }}">

                                <div id="customer_dropdown" class="list-group position-absolute w-100"
                                    style="max-height: 300px; overflow-y: auto; z-index: 1000; display: none; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                                </div>

                                <div id="customer_loading" class="position-absolute top-50 end-0 translate-middle-y me-2"
                                    style="display: none;">
                                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </div>
                            </div>
                            @error('user_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="customer_email" class="form-label fw-bold">
                                    Email get notifications
                                </label>
                                <input type="email" readonly
                                    class="form-control is-readonly @error('customer_email') is-invalid @enderror"
                                    id="customer_email" name="customer_email"
                                    value="{{ old('customer_email', $order->customer_email) }}"
                                    placeholder="email@example.com">
                                @error('customer_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="customer_name" class="form-label fw-bold">
                                    Customer Name
                                </label>
                                <input type="text" readonly
                                    class="form-control is-readonly @error('customer_name') is-invalid @enderror"
                                    id="customer_name" name="customer_name"
                                    value="{{ old('customer_name', $order->customer_name) }}" placeholder="Nguyễn Văn A">
                                @error('customer_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="customer_phone" class="form-label fw-bold">
                                    Phone Number
                                </label>
                                <input type="text" readonly
                                    class="form-control is-readonly @error('customer_phone') is-invalid @enderror"
                                    id="customer_phone" name="customer_phone"
                                    value="{{ old('customer_phone', $order->customer_phone) }}" placeholder="0987654321">
                                @error('customer_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label fw-bold">
                                Status <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status"
                                name="status" required>
                                <option value="pending"
                                    {{ old('status', $order->status) == 'pending' ? 'selected' : '' }}>
                                    Pending</option>
                                <option value="confirmed"
                                    {{ old('status', $order->status) == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                <option value="processing"
                                    {{ old('status', $order->status) == 'processing' ? 'selected' : '' }}>Processing
                                </option>
                                <option value="shipping"
                                    {{ old('status', $order->status) == 'shipping' ? 'selected' : '' }}>Shipping</option>
                                <option value="completed"
                                    {{ old('status', $order->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled"
                                    {{ old('status', $order->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label fw-bold">
                                Delivery Address
                            </label>
                            <input type="text" class="form-control @error('address') is-invalid @enderror"
                                id="address" name="address" value="{{ old('address', $order->address) }}"
                                placeholder="Enter delivery address...">
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-0">
                            <label for="note" class="form-label fw-bold">
                                Order Note
                            </label>
                            <textarea class="form-control @error('note') is-invalid @enderror" id="note" name="note" rows="3"
                                placeholder="Add any special instructions or notes...">{{ old('note', $order->note) }}</textarea>
                            @error('note')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Products</h5>
                        <button type="button" class="btn btn-sm btn-primary" id="addProductBtn">
                            <i class="fas fa-plus me-1"></i>Select Products
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="productsContainer">
                            @foreach ($order->orderDetails as $index => $detail)
                                <div class="product-row" data-index="{{ $index }}"
                                    data-product-id="{{ $detail->product_id }}">
                                    <div class="row align-items-center">
                                        <div class="col-md-5">
                                            <div class="d-flex align-items-center">
                                                @if ($detail->product && $detail->product->main_image)
                                                    <img src="{{ $detail->product->image_url }}"
                                                        alt="{{ $detail->product_name }}" class="rounded me-2"
                                                        style="width: 40px; height: 40px; object-fit: cover;">
                                                @else
                                                    <div class="bg-light rounded me-2 d-flex align-items-center justify-content-center"
                                                        style="width: 40px; height: 40px;">
                                                        <i class="fas fa-image text-muted"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <strong>{{ $detail->product_name }}</strong>
                                                    <input type="hidden"
                                                        name="products[{{ $index }}][product_id]"
                                                        value="{{ $detail->product_id }}">
                                                    <br><small class="text-muted">Price: <span
                                                            class="product-price">{{ number_format($detail->product->price ?? 0, 0, ',', '.') }}</span>
                                                        ₫</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-bold small mb-1">Quantity</label>
                                            <input type="number" class="form-control form-control-sm quantity-input"
                                                name="products[{{ $index }}][quantity]"
                                                value="{{ $detail->quantity }}" min="1" max="9999"
                                                data-price="{{ $detail->product->price ?? 0 }}" required>
                                        </div>
                                        <div class="col-md-3 text-end">
                                            <label class="form-label fw-bold small mb-1">Subtotal</label>
                                            <div class="fw-bold text-success subtotal">
                                                {{ number_format(($detail->product->price ?? 0) * $detail->quantity, 0, ',', '.') }}
                                                ₫</div>
                                        </div>
                                        <div class="col-md-1 text-end">
                                            <button type="button" class="btn btn-danger btn-sm remove-product"
                                                data-product-id="{{ $detail->product_id }}" title="Remove">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div id="emptyState" class="text-center text-muted py-5" style="display: none;">
                            <i class="fas fa-inbox fa-3x mb-3 opacity-25"></i>
                            <p>No products added yet. Click "Select Products" to add items.</p>
                        </div>
                        @error('products')
                            <div class="alert alert-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Total Items:</span>
                                <span class="fw-bold" id="totalItemsDisplay">{{ $order->total_items }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted">Total Products:</span>
                                <span class="fw-bold"
                                    id="totalProductsDisplay">{{ $order->orderDetails->count() }}</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0 fw-bold">TOTAL:</h5>
                                <h4 class="mb-0 fw-bold text-success" id="grandTotalDisplay">
                                    {{ $order->formatted_total_amount }}</h4>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer bg-light">
                        <button type="submit" class="btn btn-primary w-100" id="submitBtn">
                            <i class="fas fa-save me-2"></i>Update
                        </button>
                        <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-secondary w-100 mt-2">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div class="modal fade" id="productModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-search me-2"></i>Select Products
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <input type="text" class="form-control me-2" id="productSearch"
                            placeholder="Search products..." style="max-width: 400px;">
                        <span class="text-muted">
                            <span id="selectedCount">0</span> selected
                        </span>
                    </div>

                    <div class="list-group" id="productList" style="max-height: 400px; overflow-y: auto;">
                        @foreach ($products as $product)
                            <label class="list-group-item list-group-item-action product-item" style="cursor: pointer;">
                                <div class="d-flex align-items-center">
                                    <input type="checkbox" class="form-check-input me-3 product-checkbox"
                                        data-id="{{ $product->id }}" data-name="{{ $product->name }}"
                                        data-price="{{ $product->price }}"
                                        data-category="{{ $product->category->name ?? 'N/A' }}"
                                        data-image="{{ $product->image_url }}">

                                    @if ($product->main_image)
                                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                                            class="rounded me-3" style="width: 50px; height: 50px; object-fit: cover;">
                                    @else
                                        <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center"
                                            style="width: 50px; height: 50px;">
                                            <i class="fas fa-image text-muted"></i>
                                        </div>
                                    @endif
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0">{{ $product->name }}</h6>
                                        <small
                                            class="text-muted">{{ $product->category->name ?? 'Uncategorized' }}</small>
                                    </div>
                                    <div class="text-end">
                                        <span class="fw-bold text-primary">{{ $product->formatted_price }}</span>
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmSelection">
                        <i class="fas fa-check me-1"></i>Add Selected Products
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .product-row {
            animation: fadeIn 0.3s ease;
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 10px;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .product-item:hover {
            background-color: rgba(0, 123, 255, 0.1);
        }

        .highlight {
            background-color: #fff3cd;
            font-weight: 600;
            padding: 0 2px;
        }
    </style>
@endpush

@push('scripts')
    <script>
        let productIndex = {{ $order->orderDetails->count() }};
        const selectedProducts = new Set(@json($order->orderDetails->pluck('product_id')));
        const productModal = new bootstrap.Modal($('#productModal')[0]);

        // Customer autocomplete (edit page)
        let editSearchTimeout;
        let editCustomers = [];
        let editCurrentIndex = -1;

        function editDebounce(fn, delay) {
            return function(...args) {
                clearTimeout(editSearchTimeout);
                editSearchTimeout = setTimeout(() => fn.apply(this, args), delay);
            };
        }

        function editHighlight(text, q) {
            if (!q) return text;
            const regex = new RegExp(`(${q})`, 'gi');
            return text.replace(regex, '<span class="highlight">$1</span>');
        }

        function editSearchCustomers(q) {
            if ((q || '').length < 2) {
                $('#customer_dropdown').hide().empty();
                return;
            }
            $('#customer_loading').show();
            $.get('{{ route('admin.orders.search-customers') }}', {
                q: q
            }, function(resp) {
                $('#customer_loading').hide();
                editCustomers = resp.users || [];
                editCurrentIndex = -1;
                if (editCustomers.length) {
                    const dropdown = $('#customer_dropdown');
                    dropdown.empty();
                    editCustomers.forEach((c, idx) => {
                        const name = editHighlight(c.fullname, q);
                        const email = editHighlight(c.email, q);
                        const item = $(
                            `<a href="#" class="list-group-item list-group-item-action customer-item" data-index="${idx}"><div class="d-flex justify-content-between align-items-center"><div><strong>${name}</strong><br><small class="text-muted">${email}</small><br>${c.phone ? `<small class="text-muted">${c.phone}</small>` : ''}</div></div></a>`
                            );
                        dropdown.append(item);
                    });
                    dropdown.show();
                } else {
                    $('#customer_dropdown').html(
                        '<div class="list-group-item text-muted text-center py-3"><i class="fas fa-info-circle me-2"></i>No customers found</div>'
                        ).show();
                }
            }).fail(function() {
                $('#customer_loading').hide();
                $('#customer_dropdown').html(
                    '<div class="list-group-item text-danger text-center py-3"><i class="fas fa-exclamation-triangle me-2"></i>Error searching customers</div>'
                    ).show();
            });
        }

        function editSelectCustomer(c) {
            $('#user_id').val(c.id);
            $('#customer_search').val('');
            $('#customer_dropdown').hide();
            $('#customer_email').val(c.email);
            $('#customer_name').val(c.fullname);
            $('#customer_phone').val(c.phone && c.phone !== 'N/A' ? c.phone : '');
            $('#selected_customer_name').text(c.fullname);
            $('#selected_customer_email').text(c.email);
            $('#selected_customer_phone').text(c.phone && c.phone !== 'N/A' ? c.phone : '');
            $('#selected_customer').show();
            if (typeof c.can_receive_email !== 'undefined') {
                $('#send_email_checkbox').prop('checked', !!c.can_receive_email);
            }
            // fill address only if address field is empty (don't overwrite existing order address)
            if (!$('#address').val()) {
                $('#address').val(c.address || '');
            }
            editCustomers = [];
            editCurrentIndex = -1;
        }

        $('#customer_search').on('input', editDebounce(function() {
            editSearchCustomers($(this).val().trim());
        }, 350));

        $('#customer_search').on('keydown', function(e) {
            const dropdown = $('#customer_dropdown');
            if (!dropdown.is(':visible')) return;
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                editCurrentIndex = Math.min(editCurrentIndex + 1, editCustomers.length - 1);
                $('.customer-item').removeClass('active');
                $(`.customer-item[data-index="${editCurrentIndex}"]`).addClass('active');
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                editCurrentIndex = Math.max(editCurrentIndex - 1, 0);
                $('.customer-item').removeClass('active');
                $(`.customer-item[data-index="${editCurrentIndex}"]`).addClass('active');
            } else if (e.key === 'Enter') {
                e.preventDefault();
                if (editCurrentIndex >= 0 && editCustomers[editCurrentIndex]) {
                    editSelectCustomer(editCustomers[editCurrentIndex]);
                }
            } else if (e.key === 'Escape') {
                dropdown.hide();
            }
        });

        $(document).on('click', '.customer-item', function(e) {
            e.preventDefault();
            const idx = $(this).data('index');
            if (editCustomers[idx]) editSelectCustomer(editCustomers[idx]);
        });

        $('#clear_customer').on('click', function() {
            $('#user_id').val('');
            $('#customer_search').val('').focus();
            $('#customer_email').val('');
            $('#customer_name').val('');
            $('#customer_phone').val('');
            $('#selected_customer').hide();
            $('#send_email_checkbox').prop('checked', false);
        });

        // Hide dropdown when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('#customer_search, #customer_dropdown').length) {
                $('#customer_dropdown').hide();
                editCurrentIndex = -1;
            }
        });

        $('#addProductBtn').on('click', function() {
            productModal.show();
        });

        $('#productSearch').on('input', function() {
            const searchTerm = $(this).val().toLowerCase();
            $('.product-item').each(function() {
                const productName = $(this).data('name').toLowerCase();
                const category = $(this).data('category').toLowerCase();
                if (productName.includes(searchTerm) || category.includes(searchTerm)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });

        $(document).on('change', '.product-checkbox', function() {
            updateSelectedCount();
        });

        function updateSelectedCount() {
            const count = $('.product-checkbox:checked').length;
            $('#selectedCount').text(count);
        }

        $('#confirmSelection').on('click', function() {
            const selectedCheckboxes = $('.product-checkbox:checked');

            if (selectedCheckboxes.length === 0) {
                toastr.warning('Please select at least one product!');
                return;
            }

            let addedCount = 0;
            selectedCheckboxes.each(function() {
                const productId = $(this).data('id');

                if (!selectedProducts.has(productId)) {
                    const productName = $(this).data('name');
                    const productPrice = $(this).data('price');
                    const productImage = $(this).data('image');

                    addProductRow(productId, productName, productPrice, productImage);
                    selectedProducts.add(productId);
                    addedCount++;
                }
            });

            $('.product-checkbox').prop('checked', false);
            updateSelectedCount();
            productModal.hide();
            updateSummary();

            if (addedCount > 0) {
                toastr.success(`Added ${addedCount} product(s) successfully!`);
            } else {
                toastr.info('All selected products are already added!');
            }
        });

        $('#productModal').on('hidden.bs.modal', function() {
            $('.product-checkbox').prop('checked', false);
            updateSelectedCount();
        });

        function addProductRow(id, name, price, image) {
            $('#emptyState').hide();

            const imageHtml = image ?
                `<img src="/storage/${image}" alt="${name}" class="rounded me-2" style="width: 40px; height: 40px; object-fit: cover;">` :
                `<div class="bg-light rounded me-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;"><i class="fas fa-image text-muted"></i></div>`;

            const row = `
                <div class="product-row" data-index="${productIndex}" data-product-id="${id}" style="background: #f8f9fa; border-radius: 8px; padding: 15px; margin-bottom: 10px; border: 1px solid #e9ecef;">
                    <div class="row align-items-center">
                        <div class="col-md-5">
                            <div class="d-flex align-items-center">
                                ${imageHtml}
                                <div>
                                    <strong>${name}</strong>
                                    <input type="hidden" name="products[${productIndex}][product_id]" value="${id}">
                                    <br><small class="text-muted">Price: <span class="product-price">${formatPrice(price)}</span> ₫</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold small mb-1">Quantity</label>
                            <input type="number" class="form-control form-control-sm quantity-input" name="products[${productIndex}][quantity]"
                                value="1" min="1" max="9999" data-price="${price}" required>
                        </div>
                        <div class="col-md-3 text-end">
                            <label class="form-label fw-bold small mb-1">Subtotal</label>
                            <div class="fw-bold text-success subtotal">${formatPrice(price)} ₫</div>
                        </div>
                        <div class="col-md-1 text-end">
                            <button type="button" class="btn btn-danger btn-sm remove-product" data-product-id="${id}" title="Remove">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;

            $('#productsContainer').append(row);
            productIndex++;
        }

        $(document).on('click', '.remove-product', function() {
            const productId = $(this).data('product-id');
            const row = $(this).closest('.product-row');

            row.fadeOut(300, function() {
                $(this).remove();
                selectedProducts.delete(productId);
                updateSummary();

                if ($('.product-row').length === 0) {
                    $('#emptyState').show();
                }
            });
        });

        $(document).on('input', '.quantity-input', function() {
            const quantity = parseInt($(this).val()) || 0;
            const price = parseInt($(this).data('price')) || 0;
            const subtotal = price * quantity;

            $(this).closest('.product-row').find('.subtotal').text(formatPrice(subtotal) + ' ₫');
            updateSummary();
        });

        function updateSummary() {
            let grandTotal = 0;
            let totalItems = 0;
            const productCount = $('.product-row').length;

            $('.product-row').each(function() {
                const quantity = parseInt($(this).find('.quantity-input').val()) || 0;
                const price = parseInt($(this).find('.quantity-input').data('price')) || 0;
                grandTotal += price * quantity;
                totalItems += quantity;
            });

            $('#totalItemsDisplay').text(totalItems);
            $('#totalProductsDisplay').text(productCount);
            $('#grandTotalDisplay').text(formatPrice(grandTotal) + ' ₫');
        }

        function formatPrice(price) {
            return new Intl.NumberFormat('vi-VN').format(price);
        }

        updateSummary();

        $('#orderForm').on('submit', function(e) {
            if ($('.product-row').length === 0) {
                e.preventDefault();
                toastr.error('Please add at least one product!');
                return false;
            }

            if (!$('#user_id').val()) {
                e.preventDefault();
                toastr.error('Please select a customer!');
                return false;
            }
        });
    </script>
@endpush

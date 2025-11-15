@extends('layouts.admin.app')

@section('title', 'Create New Order - Admin Panel')

@section('content')
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0">
                    <i class="fas fa-plus-circle me-2"></i>Create New Order
                </h1>
            </div>
            <div>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back
                </a>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.orders.store') }}" method="POST" id="orderForm">
        @csrf

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
                                    autocomplete="off">
                                <input type="hidden" id="user_id" name="user_id" value="{{ old('user_id') }}">

                                <div id="customer_dropdown" class="list-group position-absolute w-100"
                                    style="max-height: 300px; overflow-y: auto; z-index: 1000; display: none; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                                </div>

                                <div id="customer_loading" class="position-absolute top-50 end-0 translate-middle-y me-2"
                                    style="display: none;">
                                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </div>

                                <input type="hidden" id="customer_email_hidden" name="customer_email"
                                    value="{{ old('customer_email') }}">
                                <input type="hidden" id="customer_name_hidden" name="customer_name"
                                    value="{{ old('customer_name') }}">
                                <input type="hidden" id="customer_phone_hidden" name="customer_phone"
                                    value="{{ old('customer_phone') }}">
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
                                    id="customer_email" name="customer_email" value="{{ old('customer_email') }}">
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
                                    id="customer_name" name="customer_name" value="{{ old('customer_name') }}">
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
                                    id="customer_phone" name="customer_phone" value="{{ old('customer_phone') }}">
                                @error('customer_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label fw-bold">
                                Status <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status"
                                required>
                                <option value="pending" {{ old('status', 'pending') == 'pending' ? 'selected' : '' }}>
                                    Pending</option>
                                <option value="confirmed" {{ old('status') == 'confirmed' ? 'selected' : '' }}>Confirmed
                                </option>
                                <option value="processing" {{ old('status') == 'processing' ? 'selected' : '' }}>Processing
                                </option>
                                <option value="shipping" {{ old('status') == 'shipping' ? 'selected' : '' }}>Shipping
                                </option>
                                <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed
                                </option>
                                <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled
                                </option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label fw-bold">
                                Delivery Address
                            </label>
                            <input type="text" class="form-control @error('address') is-invalid @enderror" id="address"
                                name="address" value="{{ old('address') }}" placeholder="Enter delivery address...">
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-0">
                            <label for="note" class="form-label fw-bold">
                                Order Note
                            </label>
                            <textarea class="form-control @error('note') is-invalid @enderror" id="note" name="note" rows="3"
                                placeholder="Add any special instructions or notes...">{{ old('note') }}</textarea>
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
                        </div>
                        <div id="emptyState" class="text-center text-muted py-5">
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
                                <span class="fw-bold" id="totalItemsDisplay">0</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted">Total Products:</span>
                                <span class="fw-bold" id="totalProductsDisplay">0</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0 fw-bold">TOTAL:</h5>
                                <h4 class="mb-0 fw-bold text-success" id="grandTotalDisplay">0 ₫</h4>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer bg-light">
                        <button type="submit" class="btn btn-primary w-100" id="submitBtn">
                            <i class="fas fa-save me-2"></i>Create
                        </button>
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary w-100 mt-2">
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
                                        data-category="{{ $product->category->name ?? 'N/A' }}">

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
            border: 1px solid #e9ecef;
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
            background-color: rgba(0, 123, 255, 0.05);
        }

        .product-item input[type="checkbox"]:checked {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }

        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
        }

        .card-header h5 {
            font-size: 1rem;
            font-weight: 600;
        }

        /* Autocomplete Styles */
        #customer_dropdown .list-group-item {
            cursor: pointer;
            border-left: 0;
            border-right: 0;
            border-top: 0;
            transition: background-color 0.2s ease;
        }

        #customer_dropdown .list-group-item:first-child {
            border-top: 1px solid #dee2e6;
            border-top-left-radius: 0.25rem;
            border-top-right-radius: 0.25rem;
        }

        #customer_dropdown .list-group-item:last-child {
            border-bottom: 1px solid #dee2e6;
            border-bottom-left-radius: 0.25rem;
            border-bottom-right-radius: 0.25rem;
        }

        #customer_dropdown .list-group-item:hover,
        #customer_dropdown .list-group-item.active {
            background-color: #f8f9fa;
            z-index: 1;
        }

        #customer_dropdown .list-group-item.active {
            background-color: #e7f1ff;
            border-color: #dee2e6;
        }

        .highlight {
            background-color: #fff3cd;
            font-weight: 600;
            padding: 0 2px;
        }

        #customer_search:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }
    </style>
@endpush

@push('scripts')
    <script>
        let searchTimeout;
        let currentCustomerIndex = -1;
        let customers = [];

        function debounce(func, delay) {
            return function(...args) {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => func.apply(this, args), delay);
            };
        }

        function highlightText(text, query) {
            if (!query) return text;
            const regex = new RegExp(`(${query})`, 'gi');
            return text.replace(regex, '<span class="highlight">$1</span>');
        }

        function searchCustomers(query) {
            if (query.length < 2) {
                $('#customer_dropdown').hide().empty();
                return;
            }

            $('#customer_loading').show();

            $.ajax({
                url: '{{ route('admin.orders.search-customers') }}',
                method: 'GET',
                data: {
                    q: query
                },
                success: function(response) {
                    $('#customer_loading').hide();
                    customers = response.users || [];
                    currentCustomerIndex = -1;

                    if (customers.length > 0) {
                        displayCustomers(customers, query);
                    } else {
                        $('#customer_dropdown').html(
                            '<div class="list-group-item text-muted text-center py-3">' +
                            '<i class="fas fa-info-circle me-2"></i>No customers found' +
                            '</div>'
                        ).show();
                    }
                },
                error: function() {
                    $('#customer_loading').hide();
                    $('#customer_dropdown').html(
                        '<div class="list-group-item text-danger text-center py-3">' +
                        '<i class="fas fa-exclamation-triangle me-2"></i>Error searching customers' +
                        '</div>'
                    ).show();
                }
            });
        }

        function displayCustomers(customers, query) {
            const dropdown = $('#customer_dropdown');
            dropdown.empty();

            customers.forEach((customer, index) => {
                const highlightedName = highlightText(customer.fullname, query);
                const highlightedEmail = highlightText(customer.email, query);

                const item = $(`
                    <a href="#" class="list-group-item list-group-item-action customer-item" data-index="${index}">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>${highlightedName}</strong>
                                <br><small class="text-muted">${highlightedEmail}</small>
                                ${customer.phone ? `<br><small class="text-muted"><i class="fas fa-phone me-1"></i>${customer.phone}</small>` : ''}
                            </div>
                            <i class="fas fa-check-circle text-success" style="opacity: 0;"></i>
                        </div>
                    </a>
                `);

                dropdown.append(item);
            });

            dropdown.show();
        }

        function selectCustomer(customer) {
            $('#user_id').val(customer.id);
            $('#customer_search').val('');
            $('#customer_dropdown').hide();

            $('#selected_customer_name').text(customer.fullname);
            $('#selected_customer_email').text(customer.email);
            $('#selected_customer_phone').text(customer.phone && customer.phone !== 'N/A' ? customer.phone : 'No phone');
            $('#selected_customer').show();

            $('#customer_email_hidden').val(customer.email);
            $('#customer_name_hidden').val(customer.fullname);
            $('#customer_phone_hidden').val(customer.phone && customer.phone !== 'N/A' ? customer.phone : '');

            $('#customer_email').val(customer.email);
            $('#customer_name').val(customer.fullname);
            $('#customer_phone').val(customer.phone && customer.phone !== 'N/A' ? customer.phone : '');
            $('#address').val(customer.address || '');

            if (typeof customer.can_receive_email !== 'undefined') {
                $('#send_email_checkbox').prop('checked', !!customer.can_receive_email);
            } else {
                $('#send_email_checkbox').prop('checked', !!customer.email);
            }

            currentCustomerIndex = -1;
            customers = [];
        }

        $('#customer_search').on('input', debounce(function() {
            const query = $(this).val().trim();
            searchCustomers(query);
        }, 400));

        $('#customer_search').on('keydown', function(e) {
            const dropdown = $('#customer_dropdown');

            if (!dropdown.is(':visible')) return;

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                currentCustomerIndex = Math.min(currentCustomerIndex + 1, customers.length - 1);
                updateActiveItem();
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                currentCustomerIndex = Math.max(currentCustomerIndex - 1, 0);
                updateActiveItem();
            } else if (e.key === 'Enter') {
                e.preventDefault();
                if (currentCustomerIndex >= 0 && currentCustomerIndex < customers.length) {
                    selectCustomer(customers[currentCustomerIndex]);
                }
            } else if (e.key === 'Escape') {
                dropdown.hide();
                currentCustomerIndex = -1;
            }
        });

        function updateActiveItem() {
            $('.customer-item').removeClass('active');
            if (currentCustomerIndex >= 0) {
                const activeItem = $(`.customer-item[data-index="${currentCustomerIndex}"]`);
                activeItem.addClass('active');

                const dropdown = $('#customer_dropdown');
                const itemTop = activeItem.position().top;
                const itemBottom = itemTop + activeItem.outerHeight();
                const dropdownHeight = dropdown.height();

                if (itemBottom > dropdownHeight) {
                    dropdown.scrollTop(dropdown.scrollTop() + itemBottom - dropdownHeight);
                } else if (itemTop < 0) {
                    dropdown.scrollTop(dropdown.scrollTop() + itemTop);
                }
            }
        }

        $(document).on('click', '.customer-item', function(e) {
            e.preventDefault();
            const index = $(this).data('index');
            if (customers[index]) {
                selectCustomer(customers[index]);
            }
        });

        $(document).on('mouseenter', '.customer-item', function() {
            currentCustomerIndex = $(this).data('index');
            updateActiveItem();
        });

        $('#clear_customer').on('click', function() {
            $('#user_id').val('');
            $('#customer_search').val('').focus();
            $('#selected_customer').hide();
            $('#customer_email_hidden').val('');
            $('#customer_name_hidden').val('');
            $('#customer_phone_hidden').val('');
            $('#customer_email').val('');
            $('#customer_name').val('');
            $('#customer_phone').val('');
            $('#address').val('');
            $('#selected_customer_phone').text('');
            $('#send_email_checkbox').prop('checked', false);
        });

        $(document).on('click', function(e) {
            if (!$(e.target).closest('#customer_search, #customer_dropdown').length) {
                $('#customer_dropdown').hide();
                currentCustomerIndex = -1;
            }
        });

        @if (old('user_id'))
            $(document).ready(function() {
                $.ajax({
                    url: '{{ route('admin.orders.search-customers') }}',
                    method: 'GET',
                    data: {
                        id: '{{ old('user_id') }}'
                    },
                    success: function(response) {
                        if (response.users && response.users.length > 0) {
                            const customer = response.users[0];
                            selectCustomer(customer);
                        }
                    }
                });
            });
        @endif

        let productIndex = 0;
        const selectedProducts = new Set();
        const productModal = new bootstrap.Modal($('#productModal')[0]);

        $('#addProductBtn').on('click', function() {
            productModal.show();
        });

        $('#productSearch').on('input', function() {

            const searchTerm = $(this).val().toLowerCase();
            $('.product-item').each(function() {
                const checkbox = $(this).find('.product-checkbox');
                const productName = checkbox.data('name').toLowerCase();
                const category = checkbox.data('category').toLowerCase();
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

            selectedCheckboxes.each(function() {
                const productId = $(this).data('id');

                if (!selectedProducts.has(productId)) {
                    const productName = $(this).data('name');
                    const productPrice = $(this).data('price');

                    addProductRow(productId, productName, productPrice, null);
                    selectedProducts.add(productId);
                }
            });

            $('.product-checkbox').prop('checked', false);
            updateSelectedCount();
            productModal.hide();
            updateSummary();

            toastr.success(`Added ${selectedCheckboxes.length} product(s) successfully!`);
        });

        function addProductRow(id, name, price, image) {
            $('#emptyState').hide();

            const row = `
                <div class="product-row" data-index="${productIndex}" data-product-id="${id}">
                    <div class="row align-items-center">
                        <div class="col-md-5">
                            <div class="d-flex align-items-center">
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

        $('#productModal').on('hidden.bs.modal', function() {
            $('.product-checkbox').prop('checked', false);
            updateSelectedCount();
        });
    </script>
@endpush

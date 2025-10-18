@extends('layouts.admin.app')

@section('title', 'Product Management - Admin Panel')

@section('content')
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0">
                    <i class="fas fa-box me-2"></i>Product Management : {{ $products->total() ?? 0 }}
                </h1>
            </div>
            <div>
                <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add
                </a>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-lg bg-primary-soft rounded">
                                <i class="fas fa-box fa-lg text-primary"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Total Products</h6>
                            <h4 class="mb-0" id="totalProductsCount">{{ $totalProducts ?? 0 }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-lg bg-success-soft rounded">
                                <i class="fas fa-check-circle fa-lg text-success"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Active</h6>
                            <h4 class="mb-0" id="activeProductsCount">{{ $activeProducts ?? 0 }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-lg bg-danger-soft rounded">
                                <i class="fas fa-times-circle fa-lg text-danger"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Inactive</h6>
                            <h4 class="mb-0" id="inactiveProductsCount">{{ $inactiveProducts ?? 0 }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-lg bg-warning-soft rounded">
                                <i class="fas fa-fire fa-lg text-warning"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Hot Products</h6>
                            <h4 class="mb-0" id="hotProductsCount">{{ $hotProducts ?? 0 }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('admin.products.form')

    <div id="products-table-container">
        @include('admin.products.table')
    </div>

@endsection

@push('scripts')
    <script>
        let productPagination;

        $(document).ready(function() {
            productPagination = new AjaxPagination({
                containerId: 'products-table-container',
                paginationSelector: 'nav[aria-label="Products pagination"]',
                onCountsUpdate: function(counts) {
                    if (counts.total !== undefined) $('#totalProductsCount').text(counts.total);
                    if (counts.active !== undefined) $('#activeProductsCount').text(counts.active);
                    if (counts.inactive !== undefined) $('#inactiveProductsCount').text(counts.inactive);
                    if (counts.hot !== undefined) $('#hotProductsCount').text(counts.hot);
                },
                onAfterLoad: function(response) {
                    if (typeof initLazyLoading === 'function') {
                        initLazyLoading();
                    }
                },
                onError: function(xhr) {
                    if (typeof toastr !== 'undefined') {
                        toastr.error('Failed to load page');
                    } else {
                        alert('Failed to load page');
                    }
                }
            });
        });

        function toggleStatus(productId) {
            if (confirm('Are you sure you want to change the status of this product?')) {
                const row = $('#product-' + productId);

                $.ajax({
                    url: '/admin/products/' + productId + '/toggle-status',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            if (typeof toastr !== 'undefined') {
                                toastr.success(response.message);
                            } else {
                                alert(response.message);
                            }

                            if (response.status === 1 || response.status === true) {
                                row.find("input[type='checkbox']").prop('checked', true);
                            } else {
                                row.find("input[type='checkbox']").prop('checked', false);
                            }

                            if (response.counts) {
                                $('#totalProductsCount').text(response.counts.total);
                                $('#activeProductsCount').text(response.counts.active);
                                $('#inactiveProductsCount').text(response.counts.inactive);
                                $('#hotProductsCount').text(response.counts.hot);
                            }
                        } else {
                            if (typeof toastr !== 'undefined') {
                                toastr.error(response.message);
                            } else {
                                alert(response.message);
                            }
                        }
                    },
                    error: function(xhr) {
                        if (typeof toastr !== 'undefined') {
                            toastr.error('Failed to update status!');
                        } else {
                            alert('Failed to update status!');
                        }
                    }
                });
            }
        }

        function toggleHot(productId, button) {
            $.ajax({
                url: '/admin/products/' + productId + '/toggle-hot',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        if (typeof toastr !== 'undefined') {
                            toastr.success(response.message);
                        } else {
                            alert(response.message);
                        }

                        if (response.is_hot) {
                            $(button).removeClass('btn-outline-warning').addClass('btn-warning');
                            $(button).html('<i class="fas fa-fire"></i>');
                        } else {
                            $(button).removeClass('btn-warning').addClass('btn-outline-warning');
                            $(button).html('<i class="fas fa-fire"></i>');
                        }

                        if (response.counts) {
                            $('#totalProductsCount').text(response.counts.total);
                            $('#activeProductsCount').text(response.counts.active);
                            $('#inactiveProductsCount').text(response.counts.inactive);
                            $('#hotProductsCount').text(response.counts.hot);
                        }
                    } else {
                        if (typeof toastr !== 'undefined') {
                            toastr.error(response.message);
                        } else {
                            alert(response.message);
                        }
                    }
                },
                error: function(xhr) {
                    if (typeof toastr !== 'undefined') {
                        toastr.error('Failed to toggle hot status!');
                    } else {
                        alert('Failed to toggle hot status!');
                    }
                }
            });
        }

        function deleteProduct(productId) {
            if (confirm('Are you sure you want to delete this product? This action cannot be undone!')) {
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = '/admin/products/' + productId;

                var methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';
                form.appendChild(methodInput);

                var tokenInput = document.createElement('input');
                tokenInput.type = 'hidden';
                tokenInput.name = '_token';
                tokenInput.value = '{{ csrf_token() }}';
                form.appendChild(tokenInput);

                document.body.appendChild(form);
                form.submit();
            }
        }

        function changePerPage(value) {
            let url = new URL(window.location.href);
            url.searchParams.set('per_page', value);
            url.searchParams.set('page', 1);
            loadPage(url.toString());
        }

        @if (!request()->has('per_page') || request('per_page') == 'all')
            $(document).ready(function() {
                $('.datatable').DataTable({
                    order: [
                        [0, 'desc']
                    ],
                    pageLength: 25,
                    language: {
                        search: "Search:",
                        lengthMenu: "Show _MENU_ entries",
                        info: "Showing _START_ to _END_ of _TOTAL_ entries",
                        paginate: {
                            first: "First",
                            last: "Last",
                            next: "Next",
                            previous: "Previous"
                        }
                    }
                });
            });
        @endif
    </script>
@endpush

@push('styles')
    <style>
        .avatar-lg {
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .bg-primary-soft {
            background-color: rgba(13, 110, 253, 0.1);
        }

        .bg-success-soft {
            background-color: rgba(25, 135, 84, 0.1);
        }

        .bg-danger-soft {
            background-color: rgba(220, 53, 69, 0.1);
        }

        .bg-warning-soft {
            background-color: rgba(255, 193, 7, 0.1);
        }

        .product-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 4px;
        }

        /* Pagination Wrapper */
        nav[aria-label="Products pagination"] {
            display: flex;
            justify-content: center;
        }

        nav[aria-label="Products pagination"] .pagination {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .page-link {
                padding: 0.25rem 0.5rem;
                font-size: 0.875rem;
            }

            nav[aria-label="Products pagination"] .pagination {
                gap: 2px;
            }
        }

        /* Loading state for AJAX pagination */
        #products-table-container {
            transition: opacity 0.3s ease;
        }

        #products-table-container.loading {
            opacity: 0.5;
            pointer-events: none;
        }
    </style>
@endpush

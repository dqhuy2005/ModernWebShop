@extends('layouts.admin.app')

@section('title', 'Product Management - Admin Panel')

@section('content')
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0">
                    <i class="fas fa-box me-2"></i>Product Management
                </h1>
                <nav aria-label="breadcrumb" class="d-none d-md-block mt-2">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Products</li>
                    </ol>
                </nav>
            </div>
            <div>
                <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add New Product
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
                            <h4 class="mb-0">{{ $products->total() }}</h4>
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
                            <h4 class="mb-0">{{ $products->where('status', true)->count() }}</h4>
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
                            <h4 class="mb-0">{{ $products->where('status', false)->count() }}</h4>
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
                            <h4 class="mb-0">{{ $products->where('is_hot', true)->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('admin.products.form')

    @include('admin.products.table')

@endsection

@push('scripts')
    <script>
        // Toggle Status
        function toggleStatus(productId) {
            if (confirm('Are you sure you want to change the status of this product?')) {
                $.ajax({
                    url: '/cms/products/' + productId + '/toggle-status',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            location.reload();
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(xhr) {
                        toastr.error('Failed to update status!');
                    }
                });
            }
        }

        // Toggle Hot Status
        function toggleHot(productId, button) {
            $.ajax({
                url: '/cms/products/' + productId + '/toggle-hot',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);

                        // Update button appearance
                        if (response.is_hot) {
                            $(button).removeClass('btn-outline-warning').addClass('btn-warning');
                            $(button).html('<i class="fas fa-fire"></i>');
                        } else {
                            $(button).removeClass('btn-warning').addClass('btn-outline-warning');
                            $(button).html('<i class="far fa-fire"></i>');
                        }
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function(xhr) {
                    toastr.error('Failed to toggle hot status!');
                }
            });
        }

        // Delete Product
        function deleteProduct(productId) {
            if (confirm('Are you sure you want to delete this product? This action cannot be undone!')) {
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = '/cms/products/' + productId;

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

        // Bulk Delete
        function bulkDelete() {
            let checkedBoxes = $('.product-checkbox:checked');
            if (checkedBoxes.length === 0) {
                toastr.warning('Please select at least one product to delete!');
                return;
            }

            if (confirm('Are you sure you want to delete ' + checkedBoxes.length + ' selected products?')) {
                let ids = [];
                checkedBoxes.each(function() {
                    ids.push($(this).val());
                });

                $.ajax({
                    url: '/cms/products/bulk-delete',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        ids: ids
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            location.reload();
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(xhr) {
                        toastr.error('Failed to delete products!');
                    }
                });
            }
        }

        // Select All Checkbox
        $('#checkAll').on('change', function() {
            $('.product-checkbox').prop('checked', $(this).prop('checked'));
        });

        // Update "Check All" state
        $('.product-checkbox').on('change', function() {
            let allChecked = $('.product-checkbox:checked').length === $('.product-checkbox').length;
            $('#checkAll').prop('checked', allChecked);
        });

        // Initialize DataTable if not using pagination
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
    </style>
@endpush

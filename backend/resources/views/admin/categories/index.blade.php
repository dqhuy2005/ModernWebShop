@extends('layouts.admin.app')

@section('title', 'Categories Management')

@section('content')
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0">
                    <i class="fas fa-list me-2"></i>Categories Management
                </h1>
            </div>
            <div>
                <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> Add
                </a>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon bg-primary-soft me-3">
                            <i class="fas fa-list text-primary"></i>
                        </div>
                        <div>
                            <h5 class="mb-0" id="totalCategoriesCount">{{ $stats->total ?? 0 }}</h5>
                            <small class="text-muted">Total Categories</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon bg-success-soft me-3">
                            <i class="fas fa-check-circle text-success"></i>
                        </div>
                        <div>
                            <h5 class="mb-0" id="activeCategoriesCount">{{ $stats->active ?? 0 }}</h5>
                            <small class="text-muted">Active Categories</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon bg-danger-soft me-3">
                            <i class="fas fa-times-circle text-danger"></i>
                        </div>
                        <div>
                            <h5 class="mb-0" id="inactiveCategoriesCount">{{ $stats->inactive ?? 0 }}</h5>
                            <small class="text-muted">Inactive Categories</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('admin.categories.form')

    <div id="categories-table-container">
        @include('admin.categories.table')
    </div>
@endsection

@push('styles')
    <style>
        .stats-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
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

        .table> :not(caption)>*>* {
            padding: 1rem 0.75rem;
        }

        .badge {
            padding: 0.35em 0.65em;
            font-weight: 500;
        }
    </style>
@endpush

@push('scripts')
    <script src="{{ asset('js/ajax-pagination.js') }}"></script>
    <script>
        let categoryPagination;

        $(document).ready(function() {
            categoryPagination = new AjaxPagination({
                containerId: 'categories-table-container',
                paginationSelector: 'nav[aria-label="Categories pagination"]',
                onCountsUpdate: function(counts) {
                    if (counts.total !== undefined) $('#totalCategoriesCount').text(counts.total);
                    if (counts.active !== undefined) $('#activeCategoriesCount').text(counts.active);
                    if (counts.inactive !== undefined) $('#inactiveCategoriesCount').text(counts.inactive);
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

        function changePerPage(value) {
            let url = new URL(window.location.href);
            url.searchParams.set('per_page', value);
            url.searchParams.set('page', 1);
            categoryPagination.loadPage(url.toString());
        }

        function deleteCategory(categoryId) {
            if (!confirm('Are you sure you want to delete this category?')) return;

            $.ajax({
                url: '/admin/categories/' + categoryId,
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        if (typeof toastr !== 'undefined') {
                            toastr.success(response.message);
                        }
                        window.location.reload();
                    }
                },
                error: function(xhr) {
                    if (typeof toastr !== 'undefined') {
                        toastr.error('Failed to delete category');
                    } else {
                        alert('Failed to delete category');
                    }
                }
            });
        }

        function restoreCategory(categoryId) {
            $.ajax({
                url: '/admin/categories/' + categoryId + '/restore',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        if (typeof toastr !== 'undefined') {
                            toastr.success(response.message);
                        }
                        window.location.reload();
                    }
                },
                error: function(xhr) {
                    if (typeof toastr !== 'undefined') {
                        toastr.error('Failed to restore category');
                    } else {
                        alert('Failed to restore category');
                    }
                }
            });
        }

        function forceDeleteCategory(categoryId) {
            if (!confirm('Are you sure you want to PERMANENTLY delete this category? This action cannot be undone!'))
                return;

            $.ajax({
                url: '/admin/categories/' + categoryId + '/force-delete',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        if (typeof toastr !== 'undefined') {
                            toastr.success(response.message);
                        }
                        window.location.reload();
                    }
                },
                error: function(xhr) {
                    if (typeof toastr !== 'undefined') {
                        toastr.error('Failed to permanently delete category');
                    } else {
                        alert('Failed to permanently delete category');
                    }
                }
            });
        }
    </script>
@endpush

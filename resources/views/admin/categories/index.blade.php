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

    <div class="card">
        <div class="card-body">
            @include('admin.categories.form')
        </div>

        <div id="categories-table-container">
            @include('admin.categories.table')
        </div>
    </div>
@endsection

@push('styles')
    <style>
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
    <script src="{{ asset('js/table-sort.js') }}"></script>
    <script>
        let categoryPagination;
        let categoryTableSort;

        $(document).ready(function() {
            categoryPagination = new AjaxPagination({
                containerId: 'categories-table-container',
                paginationSelector: 'nav[aria-label="Categories pagination"]',
                onCountsUpdate: function(counts) {
                    if (counts.total !== undefined) $('#totalCategoriesCount').text(counts.total);
                    if (counts.active !== undefined) $('#activeCategoriesCount').text(counts.active);
                    if (counts.inactive !== undefined) $('#inactiveCategoriesCount').text(counts
                        .inactive);
                },
                onError: function(xhr) {
                    if (typeof toastr !== 'undefined') {
                        toastr.error('Failed to load page');
                    } else {
                        alert('Failed to load page');
                    }
                }
            });

            // Initialize table sort
            categoryTableSort = new TableSort({
                containerId: 'categories-table-container',
                sortBy: '{{ request("sort_by") }}',
                sortOrder: '{{ request("sort_order", "desc") }}',
                paginationInstance: categoryPagination,
                onAfterSort: function(column, order) {
                    if (typeof toastr !== 'undefined') {
                        toastr.success('Sorted by ' + column + ' (' + order + ')');
                    }
                }
            });

            // Set global instance for sortTable function
            window.tableSort = categoryTableSort;
        });

        function changePerPage(value) {
            let url = new URL(window.location.href);
            url.searchParams.set('per_page', value);
            url.searchParams.set('page', 1);
            categoryPagination.loadPage(url.toString());
        }

        function deleteCategory(categoryId) {
            ConfirmModal.delete('Are you sure you want to delete this category?', function() {
                deleteCategoryAjax(categoryId);
            });
        }

        function deleteCategoryAjax(categoryId) {
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
            ConfirmModal.show('Are you sure you want to restore this category?', function() {
                restoreCategoryAjax(categoryId);
            }, {
                confirmText: 'Confirm',
                cancelText: 'Cancel',
                icon: 'fas fa-undo',
                iconColor: '#28a745'
            });
        }

        function restoreCategoryAjax(categoryId) {
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
            ConfirmModal.show(
                'Are you sure you want to PERMANENTLY delete this category?<br><small class="text-danger">This action cannot be undone!</small>',
                function() {
                    forceDeleteCategoryAjax(categoryId);
                },
                {
                    confirmText: 'Confirm',
                    cancelText: 'Cancel',
                    icon: 'fas fa-exclamation-triangle',
                    iconColor: '#dc3545'
                }
            );
        }

        function forceDeleteCategoryAjax(categoryId) {
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

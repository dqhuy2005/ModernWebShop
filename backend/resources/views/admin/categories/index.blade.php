@extends('layouts.admin.app')

@section('title', 'Categories Management')

@section('content')
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0">
                    <i class="fas fa-list me-2"></i>Categories Management
                </h1>
                <p class="text-muted mb-0">Manage product categories</p>
            </div>
            <div>
                <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> Add Category
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
                            <h5 class="mb-0" id="stat-total">{{ $stats->total ?? 0 }}</h5>
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
                            <h5 class="mb-0" id="stat-active">{{ $stats->active ?? 0 }}</h5>
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
                            <h5 class="mb-0" id="stat-inactive">{{ $stats->inactive ?? 0 }}</h5>
                            <small class="text-muted">Inactive Categories</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm" id="categories-table-container">
        <div class="card-header bg-white border-0 py-3">
            <form id="filter-form" class="row g-3">
                <div class="col-md-3">
                    <input type="text"
                           name="search"
                           class="form-control"
                           placeholder="Search categories..."
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="sort_by" class="form-select">
                        <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Created Date</option>
                        <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Name</option>
                        <option value="updated_at" {{ request('sort_by') == 'updated_at' ? 'selected' : '' }}>Updated Date</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="sort_order" class="form-select">
                        <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>Descending</option>
                        <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Ascending</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <select name="per_page" class="form-select">
                        <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-1"></i> Filter
                    </button>
                </div>
            </form>
        </div>

        <div class="card-body p-0" id="table-content">
            @include('admin.categories.table')
        </div>
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

    .bg-primary-soft { background-color: rgba(13, 110, 253, 0.1); }
    .bg-success-soft { background-color: rgba(25, 135, 84, 0.1); }
    .bg-danger-soft { background-color: rgba(220, 53, 69, 0.1); }

    .table > :not(caption) > * > * {
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
    $(document).ready(function() {
        // Initialize AJAX pagination
        const pagination = new AjaxPagination({
            containerId: 'categories-table-container',
            contentId: 'table-content',
            formId: 'filter-form',
            statsConfig: {
                'total': '#stat-total',
                'active': '#stat-active',
                'inactive': '#stat-inactive'
            }
        });

        // Filter form submission
        $('#filter-form').on('submit', function(e) {
            e.preventDefault();
            pagination.loadPage(1, $(this).serialize());
        });

        // Auto-submit on filter change
        $('#filter-form select, #filter-form input[name="search"]').on('change', function() {
            $('#filter-form').submit();
        });

        // Delete category
        $(document).on('click', '.delete-category', function(e) {
            e.preventDefault();
            const url = $(this).data('url');
            const name = $(this).data('name');

            if (confirm(`Are you sure you want to delete "${name}"?`)) {
                $.ajax({
                    url: url,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        toastr.success(response.message);
                        $('#filter-form').submit();
                    },
                    error: function(xhr) {
                        toastr.error('Failed to delete category');
                    }
                });
            }
        });

        // Restore category
        $(document).on('click', '.restore-category', function(e) {
            e.preventDefault();
            const url = $(this).data('url');

            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    toastr.success(response.message);
                    $('#filter-form').submit();
                },
                error: function(xhr) {
                    toastr.error('Failed to restore category');
                }
            });
        });

        // Force delete category
        $(document).on('click', '.force-delete-category', function(e) {
            e.preventDefault();
            const url = $(this).data('url');
            const name = $(this).data('name');

            if (confirm(`Are you sure you want to PERMANENTLY delete "${name}"? This action cannot be undone!`)) {
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        toastr.success(response.message);
                        $('#filter-form').submit();
                    },
                    error: function(xhr) {
                        toastr.error('Failed to permanently delete category');
                    }
                });
            }
        });
    });
</script>
@endpush

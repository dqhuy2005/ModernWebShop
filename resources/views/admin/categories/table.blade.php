<div class="border-top mt-3 pt-3">
    @if ($categories->count() > 0)
        <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
            <table class="table table-hover align-middle" id="categoriesTable">
                <thead class="table-light">
                    <tr>
                        <th width="5%">ID</th>
                        <th width="30%">Name</th>
                        <th width="15%">Products</th>
                        <th width="15%" class="text-center">Status</th>
                        <th width="20%" class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($categories as $category)
                        <tr id="category-{{ $category->id }}"
                            class="{{ $category->deleted_at ? 'table-warning' : '' }}">
                            <td>
                                <strong>{{ $category->id }}</strong>
                            </td>

                            <td>
                                <div>
                                    <strong>{{ $category->name }}</strong>
                                </div>
                            </td>

                            <td>
                                <span class="badge bg-info">
                                    {{ $category->products_count ?? 0 }}
                                </span>
                            </td>

                            <td class="text-center">
                                @if ($category->deleted_at)
                                    <span class="badge bg-secondary">Deleted</span>
                                @else
                                    <span class="badge bg-success">
                                        Active
                                    </span>
                                @endif
                            </td>

                            <td class="text-center">
                                @if ($category->deleted_at)
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-success"
                                            onclick="restoreCategory({{ $category->id }})" title="Restore">
                                            <i class="fas fa-undo"></i>
                                        </button>

                                        <button type="button" class="btn btn-sm btn-danger"
                                            onclick="forceDeleteCategory({{ $category->id }})"
                                            title="Delete Permanently">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                @else
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.categories.show', $category) }}"
                                            class="btn btn-sm btn-info" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        <a href="{{ route('admin.categories.edit', $category) }}"
                                            class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <button type="button" class="btn btn-sm btn-danger"
                                            onclick="deleteCategory({{ $category->id }})" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center m-3">
            <div class="d-flex align-items-center gap-3">
                <div class="d-flex align-items-center gap-2">
                    <label for="per_page_bottom" class="text-muted mb-0" style="white-space: nowrap;">
                        Per page:
                    </label>
                    <select name="per_page" id="per_page_bottom" class="form-select form-select-sm" style="width: 75px;"
                        onchange="changePerPage(this.value)">
                        <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </div>
            </div>

            <nav aria-label="Categories pagination">
                {{ $categories->appends(request()->query())->links('vendor.pagination.custom-bootstrap-5') }}
            </nav>
        </div>
    @else
        <div class="text-center py-5">
            <div class="mb-3">
                <i class="fas fa-list fa-4x text-muted"></i>
            </div>
            <h5 class="text-muted">No Categories Found</h5>
            <p class="text-muted mb-4">
                @if (request()->has('search'))
                    No categories match your search criteria. Try adjusting your search.
                @else
                    Start by creating your first category.
                @endif
            </p>
        </div>
    @endif
</div>

@push('styles')
    <style>
        /* Table Container */
        .table-responsive {
            position: relative;
        }

        /* Table Styles */
        #categoriesTable thead th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #dee2e6;
            position: sticky;
            top: 0;
            background-color: #f8f9fa;
            z-index: 10;
        }

        #categoriesTable tbody tr {
            transition: all 0.2s ease;
        }

        /* Deleted Row Highlight */
        .table-warning {
            background-color: rgba(255, 193, 7, 0.1) !important;
        }

        /* Button Group */
        .btn-group .btn {
            border-radius: 0;
        }

        .btn-group .btn:first-child {
            border-top-left-radius: 0.375rem;
            border-bottom-left-radius: 0.375rem;
        }

        .btn-group .btn:last-child {
            border-top-right-radius: 0.375rem;
            border-bottom-right-radius: 0.375rem;
        }

        /* Badge Styles */
        .badge {
            font-weight: 500;
            padding: 0.35em 0.65em;
        }

        /* Pagination */
        .pagination {
            margin-bottom: 0;
        }

        .page-link {
            color: #0d6efd;
            padding: 0.375rem 0.75rem;
            text-decoration: none;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            margin: 0 2px;
            transition: all 0.2s ease;
        }

        .page-link:hover {
            color: #fff;
            background-color: #0d6efd;
            border-color: #0d6efd;
        }

        .page-item.active .page-link {
            background-color: #0d6efd;
            border-color: #0d6efd;
            color: #fff;
            z-index: 1;
        }

        .page-item.disabled .page-link {
            color: #6c757d;
            pointer-events: none;
            background-color: #fff;
            border-color: #dee2e6;
            opacity: 0.5;
        }

        /* Per Page Selector */
        .form-select-sm {
            font-size: 0.875rem;
            padding: 0.25rem 0.5rem;
        }
    </style>
@endpush

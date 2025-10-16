<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <i class="fas fa-table me-2"></i>Products List
            </h5>
            <div class="text-muted">
                <small>
                    Showing {{ $products->firstItem() ?? 0 }} to {{ $products->lastItem() ?? 0 }}
                    of {{ $products->total() }} entries
                </small>
            </div>
        </div>
    </div>
    <div class="card-body">
        @if ($products->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="productsTable">
                    <thead class="table-light">
                        <tr>
                            <th width="3%">
                                <input type="checkbox" id="checkAll" class="form-check-input">
                            </th>
                            <th width="5%">ID</th>
                            <th width="8%">Image</th>
                            <th width="20%">Product Name</th>
                            <th width="12%">Category</th>
                            <th width="8%" class="text-center">Views</th>
                            <th width="8%" class="text-center">Status</th>
                            <th width="8%" class="text-center">Hot</th>
                            <th width="8%" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $product)
                            <tr id="product-{{ $product->id }}">
                                {{-- Checkbox --}}
                                <td>
                                    <input type="checkbox" class="form-check-input product-checkbox"
                                        value="{{ $product->id }}">
                                </td>

                                {{-- ID --}}
                                <td>
                                    <strong>#{{ $product->id }}</strong>
                                </td>

                                {{-- Image --}}
                                <td>
                                    @if ($product->image)
                                        <img src="#" alt="Image Product" class="product-image">
                                    @else
                                        <div
                                            class="product-image bg-light d-flex align-items-center justify-content-center">
                                            <i class="fas fa-image text-muted"></i>
                                        </div>
                                    @endif
                                </td>

                                {{-- Product Name --}}
                                <td>
                                    <div>
                                        <strong>{{ Str::limit($product->name, 40) }}</strong>
                                        @if ($product->parent_id)
                                            <br>
                                            <small class="text-muted">
                                                <i class="fas fa-link me-1"></i>Variant of #{{ $product->parent_id }}
                                            </small>
                                        @endif
                                    </div>
                                </td>

                                {{-- Category --}}
                                <td>
                                    @if ($product->category)
                                        <span class="badge bg-info">
                                            <i class="fas fa-folder me-1"></i>
                                            {{ $product->category->name }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">No Category</span>
                                    @endif
                                </td>

                                {{-- Views --}}
                                <td class="text-center">
                                    <span class="badge bg-primary">
                                        <i class="fas fa-eye me-1"></i>
                                        {{ number_format($product->views ?? 0) }}
                                    </span>
                                </td>

                                {{-- Status Toggle --}}
                                <td class="text-center">
                                    <div class="form-check form-switch d-flex justify-content-center">
                                        <input class="form-check-input" type="checkbox" role="switch"
                                            id="status-{{ $product->id }}" {{ $product->status ? 'checked' : '' }}
                                            onchange="toggleStatus({{ $product->id }})" style="cursor: pointer;">
                                    </div>
                                </td>

                                {{-- Hot Status Toggle --}}
                                <td class="text-center">
                                    <button type="button"
                                        class="btn btn-sm {{ $product->is_hot ? 'btn-warning' : 'btn-outline-warning' }}"
                                        onclick="toggleHot({{ $product->id }}, this)"
                                        title="{{ $product->is_hot ? 'Remove from hot' : 'Mark as hot' }}">
                                        <i class="fas fa-fire"></i>
                                    </button>
                                </td>

                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="#"
                                            class="btn btn-sm btn-info" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        <a href="{{ route('admin.products.edit', $product->id) }}"
                                            class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <button type="button" class="btn btn-sm btn-danger"
                                            onclick="deleteProduct({{ $product->id }})" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    <p class="text-muted mb-0">
                        Showing {{ $products->firstItem() }} to {{ $products->lastItem() }}
                        of {{ $products->total() }} results
                    </p>
                </div>
                <div>
                    {{ $products->appends(request()->query())->links() }}
                </div>
            </div>
        @else
            {{-- Empty State --}}
            <div class="text-center py-5">
                <div class="mb-3">
                    <i class="fas fa-box-open fa-4x text-muted"></i>
                </div>
                <h5 class="text-muted">No Products Found</h5>
                <p class="text-muted mb-4">
                    @if (request()->hasAny(['search', 'category_id', 'status', 'is_hot']))
                        No products match your search criteria. Try adjusting your filters.
                    @else
                        Start by creating your first product.
                    @endif
                </p>
                <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Create First Product
                </a>
            </div>
        @endif
    </div>
</div>

@push('styles')
    <style>
        /* Table Styles */
        #productsTable thead th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #dee2e6;
        }

        #productsTable tbody tr {
            transition: all 0.2s ease;
        }

        /* Product Image */
        .product-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid #e9ecef;
            transition: transform 0.2s ease;
        }

        /* Switch Toggle */
        .form-check-input:checked {
            background-color: #198754;
            border-color: #198754;
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
        }

        .page-link:hover {
            color: #0a58ca;
        }

        .page-item.active .page-link {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }

        /* Empty State */
        .fa-box-open {
            opacity: 0.3;
        }
    </style>
@endpush

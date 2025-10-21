<div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="bg-light">
            <tr>
                <th>Name</th>
                <th>Slug</th>
                <th>Products</th>
                <th>Status</th>
                <th>Created</th>
                <th style="width: 150px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($categories as $category)
                <tr>
                    <td>
                        <strong>{{ $category->name }}</strong>
                    </td>
                    <td>
                        <code>{{ $category->slug }}</code>
                    </td>
                    <td>
                        <span class="badge bg-info">
                            {{ $category->products_count ?? 0 }} products
                        </span>
                    </td>
                    <td>
                        @if($category->deleted_at)
                            <span class="badge bg-danger">
                                <i class="fas fa-times-circle me-1"></i> Inactive
                            </span>
                        @else
                            <span class="badge bg-success">
                                <i class="fas fa-check-circle me-1"></i> Active
                            </span>
                        @endif
                    </td>
                    <td>
                        <small class="text-muted">
                            {{ $category->created_at->format('d/m/Y H:i') }}
                        </small>
                    </td>
                    <td>
                        <div class="btn-group" role="group">
                            @if(!$category->deleted_at)
                                <a href="{{ route('admin.categories.edit', $category) }}"
                                   class="btn btn-sm btn-outline-primary"
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button"
                                        class="btn btn-sm btn-outline-danger delete-category"
                                        data-url="{{ route('admin.categories.destroy', $category) }}"
                                        data-name="{{ $category->name }}"
                                        title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            @else
                                <button type="button"
                                        class="btn btn-sm btn-outline-success restore-category"
                                        data-url="{{ route('admin.categories.restore', $category->id) }}"
                                        title="Restore">
                                    <i class="fas fa-undo"></i>
                                </button>
                                <button type="button"
                                        class="btn btn-sm btn-outline-danger force-delete-category"
                                        data-url="{{ route('admin.categories.force-delete', $category->id) }}"
                                        data-name="{{ $category->name }}"
                                        title="Permanently Delete">
                                    <i class="fas fa-times"></i>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center py-5">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <p class="text-muted mb-0">No categories found</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($categories->hasPages())
    <div class="card-footer bg-white border-top py-3">
        <div class="d-flex justify-content-between align-items-center">
            <div class="text-muted small">
                Showing {{ $categories->firstItem() ?? 0 }} to {{ $categories->lastItem() ?? 0 }}
                of {{ $categories->total() }} entries
            </div>
            <div>
                {{ $categories->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
@endif

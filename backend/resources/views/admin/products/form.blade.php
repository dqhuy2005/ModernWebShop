<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="fas fa-filter me-2"></i>Search & Filter
        </h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.products.index') }}" method="GET" id="filterForm">
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">
                        <i class="fas fa-search me-1"></i>Search
                    </label>
                    <input type="text" name="search" id="search" class="form-control"
                        placeholder="Search by product name or description..." value="{{ request('search') }}">
                </div>

                <div class="col-md-3">
                    <label for="category_id" class="form-label">
                        <i class="fas fa-folder me-1"></i>Category
                    </label>
                    <select name="category_id" id="category_id" class="form-select">
                        <option value="">-- All Categories --</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="status" class="form-label">
                        <i class="fas fa-toggle-on me-1"></i>Status
                    </label>
                    <select name="status" id="status" class="form-select">
                        <option value="">-- All Status --</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>
                            Active
                        </option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>
                            Inactive
                        </option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="is_hot" class="form-label">
                        <i class="fas fa-fire me-1"></i>Hot
                    </label>
                    <select name="is_hot" id="is_hot" class="form-select">
                        <option value="">-- All --</option>
                        <option value="1" {{ request('is_hot') === '1' ? 'selected' : '' }}>
                            Hot Only
                        </option>
                        <option value="0" {{ request('is_hot') === '0' ? 'selected' : '' }}>
                            Normal
                        </option>
                    </select>
                </div>
            </div>

            <div class="row g-3 mt-2">
                <div class="col-md-3">
                    <label for="sort_by" class="form-label">
                        <i class="fas fa-sort me-1"></i>Sort By
                    </label>
                    <select name="sort_by" id="sort_by" class="form-select">
                        <option value="created_at"
                            {{ request('sort_by', 'created_at') === 'created_at' ? 'selected' : '' }}>
                            Created Date
                        </option>
                        <option value="name" {{ request('sort_by') === 'name' ? 'selected' : '' }}>
                            Product Name
                        </option>
                        <option value="views" {{ request('sort_by') === 'views' ? 'selected' : '' }}>
                            Views
                        </option>
                        <option value="category_id" {{ request('sort_by') === 'category_id' ? 'selected' : '' }}>
                            Category
                        </option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="sort_order" class="form-label">
                        <i class="fas fa-sort-amount-down me-1"></i>Order
                    </label>
                    <select name="sort_order" id="sort_order" class="form-select">
                        <option value="desc" {{ request('sort_order', 'desc') === 'desc' ? 'selected' : '' }}>
                            Descending
                        </option>
                        <option value="asc" {{ request('sort_order') === 'asc' ? 'selected' : '' }}>
                            Ascending
                        </option>
                    </select>
                </div>

                <div class="col-md-7">
                    <label class="form-label d-block">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-2"></i>Search
                        </button>
                        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                            <i class="fas fa-redo me-2"></i>Reset
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
    <script>
        // Auto submit on filter change (optional)
        $('#category_id, #status, #is_hot, #language, #sort_by, #sort_order, #per_page').on('change', function() {
            // Uncomment to auto-submit on change
            $('#filterForm').submit();
        });
    </script>
@endpush

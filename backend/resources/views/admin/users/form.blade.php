<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="fas fa-search me-2"></i>Search Users
        </h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.users.index') }}" method="GET" id="searchForm">
            <div class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label for="search" class="form-label">
                        <i class="fas fa-search me-1"></i>Search
                    </label>
                    <input type="text"
                           name="search"
                           id="search"
                           class="form-control"
                           placeholder="Search by name, email, or phone..."
                           value="{{ request('search') }}">
                </div>

                <div class="col-md-2">
                    <label for="sort_by" class="form-label">
                        <i class="fas fa-sort me-1"></i>Sort By
                    </label>
                    <select name="sort_by" id="sort_by" class="form-select">
                        <option value="created_at" {{ request('sort_by', 'created_at') === 'created_at' ? 'selected' : '' }}>
                            Created Date
                        </option>
                        <option value="fullname" {{ request('sort_by') === 'fullname' ? 'selected' : '' }}>
                            Name
                        </option>
                        <option value="email" {{ request('sort_by') === 'email' ? 'selected' : '' }}>
                            Email
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

                <div class="col-md-2">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-1"></i>Search
                        </button>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                            <i class="fas fa-redo me-1"></i>Reset
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

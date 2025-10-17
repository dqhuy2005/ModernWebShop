<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">
            <i class="fas fa-search me-2"></i>Search Products
        </h5>
        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
            data-bs-target="#filterModal">
            <i class="fas fa-filter me-2"></i>Filters
            @if (request('category_id') || request('status') !== null || request('is_hot') !== null)
                <span class="badge bg-danger ms-1">
                    {{ collect([request('category_id'), request('status'), request('is_hot')])->filter()->count() }}
                </span>
            @endif
        </button>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.products.index') }}" method="GET" id="searchForm">
            <div class="row g-3 align-items-center">
                {{-- Search Input --}}
                <div class="col-md-12">
                    <div class="position-relative">
                        <i class="fas fa-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                        <input type="text" name="search" id="search" class="form-control ps-5 pe-5"
                            placeholder="Search by product name or description..."
                            value="{{ request('search') }}" style="height: 45px;">
                        @if (request('search'))
                            <button type="button"
                                class="btn btn-link position-absolute top-50 end-0 translate-middle-y text-muted"
                                onclick="clearSearch()" title="Clear search">
                                <i class="fas fa-times"></i>
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Hidden inputs for filters and sorting --}}
            <input type="hidden" name="category_id" id="hidden_category_id" value="{{ request('category_id') }}">
            <input type="hidden" name="status" id="hidden_status" value="{{ request('status') }}">
            <input type="hidden" name="is_hot" id="hidden_is_hot" value="{{ request('is_hot') }}">
            <input type="hidden" name="sort_by" id="hidden_sort_by" value="{{ request('sort_by', 'id') }}">
            <input type="hidden" name="sort_order" id="hidden_sort_order" value="{{ request('sort_order', 'desc') }}">
        </form>

        {{-- Active Filters Display --}}
        @if (request('search') || request('category_id') || request('status') !== null || request('is_hot') !== null)
            <div class="mt-3 d-flex flex-wrap gap-2 align-items-center">
                <small class="text-muted">Active filters:</small>

                @if (request('search'))
                    <span class="badge bg-primary">
                        Search: "{{ request('search') }}"
                        <a href="{{ route('admin.products.index', array_filter(request()->except('search'))) }}"
                            class="text-white ms-1">
                            <i class="fas fa-times"></i>
                        </a>
                    </span>
                @endif

                @if (request('category_id'))
                    @php
                        $category = $categories->firstWhere('id', request('category_id'));
                    @endphp
                    <span class="badge bg-info">
                        Category: {{ $category->name ?? 'Unknown' }}
                        <a href="{{ route('admin.products.index', array_filter(request()->except('category_id'))) }}"
                            class="text-white ms-1">
                            <i class="fas fa-times"></i>
                        </a>
                    </span>
                @endif

                @if (request('status') !== null && request('status') !== '')
                    <span class="badge bg-success">
                        Status: {{ request('status') == '1' ? 'Active' : 'Inactive' }}
                        <a href="{{ route('admin.products.index', array_filter(request()->except('status'))) }}"
                            class="text-white ms-1">
                            <i class="fas fa-times"></i>
                        </a>
                    </span>
                @endif

                @if (request('is_hot') !== null && request('is_hot') !== '')
                    <span class="badge bg-warning">
                        {{ request('is_hot') == '1' ? 'Hot Only' : 'Normal Only' }}
                        <a href="{{ route('admin.products.index', array_filter(request()->except('is_hot'))) }}"
                            class="text-white ms-1">
                            <i class="fas fa-times"></i>
                        </a>
                    </span>
                @endif

                <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-outline-danger">
                    <i class="fas fa-times me-1"></i>Clear All
                </a>
            </div>
        @endif
    </div>
</div>

{{-- Filter Modal --}}
<div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="filterModalLabel">
                    <i class="fas fa-filter me-2"></i>Filter Products
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="modal_category_id" class="form-label">
                        <i class="fas fa-folder me-1"></i>Category
                    </label>
                    <select id="modal_category_id" class="form-select">
                        <option value="">-- All Categories --</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="modal_status" class="form-label">
                        <i class="fas fa-toggle-on me-1"></i>Status
                    </label>
                    <select id="modal_status" class="form-select">
                        <option value="">-- All Status --</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="modal_is_hot" class="form-label">
                        <i class="fas fa-fire me-1"></i>Hot Products
                    </label>
                    <select id="modal_is_hot" class="form-select">
                        <option value="">-- All --</option>
                        <option value="1" {{ request('is_hot') === '1' ? 'selected' : '' }}>Hot Only</option>
                        <option value="0" {{ request('is_hot') === '0' ? 'selected' : '' }}>Normal Only</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="resetFilters()">
                    <i class="fas fa-redo me-2"></i>Reset
                </button>
                <button type="button" class="btn btn-primary" onclick="applyFilters()">
                    <i class="fas fa-check me-2"></i>Apply Filters
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        // Submit form on Enter key
        $('#search').on('keypress', function(e) {
            if (e.which === 13) { // Enter key
                e.preventDefault();
                $('#searchForm').submit();
            }
        });

        // Clear search
        function clearSearch() {
            $('#search').val('');
            $('#searchForm').submit();
        }

        // Apply filters from modal
        function applyFilters() {
            $('#hidden_category_id').val($('#modal_category_id').val());
            $('#hidden_status').val($('#modal_status').val());
            $('#hidden_is_hot').val($('#modal_is_hot').val());
            $('#searchForm').submit();
        }

        // Reset filters
        function resetFilters() {
            $('#modal_category_id').val('');
            $('#modal_status').val('');
            $('#modal_is_hot').val('');
            $('#hidden_category_id').val('');
            $('#hidden_status').val('');
            $('#hidden_is_hot').val('');
            $('#filterModal').modal('hide');
            $('#searchForm').submit();
        }

        // Sort table by column
        function sortTable(column) {
            const currentSortBy = $('#hidden_sort_by').val();
            const currentSortOrder = $('#hidden_sort_order').val();

            // Toggle sort order if clicking the same column
            if (currentSortBy === column) {
                $('#hidden_sort_order').val(currentSortOrder === 'asc' ? 'desc' : 'asc');
            } else {
                $('#hidden_sort_by').val(column);
                $('#hidden_sort_order').val('desc');
            }

            $('#searchForm').submit();
        }
    </script>
@endpush

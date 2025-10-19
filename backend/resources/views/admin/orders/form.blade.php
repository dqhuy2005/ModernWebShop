<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="fas fa-search me-2"></i>Search & Filter Orders
        </h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.orders.index') }}" method="GET" id="searchForm">
            <div class="row g-3 align-items-center">
                <div class="col-md-8">
                    <div class="position-relative">
                        <i class="fas fa-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                        <input type="text" name="search" id="search" class="form-control ps-5 pe-5"
                            placeholder="Search by order ID or customer name..." value="{{ request('search') }}"
                            style="height: 45px;">
                        @if (request('search'))
                            <button type="button"
                                class="btn btn-link position-absolute top-50 end-0 translate-middle-y text-muted"
                                onclick="clearSearch()" title="Clear search">
                                <i class="fas fa-times"></i>
                            </button>
                        @endif
                    </div>
                </div>

                <div class="col-md-4">
                    <select name="status" id="status_filter" class="form-select" style="height: 45px;"
                        onchange="$('#searchForm').submit()">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>
                            Pending
                        </option>
                        <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>
                            Confirmed
                        </option>
                        <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>
                            Processing
                        </option>
                        <option value="shipping" {{ request('status') === 'shipping' ? 'selected' : '' }}>
                            Shipping
                        </option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>
                            Completed
                        </option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>
                            Cancelled
                        </option>
                        <option value="deleted" {{ request('status') === 'deleted' ? 'selected' : '' }}>
                            Deleted Only
                        </option>
                    </select>
                </div>
            </div>

            <input type="hidden" name="sort_by" id="hidden_sort_by" value="{{ request('sort_by', 'id') }}">
            <input type="hidden" name="sort_order" id="hidden_sort_order" value="{{ request('sort_order', 'desc') }}">
        </form>

        @if (request('search') || request('status'))
            <div class="mt-3 d-flex flex-wrap gap-2 align-items-center">
                <small class="text-muted">Active filters:</small>

                @if (request('search'))
                    <span class="badge bg-primary">
                        Search: "{{ request('search') }}"
                        <a href="{{ route('admin.orders.index', array_filter(request()->except('search'))) }}"
                            class="text-white ms-1">
                            <i class="fas fa-times"></i>
                        </a>
                    </span>
                @endif

                @if (request('status'))
                    <span class="badge bg-info">
                        Status: {{ ucfirst(request('status')) }}
                        <a href="{{ route('admin.orders.index', array_filter(request()->except('status'))) }}"
                            class="text-white ms-1">
                            <i class="fas fa-times"></i>
                        </a>
                    </span>
                @endif

                <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-danger">
                    <i class="fas fa-times me-1"></i>Clear All
                </a>
            </div>
        @endif
    </div>
</div>

@push('scripts')
    <script>
        $('#search').on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                $('#searchForm').submit();
            }
        });

        function clearSearch() {
            $('#search').val('');
            $('#searchForm').submit();
        }

        function sortTable(column) {
            const currentSortBy = $('#hidden_sort_by').val();
            const currentSortOrder = $('#hidden_sort_order').val();

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

@push('styles')
    <style>
        .badge a {
            text-decoration: none;
        }

        .badge a:hover {
            opacity: 0.8;
        }

        #search:focus,
        #status_filter:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }
    </style>
@endpush

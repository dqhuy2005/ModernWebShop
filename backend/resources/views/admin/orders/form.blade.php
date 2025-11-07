<div>
    <form action="{{ route('admin.orders.index') }}" method="GET" id="searchForm" class="clean-form">
        <div class="row g-3 align-items-center">
            <div class="col-md-8">
                <div class="position-relative">
                    <i class="fas fa-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                    <input type="text" name="search" id="search" class="form-control ps-5 pe-5"
                        placeholder="Search by order ID, customer name, phone, email..." value="{{ request('search') }}"
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
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                    <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Processing</option>
                    <option value="shipping" {{ request('status') === 'shipping' ? 'selected' : '' }}>Shipping</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    <option value="deleted" {{ request('status') === 'deleted' ? 'selected' : '' }}>Deleted Only</option>
                </select>
            </div>
        </div>

        <div class="mt-3">
            <a href="#advancedFilters" data-bs-toggle="collapse" class="text-decoration-none">
                <i class="fas fa-sliders-h me-2"></i>Advanced Filters
                <i class="fas fa-chevron-down ms-1"></i>
            </a>
        </div>

        <div class="collapse" id="advancedFilters">
            <div class="row g-3">
                <div class="col-md-3">
                    <label for="date_from" class="form-label small text-muted">Date From</label>
                    <input type="date" name="date_from" id="date_from" class="form-control"
                        value="{{ request('date_from') }}">
                </div>
                <div class="col-md-3">
                    <label for="date_to" class="form-label small text-muted">Date To</label>
                    <input type="date" name="date_to" id="date_to" class="form-control"
                        value="{{ request('date_to') }}">
                </div>

                <div class="col-md-3">
                    <label for="price_min" class="form-label small text-muted">Min Price (₫)</label>
                    <input type="number" name="price_min" id="price_min" class="form-control" placeholder="0"
                        value="{{ request('price_min') }}" min="0" step="1000">
                </div>
                <div class="col-md-3">
                    <label for="price_max" class="form-label small text-muted">Max Price (₫)</label>
                    <input type="number" name="price_max" id="price_max" class="form-control" placeholder="999,999,999"
                        value="{{ request('price_max') }}" min="0" step="1000">
                </div>

                <div class="col-12">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-sm btn-outline-secondary"
                            onclick="setDateFilter('today')">Today</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary"
                            onclick="setDateFilter('yesterday')">Yesterday</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary"
                            onclick="setDateFilter('week')">This Week</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary"
                            onclick="setDateFilter('month')">This Month</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary"
                            onclick="setDateFilter('last30')">Last 30 Days</button>
                    </div>
                </div>
            </div>
        </div>

        <input type="hidden" name="sort_by" id="hidden_sort_by" value="{{ request('sort_by') }}">
        <input type="hidden" name="sort_order" id="hidden_sort_order" value="{{ request('sort_order') }}">
    </form>

    @if (request('search') ||
            request('status') ||
            request('date_from') ||
            request('date_to') ||
            request('price_min') ||
            request('price_max'))
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

            @if (request('date_from'))
                <span class="badge bg-secondary">
                    From: {{ \Carbon\Carbon::parse(request('date_from'))->format('d/m/Y') }}
                    <a href="{{ route('admin.orders.index', array_filter(request()->except('date_from'))) }}"
                        class="text-white ms-1">
                        <i class="fas fa-times"></i>
                    </a>
                </span>
            @endif

            @if (request('date_to'))
                <span class="badge bg-secondary">
                    To: {{ \Carbon\Carbon::parse(request('date_to'))->format('d/m/Y') }}
                    <a href="{{ route('admin.orders.index', array_filter(request()->except('date_to'))) }}"
                        class="text-white ms-1">
                        <i class="fas fa-times"></i>
                    </a>
                </span>
            @endif

            @if (request('price_min'))
                <span class="badge bg-success">
                    Min: {{ number_format(request('price_min')) }} ₫
                    <a href="{{ route('admin.orders.index', array_filter(request()->except('price_min'))) }}"
                        class="text-white ms-1">
                        <i class="fas fa-times"></i>
                    </a>
                </span>
            @endif

            @if (request('price_max'))
                <span class="badge bg-success">
                    Max: {{ number_format(request('price_max')) }} ₫
                    <a href="{{ route('admin.orders.index', array_filter(request()->except('price_max'))) }}"
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
    </form>
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

            $('#searchForm').submit();
        }

        function setDateFilter(period) {
            const today = new Date();
            let dateFrom, dateTo;

            switch (period) {
                case 'today':
                    dateFrom = dateTo = formatDate(today);
                    break;
                case 'yesterday':
                    const yesterday = new Date(today);
                    yesterday.setDate(yesterday.getDate() - 1);
                    dateFrom = dateTo = formatDate(yesterday);
                    break;
                case 'week':
                    const weekStart = new Date(today);
                    weekStart.setDate(today.getDate() - today.getDay());
                    dateFrom = formatDate(weekStart);
                    dateTo = formatDate(today);
                    break;
                case 'month':
                    dateFrom = formatDate(new Date(today.getFullYear(), today.getMonth(), 1));
                    dateTo = formatDate(today);
                    break;
                case 'last30':
                    const last30 = new Date(today);
                    last30.setDate(today.getDate() - 30);
                    dateFrom = formatDate(last30);
                    dateTo = formatDate(today);
                    break;
            }

            $('#date_from').val(dateFrom);
            $('#date_to').val(dateTo);

            // Expand advanced filters if collapsed
            if (!$('#advancedFilters').hasClass('show')) {
                $('#advancedFilters').collapse('show');
            }
        }

        function formatDate(date) {
            return date.toISOString().split('T')[0];
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

<div>
    <h5 class="mb-3">
        <i class="fas fa-search me-2"></i>Search Users
    </h5>

    <form action="{{ route('admin.users.index') }}" method="GET" id="searchForm" class="clean-form">
        <div class="row g-3 align-items-center">
            <div class="col-md-8">
                <div class="position-relative">
                    <i class="fas fa-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                    <input type="text" name="search" id="search" class="form-control ps-5 pe-5"
                        placeholder="Search by name, email, or phone..." value="{{ request('search') }}"
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
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>
                        <i class="fas fa-check-circle"></i> Active Only
                    </option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>
                        <i class="fas fa-times-circle"></i> Inactive Only
                    </option>
                    <option value="deleted" {{ request('status') === 'deleted' ? 'selected' : '' }}>
                        <i class="fas fa-trash"></i> Deleted Only
                    </option>
                </select>
            </div>
        </div>

        <input type="hidden" name="sort_by" id="hidden_sort_by" value="{{ request('sort_by') }}">
        <input type="hidden" name="sort_order" id="hidden_sort_order" value="{{ request('sort_order') }}">
    </form>

    @if (request('search') || request('status'))
        <div class="mt-3 d-flex flex-wrap gap-2 align-items-center">
            <small class="text-muted">Active filters:</small>

            @if (request('search'))
                <span class="badge bg-primary">
                    Search: "{{ request('search') }}"
                    <a href="{{ route('admin.users.index', array_filter(request()->except('search'))) }}"
                        class="text-white ms-1">
                        <i class="fas fa-times"></i>
                    </a>
                </span>
            @endif

            @if (request('status'))
                <span class="badge bg-info">
                    Status: {{ ucfirst(request('status')) }}
                    <a href="{{ route('admin.users.index', array_filter(request()->except('status'))) }}"
                        class="text-white ms-1">
                        <i class="fas fa-times"></i>
                    </a>
                </span>
            @endif

            <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-danger">
                <i class="fas fa-times me-1"></i>Clear All
            </a>
        </div>
    @endif
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

        // let searchTimeout;
        // $('#search').on('input', function() {
        //     clearTimeout(searchTimeout);
        //     searchTimeout = setTimeout(function() {
        //         $('#searchForm').submit();
        //     }, 800);
        // });

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

        /* Sortable table headers */
        .sortable {
            cursor: pointer;
            user-select: none;
            position: relative;
            padding-right: 20px !important;
        }

        .sortable:hover {
            background-color: #e9ecef;
        }

        .sortable::after {
            content: '\f0dc';
            /* fa-sort */
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            position: absolute;
            right: 8px;
            opacity: 0.3;
        }

        .sortable.asc::after {
            content: '\f0de';
            /* fa-sort-up */
            opacity: 1;
            color: #0d6efd;
        }

        .sortable.desc::after {
            content: '\f0dd';
            /* fa-sort-down */
            opacity: 1;
            color: #0d6efd;
        }
    </style>
@endpush

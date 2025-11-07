<div>
    <form action="{{ route('admin.categories.index') }}" method="GET" id="searchForm" class="clean-form">
        <div class="position-relative">
            <i class="fas fa-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
            <input type="text" name="search" id="search" class="form-control ps-5 pe-5"
                placeholder="Search by category name..." value="{{ request('search') }}" style="height: 45px;">
            @if (request('search'))
                <button type="button" class="btn btn-link position-absolute top-50 end-0 translate-middle-y text-muted"
                    onclick="clearSearch()" title="Clear search">
                    <i class="fas fa-times"></i>
                </button>
            @endif
        </div>
    </form>

    @if (request('search'))
        <div class="mt-3 d-flex flex-wrap gap-2 align-items-center">
            <small class="text-muted">Active filters:</small>

            @if (request('search'))
                <span class="badge bg-primary">
                    Search: "{{ request('search') }}"
                    <a href="{{ route('admin.categories.index', array_filter(request()->except('search'))) }}"
                        class="text-white ms-1">
                        <i class="fas fa-times"></i>
                    </a>
                </span>
            @endif

            <a href="{{ route('admin.categories.index') }}" class="btn btn-sm btn-outline-danger">
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

        #search:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }
    </style>
@endpush

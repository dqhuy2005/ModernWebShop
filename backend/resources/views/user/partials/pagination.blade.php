<div class="pw-pagination-info d-none">
    @if ($products->total() > 0)
        <span class="text-muted small">
            <strong>{{ $products->currentPage() }}</strong>
            / {{ $products->lastPage() }}
        </span>
        <div class="btn-group btn-group-sm" role="group">
            @if ($products->onFirstPage())
                <button type="button" class="btn btn-outline-secondary" disabled>
                    ‹
                </button>
            @else
                <button type="button" class="btn btn-outline-secondary pagination-quick-btn"
                    data-page="{{ $products->currentPage() - 1 }}">
                    ‹
                </button>
            @endif

            @if ($products->hasMorePages())
                <button type="button" class="btn btn-outline-secondary pagination-quick-btn"
                    data-page="{{ $products->currentPage() + 1 }}">
                    ›
                </button>
            @else
                <button type="button" class="btn btn-outline-secondary" disabled>
                    ›
                </button>
            @endif
        </div>
    @endif
</div>

@if ($products->hasPages())
    <nav aria-label="Product pagination" class="mt-4">
        <ul class="pagination justify-content-center">
            @if ($products->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link">‹</span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $products->previousPageUrl() }}" rel="prev">‹</a>
                </li>
            @endif

            @foreach ($products->getUrlRange(1, $products->lastPage()) as $page => $url)
                @if ($page == $products->currentPage())
                    <li class="page-item active">
                        <span class="page-link">{{ $page }}</span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                    </li>
                @endif
            @endforeach

            @if ($products->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $products->nextPageUrl() }}" rel="next">›</a>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link">›</span>
                </li>
            @endif
        </ul>
    </nav>
@endif

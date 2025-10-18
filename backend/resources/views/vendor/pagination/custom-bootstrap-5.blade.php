@if ($paginator->hasPages())
    <nav>
        <ul class="pagination">
            {{-- First Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link"><i class="fas fa-angle-double-left"></i></span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->url(1) }}" rel="first" aria-label="@lang('First')">
                        <i class="fas fa-angle-double-left"></i>
                    </a>
                </li>
            @endif

            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                    <span class="page-link"><i class="fas fa-angle-left"></i></span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')">
                        <i class="fas fa-angle-left"></i>
                    </a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @php
                $currentPage = $paginator->currentPage();
                $lastPage = $paginator->lastPage();
                $maxPages = 5; // Maximum number of page links to show
                
                // Calculate start and end page
                $start = max(1, $currentPage - floor($maxPages / 2));
                $end = min($lastPage, $start + $maxPages - 1);
                
                // Adjust start if end is at last page
                if ($end - $start < $maxPages - 1) {
                    $start = max(1, $end - $maxPages + 1);
                }
            @endphp

            {{-- Show dots if not starting from page 1 --}}
            @if ($start > 1)
                <li class="page-item disabled">
                    <span class="page-link">...</span>
                </li>
            @endif

            {{-- Page Number Links (Limited to 5) --}}
            @for ($page = $start; $page <= $end; $page++)
                @if ($page == $currentPage)
                    <li class="page-item active" aria-current="page">
                        <span class="page-link">{{ $page }}</span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->url($page) }}">{{ $page }}</a>
                    </li>
                @endif
            @endfor

            {{-- Show dots if not ending at last page --}}
            @if ($end < $lastPage)
                <li class="page-item disabled">
                    <span class="page-link">...</span>
                </li>
            @endif

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')">
                        <i class="fas fa-angle-right"></i>
                    </a>
                </li>
            @else
                <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                    <span class="page-link"><i class="fas fa-angle-right"></i></span>
                </li>
            @endif

            {{-- Last Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->url($paginator->lastPage()) }}" rel="last" aria-label="@lang('Last')">
                        <i class="fas fa-angle-double-right"></i>
                    </a>
                </li>
            @else
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link"><i class="fas fa-angle-double-right"></i></span>
                </li>
            @endif
        </ul>
    </nav>
@endif

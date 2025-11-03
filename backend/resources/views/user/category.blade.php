@extends('layouts.user.app')

@section('title', $category->name . ' - Danh mục')

@section('content')
    <div class="container py-4 product-listing-page">
        {{-- Breadcrumb --}}
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                <li class="breadcrumb-item active">{{ $category->name }}</li>
            </ol>
        </nav>

        <div class="row">
            {{-- Filter Sidebar --}}
            <div class="col-lg-3 mb-4">
                <div class="pw-filter-sidebar">
                    <div class="pw-filter-section">
                        <h5 class="pw-filter-title">
                            <i class="bi bi-funnel"></i> Bộ lọc tìm kiếm
                        </h5>

                        {{-- Price Filter --}}
                        <div class="pw-filter-group">
                            <h6 class="pw-filter-label">Giá</h6>
                            <div class="form-check">
                                <input class="form-check-input price-filter" type="radio" name="price_range"
                                       value="" id="price_all"
                                       {{ empty($filters['price_range']) ? 'checked' : '' }}>
                                <label class="form-check-label" for="price_all">Tất cả</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input price-filter" type="radio" name="price_range"
                                       value="under_10" id="price_under_10"
                                       {{ ($filters['price_range'] ?? '') === 'under_10' ? 'checked' : '' }}>
                                <label class="form-check-label" for="price_under_10">Dưới 10 triệu</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input price-filter" type="radio" name="price_range"
                                       value="10_20" id="price_10_20"
                                       {{ ($filters['price_range'] ?? '') === '10_20' ? 'checked' : '' }}>
                                <label class="form-check-label" for="price_10_20">10 - 20 triệu</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input price-filter" type="radio" name="price_range"
                                       value="20_30" id="price_20_30"
                                       {{ ($filters['price_range'] ?? '') === '20_30' ? 'checked' : '' }}>
                                <label class="form-check-label" for="price_20_30">20 - 30 triệu</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input price-filter" type="radio" name="price_range"
                                       value="over_30" id="price_over_30"
                                       {{ ($filters['price_range'] ?? '') === 'over_30' ? 'checked' : '' }}>
                                <label class="form-check-label" for="price_over_30">Trên 30 triệu</label>
                            </div>
                        </div>

                        {{-- Clear Filters --}}
                        <button type="button" class="btn btn-outline-danger btn-sm w-100 mt-3" id="clear-filters">
                            <i class="bi bi-x-circle"></i> Xóa bộ lọc
                        </button>
                    </div>
                </div>
            </div>

            {{-- Products Grid --}}
            <div class="col-lg-9">
                {{-- Toolbar with Quick Sort Tags --}}
                <div class="pw-toolbar mb-3">
                    <div class="d-flex flex-wrap gap-3 align-items-center">
                        {{-- Quick Sort Tags --}}
                        <div class="pw-quick-tags d-flex gap-2">
                            <button class="tag-btn {{ ($filters['sort'] ?? 'best_selling') === 'best_selling' ? 'active' : '' }}"
                                    data-sort="best_selling">
                                Bán chạy
                            </button>
                            <button class="tag-btn {{ ($filters['sort'] ?? '') === 'newest' ? 'active' : '' }}"
                                    data-sort="newest">
                                Mới nhất
                            </button>
                        </div>

                        {{-- Dropdown Sort --}}
                        <div class="pw-sort-dropdown ms-auto">
                            <select class="form-select form-select-sm" id="sort-select">
                                <option value="name_asc" {{ ($filters['sort'] ?? '') === 'name_asc' ? 'selected' : '' }}>
                                    Tên A → Z
                                </option>
                                <option value="name_desc" {{ ($filters['sort'] ?? '') === 'name_desc' ? 'selected' : '' }}>
                                    Tên Z → A
                                </option>
                                <option value="price_asc" {{ ($filters['sort'] ?? '') === 'price_asc' ? 'selected' : '' }}>
                                    Giá thấp → cao
                                </option>
                                <option value="price_desc" {{ ($filters['sort'] ?? '') === 'price_desc' ? 'selected' : '' }}>
                                    Giá cao → thấp
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Loading Overlay --}}
                <div class="pw-loading-overlay" id="loading-overlay" style="display: none;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>

                {{-- Product Grid --}}
                <div id="product-grid-container">
                    @include('user.partials.product-grid', ['products' => $products])
                </div>

                {{-- Pagination --}}
                <div id="pagination-container">
                    @include('user.partials.pagination', ['products' => $products])
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Black & Red Color Scheme */
        .product-listing-page {
            background: #0a0a0a;
            min-height: 100vh;
        }

        .breadcrumb {
            background: transparent;
            padding: 0;
        }

        .breadcrumb-item a {
            color: #f5f5f5;
            text-decoration: none;
        }

        .breadcrumb-item.active {
            color: #DC2626;
        }

        /* Filter Sidebar - Black with Red Accents */
        .pw-filter-sidebar {
            background: #1a1a1a;
            padding: 1.5rem;
            border-radius: 8px;
            border: 1px solid #333333;
            position: sticky;
            top: 20px;
        }

        .pw-filter-title {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 1.25rem;
            color: #ffffff;
            border-bottom: 2px solid #DC2626;
            padding-bottom: 0.75rem;
        }

        .pw-filter-title i {
            color: #DC2626;
        }

        .pw-filter-group {
            margin-bottom: 1.5rem;
        }

        .pw-filter-label {
            font-size: 0.95rem;
            font-weight: 600;
            margin-bottom: 0.75rem;
            color: #f5f5f5;
        }

        .form-check {
            margin-bottom: 0.5rem;
        }

        .form-check-input:checked {
            background-color: #DC2626;
            border-color: #DC2626;
        }

        .form-check-input:focus {
            border-color: #DC2626;
            box-shadow: 0 0 0 0.25rem rgba(220, 38, 38, 0.25);
        }

        .form-check-label {
            font-size: 0.9rem;
            color: #f5f5f5;
            cursor: pointer;
        }

        /* Toolbar - Black Background */
        .pw-toolbar {
            background: #1a1a1a;
            padding: 1rem 1.25rem;
            border-radius: 8px;
            border: 1px solid #333333;
        }

        /* Quick Sort Tags - Red Active State */
        .pw-quick-tags {
            display: flex;
            gap: 0.5rem;
        }

        .tag-btn {
            padding: 0.5rem 1.25rem;
            border: 2px solid #333333;
            background: #000000;
            color: #f5f5f5;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .tag-btn:hover {
            border-color: #DC2626;
            background: #1a1a1a;
        }

        .tag-btn.active {
            background: #DC2626;
            border-color: #DC2626;
            color: #ffffff;
        }

        /* Sort Dropdown */
        .pw-sort-dropdown select {
            background: #000000;
            color: #f5f5f5;
            border: 2px solid #333333;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-size: 0.9rem;
        }

        .pw-sort-dropdown select:focus {
            border-color: #DC2626;
            box-shadow: 0 0 0 0.25rem rgba(220, 38, 38, 0.25);
            background: #000000;
            color: #f5f5f5;
        }

        .pw-sort-dropdown select option {
            background: #000000;
            color: #f5f5f5;
        }

        /* Product Grid - Black Cards with Red Accents */
        #product-grid-container {
            position: relative;
            min-height: 400px;
        }

        .pw-product-grid {
            margin-top: 1rem;
        }

        .pw-product-card {
            border: 2px solid #333333;
            border-radius: 8px;
            padding: 1rem;
            background: #1a1a1a;
            height: 100%;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
        }

        .pw-product-card:hover {
            border-color: #DC2626;
            transform: translateY(-4px);
            box-shadow: 0 4px 16px rgba(220, 38, 38, 0.3);
        }

        .pw-product-link {
            color: #f5f5f5;
            text-decoration: none;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .pw-product-image {
            position: relative;
            height: 220px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            background: #000000;
            border-radius: 6px;
            margin-bottom: 0.75rem;
            border: 1px solid #333333;
        }

        .pw-product-img {
            object-fit: contain;
            width: 100%;
            height: 100%;
            transition: transform 0.3s ease;
        }

        .pw-product-card:hover .pw-product-img {
            transform: scale(1.05);
        }

        .pw-badge {
            position: absolute;
            top: 8px;
            right: 8px;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
        }

        .pw-badge-hot {
            background: #DC2626;
            color: #ffffff;
        }

        .pw-product-body {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .pw-product-name {
            font-size: 0.95rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #ffffff;
            line-height: 1.4;
            min-height: 2.8em;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .pw-product-specs {
            list-style: none;
            padding: 0;
            margin: 0 0 0.75rem 0;
            color: #999999;
            font-size: 0.8rem;
            flex: 1;
        }

        .pw-product-specs li {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-bottom: 0.25rem;
        }

        .pw-product-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: auto;
            padding-top: 0.5rem;
            border-top: 1px solid #333333;
        }

        .pw-product-price {
            font-size: 1.1rem;
            font-weight: 700;
            color: #DC2626;
        }

        .pw-product-views {
            font-size: 0.8rem;
            color: #666666;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .pw-product-actions {
            margin-top: 0.75rem;
        }

        .pw-product-actions .btn {
            font-size: 0.875rem;
            padding: 0.5rem 1rem;
            background: #DC2626;
            border-color: #DC2626;
            color: #ffffff;
        }

        .pw-product-actions .btn:hover {
            background: #B91C1C;
            border-color: #B91C1C;
        }

        /* Loading Overlay - Black with Red Spinner */
        .pw-loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(10, 10, 10, 0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
            border-radius: 8px;
        }

        .spinner-border {
            color: #DC2626 !important;
        }

        /* Empty State - Red Text */
        .pw-empty-state {
            padding: 3rem 1rem;
            color: #f5f5f5;
        }

        .pw-empty-state i {
            font-size: 4rem;
            color: #666666;
        }

        /* Pagination - Black & Red */
        .pagination {
            margin-top: 2rem;
        }

        .page-link {
            color: #f5f5f5;
            background: #1a1a1a;
            border-color: #333333;
        }

        .page-link:hover {
            background: #000000;
            border-color: #DC2626;
            color: #DC2626;
        }

        .page-item.active .page-link {
            background-color: #DC2626;
            border-color: #DC2626;
            color: #ffffff;
        }

        .page-item.disabled .page-link {
            background: #1a1a1a;
            border-color: #333333;
            color: #666666;
        }

        .pagination .text-muted {
            color: #999999 !important;
        }

        /* Responsive */
        @media(max-width: 991px) {
            .pw-filter-sidebar {
                position: static;
                margin-bottom: 1.5rem;
            }
        }

        @media(max-width: 767px) {
            .pw-product-image {
                height: 160px;
            }

            .pw-toolbar {
                flex-direction: column;
                gap: 1rem;
            }

            .pw-sort-dropdown select {
                width: 100% !important;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        (function($) {
            let debounceTimer;
            const categorySlug = '{{ $category->slug }}';
            const baseUrl = '{{ route("categories.show", $category->slug) }}';

            // Get current URL parameters
            function getUrlParams() {
                const params = new URLSearchParams(window.location.search);
                return {
                    price_range: params.get('price_range') || '',
                    sort: params.get('sort') || 'best_selling',
                    page: params.get('page') || '1'
                };
            }

            // Update URL without reload
            function updateUrl(params) {
                const url = new URL(window.location);
                Object.keys(params).forEach(key => {
                    if (params[key]) {
                        url.searchParams.set(key, params[key]);
                    } else {
                        url.searchParams.delete(key);
                    }
                });
                window.history.pushState({}, '', url);
            }

            // Apply filters with AJAX
            function applyFilters(sortValue = null) {
                const params = {
                    price_range: $('input[name="price_range"]:checked').val() || '',
                    sort: sortValue || $('.tag-btn.active').data('sort') || $('#sort-select').val() || 'best_selling',
                    page: 1 // Reset to first page on filter change
                };

                // Show loading
                $('#loading-overlay').fadeIn(200);

                // Update URL
                updateUrl(params);

                // AJAX request
                $.ajax({
                    url: baseUrl,
                    method: 'GET',
                    data: params,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#product-grid-container').html(response.html);
                            $('#pagination-container').html(response.pagination);

                            // Scroll to top of results
                            $('html, body').animate({
                                scrollTop: $('#product-grid-container').offset().top - 100
                            }, 400);
                        }
                    },
                    error: function(xhr) {
                        console.error('Filter error:', xhr);
                        alert('Có lỗi xảy ra khi lọc sản phẩm. Vui lòng thử lại.');
                    },
                    complete: function() {
                        $('#loading-overlay').fadeOut(200);
                    }
                });
            }

            // Tag button click - Quick Sort
            $('.tag-btn').on('click', function() {
                const sortValue = $(this).data('sort');

                // Update active state
                $('.tag-btn').removeClass('active');
                $(this).addClass('active');

                // Clear dropdown selection
                $('#sort-select').val('');

                // Apply filter with tag sort
                applyFilters(sortValue);
            });

            // Sort dropdown change
            $('#sort-select').on('change', function() {
                const sortValue = $(this).val();

                // Clear tag active state
                $('.tag-btn').removeClass('active');

                // Apply filter
                applyFilters(sortValue);
            });

            // Price filter change
            $('.price-filter').on('change', function() {
                applyFilters();
            });

            // Clear filters
            $('#clear-filters').on('click', function() {
                $('input[name="price_range"][value=""]').prop('checked', true);
                $('.tag-btn').removeClass('active');
                $('.tag-btn[data-sort="best_selling"]').addClass('active');
                $('#sort-select').val('');
                applyFilters('best_selling');
            });

            // Handle pagination clicks
            $(document).on('click', '.pagination a', function(e) {
                e.preventDefault();
                const url = new URL($(this).attr('href'));
                const page = url.searchParams.get('page');

                const params = getUrlParams();
                params.page = page;

                // Show loading
                $('#loading-overlay').fadeIn(200);

                // Update URL
                updateUrl(params);

                $.ajax({
                    url: baseUrl,
                    method: 'GET',
                    data: params,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#product-grid-container').html(response.html);
                            $('#pagination-container').html(response.pagination);

                            $('html, body').animate({
                                scrollTop: $('#product-grid-container').offset().top - 100
                            }, 400);
                        }
                    },
                    complete: function() {
                        $('#loading-overlay').fadeOut(200);
                    }
                });
            });
        })(jQuery);
    </script>
@endpush

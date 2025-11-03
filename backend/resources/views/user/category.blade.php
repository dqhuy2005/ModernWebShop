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
        .product-listing-page {
            background: #f8f9fa;
            min-height: 100vh;
        }

        .breadcrumb {
            background: transparent;
            padding: 0;
        }

        .breadcrumb-item a {
            text-decoration: none;
        }

        /* Filter Sidebar */
        .pw-filter-sidebar {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            position: sticky;
            top: 20px;
        }

        .pw-filter-title {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 1.25rem;
            color: #1f2937;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 0.75rem;
        }

        .pw-filter-group {
            margin-bottom: 1.5rem;
        }

        .pw-filter-label {
            font-size: 0.95rem;
            font-weight: 600;
            margin-bottom: 0.75rem;
            color: #374151;
        }

        .form-check {
            margin-bottom: 0.5rem;
        }

        .form-check-label {
            font-size: 0.9rem;
            color: #4b5563;
            cursor: pointer;
        }

        /* Toolbar */
        .pw-toolbar {
            background: white;
            padding: 1rem 1.25rem;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        /* Quick Sort Tags */
        .pw-quick-tags {
            display: flex;
            gap: 0.5rem;
        }

        .tag-btn {
            padding: 0.5rem 1.25rem;
            border: 2px solid #e5e7eb;
            background: white;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s ease;
            color: #374151;
        }

        .tag-btn:hover {
            border-color: #4f46e5;
            background: #f9fafb;
        }

        .tag-btn.active {
            background: #4f46e5;
            border-color: #4f46e5;
            color: #ffffff;
        }

        /* Sort Dropdown */
        .pw-sort-dropdown select {
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-size: 0.9rem;
        }

        /* Product Grid */
        #product-grid-container {
            position: relative;
            min-height: 400px;
        }

        .pw-product-grid {
            margin-top: 1rem;
        }

        .pw-product-card {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 1rem;
            background: white;
            height: 100%;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }

        .pw-product-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            transform: translateY(-4px);
        }

        .pw-product-link {
            color: inherit;
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
            background: #f9fafb;
            border-radius: 6px;
            margin-bottom: 0.75rem;
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
            background: #dc2626;
            color: white;
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
            color: #1f2937;
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
            color: #6b7280;
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
            border-top: 1px solid #f3f4f6;
        }

        .pw-product-price {
            font-size: 1.1rem;
            font-weight: 700;
            color: #dc2626;
        }

        .pw-product-views {
            font-size: 0.8rem;
            color: #9ca3af;
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
        }

        /* Loading Overlay */
        .pw-loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.85);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
            border-radius: 8px;
        }

        /* Empty State */
        .pw-empty-state {
            padding: 3rem 1rem;
        }

        .pw-empty-state i {
            font-size: 4rem;
        }

        /* Pagination */
        .pagination {
            margin-top: 2rem;
        }

        .page-link {
            color: #4f46e5;
            border-color: #e5e7eb;
        }

        .page-item.active .page-link {
            background-color: #4f46e5;
            border-color: #4f46e5;
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

            function getUrlParams() {
                const params = new URLSearchParams(window.location.search);
                return {
                    price_range: params.get('price_range') || '',
                    sort: params.get('sort') || 'best_selling',
                    page: params.get('page') || '1'
                };
            }

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

            function applyFilters(sortValue = null) {
                const params = {
                    price_range: $('input[name="price_range"]:checked').val() || '',
                    sort: sortValue || $('.tag-btn.active').data('sort') || $('#sort-select').val() || 'best_selling',
                    page: 1
                };

                $('#loading-overlay').fadeIn(200);

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
                    error: function(xhr) {
                        console.error('Filter error:', xhr);
                        alert('Có lỗi xảy ra khi lọc sản phẩm. Vui lòng thử lại.');
                    },
                    complete: function() {
                        $('#loading-overlay').fadeOut(200);
                    }
                });
            }

            $('.tag-btn').on('click', function() {
                const sortValue = $(this).data('sort');

                $('.tag-btn').removeClass('active');
                $(this).addClass('active');

                $('#sort-select').val('');

                applyFilters(sortValue);
            });

            $('#sort-select').on('change', function() {
                const sortValue = $(this).val();

                $('.tag-btn').removeClass('active');

                applyFilters(sortValue);
            });

            $('.price-filter').on('change', function() {
                applyFilters();
            });

            $('#clear-filters').on('click', function() {
                $('input[name="price_range"][value=""]').prop('checked', true);
                $('.tag-btn').removeClass('active');
                $('.tag-btn[data-sort="best_selling"]').addClass('active');
                $('#sort-select').val('');
                applyFilters('best_selling');
            });

            $(document).on('click', '.pagination a', function(e) {
                e.preventDefault();
                const url = new URL($(this).attr('href'));
                const page = url.searchParams.get('page');

                const params = getUrlParams();
                params.page = page;

                $('#loading-overlay').fadeIn(200);

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

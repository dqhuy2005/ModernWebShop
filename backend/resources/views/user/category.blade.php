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

                        {{-- Search Filter --}}
                        <div class="pw-filter-group mt-3">
                            <h6 class="pw-filter-label">Tìm kiếm</h6>
                            <input type="text"
                                   class="form-control form-control-sm"
                                   id="search-input"
                                   placeholder="Tìm sản phẩm..."
                                   value="{{ $filters['search'] ?? '' }}">
                        </div>

                        <button type="button" class="btn btn-outline-secondary btn-sm w-100 mt-3" id="clear-filters">
                            <i class="bi bi-x-circle"></i> Bỏ chọn
                        </button>
                    </div>
                </div>
            </div>

            {{-- Products Grid --}}
            <div class="col-lg-9">
                {{-- Toolbar --}}
                <div class="pw-toolbar d-flex justify-content-between align-items-center mb-3">
                    <div class="pw-results-count text-muted">
                        <strong>{{ $products->total() }}</strong> sản phẩm
                    </div>

                    <div class="pw-sort-dropdown">
                        <select class="form-select form-select-sm" id="sort-select" style="width: auto;">
                            <option value="">Sắp xếp: Mặc định</option>
                            <option value="name_asc" {{ ($filters['sort'] ?? '') === 'name_asc' ? 'selected' : '' }}>
                                Tên A → Z
                            </option>
                            <option value="name_desc" {{ ($filters['sort'] ?? '') === 'name_desc' ? 'selected' : '' }}>
                                Tên Z → A
                            </option>
                            <option value="price_asc" {{ ($filters['sort'] ?? '') === 'price_asc' ? 'selected' : '' }}>
                                Giá: Thấp → Cao
                            </option>
                            <option value="price_desc" {{ ($filters['sort'] ?? '') === 'price_desc' ? 'selected' : '' }}>
                                Giá: Cao → Thấp
                            </option>
                            <option value="newest" {{ ($filters['sort'] ?? '') === 'newest' ? 'selected' : '' }}>
                                Mới nhất
                            </option>
                            <option value="popular" {{ ($filters['sort'] ?? '') === 'popular' ? 'selected' : '' }}>
                                Phổ biến nhất
                            </option>
                        </select>
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

        .pw-category-header {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .pw-category-title {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: .5rem;
            color: #1f2937;
        }

        .pw-category-desc {
            color: #6b7280;
            margin-bottom: 0;
            font-size: 0.95rem;
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

        .pw-results-count {
            font-size: 0.95rem;
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

            // Get current URL parameters
            function getUrlParams() {
                const params = new URLSearchParams(window.location.search);
                return {
                    price_range: params.get('price_range') || '',
                    sort: params.get('sort') || '',
                    search: params.get('search') || '',
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
            function applyFilters() {
                const params = {
                    price_range: $('input[name="price_range"]:checked').val() || '',
                    sort: $('#sort-select').val() || '',
                    search: $('#search-input').val() || '',
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

            // Price filter change
            $('.price-filter').on('change', function() {
                applyFilters();
            });

            // Sort change
            $('#sort-select').on('change', function() {
                applyFilters();
            });

            // Search input with debounce
            $('#search-input').on('input', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(function() {
                    applyFilters();
                }, 500);
            });

            // Clear filters
            $('#clear-filters').on('click', function() {
                $('input[name="price_range"][value=""]').prop('checked', true);
                $('#sort-select').val('');
                $('#search-input').val('');
                applyFilters();
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

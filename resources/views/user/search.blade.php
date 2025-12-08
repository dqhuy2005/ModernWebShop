@extends('layouts.user.app')

@section('title', 'Kết quả tìm kiếm: ' . $keyword)

@section('content')
    <div class="container py-4 product-listing-page">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                <li class="breadcrumb-item active">Kết quả tìm kiếm: "{{ $keyword }}"</li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-lg-3 mb-4">
                <div class="pw-filter-sidebar">
                    <div class="pw-filter-section">
                        <h5 class="pw-filter-title">
                            <i class="bi bi-funnel"></i> Bộ lọc tìm kiếm
                        </h5>

                        <div class="pw-filter-group">
                            <h6 class="pw-filter-label">Giá</h6>
                            <div class="form-check">
                                <input class="form-check-input price-filter" type="radio" name="price_range"
                                    value="" id="price_all" {{ empty($priceRange) ? 'checked' : '' }}>
                                <label class="form-check-label" for="price_all">Tất cả</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input price-filter" type="radio" name="price_range"
                                    value="under_10" id="price_under_10" {{ $priceRange === 'under_10' ? 'checked' : '' }}>
                                <label class="form-check-label" for="price_under_10">Dưới 10 triệu</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input price-filter" type="radio" name="price_range"
                                    value="10_20" id="price_10_20" {{ $priceRange === '10_20' ? 'checked' : '' }}>
                                <label class="form-check-label" for="price_10_20">10 - 20 triệu</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input price-filter" type="radio" name="price_range"
                                    value="20_30" id="price_20_30" {{ $priceRange === '20_30' ? 'checked' : '' }}>
                                <label class="form-check-label" for="price_20_30">20 - 30 triệu</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input price-filter" type="radio" name="price_range"
                                    value="over_30" id="price_over_30" {{ $priceRange === 'over_30' ? 'checked' : '' }}>
                                <label class="form-check-label" for="price_over_30">Trên 30 triệu</label>
                            </div>
                        </div>

                        <button type="button" class="btn btn-outline-danger btn-sm w-100 mt-3" id="clear-filters">
                            <i class="bi bi-x-circle"></i> Xóa bộ lọc
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-lg-9">
                <div class="pw-toolbar mb-3">
                    <div class="d-flex flex-wrap gap-3 align-items-center justify-content-between">
                        <div class="d-flex flex-wrap gap-3 align-items-center flex-grow-1">
                            <div class="pw-quick-tags d-flex gap-2">
                                <button class="tag-btn {{ $sort === 'best_selling' ? 'active' : '' }}"
                                    data-sort="best_selling">
                                    Bán chạy
                                </button>
                                <button class="tag-btn {{ $sort === 'newest' ? 'active' : '' }}" data-sort="newest">
                                    Mới nhất
                                </button>
                            </div>

                            <div class="pw-sort-dropdown">
                                <select class="form-select form-select-sm" id="sort-select">
                                    <option value="name_asc" {{ $sort === 'name_asc' ? 'selected' : '' }}>
                                        Tên A → Z
                                    </option>
                                    <option value="name_desc" {{ $sort === 'name_desc' ? 'selected' : '' }}>
                                        Tên Z → A
                                    </option>
                                    <option value="price_asc" {{ $sort === 'price_asc' ? 'selected' : '' }}>
                                        Giá thấp → cao
                                    </option>
                                    <option value="price_desc" {{ $sort === 'price_desc' ? 'selected' : '' }}>
                                        Giá cao → thấp
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                @if ($products->isEmpty())
                    <div class="alert alert-info text-center py-5">
                        <i class="bi bi-search fs-1 d-block mb-3"></i>
                        <h5>Không tìm thấy sản phẩm nào</h5>
                        <p class="text-muted mb-0">Vui lòng thử lại với từ khóa khác</p>
                    </div>
                @else
                    <div id="products-container">
                        @include('user.partials.product-grid', ['products' => $products])
                    </div>

                    <div id="pagination-container" class="mt-4">
                        @include('user.partials.pagination', ['products' => $products])
                    </div>
                @endif
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

        .pw-filter-sidebar {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
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

        .pw-toolbar {
            background: white;
            padding: 1rem 1.25rem;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .pw-quick-tags {
            display: flex;
            gap: 0.5rem;
        }

        .tag-btn {
            padding: 0.5rem 1.25rem;
            border: 2px solid #e5e7eb;
            background: white;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s ease;
            color: #374151;
        }

        .tag-btn.active {
            background: #4f46e5;
            border-color: #4f46e5;
            color: #ffffff;
        }

        #sort-select {
            width: 160px;
        }

        .pw-sort-dropdown select {
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
        }

        .pw-pagination-info {
            padding-left: 1rem;
        }

        .pw-pagination-info .text-muted {
            font-size: 0.85rem;
            white-space: nowrap;
        }

        .pw-pagination-info .btn-group .btn {
            padding: 0.35rem 0.65rem;
            border-color: #e5e7eb;
            color: #4b5563;
        }

        .pw-pagination-info .btn-group .btn:hover:not(:disabled) {
            cursor: pointer;
        }

        .pw-pagination-info .btn-group .btn:disabled {
            opacity: 0.4;
        }

        #products-container {
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
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
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
            background: #ECECEC;
            color: #6D6E72;
            font-size: 0.8rem;
            flex: 1;
        }

        .pw-product-specs li {
            padding: 0 10px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
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

        .pw-loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(255, 255, 255, 0.85);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        .pw-empty-state {
            padding: 3rem 1rem;
        }

        .pw-empty-state i {
            font-size: 4rem;
        }

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
                padding: 0.75rem 1rem;
            }

            .pw-toolbar>div {
                width: 100%;
            }

            .pw-quick-tags {
                width: 100%;
            }

            .tag-btn {
                flex: 1;
                padding: 0.5rem 0.75rem;
                font-size: 0.85rem;
            }

            .pw-sort-dropdown {
                width: 100%;
            }

            .pw-sort-dropdown select {
                width: 100% !important;
            }

            .pw-pagination-info {
                border-left: none;
                border-top: 2px solid #e5e7eb;
                padding-left: 0;
                padding-top: 0.75rem;
                margin-top: 0.75rem;
                width: 100%;
                justify-content: center !important;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            const baseUrl = "{{ route('products.search') }}";
            const keyword = "{{ $keyword }}";

            function updateProducts() {
                const priceRange = $('input[name="price_range"]:checked').val();
                const sort = $('#sort-select').val() || $('.tag-btn.active').data('sort');

                const params = new URLSearchParams({
                    q: keyword,
                    price_range: priceRange || '',
                    sort: sort || 'best_selling'
                });

                $.ajax({
                    url: baseUrl + '?' + params.toString(),
                    type: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#products-container').html(response.html);
                            $('#pagination-container').html(response.pagination);
                            window.history.pushState({}, '', baseUrl + '?' + params.toString());
                        }
                    },
                    error: function() {
                        toastr.error('Có lỗi xảy ra. Vui lòng thử lại!');
                    }
                });
            }

            $('.price-filter').on('change', function() {
                updateProducts();
            });

            $('.tag-btn').on('click', function() {
                $('.tag-btn').removeClass('active');
                $(this).addClass('active');
                $('#sort-select').val('');
                updateProducts();
            });

            $('#sort-select').on('change', function() {
                $('.tag-btn').removeClass('active');
                updateProducts();
            });

            $('#clear-filters').on('click', function() {
                $('input[name="price_range"]').prop('checked', false);
                $('#price_all').prop('checked', true);
                $('.tag-btn').removeClass('active');
                $('.tag-btn[data-sort="best_selling"]').addClass('active');
                $('#sort-select').val('best_selling');
                updateProducts();
            });

            $(document).on('click', '#pagination-container .pagination a', function(e) {
                e.preventDefault();
                const url = $(this).attr('href');
                if (url) {
                    $.ajax({
                        url: url,
                        type: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        success: function(response) {
                            if (response.success) {
                                $('#products-container').html(response.html);
                                $('#pagination-container').html(response.pagination);
                                window.history.pushState({}, '', url);
                                $('html, body').animate({
                                    scrollTop: $('#products-container').offset().top -
                                        100
                                }, 300);
                            }
                        }
                    });
                }
            });
        });
    </script>
@endpush

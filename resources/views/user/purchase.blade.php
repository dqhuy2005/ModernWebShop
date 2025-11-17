@extends('layouts.user.app')

@section('title', 'Đơn hàng của tôi - ModernWebShop')

@section('content')
    <div class="purchase-section py-5" style="background-color: #F8F9FA;">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 mb-4">
                    <div class="card shadow-sm border-0" style="border-radius: 12px;">
                        <div class="card-body p-4 text-center">
                            <div class="profile-avatar mb-3">
                                @if (Auth::user()->isOAuthUser())
                                    <img src="{{ Auth::user()->image }}" alt="Avatar" class="rounded-circle"
                                        style="width: 120px; height: 120px; object-fit: cover;">
                                @elseif (Auth::user()->image)
                                    <img src="{{ Auth::user()->image_url }}" alt="Avatar" class="rounded-circle"
                                        style="width: 120px; height: 120px; object-fit: cover;">
                                @else
                                    <div class="avatar-placeholder rounded-circle d-flex align-items-center justify-content-center"
                                        id="avatarPreview"
                                        style="width: 120px; height: 120px; background-color: #f0f0f0; color: #6c757d; font-size: 48px;">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <img src="" alt="Avatar" class="rounded-circle d-none" id="avatarImage"
                                        style="width: 120px; height: 120px; object-fit: cover;">
                                @endif
                            </div>
                            <h5 class="fw-bold mb-1" style="color: #202732;">{{ Auth::user()->fullname }}</h5>
                            <p class="text-muted small mb-3">{{ Auth::user()->email }}</p>
                            <div class="d-grid gap-2">
                                <a href="{{ route('profile.index') }}" class="btn btn-sm btn-outline-secondary"
                                    style="border: none">
                                    <i class="fas fa-user me-2"></i>Thông tin cá nhân
                                </a>
                                <a href="{{ route('purchase.index') }}" class="btn btn-sm btn-danger active">
                                    <i class="fas fa-shopping-bag me-2"></i>Đơn hàng của tôi
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-9">
                    <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
                        <div class="card-body p-4">
                            <ul class="nav nav-tabs mb-4" id="orderTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link order-tab {{ !request('status') ? 'active' : '' }}"
                                        href="{{ route('purchase.index') }}" data-status="" role="tab">
                                        Tất cả
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link order-tab {{ request('status') === 'pending' ? 'active' : '' }}"
                                        href="{{ route('purchase.index', ['status' => 'pending']) }}" data-status="pending"
                                        role="tab">
                                        Chờ xử lý
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link order-tab {{ request('status') === 'confirmed' ? 'active' : '' }}"
                                        href="{{ route('purchase.index', ['status' => 'confirmed']) }}"
                                        data-status="confirmed" role="tab">
                                        Đã xác nhận
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link order-tab {{ request('status') === 'processing' ? 'active' : '' }}"
                                        href="{{ route('purchase.index', ['status' => 'processing']) }}"
                                        data-status="processing" role="tab">
                                        Đang xử lý
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link order-tab {{ request('status') === 'shipping' ? 'active' : '' }}"
                                        href="{{ route('purchase.index', ['status' => 'shipping']) }}"
                                        data-status="shipping" role="tab">
                                        Đang giao
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link order-tab {{ request('status') === 'completed' ? 'active' : '' }}"
                                        href="{{ route('purchase.index', ['status' => 'completed']) }}"
                                        data-status="completed" role="tab">
                                        Hoàn thành
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link order-tab {{ request('status') === 'cancelled' ? 'active' : '' }}"
                                        href="{{ route('purchase.index', ['status' => 'cancelled']) }}"
                                        data-status="cancelled" role="tab">
                                        Đã hủy
                                    </a>
                                </li>
                            </ul>

                            @if (!request('status'))
                                <form method="GET" action="{{ route('purchase.index') }}" id="purchaseSearchForm"
                                    class="mb-4">
                                    <div class="row g-3">
                                        <div class="col-md-12">
                                            <div class="input-group">
                                                <span class="input-group-text" style="background-color: #eaeaea">
                                                    <i class="fas fa-search text-muted"></i>
                                                </span>
                                                <input type="text" class="form-control border-start-0"
                                                    style="background-color: #eaeaea" name="search"
                                                    id="purchaseSearchInput"
                                                    placeholder="Tìm kiếm theo mã đơn hàng hoặc tên sản phẩm"
                                                    value="{{ $search ?? '' }}">
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            @endif

                            <div id="ordersContainer">
                                <!-- Loading overlay -->
                                <div class="loading-overlay" id="loadingOverlay">
                                    <div class="spinner-container">
                                        <div class="spinner-border-custom"></div>
                                        <div class="loading-text">Đang tải đơn hàng...</div>
                                    </div>
                                </div>

                                @include('user.partials.orders-list', [
                                    'orders' => $orders,
                                    'status' => $status ?? null,
                                ])
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .order-card {
            transition: all 0.3s ease;
            background-color: white;
        }

        .input-group-text {
            border-right: none;
        }

        .input-group .form-control {
            border-left: none;
        }

        .input-group .form-control:focus {
            border-color: #dee2e6;
            box-shadow: none;
        }

        .btn-danger.active {
            background-color: #dc3545;
            border-color: #dc3545;
        }

        /* Tabs Styling */
        .nav-tabs {
            border-bottom: 2px solid #dee2e6;
        }

        .nav-tabs .nav-link {
            border: none;
            color: #6c757d;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
            border-bottom: 3px solid transparent;
            margin-bottom: -2px;
            cursor: pointer;
        }

        .nav-tabs .nav-link:hover {
            cursor: pointer;
        }

        .nav-tabs .nav-link.active {
            color: #dc3545;
            background-color: transparent;
            border-color: transparent transparent #dc3545 transparent;
            font-weight: 600;
        }

        /* Loading state */
        #ordersContainer {
            transition: opacity 0.2s ease;
            min-height: 200px;
            position: relative;
        }

        #ordersContainer.loading {
            opacity: 0.3;
            pointer-events: none;
        }

        /* Loading spinner overlay */
        .loading-overlay {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1000;
            display: none;
        }

        .loading-overlay.active {
            display: block;
        }

        .spinner-container {
            text-align: center;
        }

        .spinner-border-custom {
            width: 3rem;
            height: 3rem;
            border: 0.3em solid #f3f3f3;
            border-top: 0.3em solid #dc3545;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .loading-text {
            margin-top: 1rem;
            color: #6c757d;
            font-size: 0.9rem;
            font-weight: 500;
        }

        /* Tab loading indicator */
        .nav-tabs .nav-link.loading::after {
            content: '';
            display: inline-block;
            width: 12px;
            height: 12px;
            margin-left: 8px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid #dc3545;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            vertical-align: middle;
        }

        /* Skeleton loader for orders */
        .skeleton-loader {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s ease-in-out infinite;
            border-radius: 8px;
        }

        @keyframes loading {
            0% {
                background-position: 200% 0;
            }

            100% {
                background-position: -200% 0;
            }
        }

        .skeleton-order {
            padding: 1rem;
            margin-bottom: 1rem;
            border: 1px solid #e9ecef;
            border-radius: 12px;
        }

        .skeleton-header {
            height: 20px;
            width: 40%;
            margin-bottom: 1rem;
        }

        .skeleton-item {
            display: flex;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .skeleton-image {
            width: 60px;
            height: 60px;
            flex-shrink: 0;
        }

        .skeleton-text {
            flex: 1;
        }

        .skeleton-line {
            height: 14px;
            margin-bottom: 0.5rem;
        }

        .skeleton-line.short {
            width: 60%;
        }

        .skeleton-footer {
            height: 16px;
            width: 30%;
            margin-top: 1rem;
        }

        @media (max-width: 768px) {
            .nav-tabs {
                flex-wrap: nowrap;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .nav-tabs .nav-link {
                white-space: nowrap;
                padding: 0.75rem 1rem;
                font-size: 0.9rem;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            let currentStatus = '{{ request('status') ?? '' }}';
            let currentSearch = '{{ $search ?? '' }}';
            let currentPage = 1;
            let isLoading = false;

            const orderCache = {
                data: {},

                getCacheKey: function(status, search, page) {
                    return `orders_${status || 'all'}_${search || 'none'}_${page || 1}`;
                },

                has: function(status, search, page) {
                    const key = this.getCacheKey(status, search, page);
                    return this.data.hasOwnProperty(key);
                },

                get: function(status, search, page) {
                    const key = this.getCacheKey(status, search, page);
                    return this.data[key];
                },

                set: function(status, search, page, html) {
                    const key = this.getCacheKey(status, search, page);
                    this.data[key] = {
                        html: html,
                        timestamp: Date.now(),
                        status: status,
                        search: search,
                        page: page
                    };
                },

                clear: function(status, search, page) {
                    if (status === undefined) {
                        this.data = {};
                    } else {
                        const key = this.getCacheKey(status, search, page);
                        delete this.data[key];
                    }
                },

                size: function() {
                    return Object.keys(this.data).length;
                }
            };

            function showLoading(useSkeletonLoader = false) {
                if (useSkeletonLoader) {
                    showSkeletonLoader();
                } else {
                    $('#ordersContainer').addClass('loading');
                    $('#loadingOverlay').addClass('active');
                }
            }

            function hideLoading() {
                $('#ordersContainer').removeClass('loading');
                $('#loadingOverlay').removeClass('active');
                $('.skeleton-container').remove();
            }

            function showSkeletonLoader() {
                const skeletonHTML = `
                    <div class="skeleton-container">
                        ${generateSkeletonOrder()}
                        ${generateSkeletonOrder()}
                        ${generateSkeletonOrder()}
                    </div>
                `;
                $('#ordersContainer').html(skeletonHTML);
            }

            function generateSkeletonOrder() {
                return `
                    <div class="skeleton-order">
                        <div class="skeleton-loader skeleton-header"></div>
                        <div class="skeleton-item">
                            <div class="skeleton-loader skeleton-image"></div>
                            <div class="skeleton-text">
                                <div class="skeleton-loader skeleton-line"></div>
                                <div class="skeleton-loader skeleton-line short"></div>
                            </div>
                        </div>
                        <div class="skeleton-loader skeleton-footer"></div>
                    </div>
                `;
            }

            function loadOrders(status = '', search = '', page = 1, forceReload = false) {
                if (isLoading) return;

                if (!forceReload && orderCache.has(status, search, page)) {
                    const cachedData = orderCache.get(status, search, page);
                    $('#ordersContainer').html(cachedData.html);

                    updateUIState(status, search, page);

                    return;
                }

                isLoading = true;
                showLoading(page === 1);

                const params = new URLSearchParams();
                if (status) params.append('status', status);
                if (search) params.append('search', search);
                if (page > 1) params.append('page', page);

                $.ajax({
                    url: '{{ route('purchase.index') }}',
                    method: 'GET',
                    data: params.toString(),
                    dataType: 'json',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function(response) {
                        if (response.success) {
                            orderCache.set(status, search, page, response.html);

                            $('#ordersContainer').html(response.html);

                            updateUIState(status, search, page);
                        }
                    },
                    error: function(xhr) {
                        console.error('❌ Error loading orders:', xhr);
                        toastr.error('Có lỗi xảy ra khi tải đơn hàng. Vui lòng thử lại!');
                        hideLoading();
                    },
                    complete: function() {
                        isLoading = false;
                        hideLoading();
                    }
                });
            }

            function updateUIState(status, search, page) {
                $('.order-tab').removeClass('active');
                if (status) {
                    $(`.order-tab[data-status="${status}"]`).addClass('active');
                } else {
                    $('.order-tab[data-status=""]').addClass('active');
                }

                if (status) {
                    $('#purchaseSearchForm').slideUp(200);
                } else {
                    $('#purchaseSearchForm').slideDown(200);
                }

                let newUrl = '{{ route('purchase.index') }}';
                const urlParams = new URLSearchParams();
                if (status) urlParams.append('status', status);
                if (search) urlParams.append('search', search);
                if (page > 1) urlParams.append('page', page);

                if (urlParams.toString()) {
                    newUrl += '?' + urlParams.toString();
                }

                window.history.pushState({
                    status: status,
                    search: search,
                    page: page
                }, '', newUrl);

                currentStatus = status;
                currentSearch = search;
                currentPage = page;
            }

            $(document).on('click', '.order-tab', function(e) {
                e.preventDefault();

                const status = $(this).data('status');
                const search = status === '' ? $('#purchaseSearchInput').val() : '';

                loadOrders(status, search, 1);
            });

            $(document).on('click', '.pagination-container a', function(e) {
                e.preventDefault();

                const url = $(this).attr('href');
                const urlParams = new URLSearchParams(url.split('?')[1]);
                const page = parseInt(urlParams.get('page')) || 1;
                const search = currentStatus === '' ? $('#purchaseSearchInput').val() : '';

                loadOrders(currentStatus, search, page);
            });

            $('#purchaseSearchForm').on('submit', function(e) {
                e.preventDefault();
                const search = $('#purchaseSearchInput').val().trim();
                loadOrders('', search, 1);
            });

            $('#purchaseSearchInput').on('keypress', function(e) {
                if (e.which === 13) {
                    e.preventDefault();
                    $('#purchaseSearchForm').submit();
                }
            });

            window.addEventListener('popstate', function(e) {
                if (e.state) {
                    const status = e.state.status || '';
                    const search = e.state.search || '';
                    const page = e.state.page || 1;
                    loadOrders(status, search, page);
                }
            });

            $(document).on('click', '.cancel-order-btn', function() {
                const orderId = $(this).data('order-id');
                const $orderCard = $(`.order-card[data-order-id="${orderId}"]`);

                if (!confirm('Bạn có chắc muốn hủy đơn hàng này?')) {
                    return;
                }

                const $btn = $(this);
                const originalHtml = $btn.html();
                $btn.prop('disabled', true).html(
                    '<i class="fas fa-spinner fa-spin me-1"></i>Đang xử lý...');

                $.ajax({
                    url: `/purchase/${orderId}/cancel`,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);

                            $orderCard.find('.badge').removeClass().addClass('badge bg-danger')
                                .text('Đã hủy');
                            $orderCard.find('.cancel-order-btn').remove();

                            orderCache.clear(currentStatus, currentSearch, currentPage);
                        }
                    },
                    error: function(xhr) {
                        $btn.prop('disabled', false).html(originalHtml);

                        if (xhr.status === 400) {
                            toastr.error(xhr.responseJSON.message);
                        } else if (xhr.status === 404) {
                            toastr.error('Không tìm thấy đơn hàng');
                        } else {
                            toastr.error('Có lỗi xảy ra. Vui lòng thử lại!');
                        }
                    }
                });
            });

            @if (isset($orders))
                const initialHTML = $('#ordersContainer').html();
                orderCache.set(currentStatus, currentSearch, currentPage, initialHTML);
            @endif
        });
    </script>
@endpush

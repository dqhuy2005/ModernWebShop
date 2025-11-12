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
                                <img src="{{ Auth::user()->image ? asset('storage/' . Auth::user()->image) : asset('assets/imgs/default-avatar.png') }}"
                                    alt="Avatar" class="rounded-circle"
                                    style="width: 120px; height: 120px; object-fit: cover;">
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
                                <form method="GET" action="{{ route('purchase.index') }}" id="searchForm" class="mb-4">
                                    <div class="row g-3">
                                        <div class="col-md-12">
                                            <div class="input-group">
                                                <span class="input-group-text" style="background-color: #eaeaea">
                                                    <i class="fas fa-search text-muted"></i>
                                                </span>
                                                <input type="text" class="form-control border-start-0"
                                                    style="background-color: #eaeaea" name="search"
                                                    placeholder="Tìm kiếm theo mã đơn hàng hoặc tên sản phẩm"
                                                    value="{{ $search ?? '' }}">
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            @endif

                            <div id="ordersContainer">
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
            color: #dc3545;
            border-color: transparent;
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
        }

        #ordersContainer.opacity-50 {
            opacity: 0.5;
            pointer-events: none;
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
            let isLoading = false;

            function loadOrders(status = '', search = '', page = 1) {
                if (isLoading) return;

                isLoading = true;
                $('#ordersContainer').addClass('opacity-50');

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
                            $('#ordersContainer').html(response.html);

                            $('.order-tab').removeClass('active');
                            if (status) {
                                $(`.order-tab[data-status="${status}"]`).addClass('active');
                            } else {
                                $('.order-tab[data-status=""]').addClass('active');
                            }

                            const newUrl = status ?
                                `{{ route('purchase.index') }}?status=${status}` :
                                '{{ route('purchase.index') }}';
                            window.history.pushState({
                                status: status
                            }, '', newUrl);

                            currentStatus = status;
                        }
                    },
                    error: function(xhr) {
                        toastr.error('Có lỗi xảy ra khi tải đơn hàng. Vui lòng thử lại!');
                    },
                    complete: function() {
                        isLoading = false;
                        $('#ordersContainer').removeClass('opacity-50');
                    }
                });
            }

            $(document).on('click', '.order-tab', function(e) {
                e.preventDefault();

                const status = $(this).data('status');
                const search = status === '' ? $('input[name="search"]').val() : '';

                loadOrders(status, search);
            });

            $(document).on('click', '.pagination-container a', function(e) {
                e.preventDefault();

                const url = $(this).attr('href');
                const urlParams = new URLSearchParams(url.split('?')[1]);
                const page = urlParams.get('page') || 1;
                const search = $('input[name="search"]').val();

                loadOrders(currentStatus, search, page);
            });

            $('#searchForm').on('submit', function(e) {
                e.preventDefault();
                const search = $('input[name="search"]').val();
                loadOrders('', search);
            });

            $('input[name="search"]').on('keypress', function(e) {
                if (e.which === 13) {
                    e.preventDefault();
                    $('#searchForm').submit();
                }
            });

            window.addEventListener('popstate', function(e) {
                if (e.state && e.state.status !== undefined) {
                    loadOrders(e.state.status);
                }
            });

            $(document).on('click', '.cancel-order-btn', function() {
                const orderId = $(this).data('order-id');
                const $orderCard = $(`.order-card[data-order-id="${orderId}"]`);

                if (!confirm('Bạn có chắc muốn hủy đơn hàng này?')) {
                    return;
                }

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
                        }
                    },
                    error: function(xhr) {
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
        });
    </script>
@endpush

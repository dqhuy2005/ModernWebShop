<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=1200">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'ModernWebShop - Cửa hàng trực tuyến')</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <style>
        html,
        body {
            height: 100%;
            margin: 0;
            min-width: 1200px;
        }

        body {
            display: flex;
            flex-direction: column;
            overflow-x: auto;
        }

        .main-content {
            flex: 1 0 auto;
            background-color: #ECECEC;
        }

        .main-footer,
        .copyright-bar {
            flex-shrink: 0;
        }

        .container {
            min-width: 1200px;
            max-width: 1440px;
            width: 100%;
            margin: 0 auto;
            padding-left: 15px;
            padding-right: 15px;
        }

        .container-fluid {
            min-width: 1200px;
            width: 100%;
            padding-left: 15px;
            padding-right: 15px;
        }

        * {
            box-sizing: border-box;
        }

        img {
            max-width: 100%;
            height: auto;
        }

        @media (max-width: 1199px) and (min-width: 768px) {
            body {
                min-width: 768px;
            }

            .container {
                min-width: 768px;
                max-width: 100%;
            }

            .container-fluid {
                min-width: 768px;
            }
        }

        @media (max-width: 767px) {
            body {
                min-width: 320px;
            }

            .container {
                min-width: 320px;
                max-width: 100%;
                padding-left: 10px;
                padding-right: 10px;
            }

            .container-fluid {
                min-width: 320px;
                padding-left: 10px;
                padding-right: 10px;
            }
        }
    </style>

    @stack('styles')
</head>

<body>
    @include('components.user.header')

    <main class="main-content">
        @yield('content')
    </main>

    @include('components.user.footer')

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Toastr -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <script>
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };

        @if (session('success'))
            toastr.success("{{ session('success') }}");
        @endif

        @if (session('error'))
            toastr.error("{{ session('error') }}");
        @endif

        @if (session('warning'))
            toastr.warning("{{ session('warning') }}");
        @endif

        @if (session('info'))
            toastr.info("{{ session('info') }}");
        @endif

        function addToCart(productId, quantity = 1) {

            $.ajax({
                url: '{{ route('cart.add') }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    product_id: productId,
                    quantity: quantity
                },
                success: function(response) {
                    if (response.success) {
                        $('#cart-count').text(response.cart_count);
                        toastr.success(response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', {
                        xhr: xhr,
                        status: status,
                        error: error
                    });
                    console.error('Response:', xhr.responseText);

                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        toastr.error(xhr.responseJSON.message);
                    } else {
                        toastr.error('Có lỗi xảy ra. Vui lòng thử lại!');
                    }
                }
            });
        }

        $(document).on('click', '.add-to-cart-btn', function(e) {
            e.preventDefault();
            const productId = $(this).data('product-id');

            if (productId) {
                addToCart(productId);
            } else {
                console.error('No product ID found!');
                toastr.error('Không tìm thấy ID sản phẩm!');
            }
        });

        $(document).on('click', '.quick-view-btn', function(e) {
            e.preventDefault();
            toastr.info('Tính năng xem nhanh đang được phát triển!');
        });

        $(document).ready(function() {
            let searchTimeout = null;
            let currentFocus = -1;

            const searchInput = $('#headerSearchInput');
            const searchForm = $('#headerSearchForm');
            const suggestionsDropdown = $('#searchSuggestions');
            const suggestionsList = $('#suggestionsList');
            const historyList = $('#historyList');
            const historySection = $('#searchHistorySection');
            const suggestionsSection = $('#suggestionsSection');
            const suggestionsHeader = $('#suggestionsHeader');
            const emptyState = $('#emptyState');

            function debounce(func, delay) {
                return function() {
                    const context = this;
                    const args = arguments;
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => func.apply(context, args), delay);
                };
            }

            function loadSearchHistory() {
                $.ajax({
                    url: '{{ route('api.search-history.index') }}',
                    method: 'GET',
                    success: function(response) {
                        if (response.success && response.data && response.data.length > 0) {
                            renderHistory(response.data);
                            historySection.show();
                        } else {
                            historySection.hide();
                            historyList.empty();
                        }
                    },
                    error: function(xhr) {
                        console.error('Failed to load search history:', xhr);
                        historySection.hide();
                        historyList.empty();
                    }
                });
            }

            function renderHistory(history) {
                if (!history || history.length === 0) {
                    historyList.empty();
                    historySection.hide();
                    return;
                }

                let html = '';
                history.forEach(function(item) {
                    if (item && item.keyword && item.id) {
                        html += `
                            <div class="history-item" data-keyword="${item.keyword}">
                                <div class="history-item-content">
                                    <i class="bi bi-clock-history text-muted"></i>
                                    <span class="history-keyword">${item.keyword}</span>
                                    <span class="history-count">(${item.search_count || 1})</span>
                                </div>
                                <button type="button" class="btn-delete-history" data-id="${item.id}">
                                    <i class="bi bi-x"></i>
                                </button>
                            </div>
                        `;
                    }
                });
                historyList.html(html);
            }

            function fetchSuggestions(keyword) {
                if (keyword.length === 0) {
                    loadSearchHistory();
                    suggestionsList.empty();
                    suggestionsHeader.hide();
                    emptyState.hide();
                    suggestionsDropdown.show();
                    return;
                }

                if (keyword.length < 2) {
                    suggestionsDropdown.hide();
                    return;
                }

                suggestionsList.html(
                    '<div class="suggestion-loading"><i class="fas fa-spinner fa-spin me-2"></i>Đang tìm kiếm...</div>'
                );
                suggestionsDropdown.show();

                $.ajax({
                    url: '{{ route('products.search.suggestions') }}',
                    method: 'GET',
                    data: {
                        keyword: keyword
                    },
                    success: function(response) {
                        if (response.success) {
                            if (response.history && response.history.length > 0) {
                                renderHistory(response.history);
                                historySection.show();
                            } else {
                                historySection.hide();
                            }

                            if (response.products && response.products.length > 0) {
                                renderSuggestions(response.products);
                                suggestionsHeader.show();
                                emptyState.hide();
                            } else {
                                suggestionsList.empty();
                                suggestionsHeader.hide();
                                if (!response.history || response.history.length === 0) {
                                    emptyState.show();
                                }
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Search error:', error);
                        suggestionsList.html(
                            '<div class="suggestion-empty text-danger"><i class="fas fa-exclamation-circle me-2"></i>Có lỗi xảy ra</div>'
                        );
                    }
                });
            }

            function renderSuggestions(products) {
                let html = '';
                products.forEach(function(product) {
                    html += `
                        <a href="${product.url}" class="suggestion-item">
                            <img src="${product.image_url}" alt="${product.name}" class="suggestion-image">
                            <div class="suggestion-info">
                                <div class="suggestion-name">${product.name}</div>
                                <div class="suggestion-price">${product.formatted_price}</div>
                            </div>
                        </a>
                    `;
                });
                suggestionsList.html(html);
            }

            function deleteHistoryItem(id) {
                if (!id) {
                    console.error('Invalid history ID');
                    return;
                }

                $.ajax({
                    url: '/api/search-history/' + id,
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            loadSearchHistory();
                            const keyword = searchInput.val().trim();
                            if (keyword.length >= 2) {
                                fetchSuggestions(keyword);
                            }
                        }
                    },
                    error: function(xhr) {
                        console.error('Failed to delete history:', xhr);
                        if (xhr.status === 404) {
                            loadSearchHistory();
                        }
                    }
                });
            }

            function clearAllHistory() {
                if (!confirm('Bạn có chắc muốn xóa toàn bộ lịch sử tìm kiếm?')) {
                    return;
                }

                $.ajax({
                    url: '{{ route('api.search-history.clear') }}',
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            historySection.hide();
                            historyList.empty();
                            // If input has value, reload suggestions without history
                            const keyword = searchInput.val().trim();
                            if (keyword.length >= 2) {
                                fetchSuggestions(keyword);
                            }
                        }
                    },
                    error: function(xhr) {
                        console.error('Failed to clear history:', xhr);
                        alert('Không thể xóa lịch sử. Vui lòng thử lại!');
                    }
                });
            }

            searchInput.on('input', debounce(function() {
                const keyword = $(this).val().trim();
                fetchSuggestions(keyword);
            }, 300));

            searchInput.on('focus', function() {
                const keyword = $(this).val().trim();
                if (keyword.length === 0) {
                    loadSearchHistory();
                    suggestionsDropdown.show();
                } else if (keyword.length >= 2) {
                    fetchSuggestions(keyword);
                }
            });

            $(document).on('click', '.history-item', function(e) {
                if (!$(e.target).closest('.btn-delete-history').length) {
                    const keyword = $(this).data('keyword');
                    searchInput.val(keyword);
                    searchForm.submit();
                }
            });

            $(document).on('click', '.btn-delete-history', function(e) {
                e.stopPropagation();
                const id = $(this).data('id');
                deleteHistoryItem(id);
            });

            $(document).on('click', '#clearAllHistory', function() {
                clearAllHistory();
            });

            $(document).on('click', function(e) {
                if (!$(e.target).closest('.search-wrapper').length) {
                    suggestionsDropdown.hide();
                }
            });

            searchInput.on('keydown', function(e) {
                if (e.key === 'Escape') {
                    suggestionsDropdown.hide();
                }
            });

            searchInput.on('keyup', function(e) {
                if ($(this).val().trim().length === 0 && e.key === 'Backspace') {
                    loadSearchHistory();
                }
            });
        });
    </script>

    @stack('scripts')
</body>

</html>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'ModernWebShop - Cửa hàng trực tuyến')</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <style>
        html, body {
            height: 100%;
            margin: 0;
        }

        body {
            display: flex;
            flex-direction: column;
        }

        .main-content {
            flex: 1 0 auto;
        }

        .main-footer, .copyright-bar {
            flex-shrink: 0;
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

        @if(session('success'))
            toastr.success("{{ session('success') }}");
        @endif

        @if(session('error'))
            toastr.error("{{ session('error') }}");
        @endif

        @if(session('warning'))
            toastr.warning("{{ session('warning') }}");
        @endif

        @if(session('info'))
            toastr.info("{{ session('info') }}");
        @endif

        function addToCart(productId, quantity = 1) {

            $.ajax({
                url: '{{ route("cart.add") }}',
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
                    console.error('AJAX Error:', {xhr: xhr, status: status, error: error});
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

            const searchInput = $('#searchInput');
            const suggestionsDropdown = $('#searchSuggestions');
            const suggestionsList = suggestionsDropdown.find('.suggestions-list');

            function debounce(func, delay) {
                return function() {
                    const context = this;
                    const args = arguments;
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => func.apply(context, args), delay);
                };
            }

            function fetchSuggestions(keyword) {

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

                        if (response.success && response.products.length > 0) {
                            renderSuggestions(response.products);
                        } else {
                            suggestionsList.html(
                                '<div class="suggestion-empty"><i class="fas fa-search me-2"></i>Không tìm thấy sản phẩm nào</div>'
                            );
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Search error:', error);
                        console.error('XHR:', xhr);
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
                            <img src="${product.image}" alt="${product.name}" class="suggestion-image">
                            <div class="suggestion-info">
                                <div class="suggestion-name">${product.name}</div>
                                <div class="suggestion-price">${product.formatted_price}</div>
                            </div>
                        </a>
                    `;
                });

                suggestionsList.html(html);
            }

            searchInput.on('input', debounce(function() {
                const keyword = $(this).val().trim();
                fetchSuggestions(keyword);
            }, 300));

            searchInput.on('focus', function() {
                const keyword = $(this).val().trim();
                if (keyword.length >= 2) {
                    suggestionsDropdown.show();
                }
            });

            $(document).on('click', function(e) {
                if (!$(e.target).closest('.search-wrapper').length) {
                    suggestionsDropdown.hide();
                }
            });

            searchInput.on('keyup', function(e) {
                if ($(this).val().trim().length === 0) {
                    suggestionsDropdown.hide();
                }
            });

            searchInput.on('keydown', function(e) {
                if (e.key === 'Escape') {
                    suggestionsDropdown.hide();
                }
            });

        });
    </script>

    @stack('scripts')
</body>
</html>

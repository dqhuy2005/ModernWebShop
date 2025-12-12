<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=1200">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-authenticated" content="{{ auth()->check() ? 'true' : 'false' }}">
    <meta name="referrer" content="no-referrer" />

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

        .add-to-cart-notification {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(0);
            z-index: 9999;
            opacity: 0;
            transition: all 0.3s ease;
            pointer-events: none;
        }

        .add-to-cart-notification.show {
            transform: translate(-50%, -50%) scale(1);
            opacity: 1;
        }

        .notification-content {
            background: #fff;
            border-radius: 8px;
            padding: 30px 40px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
            min-width: 320px;
        }

        .notification-icon {
            width: 60px;
            height: 60px;
            background: #26aa99;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: scaleIn 0.3s ease;
        }

        .notification-icon i {
            font-size: 30px;
            color: #fff;
        }

        .notification-message {
            margin: 0;
            font-size: 16px;
            color: #333;
            font-weight: 500;
            text-align: center;
            line-height: 1.5;
        }

        @keyframes scaleIn {
            0% {
                transform: scale(0);
            }

            50% {
                transform: scale(1.1);
            }

            100% {
                transform: scale(1);
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

    <div id="addToCartNotification" class="add-to-cart-notification">
        <div class="notification-content">
            <div class="notification-icon">
                <i class="fas fa-check"></i>
            </div>
            <p class="notification-message">Sản phẩm đã được thêm vào Giỏ hàng</p>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

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

                        showAddToCartNotification();
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

        function showAddToCartNotification() {
            const notification = document.getElementById('addToCartNotification');
            notification.classList.add('show');

            setTimeout(() => {
                notification.classList.remove('show');
            }, 2000);
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
    </script>

    <script src="{{ asset('js/search-history-optimized.js') }}"></script>

    @stack('scripts')
</body>

</html>

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

    @stack('styles')
</head>
<body>
    @include('components.user.header')

    <main class="main-content">
        @if(session('success'))
            <div class="container mt-3">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="container mt-3">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    @include('components.user.footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        // Toastr Configuration
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "3000"
        };

        // Add to Cart Function
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
                        // Update cart count badge
                        $('#cart-count').text(response.cart_count);
                        
                        // Show success message
                        toastr.success(response.message);
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 401) {
                        toastr.error('Vui lòng đăng nhập để thêm vào giỏ hàng!');
                        setTimeout(function() {
                            window.location.href = '{{ route("login") }}';
                        }, 1500);
                    } else {
                        toastr.error('Có lỗi xảy ra. Vui lòng thử lại!');
                    }
                }
            });
        }

        // Global event handler for add to cart buttons
        $(document).on('click', '.add-to-cart-btn', function(e) {
            e.preventDefault();
            const productId = $(this).data('product-id');
            if (productId) {
                addToCart(productId);
            }
        });

        // Quick view handler (placeholder)
        $(document).on('click', '.quick-view-btn', function(e) {
            e.preventDefault();
            toastr.info('Tính năng xem nhanh đang được phát triển!');
        });
    </script>

    @stack('scripts')
</body>
</html>

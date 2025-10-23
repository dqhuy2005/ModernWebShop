@extends('layouts.user.app')

@section('title', '404 - Không tìm thấy trang')

@section('content')
    <div class="error-page"
        style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: calc(100vh - 200px);">
        <div class="container">
            <div class="row justify-content-center align-items-center" style="min-height: calc(100vh - 200px);">
                <div class="col-lg-6 col-md-8 text-center">
                    <div class="error-content">
                        <h1 class="error-number"
                            style="font-size: 10rem; font-weight: 800; color: rgba(255, 255, 255, 0.9); text-shadow: 0 10px 30px rgba(0,0,0,0.3); line-height: 1; margin-bottom: 1rem;">
                            404
                        </h1>

                        <h2 class="error-title mb-4"
                            style="color: #fff; font-weight: 600; font-size: 2rem; text-shadow: 0 2px 10px rgba(0,0,0,0.2);">
                            Không tìm thấy trang
                        </h2>

                        <p class="error-description mb-5"
                            style="color: rgba(255, 255, 255, 0.9); font-size: 1.1rem; max-width: 500px; margin: 0 auto 2rem;">
                            Xin lỗi, trang bạn đang tìm kiếm không tồn tại.
                        </p>

                        <div class="error-actions d-flex gap-3 justify-content-center flex-wrap">
                            <a href="{{ route('home') }}" class="btn btn-light btn-lg px-5 py-3"
                                style="border-radius: 50px; font-weight: 600; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
                                <i class="fas fa-home me-2"></i>Về trang chủ
                            </a>
                            <button onclick="window.history.back()" class="btn btn-outline-light btn-lg px-5 py-3"
                                style="border-radius: 50px; font-weight: 600; border-width: 2px;">
                                <i class="fas fa-arrow-left me-2"></i>Quay lại
                            </button>
                        </div>

                        <div class="mt-5 pt-4 border-top" style="border-color: rgba(255, 255, 255, 0.2) !important;">
                            <p class="mb-3" style="color: rgba(255, 255, 255, 0.8);">Có thể bạn đang tìm:</p>
                            <div class="d-flex gap-3 justify-content-center flex-wrap">
                                <a href="{{ route('home') }}" class="text-white text-decoration-none">
                                    <i class="fas fa-shopping-bag me-1"></i>Sản phẩm
                                </a>
                                <span style="color: rgba(255, 255, 255, 0.5);">|</span>
                                <a href="{{ route('hot-deals') }}" class="text-white text-decoration-none">
                                    <i class="fas fa-fire me-1"></i>Khuyến mãi
                                </a>
                                <span style="color: rgba(255, 255, 255, 0.5);">|</span>
                                <a href="{{ route('cart.index') }}" class="text-white text-decoration-none">
                                    <i class="fas fa-shopping-cart me-1"></i>Giỏ hàng
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Remove default margin from main content */
        .error-page {
            margin: 0;
            padding: 3rem 0;
        }

        .error-number {
            animation: bounceIn 1s ease-out;
        }

        .error-title {
            animation: fadeInUp 0.8s ease-out 0.2s both;
        }

        .error-description {
            animation: fadeInUp 0.8s ease-out 0.4s both;
        }

        .error-actions {
            animation: fadeInUp 0.8s ease-out 0.6s both;
        }

        @keyframes bounceIn {
            0% {
                opacity: 0;
                transform: scale(0.3);
            }

            50% {
                opacity: 1;
                transform: scale(1.05);
            }

            70% {
                transform: scale(0.9);
            }

            100% {
                transform: scale(1);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .btn-outline-light:hover {
            background-color: rgba(255, 255, 255, 0.2);
            border-color: #fff;
        }

        .error-actions a:hover,
        .error-actions button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3) !important;
            transition: all 0.3s ease;
        }

        @media (max-width: 768px) {
            .error-number {
                font-size: 6rem !important;
            }

            .error-title {
                font-size: 1.5rem !important;
            }

            .btn-lg {
                padding: 0.75rem 2rem !important;
                font-size: 0.9rem;
            }
        }
    </style>
@endsection

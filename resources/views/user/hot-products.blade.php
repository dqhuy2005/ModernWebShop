@extends('layouts.user.app')

@section('title', 'Sản Phẩm Hot - Xu Hướng 2025')

@section('content')
    <div class="container py-5">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h1 class="fw-bold mb-2">
                            <i class="bi bi-fire text-danger"></i>
                            Sản Phẩm Hot
                        </h1>
                        <p class="text-muted mb-0">
                            Top sản phẩm được quan tâm nhiều nhất trong 7 ngày gần đây (≥100 lượt xem)
                        </p>
                    </div>
                    <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-house"></i> Trang chủ
                    </a>
                </div>
            </div>
        </div>

        @if ($hotProducts->count() > 0)
            <div class="row g-4">
                @foreach ($hotProducts as $product)
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="card h-100 product-card position-relative">
                            {{-- Hot badge --}}
                            <span class="badge bg-danger position-absolute top-0 start-0 m-2 hot-badge">
                                <i class="bi bi-fire"></i> HOT
                            </span>

                            {{-- Product image --}}
                            <a href="{{ route('products.show', $product->slug) }}" class="text-decoration-none">
                                <img src="{{ $product->image_url }}" class="card-img-top product-img"
                                    alt="{{ $product->name }}">
                            </a>

                            {{-- Product info --}}
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title product-name">
                                    <a href="{{ route('products.show', $product->slug) }}"
                                        class="text-dark text-decoration-none">
                                        {{ $product->name }}
                                    </a>
                                </h5>

                                <div class="product-meta mb-2">
                                    <small class="text-muted">
                                        <i class="bi bi-eye"></i>
                                        {{ number_format($product->views ?? 0) }} lượt xem
                                    </small>
                                </div>

                                <div class="product-price fw-bold text-danger mb-3">
                                    {{ $product->formatted_price }}
                                </div>

                                <div class="mt-auto">
                                    <button class="btn btn-primary btn-sm w-100 add-to-cart-btn"
                                        data-product-id="{{ $product->id }}">
                                        <i class="bi bi-cart-plus"></i> Thêm giỏ hàng
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if ($hotProducts->hasPages())
                <div class="row mt-5">
                    <div class="col-12">
                        <div class="d-flex justify-content-center">
                            {{ $hotProducts->links() }}
                        </div>
                    </div>
                </div>
            @endif
        @else
            {{-- Empty state --}}
            <div class="row">
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="bi bi-emoji-frown" style="font-size: 4rem; color: #ccc;"></i>
                        <h3 class="mt-3 text-muted">Chưa có sản phẩm Hot</h3>
                        <p class="text-muted">Hiện tại chưa có sản phẩm nào đạt đủ điều kiện để trở thành Hot Product.</p>
                        <a href="{{ route('home') }}" class="btn btn-primary mt-3">
                            <i class="bi bi-house"></i> Về trang chủ
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('styles')
    {{-- Bootstrap Icons CDN (if not already included) --}}
    @if (!isset($bootstrapIconsLoaded))
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    @endif

    <style>
        .product-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid #e0e0e0;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        .product-img {
            height: 220px;
            object-fit: cover;
            transition: opacity 0.3s ease;
        }

        .product-card:hover .product-img {
            opacity: 0.9;
        }

        .product-name {
            font-size: 1rem;
            height: 2.8rem;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            line-height: 1.4;
        }

        .product-price {
            font-size: 1.25rem;
        }

        .hot-badge {
            z-index: 10;
            animation: pulse 2s infinite;
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(220, 53, 69, 0.3);
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
                transform: scale(1);
            }

            50% {
                opacity: 0.85;
                transform: scale(1.05);
            }
        }

        @media(max-width: 767px) {
            .product-img {
                height: 180px;
            }

            .product-name {
                font-size: 0.9rem;
            }

            .product-price {
                font-size: 1.1rem;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const addToCartBtns = document.querySelectorAll('.add-to-cart-btn');

            addToCartBtns.forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const productId = this.dataset.productId;

                    const originalHtml = this.innerHTML;
                    this.innerHTML = '<i class="bi bi-check-circle"></i> Đã thêm!';
                    this.disabled = true;

                    setTimeout(() => {
                        this.innerHTML = originalHtml;
                        this.disabled = false;
                    }, 2000);
                });
            });
        });
    </script>
@endpush

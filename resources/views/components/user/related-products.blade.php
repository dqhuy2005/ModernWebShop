@props(['relatedProducts'])

@if ($relatedProducts->isNotEmpty())
    <div class="row mt-4">
        <div class="col-12">
            <div class="card related-products-card">
                <div class="card-header bg-white">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-bag-heart"></i> Sản phẩm liên quan
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="related-products-carousel position-relative">
                        <div class="swiper-button-prev-custom">
                            <i class="bi bi-chevron-left"></i>
                        </div>
                        <div class="swiper-button-next-custom">
                            <i class="bi bi-chevron-right"></i>
                        </div>

                        <div class="swiper relatedSwiper">
                            <div class="swiper-wrapper">
                                @foreach ($relatedProducts as $relatedProduct)
                                    <div class="swiper-slide">
                                        <div class="product-card h-100">
                                            <a href="{{ route('products.show', $relatedProduct->slug) }}"
                                                class="text-decoration-none product-link">
                                                <div class="product-card-image">
                                                    @if ($relatedProduct->images->isNotEmpty())
                                                        <img src="{{ asset($relatedProduct->images->first()->path) }}"
                                                            alt="{{ $relatedProduct->name }}" class="img-fluid">
                                                    @else
                                                        <img src="{{ asset('assets/imgs/default-image.png') }}" alt="No image"
                                                            class="img-fluid">
                                                    @endif

                                                    @if ($relatedProduct->is_hot)
                                                        <span class="product-badge hot-badge">
                                                            <i class="bi bi-fire"></i> HOT
                                                        </span>
                                                    @endif
                                                </div>

                                                <div class="product-card-body">
                                                    <h6 class="product-card-title">{{ $relatedProduct->name }}</h6>

                                                    <div class="product-rating mb-2">
                                                        @php
                                                            $avgRating = $relatedProduct->getAverageRating();
                                                            $totalReviews = $relatedProduct->getReviewsCount();
                                                        @endphp

                                                        @if ($totalReviews > 0)
                                                            <div class="d-flex align-items-center gap-1">
                                                                <div class="stars-small">
                                                                    @for ($i = 1; $i <= 5; $i++)
                                                                        @if ($i <= floor($avgRating))
                                                                            <i class="bi bi-star-fill text-warning"></i>
                                                                        @elseif($i - 0.5 <= $avgRating)
                                                                            <i class="bi bi-star-half text-warning"></i>
                                                                        @else
                                                                            <i class="bi bi-star text-warning"></i>
                                                                        @endif
                                                                    @endfor
                                                                </div>
                                                                <small class="text-muted">({{ $totalReviews }})</small>
                                                            </div>
                                                        @else
                                                            <div class="stars-small text-muted">
                                                                <i class="bi bi-star"></i>
                                                                <i class="bi bi-star"></i>
                                                                <i class="bi bi-star"></i>
                                                                <i class="bi bi-star"></i>
                                                                <i class="bi bi-star"></i>
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <div class="product-card-price">
                                                        {{ $relatedProduct->formatted_price }}
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="swiper-pagination"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .related-products-card {
                border: none;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
                overflow: hidden;
            }

            .related-products-card .card-header {
                border-bottom: 1px solid #e5e7eb;
                padding: 1.25rem 1.5rem;
            }

            .related-products-carousel {
                position: relative;
            }

            .relatedSwiper {
                padding: 1rem 3rem 3rem 3rem;
            }

            .product-link {
                pointer-events: auto;
            }

            .product-card {
                background: #fff;
                border: 1px solid #e5e7eb;
                border-radius: 12px;
                overflow: hidden;
                display: flex;
                flex-direction: column;
            }

            .product-card-image {
                position: relative;
                width: 100%;
                height: 200px;
                overflow: hidden;
                background: #f8f9fa;
            }

            .product-card-image img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            .product-badge {
                position: absolute;
                top: 10px;
                right: 10px;
                padding: 0.25rem 0.5rem;
                font-size: 0.75rem;
                font-weight: 600;
                border-radius: 4px;
                z-index: 1;
            }

            .hot-badge {
                background: #dc3545;
                color: white;
            }

            .product-card-body {
                padding: 1rem;
                flex-grow: 1;
            }

            .product-card-title {
                font-size: 0.95rem;
                font-weight: 600;
                color: #1a1a1a;
                margin-bottom: 0.5rem;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
                text-overflow: ellipsis;
                line-height: 1.4;
                min-height: 2.8rem;
            }

            .product-card:hover .product-card-title {
                color: #0d6efd;
            }

            .product-rating {
                font-size: 0.85rem;
            }

            .stars-small {
                font-size: 0.85rem;
                letter-spacing: 1px;
            }

            .stars-small i {
                font-size: 0.85rem;
            }

            .product-card-price {
                font-size: 1.1rem;
                font-weight: 700;
                color: #dc3545;
            }

            .product-card-footer {
                padding: 0 1rem 1rem 1rem;
            }

            .relatedSwiper .swiper-button-next,
            .relatedSwiper .swiper-button-prev {
                color: #0d6efd;
                width: 40px;
                height: 40px;
                background: white;
                border-radius: 50%;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            }

            .relatedSwiper .swiper-button-next:after,
            .relatedSwiper .swiper-button-prev:after {
                font-size: 1.2rem;
                font-weight: 700;
            }

            .relatedSwiper .swiper-button-next:hover,
            .relatedSwiper .swiper-button-prev:hover {
                background: #0d6efd;
                color: white;
            }

            .relatedSwiper .swiper-pagination {
                bottom: 0;
                display: none;
            }

            .relatedSwiper .swiper-pagination-bullet {
                background: #0d6efd;
                opacity: 0.3;
            }

            .relatedSwiper .swiper-pagination-bullet-active {
                opacity: 1;
            }

            @media (max-width: 767px) {
                .product-card-image {
                    height: 180px;
                }

                .product-card-title {
                    font-size: 0.9rem;
                }

                .product-card-price {
                    font-size: 1rem;
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                if (typeof Swiper !== 'undefined') {
                    const relatedSwiper = new Swiper('.relatedSwiper', {
                        slidesPerView: 1,
                        spaceBetween: 15,
                        loop: false,
                        autoplay: {
                            delay: 4000,
                            disableOnInteraction: false,
                            pauseOnMouseEnter: true,
                        },
                        pagination: {
                            el: '.swiper-pagination',
                            clickable: true,
                        },
                        navigation: {
                            nextEl: '.swiper-button-next',
                            prevEl: '.swiper-button-prev',
                        },
                        breakpoints: {
                            576: {
                                slidesPerView: 2,
                                spaceBetween: 15,
                            },
                            768: {
                                slidesPerView: 3,
                                spaceBetween: 20,
                            },
                            992: {
                                slidesPerView: 4,
                                spaceBetween: 20,
                            },
                            1200: {
                                slidesPerView: 5,
                                spaceBetween: 20,
                            }
                        }
                    });
                }
            });
        </script>
    @endpush
@endif

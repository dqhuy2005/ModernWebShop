@props(['category', 'products' => [], 'limit' => 10])

@php
    $displayProducts = collect($products)->take($limit);
@endphp

<section class="category-product-carousel mb-5">
    <div class="container">
        <div class="carousel-header d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center gap-3">
                <h3 class="category-title mb-0">{{ $category->name }} bán chạy</h3>
            </div>
            <a href="{{ route('categories.show', $category->slug) }}" class="view-all-btn">
                Xem tất cả
            </a>
        </div>

        <div class="position-relative">
            <button class="carousel-nav-btn prev-btn" data-carousel="carousel-{{ $category->id }}">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button class="carousel-nav-btn next-btn" data-carousel="carousel-{{ $category->id }}">
                <i class="fas fa-chevron-right"></i>
            </button>

            <div class="carousel-overflow">
                <div class="carousel-products" id="carousel-{{ $category->id }}">
                    @forelse($displayProducts as $product)
                        <div class="carousel-product-item">
                            <div class="product-card-wrapper h-100">
                                <div class="product-image-container position-relative">
                                    @if ($product->is_hot)
                                        <span class="product-badge hot-badge">
                                            <i class="fas fa-fire"></i> Bán chạy
                                        </span>
                                    @endif

                                    @if ($product->category && $product->category->image)
                                        <span class="brand-icon">
                                            <img src="{{ $product->category->image_url }}"
                                                alt="{{ $product->category->name }}"
                                                title="{{ $product->category->name }}">
                                        </span>
                                    @endif

                                    <a href="{{ route('products.show', $product->slug) }}">
                                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                                            class="product-image" loading="lazy">
                                    </a>
                                </div>

                                <div class="product-info">
                                    <h6 class="product-name">
                                        <a href="{{ route('products.show', $product->slug) }}"
                                            class="text-decoration-none">
                                            {{ Str::limit($product->name, 60) }}
                                        </a>
                                    </h6>

                                    @if ($product->specifications && is_array($product->specifications))
                                        <div class="product-specs">
                                            @foreach (array_slice($product->specifications, 0, 3) as $key => $value)
                                                @if ($value)
                                                    <span class="spec-item">
                                                        <i class="fas fa-circle spec-dot"></i>
                                                        {{ is_string($key) ? $key . ': ' : '' }}{{ $value }}
                                                    </span>
                                                @endif
                                            @endforeach
                                        </div>
                                    @endif

                                    <div class="product-pricing">
                                        @if (isset($product->compare_price) && $product->compare_price > $product->price)
                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                <span
                                                    class="old-price">{{ number_format($product->compare_price) }}₫</span>
                                                @php
                                                    $discount = round(
                                                        (($product->compare_price - $product->price) /
                                                            $product->compare_price) *
                                                            100,
                                                    );
                                                @endphp
                                                <span class="discount-badge">-{{ $discount }}%</span>
                                            </div>
                                        @endif
                                        <div class="current-price">{{ number_format($product->price) }}₫</div>
                                    </div>

                                    <div class="product-rating d-flex align-items-center gap-2">
                                        <div class="stars">
                                            @php
                                                $rating = $product->approved_reviews_avg_rating ?? 0;
                                                $fullStars = floor($rating);
                                                $hasHalfStar = $rating - $fullStars >= 0.5;
                                            @endphp
                                            @for ($i = 1; $i <= 5; $i++)
                                                @if ($i <= $fullStars)
                                                    <i class="fas fa-star text-warning"></i>
                                                @elseif($i == $fullStars + 1 && $hasHalfStar)
                                                    <i class="fas fa-star-half-alt text-warning"></i>
                                                @else
                                                    <i class="far fa-star text-muted"></i>
                                                @endif
                                            @endfor
                                        </div>
                                        <span class="rating-count">({{ $product->approved_reviews_count ?? 0 }} đánh
                                            giá)</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="alert alert-info text-center">
                                <i class="fas fa-info-circle me-2"></i>
                                Chưa có sản phẩm trong danh mục này
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .category-product-carousel {
        background: #fff;
        padding: 2rem 0;
    }

    /* Header */
    .carousel-header {
        border-bottom: 2px solid #f0f0f0;
        padding-bottom: 1rem;
    }

    .category-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1a1a1a;
    }

    .delivery-badge {
        font-size: 0.875rem;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
    }

    .view-all-btn {
        color: #0d6efd;
        text-decoration: none;
        font-weight: 500;
        line-height: 22px;
        transition: all 0.3s ease;
    }

    .view-all-btn:hover {
        color: #dc3545;
    }

    /* Carousel Container */
    .carousel-overflow {
        overflow: hidden;
        margin: 0 50px;
    }

    .carousel-products {
        display: flex;
        gap: 1rem;
        transition: transform 0.4s ease-in-out;
    }

    .carousel-product-item {
        flex: 0 0 calc(20% - 0.8rem);
        min-width: 0;
    }

    /* Navigation Buttons */
    .carousel-nav-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 45px;
        height: 45px;
        background: white;
        border: 2px solid #e0e0e0;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        z-index: 10;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .carousel-nav-btn:hover {
        background: #0d6efd;
        border-color: #0d6efd;
        color: white;
        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
    }

    .carousel-nav-btn.prev-btn {
        left: 0;
    }

    .carousel-nav-btn.next-btn {
        right: 0;
    }

    .carousel-nav-btn:disabled {
        opacity: 0.3;
        cursor: not-allowed;
    }

    /* Product Card */
    .product-card-wrapper {
        background: #fff;
        border: 1px solid #e8e8e8;
        border-radius: 8px;
        padding: 1rem;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    /* Product Image */
    .product-image-container {
        margin-bottom: 1rem;
        aspect-ratio: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f8f9fa;
        border-radius: 8px;
        overflow: hidden;
    }

    .product-image {
        width: 100%;
        height: 100%;
        object-fit: contain;
        transition: transform 0.3s ease;
    }

    /* Badges */
    .product-badge {
        position: absolute;
        top: 8px;
        left: 8px;
        background: #dc3545;
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 600;
        z-index: 2;
    }

    .brand-icon {
        position: absolute;
        top: 8px;
        right: 8px;
        width: 40px;
        height: 40px;
        background: white;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        z-index: 2;
    }

    .brand-icon img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }

    /* Product Info */
    .product-info {
        flex: 1;
    }

    .product-name {
        font-size: 0.9rem;
        line-height: 1.4;
        margin-bottom: 0.75rem;
        min-height: 2.8em;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .product-name a {
        color: #333;
        transition: color 0.2s ease;
    }

    /* Specifications */
    .product-specs {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
        margin-bottom: 0.75rem;
        font-size: 0.8rem;
        color: #666;
    }

    .spec-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .spec-dot {
        font-size: 0.25rem;
        color: #999;
    }

    /* Pricing */
    .product-pricing {
        margin-bottom: 0.75rem;
    }

    .old-price {
        text-decoration: line-through;
        color: #999;
        font-size: 0.875rem;
    }

    .discount-badge {
        background: #dc3545;
        color: white;
        padding: 0.125rem 0.5rem;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .current-price {
        font-size: 1.125rem;
        font-weight: 700;
        color: #dc3545;
    }

    /* Rating */
    .product-rating {
        font-size: 0.875rem;
    }

    .product-rating .stars {
        display: flex;
        gap: 2px;
    }

    .product-rating .stars i {
        font-size: 0.875rem;
    }

    .rating-count {
        color: #666;
        font-size: 0.8rem;
    }

    /* Responsive Design */
    @media (max-width: 1200px) {
        .carousel-product-item {
            flex: 0 0 calc(25% - 0.75rem);
        }
    }

    @media (max-width: 992px) {
        .carousel-product-item {
            flex: 0 0 calc(33.333% - 0.67rem);
        }

        .carousel-overflow {
            margin: 0 40px;
        }

        .carousel-nav-btn {
            width: 40px;
            height: 40px;
        }
    }

    @media (max-width: 768px) {
        .carousel-product-item {
            flex: 0 0 calc(50% - 0.5rem);
        }

        .carousel-overflow {
            margin: 0 35px;
        }

        .category-title {
            font-size: 1.25rem;
        }

        .delivery-badge {
            font-size: 0.75rem;
            padding: 0.375rem 0.75rem;
        }

        .carousel-header {
            flex-direction: column;
            align-items: flex-start !important;
            gap: 1rem;
        }
    }

    @media (max-width: 576px) {
        .carousel-product-item {
            flex: 0 0 calc(100% - 0rem);
        }

        .carousel-overflow {
            margin: 0 30px;
        }

        .carousel-nav-btn {
            width: 35px;
            height: 35px;
        }
    }
</style>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const carousels = document.querySelectorAll('.carousel-products');

            carousels.forEach(carousel => {
                const carouselId = carousel.id;
                const container = carousel.closest('.category-product-carousel');
                const prevBtn = container.querySelector(`.prev-btn[data-carousel="${carouselId}"]`);
                const nextBtn = container.querySelector(`.next-btn[data-carousel="${carouselId}"]`);

                let currentIndex = 0;

                function getSlidesPerView() {
                    const width = window.innerWidth;
                    if (width <= 576) return 1;
                    if (width <= 768) return 2;
                    if (width <= 992) return 3;
                    if (width <= 1200) return 4;
                    return 5;
                }

                function updateCarousel() {
                    const items = carousel.querySelectorAll('.carousel-product-item');
                    const slidesPerView = getSlidesPerView();
                    const maxIndex = Math.max(0, items.length - slidesPerView);

                    currentIndex = Math.min(currentIndex, maxIndex);
                    currentIndex = Math.max(0, currentIndex);

                    const slideWidth = 100 / slidesPerView;
                    const offset = -(currentIndex * slideWidth);

                    carousel.style.transform = `translateX(${offset}%)`;

                    if (prevBtn) {
                        prevBtn.disabled = currentIndex === 0;
                    }
                    if (nextBtn) {
                        nextBtn.disabled = currentIndex >= maxIndex;
                    }
                }

                if (prevBtn) {
                    prevBtn.addEventListener('click', () => {
                        if (currentIndex > 0) {
                            currentIndex--;
                            updateCarousel();
                        }
                    });
                }

                if (nextBtn) {
                    nextBtn.addEventListener('click', () => {
                        const items = carousel.querySelectorAll('.carousel-product-item');
                        const slidesPerView = getSlidesPerView();
                        const maxIndex = Math.max(0, items.length - slidesPerView);

                        if (currentIndex < maxIndex) {
                            currentIndex++;
                            updateCarousel();
                        }
                    });
                }

                let resizeTimer;
                window.addEventListener('resize', () => {
                    clearTimeout(resizeTimer);
                    resizeTimer = setTimeout(() => {
                        updateCarousel();
                    }, 250);
                });

                updateCarousel();
            });
        });
    </script>
@endpush

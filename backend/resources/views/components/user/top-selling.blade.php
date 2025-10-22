<section class="top-selling-section py-5" style="background-color: #FFFFFF;">
    <div class="container">
        <div class="section-header text-center mb-4">
            <h2 class="section-title">SẢN PHẨM BÁN CHẠY</h2>
        </div>

        <div class="row g-3">
            @php
                $displayProducts = ($topSellingProducts ?? collect())->flatten();
            @endphp

            @forelse($displayProducts->take(12) as $index => $product)
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="top-product-card">
                        <a href="#" class="product-link">
                            @if ($product->is_hot)
                                <span class="product-badge hot-badge">HOT</span>
                            @endif
                            @if ($product->compare_price && $product->compare_price > $product->price)
                                @php
                                    $discount = round(
                                        (($product->compare_price - $product->price) / $product->compare_price) * 100,
                                    );
                                @endphp
                                <span class="product-badge discount-badge">-{{ $discount }}%</span>
                            @endif

                            <div class="product-image-wrapper">
                                <img src="{{ asset('storage/products/' . ($product->image ?? '1760847596_tai-nghe-bluetooth-m10-pro-tai-nghe-khong-m10-pro-phien-ban-nang-cap-pin-trau-nut-cam-ung-tu-dong-ket-noi.webp')) }}"
                                    alt="{{ $product->name }}" class="product-image">
                            </div>

                            <div class="product-details">
                                <p class="product-category">{{ $product->category->name ?? 'CATEGORY' }}</p>
                                <h6 class="product-name">{{ $product->name }}</h6>

                                <div class="product-price">
                                    <span
                                        class="current-price">{{ number_format($product->price, 0, ',', '.') }}₫</span>
                                    @if ($product->compare_price && $product->compare_price > $product->price)
                                        <span
                                            class="old-price">{{ number_format($product->compare_price, 0, ',', '.') }}₫</span>
                                    @endif
                                </div>

                                <div class="product-rating">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= ($product->rating ?? 5) ? 'rated' : '' }}"></i>
                                    @endfor
                                </div>
                            </div>
                        </a>
                    </div>
                </div>

                @if (($index + 1) % 6 === 0 && $index + 1 < 12)
                    <div class="w-100"></div>
                @endif
            @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Chưa có sản phẩm bán chạy</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</section>

<style>
    .top-selling-section {
        background-color: #FFFFFF;
    }

    .section-title {
        font-size: 2rem;
        font-weight: 700;
        color: #202732;
        margin-bottom: 0.5rem;
        letter-spacing: 1px;
    }

    .section-subtitle {
        font-size: 1rem;
        color: #6c757d;
    }

    .top-product-card {
        position: relative;
        background-color: #FFFCED;
        border-radius: 8px;
        overflow: hidden;
        transition: all 0.3s ease;
        height: 100%;
        border: 2px solid transparent;
    }

    .product-link {
        display: block;
        text-decoration: none;
        color: inherit;
        padding: 1rem;
    }

    .product-badge {
        position: absolute;
        top: 8px;
        padding: 4px 8px;
        font-size: 0.7rem;
        font-weight: 700;
        border-radius: 4px;
        z-index: 10;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .hot-badge {
        left: 8px;
        background-color: #dc3545;
        color: #FFFFFF;
    }

    .discount-badge {
        right: 8px;
        background-color: #28a745;
        color: #FFFFFF;
    }

    .product-image-wrapper {
        width: 100%;
        height: 180px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #FFFFFF;
        border-radius: 8px;
        margin-bottom: 1rem;
        overflow: hidden;
    }

    .product-image {
        width: 100%;
        height: 100%;
        object-fit: contain;
        transition: transform 0.3s ease;
    }

    .product-details {
        text-align: center;
    }

    .product-category {
        font-size: 0.7rem;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
    }

    .product-name {
        font-size: 0.9rem;
        font-weight: 600;
        color: #202732;
        margin-bottom: 0.75rem;
        line-height: 1.3;
        height: 2.6em;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }

    .product-price {
        margin-bottom: 0.5rem;
    }

    .current-price {
        font-weight: 700;
        font-size: 1rem;
        color: #dc3545;
        display: block;
    }

    .old-price {
        font-size: 0.8rem;
        color: #6c757d;
        text-decoration: line-through;
        display: block;
        margin-top: 0.25rem;
    }

    .product-rating {
        font-size: 0.75rem;
    }

    .product-rating i {
        color: #e0e0e0;
    }

    .product-rating i.rated {
        color: #FFD700;
    }

    @media (max-width: 992px) {
        .section-title {
            font-size: 1.5rem;
        }

        .product-image-wrapper {
            height: 150px;
        }
    }

    @media (max-width: 768px) {
        .section-title {
            font-size: 1.3rem;
        }

        .product-image-wrapper {
            height: 140px;
        }

        .product-name {
            font-size: 0.85rem;
        }

        .current-price {
            font-size: 0.9rem;
        }
    }

    @media (max-width: 576px) {
        .product-image-wrapper {
            height: 120px;
        }

        .product-link {
            padding: 0.75rem;
        }
    }
</style>

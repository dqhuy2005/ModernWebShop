<section class="top-selling-section py-5" style="background-color: #FFFFFF;">
    <div class="container">
        <div class="section-header text-center mb-4">
            <h2 class="section-title">Top Selling Products</h2>
            <p class="section-subtitle">Best sellers of the month</p>
        </div>

        <div class="row g-4">
            @foreach($topSellingProducts ?? [] as $productsChunk)
                <div class="col-md-4">
                    @foreach($productsChunk as $product)
                        <div class="product-item-horizontal d-flex mb-3 p-3">
                            <div class="product-thumbnail me-3">
                                <img src="{{ asset('storage/products/' . ($product->image ?? 'default.png')) }}" 
                                     alt="{{ $product->name }}" 
                                     class="img-fluid">
                            </div>
                            <div class="product-info flex-grow-1">
                                <p class="product-category mb-1">{{ $product->category->name ?? 'CATEGORY' }}</p>
                                <h6 class="product-title mb-2">
                                    <a href="{{ route('products.show', $product->slug) }}">{{ $product->name }}</a>
                                </h6>
                                <div class="product-price-section mb-1">
                                    <span class="price-current">${{ number_format($product->price, 2) }}</span>
                                    @if($product->compare_price)
                                        <span class="price-old">${{ number_format($product->compare_price, 2) }}</span>
                                    @endif
                                </div>
                                <div class="product-rating">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= 4 ? 'active' : '' }}"></i>
                                    @endfor
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
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
    }

    .section-subtitle {
        font-size: 1rem;
        color: #6c757d;
    }

    .product-item-horizontal {
        background-color: #FFFCED;
        border-radius: 8px;
        transition: all 0.3s ease;
        border: none;
    }

    .product-item-horizontal:hover {
        transform: translateX(5px);
        box-shadow: 0 3px 10px rgba(32, 39, 50, 0.1);
    }

    .product-thumbnail {
        width: 100px;
        height: 100px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        border-radius: 8px;
        flex-shrink: 0;
    }

    .product-thumbnail img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    .product-category {
        font-size: 0.75rem;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .product-title {
        font-size: 0.95rem;
        margin-bottom: 0.5rem;
    }

    .product-title a {
        color: #202732;
        text-decoration: none;
        font-weight: 600;
        transition: opacity 0.3s ease;
    }

    .product-title a:hover {
        opacity: 0.8;
    }

    .product-price-section {
        margin-bottom: 0.5rem;
    }

    .price-current {
        font-weight: 700;
        font-size: 1.1rem;
        color: #202732;
    }

    .price-old {
        font-size: 0.875rem;
        color: #6c757d;
        text-decoration: line-through;
        margin-left: 0.5rem;
    }

    .product-rating {
        font-size: 0.75rem;
    }

    .product-rating i {
        color: #e0e0e0;
    }

    .product-rating i.active {
        color: #FFD700;
    }

    @media (max-width: 768px) {
        .section-title {
            font-size: 1.5rem;
        }

        .product-thumbnail {
            width: 80px;
            height: 80px;
        }
    }
</style>

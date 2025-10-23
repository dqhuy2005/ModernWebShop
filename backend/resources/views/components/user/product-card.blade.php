<div class="product-card bg-white shadow-sm h-100">
    <div class="card-body text-center p-3">
        @if (isset($product) && $product->is_hot)
            <span class="badge bg-danger position-absolute top-0 start-0 m-2">
                HOT
            </span>
        @elseif (isset($badge))
            <span class="badge {{ $badgeClass ?? 'bg-danger' }} position-absolute top-0 start-0 m-2">
                {{ $badge }}
            </span>
        @endif
        <div class="product-image mb-3">
            @if(isset($product))
                <img src="{{ asset('storage/' . ($product->image ?? 'default.png')) }}"
                     alt="{{ $product->name }}"
                     class="img-fluid">
            @else
                <img src="{{ $image ?? asset('assets/imgs/banner/shop01.png') }}"
                     alt="{{ $name ?? 'Product' }}"
                     class="img-fluid">
            @endif
        </div>
        <p class="text-muted small mb-1">
            @if(isset($product) && $product->category)
                {{ $product->category->name }}
            @else
                {{ $category ?? 'CATEGORY' }}
            @endif
        </p>
        <h6 class="product-name mb-2">
            @if(isset($product))
                <a href="#" class="text-dark text-decoration-none">
                    {{ $product->name }}
                </a>
            @else
                <a href="{{ $url ?? '#' }}" class="text-dark text-decoration-none">
                    {{ $name ?? 'PRODUCT NAME GOES HERE' }}
                </a>
            @endif
        </h6>
        <div class="product-price mb-2">
            @if(isset($product))
                <span class="text-danger fw-bold fs-5">₫{{ number_format($product->price) }}</span>
                @if($product->compare_price && $product->compare_price > $product->price)
                    <span class="text-muted text-decoration-line-through ms-2 small">₫{{ number_format($product->compare_price) }}</span>
                @endif
            @else
                <span class="text-danger fw-bold fs-5">{{ $price ?? '₫980.000' }}</span>
                @if (isset($oldPrice))
                    <span class="text-muted text-decoration-line-through ms-2 small">{{ $oldPrice }}</span>
                @endif
            @endif
        </div>
        <div class="product-rating mb-3">
            @php
                $productRating = isset($product) ? ($product->rating ?? 5) : ($rating ?? 5);
            @endphp
            @for ($i = 1; $i <= 5; $i++)
                <i class="fas fa-star {{ $i <= $productRating ? 'text-warning' : 'text-muted' }}"></i>
            @endfor
        </div>
        <div class="product-actions d-flex justify-content-center gap-2">
            <button class="btn btn-sm btn-primary flex-fill add-to-cart-btn"
                    data-product-id="{{ isset($product) ? $product->id : '' }}"
                    title="Thêm vào giỏ hàng">
                <i class="fas fa-shopping-cart"></i> Thêm giỏ hàng
            </button>
            <button class="btn btn-sm btn-outline-secondary quick-view-btn"
                    data-product-id="{{ isset($product) ? $product->id : '' }}"
                    title="Xem nhanh">
                <i class="far fa-eye"></i>
            </button>
        </div>
    </div>
</div>

<style>
    .product-card {
        position: relative;
        transition: all 0.3s ease;
        border: none;
        background-color: #FFFCED !important;
        border-radius: 8px;
    }

    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(32, 39, 50, 0.15) !important;
    }

    .product-image {
        max-height: 200px;
        overflow: hidden;
        border-radius: 8px;
        margin-bottom: 1rem;
    }

    .product-image img {
        transition: transform 0.3s ease;
    }

    .product-card:hover .product-image img {
        transform: scale(1.05);
    }

    .product-name a {
        color: #202732 !important;
        font-weight: 600;
        font-size: 0.95rem;
    }

    .product-name a:hover {
        color: #202732 !important;
        opacity: 0.8;
    }

    .product-price {
        color: #202732;
    }

    .text-muted {
        color: #6c757d !important;
    }

    .product-actions {
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .product-card:hover .product-actions {
        opacity: 1;
    }

    .product-actions .btn {
        background-color: #202732;
        border-color: #202732;
        color: #FFFCED;
        transition: all 0.3s ease;
    }

    .product-actions .btn:hover {
        background-color: #FFFCED;
        border-color: #202732;
        color: #202732;
        transform: translateY(-2px);
    }

    .product-actions .btn-primary {
        background-color: #dc3545;
        border-color: #dc3545;
        color: #FFFFFF;
        font-weight: 600;
    }

    .product-actions .btn-primary:hover {
        background-color: #bb2d3b;
        border-color: #bb2d3b;
        color: #FFFFFF;
    }
</style>

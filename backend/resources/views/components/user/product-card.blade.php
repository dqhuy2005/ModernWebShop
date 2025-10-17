<div class="product-card bg-white rounded shadow-sm h-100">
    <div class="card-body text-center p-3">
        @if (isset($badge))
            <span class="badge {{ $badgeClass ?? 'bg-danger' }} position-absolute top-0 start-0 m-2">
                {{ $badge }}
            </span>
        @endif
        <div class="product-image mb-3">
            <img src="{{ $image ?? asset('images/default-product.png') }}" alt="{{ $name ?? 'Product' }}" class="img-fluid"
                style="max-height: 200px;">
        </div>
        <p class="text-muted small mb-1">{{ $category ?? 'CATEGORY' }}</p>
        <h6 class="product-name mb-2">
            <a href="{{ $url ?? '#' }}" class="text-dark text-decoration-none">
                {{ $name ?? 'PRODUCT NAME GOES HERE' }}
            </a>
        </h6>
        <div class="product-price mb-2">
            <span class="text-danger fw-bold fs-5">{{ $price ?? '$980.00' }}</span>
            @if (isset($oldPrice))
                <span class="text-muted text-decoration-line-through ms-2 small">{{ $oldPrice }}</span>
            @endif
        </div>
        <div class="product-rating mb-3">
            @for ($i = 1; $i <= 5; $i++)
                <i class="fas fa-star {{ $i <= ($rating ?? 5) ? 'text-warning' : 'text-muted' }}"></i>
            @endfor
        </div>
        <div class="product-actions d-flex justify-content-center gap-2">
            <button class="btn btn-sm btn-outline-secondary" title="Add to Wishlist">
                <i class="far fa-heart"></i>
            </button>
            <button class="btn btn-sm btn-outline-secondary" title="Compare">
                <i class="fas fa-exchange-alt"></i>
            </button>
            <button class="btn btn-sm btn-outline-secondary" title="Quick View">
                <i class="far fa-eye"></i>
            </button>
        </div>
    </div>
</div>

<style>
    .product-card {
        position: relative;
        transition: all 0.3s ease;
        border: 1px solid #e9ecef;
    }

    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }

    .product-name a:hover {
        color: #dc3545 !important;
    }

    .product-actions {
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .product-card:hover .product-actions {
        opacity: 1;
    }
</style>

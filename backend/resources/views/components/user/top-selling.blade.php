<section class="top-selling-section py-5">
    <div class="container">
        <h2 class="fw-bold mb-4">TOP SELLING</h2>

        <div class="row g-4">
            <div class="col-md-4">
                @for ($i = 1; $i <= 3; $i++)
                    <div class="product-item-horizontal d-flex mb-3 pb-3 border-bottom">
                        <div class="product-thumbnail me-3">
                            <img src="{{ asset('images/product-' . $i . '.png') }}" alt="Product" class="img-fluid"
                                style="width: 80px; height: 80px; object-fit: contain;">
                        </div>
                        <div class="product-info flex-grow-1">
                            <p class="text-muted small mb-1">CATEGORY</p>
                            <h6 class="mb-2">
                                <a href="#" class="text-dark text-decoration-none">PRODUCT NAME GOES HERE</a>
                            </h6>
                            <div class="mb-1">
                                <span class="text-danger fw-bold">$980.00</span>
                                <span class="text-muted text-decoration-line-through ms-2 small">$990.00</span>
                            </div>
                        </div>
                    </div>
                @endfor
            </div>

            <div class="col-md-4">
                @for ($i = 1; $i <= 3; $i++)
                    <div class="product-item-horizontal d-flex mb-3 pb-3 border-bottom">
                        <div class="product-thumbnail me-3">
                            <img src="{{ asset('images/product-' . ($i + 3) . '.png') }}" alt="Product"
                                class="img-fluid" style="width: 80px; height: 80px; object-fit: contain;">
                        </div>
                        <div class="product-info flex-grow-1">
                            <p class="text-muted small mb-1">CATEGORY</p>
                            <h6 class="mb-2">
                                <a href="#" class="text-dark text-decoration-none">PRODUCT NAME GOES HERE</a>
                            </h6>
                            <div class="mb-1">
                                <span class="text-danger fw-bold">$980.00</span>
                                <span class="text-muted text-decoration-line-through ms-2 small">$990.00</span>
                            </div>
                        </div>
                    </div>
                @endfor
            </div>

            <div class="col-md-4">
                @for ($i = 1; $i <= 3; $i++)
                    <div class="product-item-horizontal d-flex mb-3 pb-3 border-bottom">
                        <div class="product-thumbnail me-3">
                            <img src="{{ asset('images/product-' . ($i + 6) . '.png') }}" alt="Product"
                                class="img-fluid" style="width: 80px; height: 80px; object-fit: contain;">
                        </div>
                        <div class="product-info flex-grow-1">
                            <p class="text-muted small mb-1">CATEGORY</p>
                            <h6 class="mb-2">
                                <a href="#" class="text-dark text-decoration-none">PRODUCT NAME GOES HERE</a>
                            </h6>
                            <div class="mb-1">
                                <span class="text-danger fw-bold">$980.00</span>
                                <span class="text-muted text-decoration-line-through ms-2 small">$990.00</span>
                            </div>
                        </div>
                    </div>
                @endfor
            </div>
        </div>
    </div>
</section>

<style>
    .product-item-horizontal {
        transition: all 0.3s ease;
    }

    .product-item-horizontal:hover {
        background-color: #f8f9fa;
        padding: 10px;
        margin: 0 -10px;
        border-radius: 8px;
    }

    .product-item-horizontal a:hover {
        color: #dc3545 !important;
    }
</style>

<section class="product-tabs-section py-5 bg-light">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold mb-0">{{ $title ?? 'NEW PRODUCTS' }}</h2>

            <ul class="nav nav-tabs border-0" id="productTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active fw-semibold" id="laptops-tab" data-bs-toggle="tab"
                            data-bs-target="#laptops" type="button" role="tab">
                        Laptops
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-semibold" id="smartphones-tab" data-bs-toggle="tab"
                            data-bs-target="#smartphones" type="button" role="tab">
                        Smartphones
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-semibold" id="cameras-tab" data-bs-toggle="tab"
                            data-bs-target="#cameras" type="button" role="tab">
                        Cameras
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-semibold" id="accessories-tab" data-bs-toggle="tab"
                            data-bs-target="#accessories" type="button" role="tab">
                        Accessories
                    </button>
                </li>
            </ul>
        </div>

        <div class="tab-content" id="productTabContent">
            <div class="tab-pane fade show active" id="laptops" role="tabpanel">
                <div class="row g-4">
                    @for ($i = 1; $i <= 4; $i++)
                        <div class="col-md-3">
                            @include('components.user.product-card', [
                                'badge' => $i % 2 == 0 ? 'NEW' : '-20%',
                                'badgeClass' => $i % 2 == 0 ? 'bg-danger' : 'bg-warning',
                                'category' => 'CATEGORY',
                                'name' => 'PRODUCT NAME GOES HERE',
                                'price' => '$980.00',
                                'oldPrice' => '$990.00',
                                'rating' => 5
                            ])
                        </div>
                    @endfor
                </div>
            </div>

            <div class="tab-pane fade" id="smartphones" role="tabpanel">
                <div class="row g-4">
                    @for ($i = 1; $i <= 4; $i++)
                        <div class="col-md-3">
                            @include('components.user.product-card', [
                                'badge' => 'NEW',
                                'badgeClass' => 'bg-danger',
                                'category' => 'CATEGORY',
                                'name' => 'PRODUCT NAME GOES HERE',
                                'price' => '$980.00',
                                'oldPrice' => '$990.00',
                                'rating' => 4
                            ])
                        </div>
                    @endfor
                </div>
            </div>

            <div class="tab-pane fade" id="cameras" role="tabpanel">
                <div class="row g-4">
                    @for ($i = 1; $i <= 4; $i++)
                        <div class="col-md-3">
                            @include('components.user.product-card', [
                                'badge' => '-20%',
                                'badgeClass' => 'bg-warning',
                                'category' => 'CATEGORY',
                                'name' => 'PRODUCT NAME GOES HERE',
                                'price' => '$980.00',
                                'oldPrice' => '$990.00',
                                'rating' => 5
                            ])
                        </div>
                    @endfor
                </div>
            </div>

            <div class="tab-pane fade" id="accessories" role="tabpanel">
                <div class="row g-4">
                    @for ($i = 1; $i <= 4; $i++)
                        <div class="col-md-3">
                            @include('components.user.product-card', [
                                'badge' => 'NEW',
                                'badgeClass' => 'bg-danger',
                                'category' => 'CATEGORY',
                                'name' => 'PRODUCT NAME GOES HERE',
                                'price' => '$980.00',
                                'oldPrice' => '$990.00',
                                'rating' => 4
                            ])
                        </div>
                    @endfor
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .nav-tabs .nav-link {
        border: none;
        color: #6c757d;
        padding: 0.5rem 1.5rem;
        border-bottom: 2px solid transparent;
    }

    .nav-tabs .nav-link:hover {
        color: #dc3545;
        border-bottom-color: #dc3545;
    }

    .nav-tabs .nav-link.active {
        color: #dc3545;
        background-color: transparent;
        border-bottom-color: #dc3545;
    }
</style>

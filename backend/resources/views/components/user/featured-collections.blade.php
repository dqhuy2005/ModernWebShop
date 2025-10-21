<section class="featured-collections py-5">
    <div class="container">
        <div class="row g-4">            <div class="col-md-4">
                <div class="collection-card position-relative overflow-hidden rounded shadow-sm">
                    <div class="row g-0 align-items-center" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); min-height: 200px;">
                        <div class="col-6 p-4 text-white">
                            <h3 class="fw-bold mb-2">Laptop Collection</h3>
                            <a href="{{ route('categories.show', 'laptops') }}" class="btn btn-light btn-sm">
                                SHOP NOW <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                        <div class="col-6">
                            <img src="{{ asset('/imgs/banner/shop1.png') }}" alt="Laptop" class="img-fluid">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="collection-card position-relative overflow-hidden rounded shadow-sm">
                    <div class="row g-0 align-items-center" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); min-height: 200px;">
                        <div class="col-6 p-4 text-white">
                            <h3 class="fw-bold mb-2">Accessories Collection</h3>
                            <a href="{{ route('categories.show', 'accessories') }}" class="btn btn-light btn-sm">
                                SHOP NOW <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                        <div class="col-6">
                            <img src="{{ asset('/imgs/banner/shop1.png') }}" alt="Accessories" class="img-fluid">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="collection-card position-relative overflow-hidden rounded shadow-sm">
                    <div class="row g-0 align-items-center" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); min-height: 200px;">
                        <div class="col-6 p-4 text-white">
                            <h3 class="fw-bold mb-2">Cameras Collection</h3>
                            <a href="{{ route('categories.show', 'cameras') }}" class="btn btn-light btn-sm">
                                SHOP NOW <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                        <div class="col-6">
                            <img src="{{ asset('/imgs/banner/shop1.png') }}" alt="Cameras" class="img-fluid">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .collection-card {
        transition: transform 0.3s ease;
    }

    .collection-card:hover {
        transform: translateY(-5px);
    }
</style>

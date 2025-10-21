@extends('layouts.user.app')

@section('title', 'Trang chủ - ModernWebShop')

@section('content')
<div class="container py-5">
    <section class="hero-section mb-5">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="display-4 fw-bold">Chào mừng đến với ModernWebShop</h1>
                <p class="lead">Khám phá hàng ngàn sản phẩm chất lượng với giá tốt nhất</p>
                <a href="{{ route('products.index') }}" class="btn btn-primary btn-lg">
                    Mua sắm ngay <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
            <div class="col-md-6">
                <img src="/images/hero-banner.jpg" alt="Hero Banner" class="img-fluid rounded">
            </div>
        </div>
    </section>

    <section class="featured-products mb-5">
        <h2 class="text-center mb-4">Sản phẩm nổi bật</h2>
        <div class="row">
            @for($i = 1; $i <= 4; $i++)
            <div class="col-md-3 mb-4">
                <div class="card h-100">
                    <img src="/images/product-{{ $i }}.jpg" class="card-img-top" alt="Product {{ $i }}">
                    <div class="card-body">
                        <h5 class="card-title">Sản phẩm {{ $i }}</h5>
                        <p class="card-text text-muted">Mô tả ngắn về sản phẩm</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="h5 mb-0 text-danger">500.000₫</span>
                            <a href="#" class="btn btn-sm btn-primary">
                                <i class="fas fa-cart-plus"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endfor
        </div>
    </section>

    <section class="categories">
        <h2 class="text-center mb-4">Danh mục sản phẩm</h2>
        <div class="row">
            @for($i = 1; $i <= 6; $i++)
            <div class="col-md-2 col-6 mb-3">
                <a href="#" class="text-decoration-none">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-box fa-3x text-primary mb-2"></i>
                            <h6 class="card-title">Danh mục {{ $i }}</h6>
                        </div>
                    </div>
                </a>
            </div>
            @endfor
        </div>
    </section>
</div>
@endsection

@push('styles')
<style>
    .hero-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 60px;
        border-radius: 15px;
    }

    .card {
        transition: transform 0.3s;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }
</style>
@endpush

@extends('layouts.user.app')

@section('title', 'Danh sách sản phẩm - ModernWebShop')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1 class="h3 mb-3">Danh sách sản phẩm</h1>
            
            <!-- Filter Section -->
            <div class="card mb-4">
                <div class="card-body">
                    <form action="{{ route('products.index') }}" method="GET" class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Danh mục</label>
                            <select name="category" class="form-select">
                                <option value="">Tất cả danh mục</option>
                                <option value="1">Điện tử</option>
                                <option value="2">Thời trang</option>
                                <option value="3">Gia dụng</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Khoảng giá</label>
                            <select name="price_range" class="form-select">
                                <option value="">Tất cả</option>
                                <option value="0-500000">Dưới 500.000₫</option>
                                <option value="500000-1000000">500.000₫ - 1.000.000₫</option>
                                <option value="1000000-5000000">1.000.000₫ - 5.000.000₫</option>
                                <option value="5000000">Trên 5.000.000₫</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Sắp xếp</label>
                            <select name="sort" class="form-select">
                                <option value="latest">Mới nhất</option>
                                <option value="price_asc">Giá tăng dần</option>
                                <option value="price_desc">Giá giảm dần</option>
                                <option value="name">Tên A-Z</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter"></i> Lọc
                            </button>
                            <a href="{{ route('products.index') }}" class="btn btn-secondary">
                                <i class="fas fa-redo"></i> Đặt lại
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Products Grid -->
    <div class="row">
        @for($i = 1; $i <= 12; $i++)
        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
            <div class="card product-card h-100">
                <div class="product-image-wrapper">
                    <img src="https://via.placeholder.com/300x300" class="card-img-top" alt="Product {{ $i }}">
                    <div class="product-overlay">
                        <a href="{{ route('products.show', $i) }}" class="btn btn-light btn-sm">
                            <i class="fas fa-eye"></i> Xem chi tiết
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <h5 class="card-title">Sản phẩm {{ $i }}</h5>
                    <p class="card-text text-muted small">Mô tả ngắn về sản phẩm này</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="h5 mb-0 text-danger">{{ number_format(rand(100000, 5000000)) }}₫</span>
                            <br>
                            <small class="text-muted text-decoration-line-through">
                                {{ number_format(rand(150000, 6000000)) }}₫
                            </small>
                        </div>
                        <button class="btn btn-primary btn-sm" onclick="addToCart({{ $i }})">
                            <i class="fas fa-cart-plus"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endfor
    </div>

    <!-- Pagination -->
    <div class="row">
        <div class="col-12">
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
                    <li class="page-item disabled">
                        <a class="page-link" href="#" tabindex="-1">Trước</a>
                    </li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item">
                        <a class="page-link" href="#">Sau</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .product-card {
        transition: transform 0.3s, box-shadow 0.3s;
        cursor: pointer;
    }
    
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    }
    
    .product-image-wrapper {
        position: relative;
        overflow: hidden;
    }
    
    .product-image-wrapper img {
        transition: transform 0.3s;
    }
    
    .product-card:hover .product-image-wrapper img {
        transform: scale(1.1);
    }
    
    .product-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s;
    }
    
    .product-card:hover .product-overlay {
        opacity: 1;
    }
</style>
@endpush

@push('scripts')
<script>
    function addToCart(productId) {
        // AJAX call to add product to cart
        alert('Thêm sản phẩm ' + productId + ' vào giỏ hàng!');
        
        // You can implement real AJAX here
        /*
        $.ajax({
            url: '/cart/add',
            method: 'POST',
            data: {
                product_id: productId,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                // Show success message
                alert('Đã thêm vào giỏ hàng!');
            }
        });
        */
    }
</script>
@endpush

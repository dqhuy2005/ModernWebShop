
<header class="main-header">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark p-0 m-0">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('home') }}">
                <span class="text-danger">MWShop</span><span class="text-warning">.</span>
            </a>

            <div class="search-wrapper d-none d-lg-flex flex-grow-1 mx-4">
                <form action="{{ route('products.search') }}" method="GET" class="w-100 position-relative">
                    <input type="text"
                           class="form-control form-control-sm pe-5"
                           name="q"
                           placeholder="Nhập từ khóa tìm kiếm..."
                           style="border-radius: 25px; padding-right: 45px;">
                    <button type="submit"
                            class="btn btn-danger btn-sm position-absolute end-0 top-50 translate-middle-y me-1"
                            style="border-radius: 20px; padding: 0.25rem 1rem;">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>

            <div class="d-flex align-items-center gap-3">
                <div class="dropdown">
                    <a class="text-white text-decoration-none dropdown-toggle" href="#" id="accountDropdown"
                       data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user me-1"></i>
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="accountDropdown">
                        @auth
                            <li><a class="dropdown-item" href="#">Hồ sơ</a></li>
                            <li><a class="dropdown-item" href="#">Đơn hàng của tôi</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item">Đăng xuất</button>
                                </form>
                            </li>
                        @else
                            <li><a class="dropdown-item" href="{{ route('login') }}">Đăng nhập</a></li>
                            <li><a class="dropdown-item" href="{{ route('register') }}">Đăng ký</a></li>
                        @endauth
                    </ul>
                </div>

                <a href="{{ route('cart.index') }}" class="text-white text-decoration-none position-relative">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="badge bg-danger position-absolute top-0 start-100 translate-middle rounded-pill" id="cart-count">
                        {{ session('cart_count', 0) }}
                    </span>
                </a>
            </div>

            <button class="navbar-toggler ms-2" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
    </nav>

    <div class="main-menu bg-white border-bottom">
        <div class="container">
            <div class="menu-wrapper d-flex align-items-center" id="navbarNav">
                <ul class="navbar-nav d-flex flex-row">
                    <li class="nav-item">
                        <a class="nav-link text-dark fw-semibold" href="{{ route('home') }}">
                            <i class="fas fa-home me-1"></i> Trang chủ
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-dark" href="{{ route('hot-deals') }}">
                            <i class="fas fa-fire me-1"></i> Khuyến mãi
                        </a>
                    </li>
                    <li class="nav-item dropdown mega-dropdown">
                        <a class="nav-link" href="#" id="categoriesDropdown"
                           role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-th-large me-1"></i> Danh mục
                        </a>
                        <div class="dropdown-menu mega-menu p-4" aria-labelledby="categoriesDropdown">
                            <div class="row g-4">
                                @php
                                    use \App\Models\Category;

                                    $categories = Category::with(['children' => function($query) {
                                            $query->limit(5);
                                        }])
                                        ->withCount('products')
                                        ->whereNull('parent_id')
                                        ->orderBy('name')
                                        ->limit(6)
                                        ->get();
                                @endphp

                                @forelse($categories as $category)
                                    <div class="col-md-4">
                                        <div class="category-group">
                                            <h6 class="category-title fw-bold text-danger mb-3">
                                                <a href="#"
                                                   class="text-danger text-decoration-none">
                                                    {{ $category->name }}
                                                    <span class="badge bg-danger-subtle text-danger ms-2">
                                                        {{ $category->products_count }}
                                                    </span>
                                                </a>
                                            </h6>

                                            @if($category->children && $category->children->count() > 0)
                                                <ul class="list-unstyled category-list">
                                                    @foreach($category->children as $child)
                                                        <li class="mb-2">
                                                            <a href="#"
                                                               class="text-muted text-decoration-none category-link">
                                                                <i class="fas fa-angle-right me-2"></i>
                                                                {{ $child->name }}
                                                            </a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12">
                                        <p class="text-muted text-center">Chưa có danh mục</p>
                                    </div>
                                @endforelse
                            </div>

                            <div class="row mt-4 pt-3 border-top">
                                <div class="col-12 text-center">
                                    <a href="{{ route('categories.show', 'all') }}"
                                       class="btn btn-outline-danger btn-sm">
                                        Xem tất cả danh mục <i class="fas fa-arrow-right ms-2"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</header>

<style>
    .main-header .navbar {
        padding: 1rem 0;
    }

    .main-header .navbar-brand {
        font-size: 1.8rem;
    }

    .search-wrapper {
        max-width: 600px;
    }

    .search-wrapper form {
        position: relative;
    }

    .search-wrapper input.form-control {
        border: 2px solid #e9ecef;
        transition: all 0.3s ease;
    }

    .search-wrapper input.form-control:focus {
        border-color: #dc3545;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.15);
    }

    .search-wrapper button {
        height: calc(100% - 4px);
        z-index: 10;
    }

    .main-menu {
        padding: 0.5rem 0;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .main-menu .nav-link {
        padding: 0.75rem 1rem;
        position: relative;
        transition: all 0.3s ease;
    }

    .main-menu .nav-link::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 0;
        height: 2px;
        background: #dc3545;
        transition: width 0.3s ease;
    }

    .main-menu .nav-link:hover {
        color: #dc3545 !important;
    }

    .main-menu .nav-link:hover::after {
        width: 80%;
    }

    /* Mega Menu Styles */
    .mega-dropdown {
        position: static;
    }

    .mega-menu {
        width: 100%;
        left: 0 !important;
        right: 0;
        border: none;
        border-radius: 0;
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        margin-top: 0.5rem;
        animation: slideDown 0.3s ease;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .category-group {
        padding: 1rem;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .category-group:hover {
        background-color: #f8f9fa;
    }

    .category-title {
        font-size: 1rem;
        border-bottom: 2px solid #dc3545;
        padding-bottom: 0.5rem;
        margin-bottom: 1rem !important;
    }

    .category-title a:hover {
        text-decoration: underline !important;
    }

    .category-list {
        padding-left: 0;
    }

    .category-link {
        font-size: 0.9rem;
        transition: all 0.3s ease;
        display: inline-block;
    }

    .category-link:hover {
        color: #dc3545 !important;
        transform: translateX(5px);
    }

    .category-link i {
        color: #dc3545;
        font-size: 0.75rem;
    }

    /* Responsive */
    @media (max-width: 991px) {
        .mega-menu {
            position: absolute !important;
            width: auto !important;
            min-width: 300px;
        }

        .menu-wrapper .navbar-nav {
            flex-direction: column !important;
        }

        .main-menu .nav-link {
            padding: 0.5rem 1rem;
        }
    }
</style>

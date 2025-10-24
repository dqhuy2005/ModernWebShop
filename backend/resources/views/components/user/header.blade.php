<header class="main-header">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark p-0 m-0">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('home') }}">
                <span class="text-danger">MWShop</span><span class="text-warning">.</span>
            </a>

            <div class="search-wrapper d-none d-lg-flex flex-grow-1 mx-4 position-relative">
                <form action="{{ route('products.search') }}" method="GET" class="w-100 position-relative"
                    id="searchForm">
                    <input type="text" class="form-control form-control-sm pe-5" name="q" id="searchInput"
                        placeholder="Nhập từ khóa tìm kiếm..." style="border-radius: 25px; padding-right: 45px;"
                        autocomplete="off">
                    <button type="submit"
                        class="btn btn-danger btn-sm position-absolute end-0 top-50 translate-middle-y me-1"
                        style="border-radius: 20px; padding: 0.25rem 1rem; z-index: 10;">
                        <i class="fas fa-search"></i>
                    </button>
                </form>

                <div id="searchSuggestions" class="search-suggestions-dropdown" style="display: none;">
                    <div class="suggestions-list">
                    </div>
                </div>
            </div>

            <div class="d-flex align-items-center gap-3">
                @auth
                    <div class="dropdown">
                        <a class="text-white text-decoration-none d-flex align-items-center gap-2" href="#"
                            id="accountDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            @if (auth()->user()->image)
                                <img src="{{ asset('storage/' . auth()->user()->image) }}"
                                    alt="{{ auth()->user()->fullname }}" class="rounded-circle"
                                    style="width: 32px; height: 32px; object-fit: cover;">
                            @else
                                <div class="rounded-circle bg-danger d-flex align-items-center justify-content-center"
                                    style="width: 32px; height: 32px;">
                                    <i class="fas fa-user text-white"></i>
                                </div>
                            @endif
                            <span class="d-none d-lg-inline">{{ auth()->user()->fullname }}</span>
                            <i class="fas fa-chevron-down" style="font-size: 0.8rem;"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="accountDropdown">
                            <li><a class="dropdown-item" href="{{ route('profile.index') }}">
                                    <i class="fas fa-user me-2"></i>Thông tin cá nhân
                                </a></li>
                            <li><a class="dropdown-item" href="{{ route('purchase.index') }}">
                                    <i class="fas fa-shopping-bag me-2"></i>Đơn hàng của tôi
                                </a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="fas fa-sign-out-alt me-2"></i>Đăng xuất
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('login') }}" class="text-white text-decoration-none">
                            <span class="d-none d-lg-inline">Đăng nhập</span>
                        </a>
                        <span class="text-white d-none d-lg-inline">|</span>
                        <a href="{{ route('register') }}" class="text-white text-decoration-none">
                            <span class="d-none d-lg-inline">Đăng ký</span>
                        </a>
                    </div>
                @endauth

                <a href="{{ route('cart.index') }}" class="text-white text-decoration-none position-relative">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="badge bg-danger position-absolute top-0 start-100 translate-middle rounded-pill"
                        id="cart-count">
                        {{ session('cart_count', 0) }}
                    </span>
                </a>
            </div>

            <button class="navbar-toggler ms-2" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
    </nav>
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

    .search-suggestions-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        max-height: 400px;
        overflow-y: auto;
        z-index: 1000;
        margin-top: 5px;
    }

    .suggestions-list {
        padding: 0.5rem 0;
    }

    .suggestion-item {
        display: flex;
        align-items: center;
        padding: 0.75rem 1rem;
        cursor: pointer;
        transition: background-color 0.2s ease;
        text-decoration: none;
        color: inherit;
    }

    .suggestion-item:hover {
        background-color: #f8f9fa;
    }

    .suggestion-image {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 6px;
        margin-right: 1rem;
        border: 1px solid #e9ecef;
    }

    .suggestion-info {
        flex: 1;
        min-width: 0;
    }

    .suggestion-name {
        font-size: 0.9rem;
        font-weight: 500;
        color: #202732;
        margin-bottom: 0.25rem;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .suggestion-price {
        font-size: 0.85rem;
        color: #dc3545;
        font-weight: 600;
    }

    .suggestion-empty {
        padding: 1rem;
        text-align: center;
        color: #6c757d;
        font-size: 0.9rem;
    }

    .suggestion-loading {
        padding: 1rem;
        text-align: center;
        color: #6c757d;
        font-size: 0.9rem;
    }

    .main-menu {
        padding: 0.5rem 0;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
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
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
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

    /* User dropdown styles */
    .dropdown-menu {
        animation: slideDown 0.2s ease;
        border: none;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        border-radius: 8px;
        padding: 0.5rem 0;
    }

    .dropdown-item {
        padding: 0.6rem 1.5rem;
        transition: all 0.2s ease;
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

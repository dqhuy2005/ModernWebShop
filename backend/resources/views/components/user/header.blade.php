
<header class="main-header">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('home') }}">
                <span class="text-danger">Electro</span><span class="text-warning">.</span>
            </a>

            <div class="search-wrapper d-none d-lg-flex flex-grow-1 mx-4">
                <form action="{{ route('products.search') }}" method="GET" class="d-flex w-100">
                    <select class="form-select form-select-sm" name="category" style="max-width: 150px;">
                        <option value="">All Categories</option>
                        <option value="laptops">Laptops</option>
                        <option value="smartphones">Smartphones</option>
                        <option value="cameras">Cameras</option>
                        <option value="accessories">Accessories</option>
                    </select>
                    <input type="text" class="form-control form-control-sm mx-2" name="q" placeholder="Search here">
                    <button type="submit" class="btn btn-danger btn-sm">Search</button>
                </form>
            </div>

            <div class="d-flex align-items-center gap-3">
                <div class="dropdown">
                    <a class="text-white text-decoration-none dropdown-toggle" href="#" id="accountDropdown"
                       data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user me-1"></i>
                        <small>My Account</small>
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="accountDropdown">
                        @auth
                            <li><a class="dropdown-item" href="#">Profile</a></li>
                            <li><a class="dropdown-item" href="#">My Orders</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item">Logout</button>
                                </form>
                            </li>
                        @else
                            <li><a class="dropdown-item" href="{{ route('login') }}">Login</a></li>
                            <li><a class="dropdown-item" href="{{ route('register') }}">Register</a></li>
                        @endauth
                    </ul>
                </div>

                <a href="{{ route('wishlist.index') }}" class="text-white text-decoration-none position-relative">
                    <i class="fas fa-heart"></i>
                    <span class="badge bg-danger position-absolute top-0 start-100 translate-middle rounded-pill">
                        3
                    </span>
                    <br><small>Your Wishlist</small>
                </a>

                <a href="{{ route('cart.index') }}" class="text-white text-decoration-none position-relative">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="badge bg-danger position-absolute top-0 start-100 translate-middle rounded-pill">
                        {{ session('cart_count', 0) }}
                    </span>
                    <br><small>Your Cart</small>
                </a>
            </div>

            <button class="navbar-toggler ms-2" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
    </nav>

    <div class="main-menu bg-white border-bottom">
        <div class="container">
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link text-dark fw-semibold" href="{{ route('home') }}">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-dark" href="{{ route('hot-deals') }}">Hot Deals</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-dark" href="{{ route('categories.show', 'laptops') }}">Laptops</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-dark" href="{{ route('categories.show', 'smartphones') }}">Smartphones</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-dark" href="{{ route('categories.show', 'cameras') }}">Cameras</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-dark" href="{{ route('categories.show', 'accessories') }}">Accessories</a>
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

    .main-menu {
        padding: 0.5rem 0;
    }

    .main-menu .nav-link {
        padding: 0.5rem 1rem;
    }

    .main-menu .nav-link:hover {
        color: #dc3545 !important;
    }
</style>

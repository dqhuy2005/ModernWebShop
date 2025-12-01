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
                    <a class="nav-link" href="#" id="categoriesDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-th-large me-1"></i> Danh mục
                    </a>
                    <div class="dropdown-menu mega-menu p-4" aria-labelledby="categoriesDropdown">
                        <div class="row g-4">
                            @php
                                use App\Models\Category;

                                $categories = Category::with([
                                    'children' => function ($query) {
                                        $query->limit(5);
                                    },
                                ])
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
                                            <a href="{{ route('categories.show', $category->slug) }}"
                                                class="text-danger text-decoration-none">
                                                {{ $category->name }}
                                                <span class="badge bg-danger-subtle text-danger ms-2">
                                                    {{ $category->products_count }}
                                                </span>
                                            </a>
                                        </h6>

                                        @if ($category->children && $category->children->count() > 0)
                                            <ul class="list-unstyled category-list">
                                                @foreach ($category->children as $child)
                                                    <li class="mb-2">
                                                        <a href="{{ route('categories.show', $child->slug) }}"
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
                                <a href="{{ route('categories.show', 'all') }}" class="btn btn-outline-danger btn-sm">
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

<section class="carousel-banner">
    <div class="container-fluid px-0">
        <div class="banner-carousel-wrapper">
            <div class="banner-carousel" id="mainBannerCarousel">
                @php
                    $banners = [
                        [
                            'title' => 'Bộ Sưu Tập Laptop Mới',
                            'subtitle' => 'Giảm giá lên đến 30% cho các sản phẩm được chọn',
                            'image' => 'shop01.png',
                        ],
                        [
                            'title' => 'Phụ Kiện Cao Cấp',
                            'subtitle' => 'Nâng cao trải nghiệm công nghệ của bạn',
                            'image' => 'shop02.png',
                        ],
                        [
                            'title' => 'Máy Ảnh Chuyên Nghiệp',
                            'subtitle' => 'Ghi lại mọi khoảnh khắc hoàn hảo',
                            'image' => 'shop03.png',
                        ],
                    ];
                @endphp

                <div class="banner-slides">
                    @foreach ($banners as $index => $banner)
                        <div class="banner-slide {{ $index === 0 ? 'active' : '' }}" data-slide="{{ $index }}">
                            <div class="banner-content" style="background-color: #202732;">
                                <div class="container">
                                    <div class="row align-items-center min-vh-50">
                                        <div class="col-md-6">
                                            <div class="banner-text">
                                                <h1 class="banner-title">{{ $banner['title'] }}</h1>
                                                <p class="banner-subtitle">{{ $banner['subtitle'] }}</p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="banner-image">
                                                <img src="{{ asset('assets/imgs/banner/' . $banner['image']) }}"
                                                    alt="{{ $banner['title'] }}"
                                                    onerror="this.src='{{ asset('assets/imgs/banner/shop01.png') }}'">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <button class="banner-nav banner-prev" id="bannerPrevBtn">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button class="banner-nav banner-next" id="bannerNextBtn">
                    <i class="fas fa-chevron-right"></i>
                </button>

                <div class="banner-indicators" id="bannerIndicators">
                    @foreach ($banners as $index => $banner)
                        <button class="indicator {{ $index === 0 ? 'active' : '' }}" data-slide="{{ $index }}">
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .carousel-banner {
        background-color: #202732;
        position: relative;
    }

    .banner-carousel-wrapper {
        position: relative;
        overflow: hidden;
    }

    .banner-slides {
        position: relative;
        width: 100%;
    }

    .banner-slide {
        display: none;
        width: 100%;
        transition: opacity 0.5s ease;
    }

    .banner-slide.active {
        display: block;
        animation: fadeIn 0.5s ease;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    .min-vh-50 {
        min-height: 500px;
    }

    .banner-text {
        color: #FFFFFF;
        padding: 2rem 0;
    }

    .banner-title {
        font-size: 3rem;
        font-weight: 700;
        margin-bottom: 1rem;
        color: #FFFFFF;
    }

    .banner-subtitle {
        font-size: 1.25rem;
        margin-bottom: 2rem;
        color: #FFFCED;
    }

    .btn-banner {
        display: inline-block;
        padding: 1rem 2.5rem;
        background-color: #FFFCED;
        color: #202732;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        border: 2px solid #FFFCED;
    }

    .btn-banner:hover {
        cursor: pointer;
    }

    .banner-image img {
        max-width: 100%;
        height: auto;
        filter: drop-shadow(0 10px 30px rgba(255, 252, 237, 0.2));
    }

    .banner-nav {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background-color: rgba(255, 252, 237, 0.9);
        color: #202732;
        border: none;
        width: 50px;
        height: 50px;
        cursor: pointer;
        transition: all 0.3s ease;
        z-index: 10;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }

    .banner-nav:hover {
        background-color: #FFFCED;
    }

    .banner-prev {
        left: 20px;
    }

    .banner-next {
        right: 20px;
    }

    .banner-indicators {
        position: absolute;
        bottom: 30px;
        left: 50%;
        transform: translateX(-50%);
        display: flex;
        gap: 10px;
        z-index: 10;
    }

    .indicator {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background-color: rgba(255, 252, 237, 0.5);
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        padding: 0;
    }

    .indicator.active {
        background-color: #fff;
        width: 30px;
        border-radius: 6px;
    }

    @media (min-width: 1200px) {
        .banner-title {
            font-size: clamp(2.5rem, 3vw, 3rem);
        }

        .banner-subtitle {
            font-size: clamp(1.1rem, 1.5vw, 1.25rem);
        }
    }

    @media (max-width: 1199px) and (min-width: 768px) {
        .banner-title {
            font-size: 2.2rem;
        }

        .banner-subtitle {
            font-size: 1.1rem;
        }

        .min-vh-50 {
            min-height: 450px;
        }

        .banner-nav {
            width: 45px;
            height: 45px;
        }
    }

    @media (max-width: 767px) {
        .banner-title {
            font-size: 1.5rem;
        }

        .banner-subtitle {
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
        }

        .min-vh-50 {
            min-height: 350px;
        }

        .banner-nav {
            width: 35px;
            height: 35px;
            font-size: 0.9rem;
        }

        .banner-prev {
            left: 5px;
        }

        .banner-next {
            right: 5px;
        }

        .btn-banner {
            padding: 0.6rem 1.2rem;
            font-size: 0.85rem;
        }

        .banner-text {
            padding: 1rem 0;
        }

        .banner-indicators {
            bottom: 15px;
        }

        .indicator {
            width: 8px;
            height: 8px;
        }

        .indicator.active {
            width: 20px;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let currentBannerSlide = 0;
        const slides = document.querySelectorAll('.banner-slide');
        const indicators = document.querySelectorAll('.banner-indicators .indicator');
        const prevBtn = document.getElementById('bannerPrevBtn');
        const nextBtn = document.getElementById('bannerNextBtn');

        function showBannerSlide(index) {
            slides.forEach(slide => slide.classList.remove('active'));
            indicators.forEach(indicator => indicator.classList.remove('active'));

            if (index >= slides.length) {
                currentBannerSlide = 0;
            } else if (index < 0) {
                currentBannerSlide = slides.length - 1;
            } else {
                currentBannerSlide = index;
            }

            if (slides[currentBannerSlide]) {
                slides[currentBannerSlide].classList.add('active');
            }
            if (indicators[currentBannerSlide]) {
                indicators[currentBannerSlide].classList.add('active');
            }
        }

        function moveBannerSlide(direction) {
            showBannerSlide(currentBannerSlide + direction);
        }

        function goToBannerSlide(index) {
            showBannerSlide(index);
        }

        if (prevBtn) {
            prevBtn.addEventListener('click', function() {
                moveBannerSlide(-1);
            });
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', function() {
                moveBannerSlide(1);
            });
        }

        indicators.forEach(function(indicator, index) {
            indicator.addEventListener('click', function() {
                goToBannerSlide(index);
            });
        });

        let autoPlayInterval = setInterval(function() {
            moveBannerSlide(1);
        }, 5000);

        const carouselWrapper = document.querySelector('.banner-carousel-wrapper');
        if (carouselWrapper) {
            carouselWrapper.addEventListener('mouseenter', function() {
                clearInterval(autoPlayInterval);
            });

            carouselWrapper.addEventListener('mouseleave', function() {
                autoPlayInterval = setInterval(function() {
                    moveBannerSlide(1);
                }, 5000);
            });
        }

        showBannerSlide(0);
    });
</script>

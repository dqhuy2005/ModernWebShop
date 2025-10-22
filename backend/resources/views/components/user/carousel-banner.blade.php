<section class="carousel-banner">
    <div class="container-fluid px-0">
        <div class="banner-carousel-wrapper">
            <div class="banner-carousel" id="mainBannerCarousel">
                @php
                    $banners = [
                        [
                            'title' => 'Bộ Sưu Tập Laptop Mới 2025',
                            'subtitle' => 'Giảm giá lên đến 30% cho các sản phẩm được chọn',
                            'image' => 'shop01.png',
                            'link' => route('categories.show', 'laptops')
                        ],
                        [
                            'title' => 'Phụ Kiện Cao Cấp',
                            'subtitle' => 'Nâng cao trải nghiệm công nghệ của bạn',
                            'image' => 'shop02.png',
                            'link' => route('categories.show', 'accessories')
                        ],
                        [
                            'title' => 'Máy Ảnh Chuyên Nghiệp',
                            'subtitle' => 'Ghi lại mọi khoảnh khắc hoàn hảo',
                            'image' => 'shop03.png',
                            'link' => route('categories.show', 'cameras')
                        ]
                    ];
                @endphp

                <div class="banner-slides">
                    @foreach($banners as $index => $banner)
                        <div class="banner-slide {{ $index === 0 ? 'active' : '' }}" data-slide="{{ $index }}">
                            <div class="banner-content" style="background-color: #202732;">
                                <div class="container">
                                    <div class="row align-items-center min-vh-50">
                                        <div class="col-md-6">
                                            <div class="banner-text">
                                                <h1 class="banner-title">{{ $banner['title'] }}</h1>
                                                <p class="banner-subtitle">{{ $banner['subtitle'] }}</p>
                                                <a href="{{ $banner['link'] }}" class="btn-banner">
                                                    Mua Ngay
                                                </a>
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

                <button class="banner-nav banner-prev" onclick="moveBannerSlide(-1)">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button class="banner-nav banner-next" onclick="moveBannerSlide(1)">
                    <i class="fas fa-chevron-right"></i>
                </button>

                <div class="banner-indicators">
                    @foreach($banners as $index => $banner)
                        <button class="indicator {{ $index === 0 ? 'active' : '' }}"
                                onclick="goToBannerSlide({{ $index }})"
                                data-slide="{{ $index }}">
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
        from { opacity: 0; }
        to { opacity: 1; }
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
        background-color: transparent;
        color: #FFFCED;
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

    @media (max-width: 768px) {
        .banner-title {
            font-size: 2rem;
        }

        .banner-subtitle {
            font-size: 1rem;
        }

        .min-vh-50 {
            min-height: 400px;
        }

        .banner-nav {
            width: 40px;
            height: 40px;
            font-size: 1rem;
        }

        .banner-prev {
            left: 10px;
        }

        .banner-next {
            right: 10px;
        }
    }
</style>

<script>
    let currentBannerSlide = 0;

    function showBannerSlide(index) {
        const slides = document.querySelectorAll('.banner-slide');
        const indicators = document.querySelectorAll('.banner-indicators .indicator');

        slides.forEach(slide => slide.classList.remove('active'));
        indicators.forEach(indicator => indicator.classList.remove('active'));

        if (index >= slides.length) {
            currentBannerSlide = 0;
        } else if (index < 0) {
            currentBannerSlide = slides.length - 1;
        } else {
            currentBannerSlide = index;
        }

        slides[currentBannerSlide].classList.add('active');
        indicators[currentBannerSlide].classList.add('active');
    }

    function moveBannerSlide(direction) {
        showBannerSlide(currentBannerSlide + direction);
    }

    function goToBannerSlide(index) {
        showBannerSlide(index);
    }
</script>

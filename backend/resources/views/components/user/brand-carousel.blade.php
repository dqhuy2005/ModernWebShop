<section class="brand-carousel py-5" style="background-color: #F8F9FA;">
    <div class="container">
        <div class="section-header text-center mb-4">
            <h2 class="section-title">THƯƠNG HIỆU NỔI BẬT</h2>
        </div>

        <div class="brand-carousel-wrapper">
            <button class="brand-carousel-btn brand-prev" onclick="moveBrandSlide(-1)">
                <i class="fas fa-chevron-left"></i>
            </button>

            <div class="brand-track-container">
                <div class="brand-track" id="brandTrack">
                    @php
                        $brands = [
                            ['name' => 'ASUS ROG', 'logo' => 'rog_logo.png'],
                            ['name' => 'ASUS', 'logo' => 'asus_logo.png'],
                            ['name' => 'Lenovo Legion', 'logo' => 'lenovo_logo.png'],
                            ['name' => 'MSI', 'logo' => 'msi_logo.png'],
                            ['name' => 'Dell', 'logo' => 'dell_logo.png'],
                            ['name' => 'Acer', 'logo' => 'acer_logo.png'],
                            ['name' => 'Razer', 'logo' => 'razer_logo.png'],
                            ['name' => 'Gigabyte', 'logo' => 'gigabyte_logo.png'],
                        ];
                    @endphp

                    @foreach ($brands as $brand)
                        <div class="brand-slide">
                            <div class="brand-card">
                                <img src="{{ asset('assets/imgs/thumbnail/' . $brand['logo']) }}"
                                    alt="{{ $brand['name'] }}">
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <button class="brand-carousel-btn brand-next" onclick="moveBrandSlide(1)">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>

        <div class="brand-indicators" id="brandIndicators">
            @php
                $totalSlides = ceil(count($brands) / 6);
            @endphp
            @for ($i = 0; $i < $totalSlides; $i++)
                <span class="brand-indicator {{ $i === 0 ? 'active' : '' }}"
                    onclick="goToBrandSlide({{ $i }})"></span>
            @endfor
        </div>
    </div>
</section>

<style>
    .brand-carousel {
        background-color: #F8F9FA;
    }

    .section-title {
        font-size: 2rem;
        font-weight: 700;
        color: #202732;
        margin-bottom: 0.5rem;
        letter-spacing: 1px;
    }

    .section-subtitle {
        font-size: 1rem;
        color: #6c757d;
        margin-bottom: 0;
    }

    .brand-carousel-wrapper {
        position: relative;
        padding: 0 60px;
    }

    .brand-track-container {
        overflow: hidden;
    }

    .brand-track {
        display: flex;
        gap: 1.5rem;
        transition: transform 0.5s ease;
    }

    .brand-slide {
        flex: 0 0 calc(16.666% - 1.25rem);
        min-width: 0;
    }

    .brand-card {
        background-color: #FFFFFF;
        border: 2px solid #E9ECEF;
        border-radius: 8px;
        padding: 2rem 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        height: 120px;
        transition: all 0.3s ease;
    }

    .brand-card img {
        max-width: 100%;
        max-height: 80px;
        object-fit: contain;
        filter: grayscale(100%);
        opacity: 0.7;
        transition: all 0.3s ease;
    }

    .brand-card:hover img {
        filter: grayscale(0%);
        opacity: 1;
    }

    /* Navigation Buttons */
    .brand-carousel-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 45px;
        height: 45px;
        background-color: #202732;
        border: none;
        border-radius: 50%;
        color: #FFFFFF;
        cursor: pointer;
        z-index: 10;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }

    .brand-carousel-btn:hover {
        background-color: #FFFCED;
        color: #202732;
        box-shadow: 0 4px 12px rgba(32, 39, 50, 0.2);
    }

    .brand-prev {
        left: 0;
    }

    .brand-next {
        right: 0;
    }

    /* Indicators */
    .brand-indicators {
        display: flex;
        gap: 0.5rem;
        justify-content: center;
        margin-top: 2rem;
    }

    .brand-indicator {
        width: 10px;
        height: 10px;
        background-color: #E9ECEF;
        border: 2px solid #202732;
        border-radius: 50%;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .brand-indicator.active {
        background-color: #202732;
        width: 30px;
        border-radius: 5px;
    }

    /* Responsive Design */
    @media (max-width: 1200px) {
        .brand-slide {
            flex: 0 0 calc(20% - 1.2rem);
            /* 5 items per view */
        }
    }

    @media (max-width: 992px) {
        .brand-slide {
            flex: 0 0 calc(25% - 1.125rem);
            /* 4 items per view */
        }

        .section-title {
            font-size: 1.5rem;
        }
    }

    @media (max-width: 768px) {
        .brand-slide {
            flex: 0 0 calc(33.333% - 1rem);
            /* 3 items per view */
        }

        .brand-carousel-wrapper {
            padding: 0 50px;
        }

        .brand-carousel-btn {
            width: 40px;
            height: 40px;
            font-size: 1rem;
        }

        .brand-card {
            height: 100px;
            padding: 1.5rem 0.5rem;
        }
    }

    @media (max-width: 576px) {
        .brand-slide {
            flex: 0 0 calc(50% - 0.75rem);
            /* 2 items per view */
        }

        .brand-track {
            gap: 1rem;
        }

        .section-title {
            font-size: 1.3rem;
        }
    }
</style>

<script>
    let currentBrandSlide = 0;

    function getBrandsPerView() {
        if (window.innerWidth <= 576) return 2;
        if (window.innerWidth <= 768) return 3;
        if (window.innerWidth <= 992) return 4;
        if (window.innerWidth <= 1200) return 5;
        return 6;
    }

    function moveBrandSlide(direction) {
        const track = document.getElementById('brandTrack');
        const slides = track.querySelectorAll('.brand-slide');
        const totalSlides = slides.length;
        const brandsPerView = getBrandsPerView();
        const maxSlide = Math.ceil(totalSlides / brandsPerView) - 1;

        currentBrandSlide += direction;

        // Wrap around
        if (currentBrandSlide < 0) {
            currentBrandSlide = maxSlide;
        } else if (currentBrandSlide > maxSlide) {
            currentBrandSlide = 0;
        }

        showBrandSlide();
    }

    function goToBrandSlide(slideIndex) {
        currentBrandSlide = slideIndex;
        showBrandSlide();
    }

    function showBrandSlide() {
        const track = document.getElementById('brandTrack');
        const indicators = document.querySelectorAll('.brand-indicator');
        const brandsPerView = getBrandsPerView();

        const offset = -currentBrandSlide * 100;
        track.style.transform = `translateX(${offset}%)`;

        // Update indicators
        indicators.forEach((indicator, index) => {
            indicator.classList.toggle('active', index === currentBrandSlide);
        });
    }

    // Recalculate on window resize
    let brandResizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(brandResizeTimer);
        brandResizeTimer = setTimeout(function() {
            currentBrandSlide = 0;
            showBrandSlide();
        }, 250);
    });
</script>

<section class="product-carousel py-5" style="background-color: #FFFFFF;">
    <div class="container">
        <div class="section-header text-center mb-4">
            <h2 class="section-title">SẢN PHẨM NỔI BẬT</h2>
        </div>

        <div class="category-tabs mb-4">
            <div class="category-tabs-wrapper">
                @foreach ($categories ?? [] as $index => $category)
                    <button class="category-tab {{ $index === 0 ? 'active' : '' }}" data-category="{{ $index }}">
                        {{ $category->name }}
                    </button>
                @endforeach
            </div>
        </div>

        @foreach ($categories ?? [] as $categoryIndex => $category)
            <div class="carousel-container {{ $categoryIndex === 0 ? 'active' : '' }}"
                id="carousel-{{ $categoryIndex }}">

                <button class="carousel-btn carousel-prev" data-direction="-1" data-category="{{ $categoryIndex }}">
                    <i class="fas fa-chevron-left"></i>
                </button>

                <div class="carousel-wrapper">
                    <div class="carousel-track" id="track-{{ $categoryIndex }}">
                        @forelse($category->products as $product)
                            <div class="carousel-slide">
                                @include('components.user.product-card', ['product' => $product])
                            </div>
                        @empty
                            @for ($i = 0; $i < 4; $i++)
                                <div class="carousel-slide">
                                    <div class="product-card-empty">
                                        <div class="empty-icon">
                                            <i class="fas fa-box-open"></i>
                                        </div>
                                        <p>Chưa có sản phẩm</p>
                                    </div>
                                </div>
                            @endfor
                        @endforelse
                    </div>
                </div>

                <button class="carousel-btn carousel-next" data-direction="1" data-category="{{ $categoryIndex }}">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        @endforeach
    </div>
</section>

<style>
    .product-carousel {
        background-color: #FFFFFF;
    }

    .section-title {
        font-size: 2rem;
        font-weight: 700;
        color: #202732;
        margin-bottom: 0.5rem;
    }

    .section-subtitle {
        font-size: 1rem;
        color: #6c757d;
    }

    /* Category Tabs */
    .category-tabs {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .category-tabs-wrapper {
        display: flex;
        gap: 1rem;
        justify-content: center;
        flex-wrap: wrap;
    }

    .category-tab {
        padding: 0.75rem 1.5rem;
        background-color: #FFFCED;
        border: 2px solid transparent;
        border-radius: 25px;
        color: #202732;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        white-space: nowrap;
    }

    .category-tab:hover {
        border-color: #202732;
    }

    .category-tab.active {
        background-color: #202732;
        color: #FFFCED;
    }

    .tab-count {
        display: inline-block;
        margin-left: 0.5rem;
        padding: 0.125rem 0.5rem;
        background-color: rgba(255, 252, 237, 0.3);
        border-radius: 12px;
        font-size: 0.875rem;
    }

    .category-tab.active .tab-count {
        background-color: rgba(255, 252, 237, 0.2);
    }

    /* Carousel Container */
    .carousel-container {
        display: none;
        position: relative;
        padding: 2rem 0;
    }

    .carousel-container.active {
        display: block;
    }

    .carousel-wrapper {
        overflow: hidden;
        margin: 0 60px;
    }

    .carousel-track {
        display: flex;
        gap: 1.5rem;
        transition: transform 0.5s ease;
    }

    .carousel-slide {
        flex: 0 0 calc(25% - 1.125rem);
        min-width: 0;
    }

    /* Navigation Buttons */
    .carousel-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 45px;
        height: 45px;
        background-color: #FFFCED;
        border: 2px solid #202732;
        border-radius: 50%;
        color: #202732;
        cursor: pointer;
        z-index: 10;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }

    .carousel-btn:hover {
        background-color: #202732;
        color: #FFFCED;
    }

    .carousel-prev {
        left: 0;
    }

    .carousel-next {
        right: 0;
    }

    /* Empty State */
    .product-card-empty {
        background-color: #FFFCED;
        border-radius: 8px;
        padding: 3rem 1rem;
        text-align: center;
        color: #202732;
    }

    .empty-icon {
        font-size: 3rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }

    /* Responsive Design */
    @media (max-width: 1024px) {
        .carousel-slide {
            flex: 0 0 calc(33.333% - 1rem);
        }
    }

    @media (max-width: 768px) {
        .section-title {
            font-size: 1.5rem;
        }

        .carousel-slide {
            flex: 0 0 calc(50% - 0.75rem);
        }

        .carousel-wrapper {
            margin: 0 50px;
        }

        .carousel-btn {
            width: 40px;
            height: 40px;
            font-size: 1rem;
        }
    }

    @media (max-width: 480px) {
        .carousel-slide {
            flex: 0 0 100%;
        }

        .carousel-track {
            gap: 1rem;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const currentProductSlides = {};

        @foreach ($categories ?? [] as $index => $category)
            currentProductSlides[{{ $index }}] = 0;
        @endforeach

        const categoryTabs = document.querySelectorAll('.category-tab');
        const carouselContainers = document.querySelectorAll('.carousel-container');

        function getSlidesPerView() {
            const width = window.innerWidth;
            if (width <= 480) return 1;
            if (width <= 768) return 2;
            if (width <= 1024) return 3;
            return 4;
        }

        function switchCategory(categoryIndex) {
            categoryTabs.forEach(tab => tab.classList.remove('active'));
            carouselContainers.forEach(container => container.classList.remove('active'));

            if (categoryTabs[categoryIndex]) {
                categoryTabs[categoryIndex].classList.add('active');
            }
            const targetContainer = document.getElementById('carousel-' + categoryIndex);
            if (targetContainer) {
                targetContainer.classList.add('active');
            }
        }

        function moveProductSlide(direction, categoryIndex) {
            const track = document.getElementById('track-' + categoryIndex);
            if (!track) return;

            const slides = track.querySelectorAll('.carousel-slide');
            const totalSlides = slides.length;
            const slidesPerView = getSlidesPerView();
            const maxSlide = Math.ceil(totalSlides / slidesPerView) - 1;

            currentProductSlides[categoryIndex] += direction;

            if (currentProductSlides[categoryIndex] < 0) {
                currentProductSlides[categoryIndex] = maxSlide;
            } else if (currentProductSlides[categoryIndex] > maxSlide) {
                currentProductSlides[categoryIndex] = 0;
            }

            showProductSlide(categoryIndex);
        }

        function showProductSlide(categoryIndex) {
            const track = document.getElementById('track-' + categoryIndex);
            if (!track) return;

            const slidesPerView = getSlidesPerView();
            const offset = -currentProductSlides[categoryIndex] * 100;

            track.style.transform = `translateX(${offset}%)`;
        }

        categoryTabs.forEach((tab, index) => {
            tab.addEventListener('click', function() {
                const categoryIndex = parseInt(this.getAttribute('data-category'));
                switchCategory(categoryIndex);
            });
        });

        const carouselButtons = document.querySelectorAll('.carousel-btn');
        carouselButtons.forEach(button => {
            button.addEventListener('click', function() {
                const direction = parseInt(this.getAttribute('data-direction'));
                const categoryIndex = parseInt(this.getAttribute('data-category'));
                moveProductSlide(direction, categoryIndex);
            });
        });

        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                @foreach ($categories ?? [] as $index => $category)
                    showProductSlide({{ $index }});
                @endforeach
            }, 250);
        });

        @foreach ($categories ?? [] as $index => $category)
            showProductSlide({{ $index }});
        @endforeach
    });
</script>

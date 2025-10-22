<section class="product-carousel py-5" style="background-color: #FFFFFF;">
    <div class="container">
        <div class="section-header text-center mb-4">
            <h2 class="section-title">SẢN PHẨM NỔI BẬT</h2>
        </div>

        <div class="category-tabs mb-4">
            <div class="category-tabs-wrapper">
                @foreach($categories ?? [] as $index => $category)
                    <button class="category-tab {{ $index === 0 ? 'active' : '' }}"
                            onclick="switchCategory({{ $index }})">
                        {{ $category->name }}
                        <span class="tab-count">{{ $category->products->count() }}</span>
                    </button>
                @endforeach
            </div>
        </div>

        @foreach($categories ?? [] as $categoryIndex => $category)
            <div class="carousel-container {{ $categoryIndex === 0 ? 'active' : '' }}"
                 id="carousel-{{ $categoryIndex }}">

                <button class="carousel-btn carousel-prev"
                        onclick="moveProductSlide(-1, {{ $categoryIndex }})">
                    <i class="fas fa-chevron-left"></i>
                </button>

                <div class="carousel-wrapper">
                    <div class="carousel-track" id="track-{{ $categoryIndex }}">
                        @forelse($category->products as $product)
                            <div class="carousel-slide">
                                @include('components.user.product-card', ['product' => $product])
                            </div>
                        @empty
                            @for($i = 0; $i < 4; $i++)
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

                <button class="carousel-btn carousel-next"
                        onclick="moveProductSlide(1, {{ $categoryIndex }})">
                    <i class="fas fa-chevron-right"></i>
                </button>

                @if($category->products->count() > 0)
                    <div class="carousel-indicators">
                        @php
                            $totalSlides = ceil($category->products->count() / 4);
                        @endphp
                        @for($i = 0; $i < $totalSlides; $i++)
                            <span class="indicator {{ $i === 0 ? 'active' : '' }}"
                                  onclick="goToProductSlide({{ $i }}, {{ $categoryIndex }})"></span>
                        @endfor
                    </div>
                @endif
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

    /* Indicators */
    .carousel-indicators {
        display: flex;
        gap: 0.5rem;
        justify-content: center;
        margin-top: 2rem;
    }

    .indicator {
        width: 10px;
        height: 10px;
        background-color: #FFFCED;
        border: 2px solid #202732;
        border-radius: 50%;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .indicator.active {
        background-color: #fff;
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
    // Track current slide for each carousel
    let currentProductSlides = {};

    // Initialize all carousels at slide 0
    @foreach($categories ?? [] as $index => $category)
        currentProductSlides[{{ $index }}] = 0;
    @endforeach

    // Switch category tab
    function switchCategory(categoryIndex) {
        // Remove active class from all tabs and carousels
        document.querySelectorAll('.category-tab').forEach(tab => tab.classList.remove('active'));
        document.querySelectorAll('.carousel-container').forEach(carousel => carousel.classList.remove('active'));

        // Add active class to selected tab and carousel
        document.querySelectorAll('.category-tab')[categoryIndex].classList.add('active');
        document.getElementById('carousel-' + categoryIndex).classList.add('active');
    }

    // Move product slide
    function moveProductSlide(direction, categoryIndex) {
        const track = document.getElementById('track-' + categoryIndex);
        const slides = track.querySelectorAll('.carousel-slide');
        const totalSlides = slides.length;

        // Calculate slides per view based on screen width
        let slidesPerView = 4;
        if (window.innerWidth <= 480) slidesPerView = 1;
        else if (window.innerWidth <= 768) slidesPerView = 2;
        else if (window.innerWidth <= 1024) slidesPerView = 3;

        const maxSlide = Math.ceil(totalSlides / slidesPerView) - 1;

        // Update current slide
        currentProductSlides[categoryIndex] += direction;

        // Wrap around
        if (currentProductSlides[categoryIndex] < 0) {
            currentProductSlides[categoryIndex] = maxSlide;
        } else if (currentProductSlides[categoryIndex] > maxSlide) {
            currentProductSlides[categoryIndex] = 0;
        }

        showProductSlide(categoryIndex);
    }

    // Go to specific slide
    function goToProductSlide(slideIndex, categoryIndex) {
        currentProductSlides[categoryIndex] = slideIndex;
        showProductSlide(categoryIndex);
    }

    // Show product slide
    function showProductSlide(categoryIndex) {
        const track = document.getElementById('track-' + categoryIndex);
        const indicators = document.querySelectorAll(`#carousel-${categoryIndex} .indicator`);

        // Calculate slides per view
        let slidesPerView = 4;
        if (window.innerWidth <= 480) slidesPerView = 1;
        else if (window.innerWidth <= 768) slidesPerView = 2;
        else if (window.innerWidth <= 1024) slidesPerView = 3;

        const slideWidth = 100 / slidesPerView;
        const offset = -currentProductSlides[categoryIndex] * 100;

        track.style.transform = `translateX(${offset}%)`;

        // Update indicators
        indicators.forEach((indicator, index) => {
            indicator.classList.toggle('active', index === currentProductSlides[categoryIndex]);
        });
    }

    // Recalculate on window resize
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            @foreach($categories ?? [] as $index => $category)
                showProductSlide({{ $index }});
            @endforeach
        }, 250);
    });
</script>

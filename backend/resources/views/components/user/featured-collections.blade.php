<section class="featured-collections py-5 bg-light">
    <div class="container">
        <div class="section-header text-center mb-5">
            <h2 class="fw-bold mb-2">Featured Collections</h2>
            <p class="text-muted">Explore our curated collections of premium products</p>
        </div>

        <div class="row g-4">
            @php
                $collections = [
                    [
                        'title' => 'Bộ sưu tập máy tính xách tay',
                        'subtitle' => 'Hiệu năng vượt trội',
                        'slug' => 'laptops',
                        'image' => 'shop01.png',
                        'gradient' => 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                        'icon' => 'fa-laptop',
                    ],
                    [
                        'title' => 'Bộ sưu tập phụ kiện',
                        'subtitle' => 'Hoàn thiện trải nghiệm',
                        'slug' => 'accessories',
                        'image' => 'shop02.png',
                        'gradient' => 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
                        'icon' => 'fa-headphones',
                    ],
                    [
                        'title' => 'Bộ sưu tập máy ảnh',
                        'subtitle' => 'Lưu giữ khoảnh khắc',
                        'slug' => 'cameras',
                        'image' => 'shop03.png',
                        'gradient' => 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)',
                        'icon' => 'fa-camera',
                    ],
                ];
            @endphp

            @foreach ($collections as $collection)
                <div class="col-md-4">
                    <div class="collection-card position-relative overflow-hidden rounded-3 shadow-sm">
                        <div class="collection-content"
                            style="background: {{ $collection['gradient'] }}; min-height: 280px;">
                            <div class="row g-0 h-100 align-items-center">
                                <div class="col-7 p-4">
                                    <div class="collection-icon mb-3">
                                        <i class="fas {{ $collection['icon'] }} fa-3x text-white opacity-75"></i>
                                    </div>
                                    <h4 class="fw-bold text-white mb-2">{{ $collection['title'] }}</h4>
                                    <p class="text-white-50 small mb-3">{{ $collection['subtitle'] }}</p>
                                    <a href="{{ route('categories.show', $collection['slug']) }}"
                                        class="btn btn-light btn-sm rounded-pill px-4 fw-semibold">
                                        MUA NGAY <i class="fas fa-arrow-right ms-2"></i>
                                    </a>
                                </div>
                                <div class="col-5">
                                    <div class="collection-image p-3">
                                        <img src="{{ asset('assets/imgs/banner/' . $collection['image']) }}"
                                            alt="{{ $collection['title'] }}" class="img-fluid"
                                            style="filter: drop-shadow(0 10px 20px rgba(0,0,0,0.2));"
                                            onerror="this.src='{{ asset('assets/imgs/banner/shop01.png') }}'">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="collection-overlay"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<style>
    .featured-collections {
        position: relative;
    }

    .section-header h2 {
        font-size: 2.5rem;
        color: #2d3748;
    }

    .collection-card {
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        border: none;
        position: relative;
    }

    .collection-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0) 100%);
        opacity: 0;
        transition: opacity 0.4s ease;
        border-radius: 1rem;
        z-index: 1;
    }

    .collection-card:hover::before {
        opacity: 1;
    }

    .collection-card:hover {
        transform: translateY(-10px) scale(1.02);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15) !important;
    }

    .collection-content {
        border-radius: 1rem;
        position: relative;
        overflow: hidden;
    }

    .collection-icon {
        animation: float 3s ease-in-out infinite;
    }

    @keyframes float {

        0%,
        100% {
            transform: translateY(0px);
        }

        50% {
            transform: translateY(-10px);
        }
    }

    .collection-image {
        position: relative;
        z-index: 2;
    }

    .collection-image img {
        transition: transform 0.4s ease;
        max-height: 200px;
        width: auto;
        margin: 0 auto;
        display: block;
    }

    .collection-card:hover .collection-image img {
        transform: scale(1.1) rotate(3deg);
    }

    .btn-light {
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }

    .btn-light:hover {
        background-color: white !important;
        color: #dc3545 !important;
        border-color: white;
        transform: translateX(5px);
    }

    .collection-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: radial-gradient(circle at top right, rgba(255, 255, 255, 0.2) 0%, transparent 60%);
        pointer-events: none;
        border-radius: 1rem;
    }

    @media (max-width: 768px) {
        .section-header h2 {
            font-size: 1.8rem;
        }

        .collection-content {
            min-height: 220px !important;
        }

        .collection-icon i {
            font-size: 2rem !important;
        }
    }
</style>

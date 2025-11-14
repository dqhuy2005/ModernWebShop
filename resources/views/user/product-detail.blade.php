@extends('layouts.user.app')

@section('title', $product->name)

@section('content')
    <div class="container py-4 product-detail-page">
        <div class="row mb-4">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent px-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                        @if ($product->category)
                            <li class="breadcrumb-item"><a
                                    href="{{ route('categories.show', $product->category->slug) }}">{{ $product->category->name }}</a>
                            </li>
                        @endif
                        <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <div class="product-main-card bg-white rounded shadow-sm">
                    <div class="row g-0">
                        <div class="col-lg-6 product-images-section">
                            <div class="pw-product-gallery p-4">
                                @php
                                    $images = [];
                                    $hasImages = false;

                                    if ($product->relationLoaded('images') && $product->images->isNotEmpty()) {
                                        $images = $product->images
                                            ->map(function ($img) {
                                                return str_starts_with($img->path, 'http')
                                                    ? $img->path
                                                    : asset('storage/' . $img->path);
                                            })
                                            ->values()
                                            ->all();
                                    }

                                    $hasImages = !empty($images) && $images[0] != asset('storage/');
                                @endphp

                                @if ($hasImages)
                                    <div class="pw-main-image-wrapper mb-3">
                                        <div id="pwCarousel" class="carousel slide pw-carousel" data-bs-ride="false">
                                            <div class="carousel-inner pw-carousel-inner">
                                                @foreach ($images as $index => $img)
                                                    <div
                                                        class="carousel-item {{ $index == 0 ? 'active' : '' }} pw-carousel-item">
                                                        <img src="{{ $img }}" class="d-block w-100 pw-main-img"
                                                            alt="{{ $product->name }}" loading="lazy">
                                                    </div>
                                                @endforeach

                                                @if (count($images) > 1)
                                                    <button class="carousel-control-prev" type="button"
                                                        data-bs-target="#pwCarousel" data-bs-slide="prev">
                                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                                        <span class="visually-hidden">Previous</span>
                                                    </button>
                                                    <button class="carousel-control-next" type="button"
                                                        data-bs-target="#pwCarousel" data-bs-slide="next">
                                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                                        <span class="visually-hidden">Next</span>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    @if (count($images) > 1)
                                        <div class="pw-thumbnails-wrapper">
                                            <div class="pw-thumbs d-flex gap-2">
                                                @foreach ($images as $index => $img)
                                                    <div class="pw-thumb-item {{ $index == 0 ? 'active' : '' }}"
                                                        data-index="{{ $index }}">
                                                        <img src="{{ $img }}" alt="thumb-{{ $index }}"
                                                            class="img-thumbnail pw-thumb-img" loading="lazy">
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                @else
                                    <div class="image-skeleton">
                                        <div class="skeleton-main-image mb-3"></div>
                                        <div class="d-flex gap-2">
                                            <div class="skeleton-thumb"></div>
                                            <div class="skeleton-thumb"></div>
                                            <div class="skeleton-thumb"></div>
                                            <div class="skeleton-thumb"></div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="col-lg-6 product-info-section">
                            <div class="pw-product-info p-4">
                                <div class="d-flex align-items-start justify-content-between mb-2">
                                    <div>
                                        <h1 class="pw-product-title mb-0">{{ $product->name }}</h1>
                                        <div class="pw-rate mt-2">
                                            @if ($reviewStats['total_reviews'] > 0)
                                                <span class="text-warning fs-5">
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        @if ($i <= floor($reviewStats['average_rating']))
                                                            ★
                                                        @elseif($i - 0.5 <= $reviewStats['average_rating'])
                                                            <span style="position: relative; display: inline-block;">
                                                                <span style="color: #ddd;">★</span>
                                                                <span
                                                                    style="position: absolute; left: 0; overflow: hidden; width: 50%; color: #ffc107;">★</span>
                                                            </span>
                                                        @else
                                                            <span style="color: #ddd;">★</span>
                                                        @endif
                                                    @endfor
                                                </span>
                                                <small class="text-muted ms-2">
                                                    ({{ $reviewStats['total_reviews'] }} đánh giá)
                                                </small>
                                            @else
                                                <span class="text-muted fs-5">☆☆☆☆☆</span>
                                                <small class="text-muted ms-2">(0 đánh giá)</small>
                                            @endif
                                        </div>
                                    </div>
                                    @if ($product->is_hot)
                                        <span class="badge bg-danger ms-2 align-self-start">
                                            <i class="bi bi-fire"></i> HOT
                                        </span>
                                    @endif
                                </div>

                                <div class="pw-product-price-large mb-3">{{ $product->formatted_price }}</div>

                                <div class="mb-3">
                                    <button class="btn btn-lg btn-primary add-to-cart-btn"
                                        data-product-id="{{ $product->id }}">
                                        <i class="bi bi-cart-plus me-2"></i>Thêm giỏ hàng
                                    </button>
                                </div>

                                <div class="pw-short-desc text-muted small">
                                    @if ($product->description)
                                        {{ Str::limit(strip_tags($product->description), 220) }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4 g-3">
                <div class="col-lg-6">
                    <div class="card pw-specs-card h-100">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="bi bi-list-check"></i> Thông số kỹ thuật:
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            @php
                                $specs = $product->specifications;
                                if (is_string($specs)) {
                                    $specs = json_decode($specs, true);
                                }
                            @endphp

                            @if (!empty($specs) && is_array($specs))
                                <table class="table table-hover pw-specs-table mb-0">
                                    <tbody>
                                        @foreach ($specs as $key => $val)
                                            <tr>
                                                <th class="pw-spec-label">
                                                    {{ is_string($key) ? $key : 'Thuộc tính' }}
                                                </th>
                                                <td class="pw-spec-value">
                                                    {{ is_array($val) ? implode(', ', $val) : $val }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <div class="p-4 text-center text-muted">
                                    <i class="bi bi-info-circle fs-3 d-block mb-2"></i>
                                    <p class="mb-0">Chưa có thông số kỹ thuật cho sản phẩm này.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card pw-desc-card h-100">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="bi bi-file-text"></i> Mô tả sản phẩm
                            </h5>
                        </div>
                        <div class="card-body pw-product-desc">
                            {!! $product->description ??
                                '<div class="text-center text-muted"><i class="bi bi-info-circle fs-3 d-block mb-2"></i><p class="mb-0">Chưa có mô tả.</p></div>' !!}
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-12">
                    <div class="card pw-reviews-card">
                        <div class="card-header bg-white">
                            <h5 class="mb-0 fw-bold">
                                Đánh giá & Nhận xét {{ $product->name }}
                            </h5>
                        </div>
                        <div class="card-body">
                            @if ($reviewStats['total_reviews'] > 0)
                                <div class="review-summary-compact pb-3">
                                    <div class="d-flex align-items-center gap-2 mb-3">
                                        <span class="display-6 fw-bold text-dark mb-0">
                                            {{ number_format($reviewStats['average_rating'], 2) }}
                                        </span>
                                        <div class="d-flex flex-column">
                                            <div class="stars-inline">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    @if ($i <= floor($reviewStats['average_rating']))
                                                        <i class="bi bi-star-fill text-warning"></i>
                                                    @elseif($i - 0.5 <= $reviewStats['average_rating'])
                                                        <i class="bi bi-star-half text-warning"></i>
                                                    @else
                                                        <i class="bi bi-star text-warning"></i>
                                                    @endif
                                                @endfor
                                            </div>
                                            <small class="text-muted">({{ $reviewStats['total_reviews'] }} đánh
                                                giá)</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="reviews-list">
                                    @foreach ($reviews as $review)
                                        <div class="review-item pb-3 mb-3">
                                            <div class="d-flex align-items-start gap-3">
                                                <div class="review-avatar flex-shrink-0">
                                                    @if ($review->user->avatar)
                                                        <img src="{{ asset('storage/' . $review->user->avatar) }}"
                                                            alt="{{ $review->user->fullname }}" class="rounded-circle"
                                                            style="width: 40px; height: 40px; object-fit: cover;">
                                                    @else
                                                        <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center"
                                                            style="width: 40px; height: 40px; font-size: 16px;">
                                                            <strong>{{ strtoupper(substr($review->user->fullname, 0, 1)) }}</strong>
                                                        </div>
                                                    @endif
                                                </div>

                                                <div class="flex-grow-1">
                                                    <div class="d-flex align-items-center gap-2 mb-1">
                                                        <span
                                                            class="fw-semibold text-dark">{{ $review->user->fullname ?? substr($review->user->email, 0, 2) . '******' . substr($review->user->email, -2) }}</span>
                                                        <span class="text-muted">|</span>
                                                        <small
                                                            class="text-muted">{{ $review->created_at->format('d-m-Y H:i:s') }}</small>
                                                        @if ($review->order_id)
                                                            <span class="text-muted">|</span>
                                                            <small class="text-success">
                                                                <i class="bi bi-check-circle-fill"></i> Đã mua hàng
                                                            </small>
                                                        @endif
                                                    </div>

                                                    <div class="review-stars text-warning mb-2">
                                                        @for ($i = 1; $i <= 5; $i++)
                                                            @if ($i <= $review->rating)
                                                                <i class="bi bi-star-fill"></i>
                                                            @else
                                                                <i class="bi bi-star"></i>
                                                            @endif
                                                        @endfor
                                                    </div>

                                                    @if ($review->title)
                                                        <h6 class="review-title mb-2 fw-semibold">{{ $review->title }}
                                                        </h6>
                                                    @endif

                                                    <p class="review-comment mb-2 text-dark">{{ $review->comment }}</p>

                                                    @if ($review->images && count($review->images) > 0)
                                                        <div class="review-images d-flex gap-2 mb-2 flex-wrap">
                                                            @foreach ($review->images as $image)
                                                                <a href="{{ asset('storage/' . $image) }}"
                                                                    data-lightbox="review-{{ $review->id }}"
                                                                    class="review-image-thumb">
                                                                    <img src="{{ asset('storage/' . $image) }}"
                                                                        alt="Review image" class="rounded"
                                                                        style="width: 80px; height: 80px; object-fit: cover; cursor: pointer;">
                                                                </a>
                                                            @endforeach
                                                        </div>
                                                    @endif

                                                    @if ($review->videos && count($review->videos) > 0)
                                                        <div class="review-videos d-flex gap-2 mb-2 flex-wrap">
                                                            @foreach ($review->videos as $video)
                                                                <video width="120" height="120" controls
                                                                    class="rounded">
                                                                    <source src="{{ asset('storage/' . $video) }}"
                                                                        type="video/mp4">
                                                                    Your browser does not support the video tag.
                                                                </video>
                                                            @endforeach
                                                        </div>
                                                    @endif

                                                    @if ($review->admin_reply)
                                                        <div class="admin-reply mt-3 p-3 bg-light rounded">
                                                            <strong class="text-primary">
                                                                <i class="bi bi-person-badge"></i> Phản hồi từ người bán:
                                                            </strong>
                                                            <p class="mb-0 mt-2">{{ $review->admin_reply }}</p>
                                                            @if ($review->admin_reply_at)
                                                                <small
                                                                    class="text-muted">{{ $review->admin_reply_at->format('d/m/Y H:i') }}</small>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                @if ($reviews->hasPages())
                                    <div class="d-flex justify-content-center mt-4">
                                        {{ $reviews->links() }}
                                    </div>
                                @endif
                            @else
                                <div class="review-summary-compact pb-3">
                                    <div class="d-flex align-items-center gap-2 mb-3">
                                        <span class="display-6 fw-bold text-dark mb-0">
                                            {{ number_format($reviewStats['average_rating'], 2) }}
                                        </span>
                                        <div class="d-flex flex-column">
                                            <div class="stars-inline">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    @if ($i <= floor($reviewStats['average_rating']))
                                                        <i class="bi bi-star-fill text-warning"></i>
                                                    @elseif($i - 0.5 <= $reviewStats['average_rating'])
                                                        <i class="bi bi-star-half text-warning"></i>
                                                    @else
                                                        <i class="bi bi-star text-warning"></i>
                                                    @endif
                                                @endfor
                                            </div>
                                            <small class="text-muted">({{ $reviewStats['total_reviews'] }} đánh
                                                giá)</small>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @push('styles')
        <style>
            .product-detail-page {
                color: #23262b;
            }

            /* Main Product Card */
            .product-main-card {
                overflow: hidden;
            }

            @media (max-width: 991px) {
                .product-images-section {
                    border-right: none;
                    border-bottom: 1px solid #e5e7eb;
                }
            }

            /* Image Skeleton Loading */
            .image-skeleton {
                padding: 1rem;
            }

            .skeleton-main-image {
                width: 100%;
                height: 360px;
                background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
                background-size: 200% 100%;
                animation: loading 1.5s infinite;
                border-radius: 8px;
            }

            .skeleton-thumb {
                width: 80px;
                height: 60px;
                background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
                background-size: 200% 100%;
                animation: loading 1.5s infinite;
                border-radius: 4px;
            }

            @keyframes loading {
                0% {
                    background-position: 200% 0;
                }

                100% {
                    background-position: -200% 0;
                }
            }

            .badge.bg-danger {
                font-size: 0.85rem;
                padding: 0.4rem 0.6rem;
            }

            .pw-product-gallery {
                background: transparent;
            }

            .pw-main-image-wrapper {
                position: relative;
                background: #f8f9fa;
                border-radius: 8px;
                overflow: hidden;
            }

            .pw-carousel-inner {
                position: relative;
            }

            .pw-carousel-inner .carousel-control-prev,
            .pw-carousel-inner .carousel-control-next {
                width: 40px;
                height: 40px;
                background-color: rgba(0, 0, 0, 0.5);
                border-radius: 50%;
                top: 50%;
                transform: translateY(-50%);
                opacity: 0.8;
            }

            .pw-carousel-inner .carousel-control-prev {
                left: 10px;
            }

            .pw-carousel-inner .carousel-control-next {
                right: 10px;
            }

            .pw-carousel-inner .carousel-control-prev:hover,
            .pw-carousel-inner .carousel-control-next:hover {
                opacity: 1;
            }

            .pw-main-img {
                object-fit: contain;
                height: 360px;
                width: 100%;
                background: #fff;
            }

            .pw-thumbnails-wrapper {
                overflow-x: auto;
                padding: 8px 0;
            }

            .pw-thumbs {
                min-width: min-content;
            }

            .pw-thumb-item {
                flex-shrink: 0;
                cursor: pointer;
                transition: all 0.2s;
            }

            .pw-thumb-img {
                width: 80px;
                height: 60px;
                object-fit: cover;
                border: 2px solid #dee2e6;
                border-radius: 4px;
                background: #fff;
            }

            .pw-thumb-item:hover .pw-thumb-img {
                border-color: #adb5bd;
            }

            .pw-thumb-item.active .pw-thumb-img {
                border-color: #dc3545;
            }

            .pw-product-title {
                font-size: 1.5rem;
                font-weight: 700;
            }

            .pw-product-price-large {
                font-size: 1.35rem;
                color: #d6336c;
                font-weight: 700;
            }

            /* Specifications Card Styling */
            .pw-specs-card,
            .pw-desc-card {
                border: none;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
                border-radius: 8px;
                overflow: hidden;
                background-color: #fff;
            }

            .pw-specs-card .card-header,
            .pw-desc-card .card-header {
                padding: 1rem 1.25rem;
                border-bottom: 2px solid rgba(0, 0, 0, 0.05);
                background-color: #fff;
            }

            .pw-specs-table {
                font-size: 0.9rem;
            }

            .pw-specs-table tbody tr {
                border-bottom: 1px solid #e5e7eb;
            }

            .pw-specs-table tbody tr:hover {
                background-color: #f9fafb;
            }

            .pw-spec-label {
                width: 40%;
                padding: 0.95rem 1.25rem;
                font-weight: 600;
                color: #374151;
                background-color: #f8f9fa;
                border-right: 2px solid #dee2e6;
                vertical-align: middle;
            }

            .pw-spec-value {
                padding: 0.95rem 1.25rem;
                color: #1f2937;
                background-color: white;
                vertical-align: middle;
            }

            .pw-product-desc {
                line-height: 1.7;
                color: #4b5563;
            }

            .pw-product-desc h1,
            .pw-product-desc h2,
            .pw-product-desc h3,
            .pw-product-desc h4 {
                margin-top: 1.5rem;
                margin-bottom: 0.75rem;
                color: #1f2937;
                font-weight: 600;
            }

            .pw-product-desc p {
                margin-bottom: 1rem;
            }

            .pw-product-desc ul,
            .pw-product-desc ol {
                padding-left: 1.5rem;
                margin-bottom: 1rem;
            }

            /* Reviews Section Styling */
            .pw-reviews-card {
                border: none;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
                border-radius: 8px;
                overflow: hidden;
                background-color: #fff;
            }

            .pw-reviews-card .card-header {
                padding: 1rem 1.25rem;
                background-color: #fff;
                border-bottom: 1px solid #e5e7eb;
            }

            .review-summary-box {
                background-color: #f8f9fa;
                border: 1px solid #e0e0e0;
                border-radius: 8px;
                padding: 1.5rem;
            }

            .stars-inline i {
                font-size: 1rem;
            }

            .review-item {
                border-bottom: 1px solid #e9ecef;
            }

            .review-item:last-child {
                border-bottom: none;
            }

            .review-avatar {
                width: 40px;
                height: 40px;
            }

            .review-title {
                font-weight: 600;
                color: #333;
                font-size: 0.95rem;
            }

            .review-comment {
                color: #333;
                line-height: 1.6;
                font-size: 0.9rem;
            }

            .review-stars {
                font-size: 0.95rem;
            }

            .review-image-thumb img {
                transition: transform 0.2s;
                border: 1px solid #dee2e6;
            }

            .review-image-thumb:hover img {
                transform: scale(1.05);
                border-color: #adb5bd;
            }

            .admin-reply {
                border-left: 3px solid #0d6efd;
                background-color: #f8f9fa !important;
            }

            @media(max-width:767px) {
                .pw-main-img {
                    height: 320px;
                }

                .pw-thumb-img {
                    width: 60px;
                    height: 48px;
                }

                .pw-spec-label {
                    width: 35%;
                    font-size: 0.85rem;
                    padding: 0.75rem 1rem;
                }

                .pw-spec-value {
                    font-size: 0.85rem;
                    padding: 0.75rem 1rem;
                }

                .pw-specs-card .card-header h5,
                .pw-desc-card .card-header h5 {
                    font-size: 1rem;
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            (function($) {
                $(function() {
                    var $carousel = $('#pwCarousel');
                    if ($carousel.length) {
                        $carousel.on('slid.bs.carousel', function(e) {
                            var index = $(e.relatedTarget).index();
                            $('.pw-thumb-item').removeClass('active');
                            $('.pw-thumb-item[data-index="' + index + '"]').addClass('active');
                        });
                    }

                    $('.pw-thumb-item').on('click', function() {
                        var idx = $(this).data('index');
                        if ($carousel.length && typeof bootstrap !== 'undefined') {
                            var bsCarousel = bootstrap.Carousel.getInstance($carousel[0]) || new bootstrap
                                .Carousel($carousel[0], {
                                    ride: false
                                });
                            bsCarousel.to(idx);
                        } else {
                            $('.pw-carousel .carousel-item').removeClass('active').eq(idx).addClass(
                                'active');
                        }

                        $('.pw-thumb-item').removeClass('active');
                        $(this).addClass('active');
                    });

                    $('.pw-thumb-item').first().addClass('active');
                });
            })(jQuery);
        </script>
    @endpush

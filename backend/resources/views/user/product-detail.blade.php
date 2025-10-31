@extends('layouts.user.app')

@section('title', $product->name)

@section('content')
    <div class="container py-5 product-detail-page">
        <div class="row mb-4">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent px-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                        @if ($product->category)
                            <li class="breadcrumb-item"><a
                                    href="{{ route('categories.show', $product->category->name) }}">{{ $product->category->name }}</a>
                            </li>
                        @endif
                        <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row product-detail-top g-4">
            <div class="col-lg-6">
                <div class="pw-product-gallery">
                    @php
                        // Prefer product_images relationship if present
                        $images = [];
                        if ($product->relationLoaded('images') || $product->images()->exists()) {
                            $images = $product->images->map(function ($img) {
                                return str_starts_with($img->path, 'http') ? $img->path : asset('storage/' . $img->path);
                            })->values()->all();
                        } else {
                            if (is_array($product->image)) {
                                $images = $product->image;
                            } else {
                                $decoded = @json_decode($product->image, true);
                                if (is_array($decoded)) {
                                    $images = $decoded;
                                } else {
                                    $images = [$product->image ?: 'assets/imgs/products/default.png'];
                                }
                            }

                            $images = collect($images)
                                ->map(function ($i) {
                                    return str_starts_with($i, 'http') ? $i : asset('storage/' . $i);
                                })
                                ->values()
                                ->all();
                        }
                    @endphp

                    {{-- Main Image Carousel --}}
                    <div class="pw-main-image-wrapper mb-3">
                        <div id="pwCarousel" class="carousel slide pw-carousel" data-bs-ride="false">
                            <div class="carousel-inner pw-carousel-inner">
                                @foreach ($images as $index => $img)
                                    <div class="carousel-item {{ $index == 0 ? 'active' : '' }} pw-carousel-item">
                                        <img src="{{ $img }}" class="d-block w-100 pw-main-img"
                                            alt="{{ $product->name }}">
                                    </div>
                                @endforeach

                                @if (count($images) > 1)
                                    <button class="carousel-control-prev" type="button" data-bs-target="#pwCarousel"
                                        data-bs-slide="prev">
                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Previous</span>
                                    </button>
                                    <button class="carousel-control-next" type="button" data-bs-target="#pwCarousel"
                                        data-bs-slide="next">
                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Next</span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Thumbnails --}}
                    <div class="pw-thumbnails-wrapper">
                        <div class="pw-thumbs d-flex gap-2">
                            @foreach ($images as $index => $img)
                                <div class="pw-thumb-item {{ $index == 0 ? 'active' : '' }}" data-index="{{ $index }}">
                                    <img src="{{ $img }}" alt="thumb-{{ $index }}"
                                        class="img-thumbnail pw-thumb-img">
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="pw-product-info p-3 border rounded bg-white">
                    <div class="d-flex align-items-start justify-content-between mb-2">
                        <h1 class="pw-product-title mb-0">{{ $product->name }}</h1>
                        @if ($product->is_hot)
                            <span class="badge bg-danger ms-2">
                                <i class="bi bi-fire"></i> HOT
                            </span>
                        @endif
                    </div>

                    <div class="pw-product-meta d-flex align-items-center flex-wrap gap-3 mb-3">
                        <div class="pw-rate">
                            {{-- placeholder rating: if you have reviews, replace accordingly --}}
                            <span class="text-warning">★★★★☆</span>
                            <small class="text-muted ms-2">(0 đánh giá)</small>
                        </div>
                    </div>

                    <div class="pw-product-price-large mb-3">{{ $product->formatted_price }}</div>

                    <div class="mb-3">
                        <button class="btn btn-lg btn-primary add-to-cart-btn" data-product-id="{{ $product->id }}">Thêm
                            giỏ hàng</button>
                    </div>

                    <div class="pw-short-desc text-muted small">
                        @if ($product->description)
                            {{ Str::limit(strip_tags($product->description), 220) }}
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <div class="pw-product-bottom bg-white p-3 border rounded">
                    <h4>Thông số kỹ thuật</h4>
                    @if (!empty($product->specifications) && is_array($product->specifications))
                        <table class="table table-striped pw-specs-table">
                            <tbody>
                                @foreach ($product->specifications as $key => $val)
                                    <tr>
                                        <th style="width:25%;">{{ is_string($key) ? $key : 'Thuộc tính' }}</th>
                                        <td>{{ is_array($val) ? implode(', ', $val) : $val }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-muted">Chưa có thông số kỹ thuật cho sản phẩm này.</p>
                    @endif

                    <hr />

                    <h4>Mô tả</h4>
                    <div class="pw-product-desc">
                        {!! $product->description ?? '<p class="text-muted">Chưa có mô tả.</p>' !!}
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

        .badge.bg-danger {
            font-size: 0.85rem;
            padding: 0.4rem 0.6rem;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.8; }
        }

        .pw-product-gallery {
            background: #fff;
            padding: .5rem;
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
        }

        .pw-thumb-item:hover .pw-thumb-img {
            border-color: #adb5bd;
        }

        .pw-thumb-item.active .pw-thumb-img {
            border-color: #0d6efd;
            box-shadow: 0 0 0 2px rgba(13, 110, 253, 0.25);
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

        .pw-specs-table th {
            width: 30%;
            font-weight: 600;
        }

        @media(max-width:767px) {
            .pw-main-img {
                height: 320px;
            }

            .pw-thumb-img {
                width: 60px;
                height: 48px;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        (function($) {
            $(function() {
                $('.pw-thumb-item').on('click', function() {
                    var idx = $(this).data('index');
                    var $carousel = $('#pwCarousel');
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

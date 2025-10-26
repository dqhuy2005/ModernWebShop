@extends('layouts.user.app')

@section('title', $category->name . ' - Danh mục')

@section('content')
    <div class="container py-5 product-listing-grid">
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="pw-category-title">{{ $category->name }}</h2>
                @if ($category->description)
                    <p class="pw-category-desc">{{ $category->description }}</p>
                @endif
            </div>
        </div>

        @php
            $products = collect($category->products ?? [])
                ->filter(function ($p) {
                    return data_get($p, 'status', false);
                })
                ->take(20);
        @endphp

        @foreach ($products as $product)
            <div class="row g-4 mb-3 pw-product-row">
                <div class="col-6 col-md-4 col-lg-2-4">
                    <div class="pw-product-card">
                        <a href="{{ route('products.show', $product->name) }}" class="pw-product-link">
                            <div class="pw-product-image">
                                @php
                                    $img = $product->image ?: 'assets/imgs/products/default.png';
                                    $imgUrl = str_starts_with($img, 'http') ? $img : asset('storage/' . $img);
                                @endphp
                                <img src="{{ $imgUrl }}" alt="{{ $product->name }}" class="img-fluid pw-product-img">
                            </div>
                            <div class="pw-product-body">
                                <h5 class="pw-product-name">{{ $product->name }}</h5>

                                @if (!empty($product->specifications) && is_array($product->specifications))
                                    <ul class="pw-product-specs">
                                        @foreach (array_slice($product->specifications, 0, 3) as $key => $val)
                                            <li><strong>{{ is_string($key) ? $key : '' }}</strong>
                                                {{ is_array($val) ? implode(', ', $val) : $val }}</li>
                                        @endforeach
                                    </ul>
                                @endif

                                <div class="pw-product-price">{{ $product->formatted_price }}</div>
                            </div>
                        </a>

                        <div class="pw-product-actions">
                            <button class="btn btn-sm btn-primary add-to-cart-btn"
                                data-product-id="{{ $product->id }}">Thêm giỏ hàng</button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        @if ($products->isEmpty())
            <div class="row">
                <div class="col-12 text-center py-5">
                    <p>Không có sản phẩm nào trong danh mục này.</p>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('styles')
    <style>
        .pw-category-title {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: .25rem;
        }

        .pw-category-desc {
            color: #6b7280;
            margin-bottom: 1rem;
        }

        .pw-product-row {
            --gap: 1rem;
        }

        .pw-product-card {
            border: 1px solid #e6e6e6;
            border-radius: 6px;
            padding: .75rem;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            background: #fff;
        }

        .pw-product-link {
            color: inherit;
            text-decoration: none;
        }

        .pw-product-image {
            height: 180px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            background: #fff;
            border: 1px solid #f3f3f3;
            padding: .5rem;
        }

        .pw-product-img {
            object-fit: contain;
            width: 100%;
            height: 100%;
        }

        .pw-product-name {
            font-size: .95rem;
            font-weight: 600;
            margin: .5rem 0;
            color: #222;
        }

        .pw-product-specs {
            list-style: none;
            padding: 0;
            margin: 0 0 .5rem 0;
            color: #6b7280;
            font-size: .85rem;
        }

        .pw-product-specs li {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .pw-product-price {
            font-size: 1.05rem;
            font-weight: 700;
            color: #d6336c;
            margin-top: .5rem;
        }

        .pw-product-actions {
            margin-top: .5rem;
            display: flex;
            gap: .5rem;
        }

        @media(min-width:1200px) {
            .col-lg-2-4 {
                width: 20%;
                float: left;
            }
        }

        @media(max-width:1199px) {
            .col-lg-2-4 {
                width: 33.3333%;
            }
        }

        @media(max-width:767px) {
            .pw-product-image {
                height: 140px;
            }

            .col-6 {
                width: 50%;
            }
        }
    </style>
@endpush

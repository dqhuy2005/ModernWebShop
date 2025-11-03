<div class="row g-3 pw-product-grid">
    @forelse ($products as $product)
        <div class="col-6 col-md-4 col-lg-3">
            <div class="pw-product-card h-100">
                <a href="{{ route('products.show', $product->slug) }}" class="pw-product-link">
                    <div class="pw-product-image">
                        @php
                            $mainImage = $product->main_image ?? $product->image ?? 'assets/imgs/products/default.png';
                            $imgUrl = str_starts_with($mainImage, 'http') ? $mainImage : asset('storage/' . $mainImage);
                        @endphp
                        <img src="{{ $imgUrl }}"
                             alt="{{ $product->name }}"
                             class="img-fluid pw-product-img"
                             loading="lazy">

                        @if($product->is_hot)
                            <span class="pw-badge pw-badge-hot">HOT</span>
                        @endif
                    </div>

                    <div class="pw-product-body">
                        <h5 class="pw-product-name">{{ Str::limit($product->name, 60) }}</h5>

                        @if (!empty($product->specifications) && is_array($product->specifications))
                            <ul class="pw-product-specs">
                                @foreach (array_slice($product->specifications, 0, 3) as $key => $val)
                                    <li>
                                        @if(is_string($key))
                                            <strong>{{ $key }}:</strong>
                                        @endif
                                        {{ is_array($val) ? implode(', ', $val) : $val }}
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                        <div class="pw-product-footer">
                            <div class="pw-product-price">{{ $product->formatted_price }}</div>
                            @if($product->views > 0)
                                <div class="pw-product-views">
                                    <i class="bi bi-eye"></i> {{ number_format($product->views) }}
                                </div>
                            @endif
                        </div>
                    </div>
                </a>

                <div class="pw-product-actions">
                    <button class="btn btn-sm btn-primary w-100 add-to-cart-btn"
                            data-product-id="{{ $product->id }}">
                        <i class="bi bi-cart-plus"></i> Thêm giỏ hàng
                    </button>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12 text-center py-5">
            <div class="pw-empty-state">
                <i class="bi bi-inbox display-1 text-muted"></i>
                <p class="mt-3 text-muted">Không tìm thấy sản phẩm nào phù hợp với tiêu chí lọc.</p>
            </div>
        </div>
    @endforelse
</div>

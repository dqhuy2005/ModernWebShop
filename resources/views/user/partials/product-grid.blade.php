<div class="row g-3 pw-product-grid">
    @forelse ($products as $product)
        <div class="col-6 col-md-4 col-lg-3">
            <div class="pw-product-card h-100">
                <a href="{{ route('products.show', $product->slug) }}" class="pw-product-link">
                    <div class="pw-product-image">
                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="img-fluid pw-product-img"
                            loading="lazy">

                        @if ($product->is_hot)
                            <span class="pw-badge pw-badge-hot">HOT</span>
                        @endif
                    </div>

                    <div class="pw-product-body">
                        <h5 class="pw-product-name">{{ Str::limit($product->name, 60) }}</h5>

                        @php
                            $specs = $product->specifications;
                            if (is_string($specs)) {
                                $specs = json_decode($specs, true);
                            }
                            $hasValidSpecs = !empty($specs) && is_array($specs) && count(array_filter($specs)) > 0;
                        @endphp

                        @if ($hasValidSpecs)
                            <ul class="pw-product-specs">
                                @php
                                    $displayedCount = 0;
                                    $maxSpecs = 3;
                                @endphp
                                @foreach ($specs as $key => $val)
                                    @if ($displayedCount < $maxSpecs && !empty($val))
                                        <li>
                                            @if (is_string($key) && !is_numeric($key))
                                                <strong>{{ $key }}:</strong>
                                            @endif
                                            {{ is_array($val) ? implode(', ', array_filter($val)) : $val }}
                                        </li>
                                        @php $displayedCount++; @endphp
                                    @endif
                                @endforeach
                            </ul>
                        @endif

                        <div class="pw-product-footer">
                            <div class="pw-product-price">{{ $product->formatted_price }}</div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    @empty
        <div class="col-12 text-center py-5">
            <div class="pw-empty-state">
                <i class="bi bi-inbox display-1 text-muted"></i>
                <p class="mt-3 text-muted">Không tìm thấy sản phẩm nào phù hợp.</p>
            </div>
        </div>
    @endforelse
</div>

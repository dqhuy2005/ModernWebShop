{{-- Category Grid Component --}}
<section class="category-grid py-5" style="background-color: #FFFFFF;">
    <div class="container">
        <div class="section-header text-center mb-4">
            <h2 class="section-title">Shop by Category</h2>
            <p class="section-subtitle">Browse our product categories</p>
        </div>

        <div class="row g-4">
            @php
                $categoryIcons = [
                    'laptops' => 'fa-laptop',
                    'smartphones' => 'fa-mobile-alt',
                    'cameras' => 'fa-camera',
                    'accessories' => 'fa-headphones',
                    'tablets' => 'fa-tablet-alt',
                    'smartwatches' => 'fa-watch'
                ];

                $displayCategories = \App\Models\Category::active()
                    ->withCount('products')
                    ->whereNull('parent_id')
                    ->orderBy('products_count', 'desc')
                    ->limit(6)
                    ->get();
            @endphp

            @forelse($displayCategories as $category)
                <div class="col-6 col-md-4 col-lg-2">
                    <a href="{{ route('categories.show', $category->slug) }}" class="category-card">
                        <div class="category-icon">
                            <i class="fas {{ $categoryIcons[$category->slug] ?? 'fa-box' }}"></i>
                        </div>
                        <h6 class="category-name">{{ $category->name }}</h6>
                        <span class="category-count">{{ $category->products_count }} Items</span>
                    </a>
                </div>
            @empty
                @foreach(['Laptops', 'Smartphones', 'Cameras', 'Accessories', 'Tablets', 'Watches'] as $index => $cat)
                    <div class="col-6 col-md-4 col-lg-2">
                        <a href="#" class="category-card">
                            <div class="category-icon">
                                <i class="fas {{ array_values($categoryIcons)[$index] ?? 'fa-box' }}"></i>
                            </div>
                            <h6 class="category-name">{{ $cat }}</h6>
                            <span class="category-count">0 Items</span>
                        </a>
                    </div>
                @endforeach
            @endforelse
        </div>
    </div>
</section>

<style>
    .category-grid {
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
        margin-bottom: 0;
    }

    .category-card {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 2rem 1rem;
        background-color: #FFFCED;
        border-radius: 8px;
        text-decoration: none;
        transition: all 0.3s ease;
        height: 100%;
    }

    .category-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(32, 39, 50, 0.1);
    }

    .category-icon {
        width: 70px;
        height: 70px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #202732;
        color: #FFFCED;
        border-radius: 50%;
        margin-bottom: 1rem;
        font-size: 2rem;
        transition: all 0.3s ease;
    }

    .category-card:hover .category-icon {
        background-color: #FFFCED;
        color: #202732;
        border: 2px solid #202732;
    }

    .category-name {
        font-size: 1rem;
        font-weight: 600;
        color: #202732;
        margin-bottom: 0.25rem;
        text-align: center;
    }

    .category-count {
        font-size: 0.875rem;
        color: #6c757d;
    }

    @media (max-width: 768px) {
        .section-title {
            font-size: 1.5rem;
        }

        .category-icon {
            width: 60px;
            height: 60px;
            font-size: 1.5rem;
        }

        .category-card {
            padding: 1.5rem 0.5rem;
        }
    }
</style>

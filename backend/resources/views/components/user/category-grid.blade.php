<section class="category-grid py-5" style="background-color: #FFFFFF;">
    <div class="container">
        <div class="section-header text-center mb-4">
            <h2 class="section-title">DANH MỤC SẢN PHẨM</h2>
        </div>

        <div class="row g-4 justify-content-center">
            @php
                $displayCategories = \App\Models\Category::active()
                    ->withCount('products')
                    ->whereNull('parent_id')
                    ->whereNotNull('image')
                    ->orderBy('updated_at', 'desc')
                    ->limit(5)
                    ->get();
            @endphp

            @forelse($displayCategories as $category)
                <div class="col-6 col-md-4 col-lg">
                    <a href="{{ route('categories.show', $category->slug . '.html') }}" class="category-card">
                        <div class="category-image-wrapper">
                            <img src="{{ asset('storage/categories/' . $category->image) }}"
                                 alt="{{ $category->name }}"
                                 class="category-image">
                        </div>
                        <h6 class="category-name">{{ strtoupper($category->name) }}</h6>
                    </a>
                </div>
            @empty
                @foreach(['LAPTOP', 'LAPTOP GAMING', 'PC', 'TABLET', 'HARD DRIVE'] as $index => $cat)
                    <div class="col-6 col-md-4 col-lg">
                        <a href="#" class="category-card">
                            <div class="category-image-wrapper">
                                <img src="{{ asset('assets/imgs/categories/default.png') }}"
                                     alt="{{ $cat }}"
                                     class="category-image">
                            </div>
                            <h6 class="category-name">{{ $cat }}</h6>
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
        margin-bottom: 2rem;
        letter-spacing: 1px;
    }

    .category-card {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 1.5rem 1rem;
        background-color: #e8e8e8;
        border-radius: 4px;
        text-decoration: none;
        transition: all 0.3s ease;
        height: 100%;
        border: 2px solid transparent;
    }

    .category-image-wrapper {
        width: 100%;
        height: 200px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
        overflow: hidden;
        background-color: #FFFFFF;
        border: 1px solid #ccc;

        padding: 1rem;
    }

    .category-image-wrapper:hover {
        opacity: 0.8;
    }

    .category-image {
        width: 100%;
        height: 100%;
        object-fit: contain;
        transition: transform 0.3s ease;
    }

    .category-name {
        font-size: 1rem;
        font-weight: 700;
        color: #202732;
        margin-bottom: 0;
        text-align: center;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }

    @media (max-width: 992px) {
        .section-title {
            font-size: 1.5rem;
        }

        .category-image-wrapper {
            height: 160px;
        }
    }

    @media (max-width: 768px) {
        .section-title {
            font-size: 1.3rem;
        }

        .category-image-wrapper {
            height: 140px;
        }

        .category-card {
            padding: 1rem 0.5rem;
        }

        .category-name {
            font-size: 0.875rem;
        }
    }

    @media (max-width: 576px) {
        .category-image-wrapper {
            height: 120px;
        }
    }
</style>

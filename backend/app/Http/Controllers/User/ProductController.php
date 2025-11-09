<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\ProductViewService;
use App\Services\ReviewService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    protected ProductViewService $viewService;
    protected ReviewService $reviewService;

    public function __construct(ProductViewService $viewService, ReviewService $reviewService)
    {
        $this->viewService = $viewService;
        $this->reviewService = $reviewService;
    }

    public function show(Request $request, string $slug)
    {
        try {
            $product = Cache::remember(
                "product_detail_{$slug}",
                now()->addMinutes(10),
                function () use ($slug) {
                    return Product::select('id', 'name', 'slug', 'description', 'specifications', 'price', 'currency', 'image', 'category_id', 'status', 'is_hot', 'views', 'view_count', 'created_at', 'updated_at')
                        ->with([
                            'category:id,name,slug',
                            'images' => function ($query) {
                                $query->select('id', 'product_id', 'image_path', 'sort_order')
                                    ->orderBy('sort_order')
                                    ->orderBy('id');
                            }
                        ])
                        ->where('slug', $slug)
                        ->firstOrFail();
                }
            );

            $this->viewService->trackView(
                $product,
                $request->ip(),
                $request->userAgent()
            );

            $relatedProducts = $this->getRelatedProducts($product);

            $viewStats = [
                'total_views' => $product->view_count ?? $product->views ?? 0,
                'recent_views_7days' => $this->viewService->getRecentViewCount($product->id, 7),
                'unique_visitors' => $this->viewService->getUniqueVisitorsCount($product->id, 7),
                'is_hot' => $product->is_hot,
            ];

            // Get reviews data
            $reviews = $product->approvedReviews()
                ->select('id', 'product_id', 'user_id', 'rating', 'comment', 'media', 'status', 'created_at')
                ->with('user:id,fullname,email,image')
                ->latest()
                ->paginate(10);

            $reviewStats = $this->reviewService->getProductReviewStats($product);

            return view('user.product-detail', compact('product', 'relatedProducts', 'viewStats', 'reviews', 'reviewStats'));

        } catch (\Exception $e) {
            Log::error('Product detail error', [
                'slug' => $slug,
                'error' => $e->getMessage(),
            ]);

            abort(404, 'Sản phẩm không tồn tại');
        }
    }

    private function getRelatedProducts(Product $product, int $limit = 4)
    {
        return Cache::remember(
            "related_products_{$product->id}",
            now()->addHour(),
            function () use ($product, $limit) {
                return Product::where('category_id', $product->category_id)
                    ->where('id', '!=', $product->id)
                    ->where('status', true)
                    ->select('id', 'name', 'slug', 'image', 'price', 'views', 'is_hot')
                    ->orderBy('views', 'desc')
                    ->limit($limit)
                    ->get();
            }
        );
    }

    public function hotProducts()
    {
        $hotProducts = Product::where('is_hot', true)
            ->where('status', true)
            ->select('id', 'name', 'slug', 'image', 'price', 'views', 'is_hot', 'category_id')
            ->with('category:id,name')
            ->orderBy('views', 'desc')
            ->paginate(20);

        return view('user.hot-products', compact('hotProducts'));
    }
}

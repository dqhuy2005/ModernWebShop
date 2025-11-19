<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\ProductViewService;
use App\Services\ReviewService;
use App\Services\Cache\RedisService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    protected ProductViewService $viewService;
    protected ReviewService $reviewService;
    protected RedisService $redis;

    public function __construct(
        ProductViewService $viewService,
        ReviewService $reviewService,
        RedisService $redis
    ) {
        $this->viewService = $viewService;
        $this->reviewService = $reviewService;
        $this->redis = $redis;
    }

    public function show(Request $request, string $slug)
    {
        try {
            $product = $this->redis->remember(
                "product_detail_{$slug}",
                600,
                function () use ($slug) {
                    return Product::select('id', 'name', 'slug', 'description', 'specifications', 'price', 'currency', 'category_id', 'status', 'is_hot', 'views', 'created_at', 'updated_at')
                        ->with([
                            'category:id,name,slug',
                            'images' => function ($query) {
                                $query->select('id', 'product_id', 'path', 'sort_order')
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

            $viewStats = $this->redis->remember(
                "product_view_stats_{$product->id}",
                300,
                function () use ($product) {
                    return [
                        'total_views' => $product->views ?? 0,
                        'recent_views_7days' => $this->viewService->getRecentViewCount($product->id, 7),
                        'unique_visitors' => $this->viewService->getUniqueVisitorsCount($product->id, 7),
                        'is_hot' => $product->is_hot,
                    ];
                }
            );

            $page = $request->get('page', 1);
            $reviews = $this->redis->remember(
                "product_reviews_{$product->id}_page_{$page}",
                600,
                function () use ($product) {
                    return $product->approvedReviews()
                        ->select('id', 'product_id', 'user_id', 'rating', 'comment', 'status', 'created_at')
                        ->with('user:id,fullname,email')
                        ->latest()
                        ->paginate(10);
                }
            );

            $reviewStats = $this->redis->remember(
                "product_review_stats_{$product->id}",
                600,
                function () use ($product) {
                    return $this->reviewService->getProductReviewStats($product);
                }
            );

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
        return $this->redis->remember(
            "related_products_{$product->id}",
            3600,
            function () use ($product, $limit) {
                return Product::where('category_id', $product->category_id)
                    ->where('id', '!=', $product->id)
                    ->where('status', true)
                    ->select('id', 'name', 'slug', 'price', 'views', 'is_hot')
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
            ->select('id', 'name', 'slug', 'price', 'views', 'is_hot', 'category_id')
            ->with('category:id,name')
            ->orderBy('views', 'desc')
            ->paginate(20);

        return view('user.hot-products', compact('hotProducts'));
    }
}

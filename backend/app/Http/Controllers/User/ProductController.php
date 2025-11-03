<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\ProductViewService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    protected ProductViewService $viewService;

    public function __construct(ProductViewService $viewService)
    {
        $this->viewService = $viewService;
    }

    public function show(Request $request, string $slug)
    {
        try {
            $product = Cache::remember(
                "product_detail_{$slug}",
                now()->addMinutes(10),
                function () use ($slug) {
                    return Product::with([
                        'category',
                        'images' => function ($query) {
                            $query->orderBy('sort_order')->orderBy('id');
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

            return view('user.product-detail', compact('product', 'relatedProducts', 'viewStats'));

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

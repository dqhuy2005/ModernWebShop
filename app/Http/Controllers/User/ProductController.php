<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\impl\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    protected ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function show(Request $request, string $slug)
    {
        try {
            $data = $this->productService->getProductDetail($slug, $request);

            return view('user.product-detail', [
                'product' => $data['product'],
                'relatedProducts' => $data['relatedProducts'],
                'viewStats' => $data['viewStats'],
                'reviews' => $data['reviews'],
                'reviewStats' => $data['reviewStats'],
            ]);

        } catch (\Exception $e) {
            Log::error('Product detail error', [
                'slug' => $slug,
                'error' => $e->getMessage(),
            ]);

            abort(404, 'Sản phẩm không tồn tại');
        }
    }

    public function hotProducts()
    {
        $hotProducts = $this->productService->getHotProducts();

        return view('user.hot-products', compact('hotProducts'));
    }
}

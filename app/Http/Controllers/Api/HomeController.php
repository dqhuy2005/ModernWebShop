<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends AppBaseController
{
    public function index(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 50);
            $page = $request->get('page', default: 1);

            $products = Product::select('id', 'name', 'slug', 'description', 'price', 'currency', 'category_id', 'status', 'is_hot', 'views', 'created_at')
                ->with(['category:id,name,slug', 'images:id,product_id,path,sort_order'])
                ->active()
                ->paginate($perPage);

            $data = [
                'total_products' => $products->total(),
                'current_page' => $products->currentPage(),
                'per_page' => $products->perPage(),
                'last_page' => $products->lastPage(),
                'has_more' => $products->hasMorePages(),
                'next_page' => $products->hasMorePages() ? $products->currentPage() + 1 : null,
                'products' => collect($products->items())->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'slug' => $product->slug,
                        'description' => $product->description,
                        'price' => $product->price,
                        'formatted_price' => $product->formatted_price,
                        'currency' => $product->currency,
                        'status' => $product->status,
                        'is_hot' => $product->is_hot,
                        'views' => $product->views,
                        'image_url' => $product->image_url,
                        'category' => $product->category,
                        'created_at' => $product->created_at,
                    ];
                })->toArray()
            ];

            return $this->sendResponse($data, 'Products retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve products: ' . $e->getMessage(), 500);
        }
    }
}

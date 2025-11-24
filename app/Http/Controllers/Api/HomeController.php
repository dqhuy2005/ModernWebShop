<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use App\Models\Product;

class HomeController extends AppBaseController
{
    public function index()
    {
        try {
            $perPage = request()->get('per_page', 10);
            $page = request()->get('page', 1);

            $products = Product::select('id', 'name', 'slug', 'description', 'price', 'currency', 'category_id', 'status', 'is_hot', 'views', 'created_at')
                ->with('category:id,name,slug')
                ->parentOnly()
                ->active()
                ->paginate($perPage);

            $data = [
                'total_products' => $products->total(),
                'current_page' => $products->currentPage(),
                'per_page' => $products->perPage(),
                'last_page' => $products->lastPage(),
                'has_more' => $products->hasMorePages(),
                'products' => $products->items()
            ];

            return $this->sendResponse($data, 'Products retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve products: ' . $e->getMessage(), 500);
        }
    }
}

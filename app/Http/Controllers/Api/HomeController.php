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
            $perPage = $request->get('per_page', 10);
            $page = $request->get('page', default: 1);
            $search = $request->get('search', '');

            $query = Product::query()
                ->select('id', 'name', 'slug', 'description', 'price', 'currency', 'category_id', 'status', 'is_hot', 'views', 'created_at')
                ->with([
                    'category:id,name,slug',
                    'images' => function ($query) {
                        $query->select('id', 'product_id', 'path', 'sort_order')
                            ->orderBy('sort_order')
                            ->limit(1);
                    }
                ])
                ->where('status', true);

            if (!empty($search)) {
                $query->where('name', 'LIKE', '%' . $search . '%');
            }

            $query->whereNull('parent_id');

            $query->orderByDesc('is_hot')
                ->orderByDesc('created_at');

            $products = $query->paginate($perPage);

            $data = [
                'total_products' => $products->total(),
                'current_page' => $products->currentPage(),
                'per_page' => $products->perPage(),
                'last_page' => $products->lastPage(),
                'has_more' => $products->hasMorePages(),
                'next_page' => $products->hasMorePages() ? $products->currentPage() + 1 : null,
                'search_query' => $search,
                'products' => $products->getCollection()->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'slug' => $product->slug,
                        'description' => $product->description,
                        'price' => $product->price,
                        'formatted_price' => $product->formatted_price,
                        'currency' => $product->currency,
                        'is_hot' => $product->is_hot,
                        'views' => $product->views,
                        'image_url' => $product->image_url,
                        'category' => $product->category ? [
                            'id' => $product->category->id,
                            'name' => $product->category->name,
                            'slug' => $product->category->slug,
                        ] : null,
                        'created_at' => $product->created_at->format('Y-m-d H:i:s'),
                    ];
                })->toArray()
            ];

            return $this->sendResponse($data, 'Products retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve products: ' . $e->getMessage(), 500);
        }
    }

    public function search(Request $request)
    {
        try {
            $search = $request->get('q', '');
            $perPage = $request->get('per_page', 10);

            if (empty($search)) {
                return $this->sendError('Search query is required', 400);
            }

            $products = Product::query()
                ->select('id', 'name', 'slug', 'price', 'currency', 'category_id', 'is_hot', 'views')
                ->with([
                    'category:id,name,slug',
                    'images' => function ($query) {
                        $query->select('id', 'product_id', 'path', 'sort_order')
                            ->orderBy('sort_order')
                            ->limit(1);
                    }
                ])
                ->where('status', true)
                ->whereNull('parent_id')
                ->where('name', 'LIKE', '%' . $search . '%')
                ->orderByRaw("CASE WHEN name LIKE ? THEN 1 ELSE 2 END", [$search . '%'])
                ->orderByDesc('views')
                ->paginate($perPage);

            $data = [
                'total_results' => $products->total(),
                'current_page' => $products->currentPage(),
                'per_page' => $products->perPage(),
                'last_page' => $products->lastPage(),
                'has_more' => $products->hasMorePages(),
                'next_page' => $products->hasMorePages() ? $products->currentPage() + 1 : null,
                'search_query' => $search,
                'products' => $products->getCollection()->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'slug' => $product->slug,
                        'price' => $product->price,
                        'formatted_price' => $product->formatted_price,
                        'currency' => $product->currency,
                        'is_hot' => $product->is_hot,
                        'views' => $product->views,
                        'image_url' => $product->image_url,
                        'category' => $product->category ? [
                            'id' => $product->category->id,
                            'name' => $product->category->name,
                            'slug' => $product->category->slug,
                        ] : null,
                    ];
                })->toArray()
            ];

            return $this->sendResponse($data, 'Search results retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Search failed: ' . $e->getMessage(), 500);
        }
    }
}

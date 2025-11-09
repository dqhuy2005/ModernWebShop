<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends AppBaseController
{
    public function index()
    {
        try {
            $products = Product::select('id', 'name', 'slug', 'description', 'price', 'currency', 'image', 'category_id', 'status', 'is_hot', 'views', 'created_at')
                ->with('category:id,name,slug')
                ->parentOnly()
                ->active()
                ->get();
            return $this->sendResponse($products, 'Products retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve products: ' . $e->getMessage(), 500);
        }
    }

    public function show($id)
    {
        try {
            $product = Product::select('id', 'name', 'slug', 'description', 'specifications', 'price', 'currency', 'image', 'category_id', 'status', 'is_hot', 'views', 'created_at', 'updated_at')
                ->with('category:id,name,slug')
                ->find($id);

            if (!$product) {
                return $this->sendError('Product not found', 404);
            }

            // Increment view count
            $product->increment('views');

            return $this->sendResponse($product, 'Product retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve product: ' . $e->getMessage(), 500);
        }
    }

    public function getByCategory($categoryId)
    {
        try {
            $category = Category::select('id', 'name', 'slug')->find($categoryId);

            if (!$category) {
                return $this->sendError('Category not found', 404);
            }

            $products = Product::select('id', 'name', 'slug', 'description', 'price', 'currency', 'image', 'category_id', 'status', 'is_hot', 'views', 'created_at')
                ->with('category:id,name,slug')
                ->where('category_id', $categoryId)
                ->active()
                ->get();

            return $this->sendResponse($products, 'Products by category retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve products by category: ' . $e->getMessage(), 500);
        }
    }

    public function getMostViewed(Request $request)
    {
        try {
            $limit = $request->query('limit', default: 10);

            $products = Product::select('id', 'name', 'slug', 'description', 'price', 'currency', 'image', 'category_id', 'status', 'is_hot', 'views', 'created_at')
                ->with('category:id,name,slug')
                ->active()
                ->mostViewed($limit)
                ->get();

            return $this->sendResponse($products, 'Most viewed products retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve most viewed products: ' . $e->getMessage(), 500);
        }
    }

    public function getHotProducts()
    {
        try {
            $products = Product::select('id', 'name', 'slug', 'description', 'price', 'currency', 'image', 'category_id', 'status', 'is_hot', 'views', 'created_at')
                ->with('category:id,name,slug')
                ->hot()
                ->active()
                ->get();

            return $this->sendResponse($products, 'Hot products retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve hot products: ' . $e->getMessage(), 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|integer|exists:categories,id',
            'name' => 'required|string|max:255',
            'specifications' => 'nullable|array',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'status' => 'boolean',
            'parent_id' => 'nullable|integer|exists:products,id',
            'language' => 'nullable|string|max:10',
            'views' => 'integer|min:0',
            'is_hot' => 'boolean',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationErrorWithDetails($validator->errors()->toArray());
        }

        try {
            $data = $request->all();
            $data['views'] = 0; // Initialize views to 0

            $product = Product::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Product created successfully',
                'data' => $product->load('category')
            ], 201);
        } catch (\Exception $e) {
            return $this->sendError('Failed to create product: ' . $e->getMessage(), 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'sometimes|required|integer|exists:categories,id',
            'name' => 'sometimes|required|string|max:255',
            'specifications' => 'nullable|array',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'status' => 'boolean',
            'parent_id' => 'nullable|integer|exists:products,id',
            'language' => 'nullable|string|max:10',
            'is_hot' => 'boolean',
            'views' => 'integer|min:0',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationErrorWithDetails($validator->errors()->toArray());
        }

        try {
            $product = Product::find($id);

            if (!$product) {
                return $this->sendError('Product not found', 404);
            }

            // Prevent setting product as its own parent
            if ($request->has('parent_id') && $request->parent_id == $id) {
                return $this->sendError('Product cannot be its own parent', 422);
            }

            $product->update($request->all());

            return $this->sendResponse($product->load('category'), 'Product updated successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to update product: ' . $e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try {
            $product = Product::find($id);

            if (!$product) {
                return $this->sendError('Product not found', 404);
            }

            if ($product->carts()->count() > 0) {
                return $this->sendError('Cannot delete product that is in shopping carts', 422);
            }

            if ($product->orderDetails()->count() > 0) {
                return $this->sendError('Cannot delete product that has been ordered', 422);
            }

            $product->delete();

            return $this->sendResponse(null, 'Product deleted successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to delete product: ' . $e->getMessage(), 500);
        }
    }

    public function toggleHotStatus($id)
    {
        try {
            $product = Product::find($id);

            if (!$product) {
                return $this->sendError('Product not found', 404);
            }

            $product->is_hot = !$product->is_hot;
            $product->save();

            return $this->sendResponse($product, 'Product hot status updated successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to toggle hot status: ' . $e->getMessage(), 500);
        }
    }
}

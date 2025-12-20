<?php

namespace App\Http\Controllers\CMS;

use App\Http\Controllers\BaseController;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use App\Models\Category;
use App\Services\impl\ProductService;
use Illuminate\Http\Request;

class ProductController extends BaseController
{
    protected ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }


    public function index(Request $request)
    {
        try {
            $query = Product::select('products.id', 'products.name', 'products.slug', 'products.price', 'products.currency', 'products.category_id', 'products.status', 'products.is_hot', 'products.views', 'products.created_at', 'products.updated_at');

            $this->applyProductFilters($query, $request);

            $this->applySorting(
                $query,
                $request,
                'id',
                'desc',
                ['id', 'name', 'price', 'created_at', 'updated_at', 'category_id']
            );

            if ($request->get('sort_by') === 'category_id') {
                $query->leftJoin('categories', 'products.category_id', '=', 'categories.id')
                    ->select('products.id', 'products.name', 'products.slug', 'products.price', 'products.currency', 'products.category_id', 'products.status', 'products.is_hot', 'products.views', 'products.created_at', 'products.updated_at', 'categories.name as category_name')
                    ->orderBy('categories.name', $request->get('sort_order', 'desc'));
            }

            $perPage = $request->get('per_page', 15);
            $allowedPerPage = [15, 25, 50, 100];
            if (!in_array($perPage, $allowedPerPage)) {
                $perPage = 15;
            }

            $products = $query->with('category:id,name,slug')->paginate($perPage)->withQueryString();

            $categories = Category::select('id', 'name')->orderBy('name')->get();

            return view('admin.products.index', compact('products', 'categories'));
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to load products: ' . $e->getMessage());
        }
    }

    protected function applyProductFilters($query, Request $request)
    {
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('products.name', 'like', "%{$search}%")
                    ->orWhere('products.description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('products.category_id', $request->category_id);
        }

        if ($request->filled('status')) {
            $query->where('products.status', (bool) $request->status);
        }

        if ($request->filled('is_hot')) {
            $query->where('products.is_hot', (bool) $request->is_hot);
        }

        if ($request->filled('price_min')) {
            $query->where('products.price', '>=', $request->price_min);
        }

        if ($request->filled('price_max')) {
            $query->where('products.price', '<=', $request->price_max);
        }

        return $query;
    }

    public function create()
    {
        try {
            $categories = Category::select('id', 'name')
                ->orderBy('name')
                ->get();

            return view('admin.products.create', compact('categories'));
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to load create form: ' . $e->getMessage());
        }
    }

    public function store(StoreProductRequest $request)
    {
        try {
            $data = $request->except(['images', 'specifications', 'action']);

            $data['status'] = $request->has('status') ? 1 : 0;
            $data['is_hot'] = $request->has('is_hot') ? 1 : 0;
            $data['currency'] = $request->input('currency', 'VND');
            $data['specifications'] = $this->productService->formatSpecifications($request->specifications);

            $imagePaths = [];
            if ($request->filled('images')) {
                $imagePaths = json_decode($request->input('images'), true) ?? [];
            }

            $product = $this->productService->createProduct(
                $data,
                null,
                $imagePaths
            );

            if ($request->input('action') === 'save_and_continue') {
                return redirect()
                    ->route('admin.products.create')
                    ->with('success', 'Product created successfully! You can add another one.');
            }

            return redirect()
                ->route('admin.products.show', $product->id)
                ->with('success', 'Product created successfully!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to create product: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $product = Product::with(['category', 'images'])->findOrFail($id);

            return view('admin.products.show', compact('product'));
        } catch (\Exception $e) {
            return back()->with('error', 'Product not found: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $product = Product::with('images')->findOrFail($id);

            $categories = Category::select('id', 'name')
                ->orderBy('name')
                ->get();

            return view('admin.products.edit', compact('product', 'categories'));
        } catch (\Exception $e) {
            return back()->with('error', 'Product not found: ' . $e->getMessage());
        }
    }

    public function update(UpdateProductRequest $request, $id)
    {
        try {
            $product = Product::findOrFail($id);
            $data = $request->except(['images', 'specifications', '_method', '_token']);

            $data['status'] = $request->has('status') ? 1 : 0;
            $data['is_hot'] = $request->has('is_hot') ? 1 : 0;
            $data['currency'] = $request->input('currency', 'VND');
            $data['specifications'] = $this->productService->formatSpecifications($request->specifications);

            $imagePaths = [];
            if ($request->filled('images')) {
                $imagePaths = json_decode($request->input('images'), true) ?? [];
            }

            $product = $this->productService->updateProduct(
                $product,
                $data,
                null,
                $imagePaths
            );

            return redirect()
                ->route('admin.products.index')
                ->with('success', 'Product updated successfully!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to update product: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);
            $this->productService->deleteProduct($product);

            return redirect()
                ->route('admin.products.index')
                ->with('success', 'Product deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete product: ' . $e->getMessage());
        }
    }

    public function toggleHot($id)
    {
        try {
            $product = Product::findOrFail($id);
            $product->is_hot = !$product->is_hot;
            $product->save();

            $status = $product->is_hot ? 'hot' : 'normal';

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "Product marked as {$status} successfully!",
                    'is_hot' => $product->is_hot
                ]);
            }

            return back()->with('success', "Product marked as {$status} successfully!");
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to toggle hot status: ' . $e->getMessage(),
                ], 500);
            }

            return back()->with('error', 'Failed to toggle hot status: ' . $e->getMessage());
        }
    }

    public function toggleStatus($id)
    {
        try {
            $product = Product::findOrFail($id);
            $product->status = !$product->status;
            $product->save();

            $status = $product->status ? 'active' : 'inactive';

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "Product marked as {$status} successfully!",
                    'status' => $product->status
                ]);
            }

            return back()->with('success', "Product marked as {$status} successfully!");
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to toggle status: ' . $e->getMessage(),
                ], 500);
            }

            return back()->with('error', 'Failed to toggle status: ' . $e->getMessage());
        }
    }

    public function restore($id)
    {
        try {
            $product = Product::withTrashed()->findOrFail($id);
            $product->restore();

            return back()->with('success', 'Product restored successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to restore product: ' . $e->getMessage());
        }
    }

    public function forceDelete($id)
    {
        try {
            $product = Product::withTrashed()->findOrFail($id);

            $product->forceDelete();

            return redirect()
                ->route('admin.products.index')
                ->with('success', 'Product permanently deleted!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to permanently delete product: ' . $e->getMessage());
        }
    }

    public function deleteImage($productId, $imageId)
    {
        try {
            $product = Product::findOrFail($productId);
            $this->productService->deleteProductImage($product, $imageId);

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Image deleted successfully!',
                ]);
            }

            return back()->with('success', 'Image deleted successfully!');
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete image: ' . $e->getMessage(),
                ], 500);
            }

            return back()->with('error', 'Failed to delete image: ' . $e->getMessage());
        }
    }

}

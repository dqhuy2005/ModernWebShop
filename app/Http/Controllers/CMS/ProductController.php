<?php

namespace App\Http\Controllers\CMS;

use App\DTOs\ProductData;
use App\Http\Controllers\BaseController;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use App\Models\Category;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Services\CMS\ProductService;
use Illuminate\Http\Request;

class ProductController extends BaseController
{
    public function __construct(
        private ProductService $productService,
        private ProductRepositoryInterface $productRepository
    ) {
    }


    public function index(Request $request)
    {
        try {
            $filters = [
                'search' => $request->input('search'),
                'category_id' => $request->input('category_id'),
                'status' => $request->input('status'),
                'is_hot' => $request->input('is_hot'),
                'price_min' => $request->input('price_min'),
                'price_max' => $request->input('price_max'),
                'sort_by' => $request->input('sort_by', 'id'),
                'sort_order' => $request->input('sort_order', 'desc'),
            ];

            $perPage = $request->get('per_page', 15);
            $allowedPerPage = [15, 25, 50, 100];
            if (!in_array($perPage, $allowedPerPage)) {
                $perPage = 15;
            }

            $products = $this->productRepository->paginate($filters, $perPage);

            $categories = Category::select('id', 'name')->orderBy('name')->get();

            return view('admin.products.index', compact('products', 'categories'));
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to load products: ' . $e->getMessage());
        }
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
            $productData = ProductData::fromRequest($request);

            $imagePaths = [];
            if ($request->filled('images')) {
                $imagePaths = json_decode($request->input('images'), true) ?? [];
            }

            $product = $this->productService->createProduct(
                $productData,
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
            $product = $this->productRepository->find($id);

            if (!$product) {
                return back()->with('error', 'Product not found');
            }

            return view('admin.products.show', compact('product'));
        } catch (\Exception $e) {
            return back()->with('error', 'Product not found: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $product = $this->productRepository->findWithTrashed($id);

            if (!$product) {
                return back()->with('error', 'Product not found');
            }

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
            $product = $this->productRepository->find($id);

            if (!$product) {
                return back()->with('error', 'Product not found');
            }

            $productData = ProductData::fromRequest($request);

            $imagePaths = [];
            if ($request->filled('images')) {
                $imagePaths = json_decode($request->input('images'), true) ?? [];
            }

            $this->productService->updateProduct(
                $product,
                $productData,
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
            $product = $this->productRepository->find($id);

            if (!$product) {
                return back()->with('error', 'Product not found');
            }

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
            $product = $this->productRepository->find($id);

            if (!$product) {
                throw new \Exception('Product not found');
            }

            $this->productService->toggleHotStatus($product);

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
            $product = $this->productRepository->find($id);

            if (!$product) {
                throw new \Exception('Product not found');
            }

            $this->productService->toggleStatus($product);

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
            $this->productService->restoreProduct($id);

            return back()->with('success', 'Product restored successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to restore product: ' . $e->getMessage());
        }
    }

    public function forceDelete($id)
    {
        try {
            $this->productService->forceDeleteProduct($id);

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
            $product = $this->productRepository->find($productId);

            if (!$product) {
                throw new \Exception('Product not found');
            }

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

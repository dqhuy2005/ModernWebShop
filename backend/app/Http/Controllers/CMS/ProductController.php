<?php

namespace App\Http\Controllers\CMS;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Product::with('category');

            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            }

            if ($request->has('category_id') && !empty($request->category_id)) {
                $query->where('category_id', $request->category_id);
            }

            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            if ($request->has('is_hot')) {
                $query->where('is_hot', $request->is_hot);
            }

            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            $perPage = $request->get('per_page', 15);
            $products = $query->paginate($perPage);

            $categories = Category::select('id', 'name')->orderBy('name')
                ->get();

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

    public function store(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'specifications' => 'nullable|array',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'status' => 'boolean',
            'is_hot' => 'boolean',
            'language' => 'nullable|string|max:10',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $data = $request->all();

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_' . Str::slug($request->name) . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->storeAs('products', $imageName, 'public');
                $data['image'] = $imagePath;
            }

            $product = Product::create($data);

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
            $product = Product::with(['category', 'carts', 'orderDetails'])
                ->findOrFail($id);

            // Get related products from same category
            $relatedProducts = Product::where('category_id', $product->category_id)
                ->where('id', '!=', $product->id)
                ->where('status', 1)
                ->limit(4)
                ->get();

            return view('admin.products.show', compact('product', 'relatedProducts'));
        } catch (\Exception $e) {
            return back()->with('error', 'Product not found: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $product = Product::findOrFail($id);

            $categories = Category::select('id', 'name')
                ->orderBy('name')
                ->get();

            return view('admin.products.edit', compact('product', 'categories'));
        } catch (\Exception $e) {
            return back()->with('error', 'Product not found: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'specifications' => 'nullable|array',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'status' => 'boolean',
            'is_hot' => 'boolean',
            'language' => 'nullable|string|max:10',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $product = Product::findOrFail($id);
            $data = $request->all();

            if ($request->hasFile('image')) {
                if ($product->image && Storage::disk('public')->exists($product->image)) {
                    Storage::disk('public')->delete($product->image);
                }

                $image = $request->file('image');
                $imageName = time() . '_' . Str::slug($request->name) . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->storeAs('products', $imageName, 'public');
                $data['image'] = $imagePath;
            }

            // Update product
            $product->update($data);

            return redirect()
                ->route('admin.products.show', $product->id)
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

            // Delete image
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }

            // Soft delete
            $product->delete();

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

            // Check if it's an AJAX request
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "Product marked as {$status} successfully!",
                    'is_hot' => $product->is_hot,
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

            // Check if it's an AJAX request
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "Product marked as {$status} successfully!",
                    'status' => $product->status,
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

    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'exists:products,id',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->with('error', 'Invalid product IDs');
        }

        try {
            $products = Product::whereIn('id', $request->ids)->get();

            foreach ($products as $product) {
                // Delete image
                if ($product->image && Storage::disk('public')->exists($product->image)) {
                    Storage::disk('public')->delete($product->image);
                }
                $product->delete();
            }

            $count = count($request->ids);

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "{$count} products deleted successfully!",
                ]);
            }

            return back()->with('success', "{$count} products deleted successfully!");
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete products: ' . $e->getMessage(),
                ], 500);
            }

            return back()->with('error', 'Failed to delete products: ' . $e->getMessage());
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

            // Delete image
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }

            $product->forceDelete();

            return redirect()
                ->route('admin.products.index')
                ->with('success', 'Product permanently deleted!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to permanently delete product: ' . $e->getMessage());
        }
    }
}

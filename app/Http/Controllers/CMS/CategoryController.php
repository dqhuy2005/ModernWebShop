<?php

namespace App\Http\Controllers\CMS;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Services\ImageService;

class CategoryController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function index(Request $request)
    {
        $query = Category::select('id', 'name', 'slug', 'image', 'created_at', 'updated_at', 'deleted_at')
            ->withCount('products')
            ->withTrashed();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        $query->orderBy('created_at', 'desc');

        $perPage = $request->get('per_page', 15);
        $categories = $query->paginate($perPage);
        if ($request->ajax()) {
            return view('admin.categories.table', compact('categories'))->render();
        }

        return view('admin.categories.index', compact('categories'));
    }



    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'slug' => 'nullable|string|max:255|unique:categories,slug',
            'image' => 'nullable|string|max:500',
            'language' => 'nullable|string|max:10',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        Category::create($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Category created successfully!'
            ]);
        }

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category created successfully!');
    }

    public function show(Category $category)
    {
        $category->load(['products', 'parent', 'children']);
        return view('admin.categories.show', compact('category'));
    }

    public function edit(Category $category)
    {
        $categories = Category::select('id', 'name', 'slug')
            ->whereNull('deleted_at')
            ->where('id', '!=', $category->id)
            ->get();

        return view('admin.categories.edit', compact('category', 'categories'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'slug' => 'nullable|string|max:255|unique:categories,slug,' . $category->id,
            'image' => 'nullable|string|max:500',
            'language' => 'nullable|string|max:10',
            'parent_id' => 'nullable|exists:categories,id'
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Image is now a path from LFM, no upload needed
        // Just save the path directly

        $category->update($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Category updated successfully!'
            ]);
        }

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category updated successfully!');
    }

    public function destroy(Category $category)
    {
        $category->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully!'
            ]);
        }

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category deleted successfully!');
    }

    public function restore($id)
    {
        $category = Category::withTrashed()->findOrFail($id);
        $category->restore();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Category restored successfully!'
            ]);
        }

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category restored successfully!');
    }

    public function forceDelete($id)
    {
        $category = Category::withTrashed()->findOrFail($id);

        if ($category->image) {
            $this->imageService->deleteImage($category->image);
        }

        $category->forceDelete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Category permanently deleted!'
            ]);
        }

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category permanently deleted!');
    }
}

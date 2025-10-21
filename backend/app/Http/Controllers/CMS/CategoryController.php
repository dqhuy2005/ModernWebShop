<?php

namespace App\Http\Controllers\CMS;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories with filters, sort, and pagination
     */
    public function index(Request $request)
    {
        $query = Category::withCount('products');

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->whereNull('deleted_at');
            } elseif ($request->status === 'inactive') {
                $query->onlyTrashed();
            }
        }

        // Sort
        $sortField = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        $allowedSorts = ['name', 'slug', 'created_at', 'updated_at'];
        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($sortField, $sortOrder);
        }

        // Get statistics
        $stats = $this->getCategoryStatistics();

        // Pagination
        $perPage = $request->get('per_page', 10);
        $categories = $query->withTrashed()->paginate($perPage);

        if ($request->ajax()) {
            return view('admin.categories.table', compact('categories', 'stats'))->render();
        }

        return view('admin.categories.index', compact('categories', 'stats'));
    }

    /**
     * Get category statistics (optimized single query)
     */
    protected function getCategoryStatistics()
    {
        return Category::selectRaw('
            COUNT(*) as total,
            SUM(CASE WHEN deleted_at IS NULL THEN 1 ELSE 0 END) as active,
            SUM(CASE WHEN deleted_at IS NOT NULL THEN 1 ELSE 0 END) as inactive
        ')->withTrashed()->first();
    }

    /**
     * Show the form for creating a new category
     */
    public function create()
    {
        return view('admin.categories.create');
    }

    /**
     * Store a newly created category
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'slug' => 'nullable|string|max:255|unique:categories,slug',
            'parent_id' => 'nullable|exists:categories,id'
        ]);

        // Auto-generate slug if not provided
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

    /**
     * Display the specified category
     */
    public function show(Category $category)
    {
        $category->load('products');
        return view('admin.categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified category
     */
    public function edit(Category $category)
    {
        $categories = Category::whereNull('deleted_at')
            ->where('id', '!=', $category->id)
            ->get();

        return view('admin.categories.edit', compact('category', 'categories'));
    }

    /**
     * Update the specified category
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'slug' => 'nullable|string|max:255|unique:categories,slug,' . $category->id,
            'parent_id' => 'nullable|exists:categories,id'
        ]);

        // Auto-generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

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

    /**
     * Soft delete the specified category
     */
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

    /**
     * Restore a soft deleted category
     */
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

    /**
     * Permanently delete a category
     */
    public function forceDelete($id)
    {
        $category = Category::withTrashed()->findOrFail($id);

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

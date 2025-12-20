<?php

namespace App\Http\Controllers\CMS;

use App\DTOs\CategoryData;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Services\CMS\CategoryService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct(
        private CategoryService $categoryService,
        private CategoryRepositoryInterface $categoryRepository
    ) {
    }

    public function index(Request $request)
    {
        $filters = [
            'search' => $request->input('search'),
        ];

        $perPage = $request->get('per_page', 15);
        $categories = $this->categoryRepository->paginate($filters, $perPage);

        if ($request->ajax()) {
            return view('admin.categories.table', compact('categories'))->render();
        }

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(StoreCategoryRequest $request)
    {
        try {
            $category = $this->categoryService->createCategory(
                CategoryData::fromRequest($request)
            );

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Category created successfully!'
                ]);
            }

            return redirect()->route('admin.categories.index')
                ->with('success', 'Category created successfully!');

        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create category: ' . $e->getMessage()
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', 'Failed to create category: ' . $e->getMessage());
        }
    }

    public function show(Category $category)
    {
        $category->load(['products', 'parent', 'children']);
        return view('admin.categories.show', compact('category'));
    }

    public function edit(Category $category)
    {
        $categories = $this->categoryRepository->getAllExcept($category->id);

        return view('admin.categories.edit', compact('category', 'categories'));
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        try {
            $category = $this->categoryService->updateCategory(
                $category,
                CategoryData::fromRequest($request)
            );

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Category updated successfully!'
                ]);
            }

            return redirect()->route('admin.categories.index')
                ->with('success', 'Category updated successfully!');

        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update category: ' . $e->getMessage()
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', 'Failed to update category: ' . $e->getMessage());
        }
    }

    public function destroy(Category $category)
    {
        try {
            $this->categoryService->deleteCategory($category);

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Category deleted successfully!'
                ]);
            }

            return redirect()->route('admin.categories.index')
                ->with('success', 'Category deleted successfully!');

        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete category: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Failed to delete category: ' . $e->getMessage());
        }
    }

    public function restore($id)
    {
        try {
            $this->categoryService->restoreCategory($id);

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Category restored successfully!'
                ]);
            }

            return redirect()->route('admin.categories.index')
                ->with('success', 'Category restored successfully!');

        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to restore category: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Failed to restore category: ' . $e->getMessage());
        }
    }

    public function forceDelete($id)
    {
        try {
            $this->categoryService->forceDeleteCategory($id);

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Category permanently deleted!'
                ]);
            }

            return redirect()->route('admin.categories.index')
                ->with('success', 'Category permanently deleted!');

        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to permanently delete category: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Failed to permanently delete category: ' . $e->getMessage());
        }
    }
}

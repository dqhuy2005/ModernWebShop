<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends AppBaseController
{
    public function index()
    {
        try {
            $categories = Category::with('children')->get();
            return $this->sendResponse($categories, 'Categories retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve categories: ' . $e->getMessage(), 500);
        }
    }

    public function getParentCategories()
    {
        try {
            $categories = Category::parent()
                ->with('children')
                ->get();
            return $this->sendResponse($categories, 'Parent categories retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve parent categories: ' . $e->getMessage(), 500);
        }
    }

    public function getChildCategories()
    {
        try {
            $categories = Category::child()
                ->with('parent')
                ->get();
            return $this->sendResponse($categories, 'Child categories retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve child categories: ' . $e->getMessage(), 500);
        }
    }

    public function show($id)
    {
        try {
            $category = Category::with(['children', 'products'])->find($id);

            if (!$category) {
                return $this->sendError('Category not found', 404);
            }

            return $this->sendResponse($category, 'Category retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve category: ' . $e->getMessage(), 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|integer|exists:categories,id',
            'language' => 'nullable|string|max:10',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationErrorWithDetails($validator->errors()->toArray());
        }

        try {
            $category = Category::create($request->all());
            return response()->json([
                'success' => true,
                'message' => 'Category created successfully',
                'data' => $category
            ], 201);
        } catch (\Exception $e) {
            return $this->sendError('Failed to create category: ' . $e->getMessage(), 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'parent_id' => 'nullable|integer|exists:categories,id',
            'language' => 'nullable|string|max:10',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationErrorWithDetails($validator->errors()->toArray());
        }

        try {
            $category = Category::find($id);

            if (!$category) {
                return $this->sendError('Category not found', 404);
            }

            if ($request->has('parent_id') && $request->parent_id == $id) {
                return $this->sendError('Category cannot be its own parent', 422);
            }

            $category->update($request->all());
            return $this->sendResponse($category, 'Category updated successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to update category: ' . $e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try {
            $category = Category::find($id);

            if (!$category) {
                return $this->sendError('Category not found', 404);
            }

            if ($category->children()->count() > 0) {
                return $this->sendError('Cannot delete category with child categories', 422);
            }

            if ($category->products()->count() > 0) {
                return $this->sendError('Cannot delete category with products', 422);
            }

            $category->delete();
            return $this->sendResponse(null, 'Category deleted successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to delete category: ' . $e->getMessage(), 500);
        }
    }
}

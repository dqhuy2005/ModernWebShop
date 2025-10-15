<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\AppBaseController;
use App\Repositories\ProductRepository;
use App\Repositories\CategoryRepository;
class ProductController extends AppBaseController
{
    private $productRepository;
    private $categoryRepository;

    public function __construct(ProductRepository $productRepo, CategoryRepository $categoryRepo)
    {
        $this->productRepository = $productRepo;
        $this->categoryRepository = $categoryRepo;
    }

    public function index()
    {
        $products = $this->productRepository->findBuild()->all();
        return $this->sendResponse($products->toArray(), 'Products retrieved successfully');
    }

    public function show($id)
    {
        $product = $this->productRepository->findBuild()->find($id);
        if (empty($product)) {
            return $this->sendError('Product not found');
        }
        return $this->sendResponse($product->toArray(), 'Product retrieved successfully');
    }

    public function productsByCategory($categoryId)
    {
        $category = $this->categoryRepository->find($categoryId);
        if (empty($category)) {
            return $this->sendError('Category not found');
        }
        $products = $this->productRepository->scopeQuery(function($query) use ($categoryId) {
            return $query->where('category_id', $categoryId);
        })->all();
        return $this->sendResponse($products->toArray(), 'Products by category retrieved successfully');
    }


}
?>

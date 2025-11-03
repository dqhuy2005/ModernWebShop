<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductFilterRequest;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;

class HomeController extends Controller
{
    protected $productRepository;
    protected $categoryRepository;

    public function __construct(
        ProductRepository $productRepository,
        CategoryRepository $categoryRepository
    ) {
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
    }

    public function index()
    {
        $featuredCategories = $this->categoryRepository->getFeaturedCategories(3);

        $newProducts = $this->productRepository->getNewProducts(8);

        $categories = $this->categoryRepository->getCategoriesWithHotProducts(5, 15);

        $topSellingProducts = $this->productRepository
            ->getTopSellingProducts(12)
            ->chunk(6);

        $hotDeals = $this->productRepository->getHotDeals(8);

        return view('user.home', compact(
            'featuredCategories',
            'newProducts',
            'categories',
            'topSellingProducts',
            'hotDeals'
        ));
    }

    public function showCategory($slug, ProductFilterRequest $request)
    {
        $category = $this->categoryRepository->findBySlug($slug);

        $filters = $request->getFilters();

        $products = $this->productRepository
            ->getFilteredProducts($category->id, $filters)
            ->paginate(12)
            ->withQueryString();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'html' => view('user.partials.product-grid', compact('products'))->render(),
                'pagination' => view('user.partials.pagination', compact('products'))->render()
            ]);
        }

        return view('user.category', compact('category', 'products', 'filters'));
    }

    public function hotDeals()
    {
        $hotDeals = $this->productRepository->getPaginatedHotDeals(12);

        return view('user.hot-deals', compact('hotDeals'));
    }

    public function searchSuggestions(Request $request)
    {
        $keyword = $request->input('keyword', '');

        $products = $this->productRepository->searchSuggestions($keyword, 10);

        return response()->json([
            'success' => true,
            'products' => $products
        ]);
    }
}

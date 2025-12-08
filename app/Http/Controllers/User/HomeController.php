<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductFilterRequest;
use App\Repository\impl\ProductRepository;
use App\Repository\impl\CategoryRepository;
use App\Services\impl\HomePageService;

class HomeController extends Controller
{
    protected $productRepository;
    protected $categoryRepository;
    protected $homePageService;

    public function __construct(
        ProductRepository $productRepository,
        CategoryRepository $categoryRepository,
        HomePageService $homePageService
    ) {
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
        $this->homePageService = $homePageService;
    }

    public function index()
    {
        $data = $this->homePageService->getHomePageData();

        return view('user.home', $data);
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

    public function search(Request $request)
    {
        $keyword = $request->input('q', '');
        $priceRange = $request->input('price_range', '');
        $sort = $request->input('sort', 'best_selling');

        if (empty($keyword) || strlen($keyword) < 2) {
            return redirect()->route('home')->with('error', 'Vui lòng nhập từ khóa tìm kiếm (tối thiểu 2 ký tự)');
        }

        $query = $this->productRepository->getSearchResults($keyword, [
            'price_range' => $priceRange,
            'sort' => $sort
        ]);

        $products = $query->paginate(12)->withQueryString();
        $categories = $this->categoryRepository->all(['id', 'name', 'slug']);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'html' => view('user.partials.product-grid', compact('products'))->render(),
                'pagination' => view('user.partials.pagination', compact('products'))->render()
            ]);
        }

        return view('user.search', compact('products', 'categories', 'keyword', 'priceRange', 'sort'));
    }
}

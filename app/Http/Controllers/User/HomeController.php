<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductFilterRequest;
use App\Repository\impl\ProductRepository;
use App\Repository\impl\CategoryRepository;
use App\Services\impl\HomePageService;
use App\Services\impl\SearchHistoryService;

class HomeController extends Controller
{
    protected $productRepository;
    protected $categoryRepository;
    protected $homePageService;
    protected $searchHistoryService;

    public function __construct(
        ProductRepository $productRepository,
        CategoryRepository $categoryRepository,
        HomePageService $homePageService,
        SearchHistoryService $searchHistoryService
    ) {
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
        $this->homePageService = $homePageService;
        $this->searchHistoryService = $searchHistoryService;
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
        $sessionId = session()->getId();

        // Get search history
        $history = $this->searchHistoryService->getSearchHistory($sessionId, 10);

        // If no keyword, return only history
        if (empty($keyword)) {
            return response()->json([
                'success' => true,
                'history' => $history,
                'products' => []
            ]);
        }

        // Get product suggestions
        $products = $this->productRepository->searchSuggestions($keyword, 10);

        // Filter relevant history
        $relevantHistory = array_filter($history, function ($item) use ($keyword) {
            return stripos($item['keyword'], $keyword) !== false;
        });

        return response()->json([
            'success' => true,
            'history' => array_values($relevantHistory),
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

        // Save search history
        $this->searchHistoryService->saveSearchHistory($keyword, session()->getId());

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

    public function getSearchHistory(Request $request)
    {
        try {
            $sessionId = session()->getId();
            $history = $this->searchHistoryService->getSearchHistory($sessionId, 10);

            return response()->json([
                'success' => true,
                'data' => $history ?? []
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể tải lịch sử tìm kiếm',
                'data' => []
            ], 500);
        }
    }

    /**
     * Delete search history item
     */
    public function deleteSearchHistory(Request $request, $id)
    {
        try {
            if (!$id || !is_numeric($id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'ID không hợp lệ'
                ], 400);
            }

            $sessionId = session()->getId();
            $deleted = $this->searchHistoryService->deleteSearchHistory($id, $sessionId);

            if ($deleted) {
                return response()->json([
                    'success' => true,
                    'message' => 'Đã xóa lịch sử tìm kiếm'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy lịch sử tìm kiếm'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể xóa lịch sử tìm kiếm'
            ], 500);
        }
    }

    /**
     * Clear all search history
     */
    public function clearSearchHistory(Request $request)
    {
        try {
            $sessionId = session()->getId();
            $count = $this->searchHistoryService->clearAllHistory($sessionId);

            return response()->json([
                'success' => true,
                'message' => $count > 0 ? "Đã xóa {$count} lịch sử tìm kiếm" : 'Không có lịch sử nào để xóa',
                'count' => $count
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể xóa lịch sử tìm kiếm'
            ], 500);
        }
    }

    /**
     * Get popular keywords
     */
    public function getPopularKeywords(Request $request)
    {
        try {
            $keywords = $this->searchHistoryService->getPopularKeywords(10);

            return response()->json([
                'success' => true,
                'data' => $keywords ?? []
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể tải danh sách từ khóa phổ biến',
                'data' => []
            ], 500);
        }
    }
}

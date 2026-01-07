<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductFilterRequest;
use App\Repository\impl\ProductRepository;
use App\Repository\impl\CategoryRepository;
use App\Services\impl\HomePageService;
use App\Services\impl\SearchHistoryService;
use App\Services\impl\SearchService;
use App\Services\impl\CategoryService;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    protected $productRepository;
    protected $categoryRepository;
    protected $homePageService;
    protected $searchHistoryService;
    protected $searchService;
    protected $categoryService;

    public function __construct(
        ProductRepository $productRepository,
        CategoryRepository $categoryRepository,
        HomePageService $homePageService,
        SearchHistoryService $searchHistoryService,
        SearchService $searchService,
        CategoryService $categoryService
    ) {
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
        $this->homePageService = $homePageService;
        $this->searchHistoryService = $searchHistoryService;
        $this->searchService = $searchService;
        $this->categoryService = $categoryService;
    }

    public function index()
    {
        $data = $this->homePageService->getHomePageData();

        $data['navigationCategories'] = $this->homePageService->getNavigationCategories();
        $data['displayCategories'] = $this->homePageService->getDisplayCategories();

        return view('user.home', $data);
    }

    public function showCategory($slug, ProductFilterRequest $request)
    {
        $category = $this->categoryRepository->findBySlug($slug);
        $filters = $request->getFilters();

        $products = $this->categoryService->getFilteredProducts($category->id, $filters, 12);

        if ($request->ajax()) {
            return response()->json($this->categoryService->formatAjaxResponse($products));
        }

        return view('user.category', compact('category', 'products', 'filters'));
    }

    public function search(Request $request)
    {
        $keyword = $request->input('q', '');
        $priceRange = $request->input('price_range', '');
        $sort = $request->input('sort', 'best_selling');

        if (empty($keyword) || strlen($keyword) < 2) {
            return redirect()->route('home')->with('error', 'Vui lòng nhập từ khóa tìm kiếm (tối thiểu 2 ký tự)');
        }

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
            $keyword = trim($request->input('q', ''));
            $sessionId = session()->getId();

            if (empty($keyword)) {
                return $this->handleSearchHistoryRequest($sessionId, $request, 15);
            }

            return $this->handleSearchInHistoryRequest($keyword, $sessionId, 15);

        } catch (\Exception $e) {
            Log::error('Search history error: ' . $e->getMessage(), [
                'keyword' => $request->input('q', ''),
                'session_id' => session()->getId()
            ]);

            $errorResponse = $this->searchService->getErrorResponse();
            return response()->json($errorResponse, 500);
        }
    }

    private function handleSearchHistoryRequest(string $sessionId, Request $request, int $limit = 15)
    {
        $history = $this->searchHistoryService->getSearchHistory($sessionId, $limit);
        $etag = $this->searchService->generateETag($history);

        if ($this->searchService->eTagMatches($request->header('If-None-Match'), $etag)) {
            return response()->json(null, 304);
        }

        $response = $this->searchService->formatSearchHistoryResponse($history, $etag);

        return response()->json($response)
            ->header('Cache-Control', 'private, max-age=300')
            ->header('ETag', $etag);
    }

    private function handleSearchInHistoryRequest(string $keyword, string $sessionId, int $limit = 15)
    {
        $response = $this->searchService->searchInHistory($keyword, $sessionId, $limit);

        if (empty($response['data'])) {
            $response = $this->searchService->getEmptyHistoryResponse($keyword);
        }

        return response()->json($response)
            ->header('Cache-Control', 'private, max-age=60');
    }

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

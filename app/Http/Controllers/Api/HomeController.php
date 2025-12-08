<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use App\Services\impl\HomeService;
use Illuminate\Http\Request;

class HomeController extends AppBaseController
{
    protected HomeService $homeService;

    public function __construct(HomeService $homeService)
    {
        $this->homeService = $homeService;
    }

    public function index(Request $request)
    {
        try {
            $perPage = (int) $request->get('per_page', 10);
            $page = (int) $request->get('page', 1);

            $data = $this->homeService->getHomeProducts($perPage, $page);

            return $this->sendResponse($data, 'Products retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve products: ' . $e->getMessage(), 500);
        }
    }

    public function search(Request $request)
    {
        try {
            $search = trim($request->get('q', ''));
            $perPage = (int) $request->get('per_page', 10);

            if (empty($search)) {
                return $this->sendError('Search query is required', 400);
            }

            $data = $this->homeService->searchProducts($search, $perPage);

            return $this->sendResponse($data, 'Search results retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Search failed: ' . $e->getMessage(), 500);
        }
    }
}

<?php

namespace App\Http\Controllers\CMS;

use App\Http\Controllers\Controller;
use App\Services\HomePageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CacheManagementController extends Controller
{
    protected HomePageService $homePageService;

    public function __construct(HomePageService $homePageService)
    {
        $this->homePageService = $homePageService;
    }

    public function index()
    {
        $stats = $this->homePageService->getCacheStats();

        $hits = count(array_filter($stats, fn($status) => $status === 'HIT'));
        $misses = count(array_filter($stats, fn($status) => $status === 'MISS'));
        $total = count($stats);
        $hitRate = $total > 0 ? round(($hits / $total) * 100, 2) : 0;

        return view('admin.cache.index', compact(
            'stats',
            'hits',
            'misses',
            'total',
            'hitRate'
        ));
    }

    public function clearHomePage(Request $request)
    {
        try {
            $this->homePageService->clearHomePageCache();

            $warmUp = $request->input('warm_up', false);

            if ($warmUp) {
                $startTime = microtime(true);
                $this->homePageService->warmUpCache();
                $duration = round((microtime(true) - $startTime) * 1000, 2);

                return response()->json([
                    'success' => true,
                    'message' => "Cache cleared and warmed up successfully in {$duration}ms"
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Homepage cache cleared successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error clearing cache: ' . $e->getMessage()
            ], 500);
        }
    }

    public function warmUp()
    {
        try {
            $startTime = microtime(true);

            $this->homePageService->warmUpCache();

            $duration = round((microtime(true) - $startTime) * 1000, 2);

            return response()->json([
                'success' => true,
                'message' => "Cache warmed up successfully in {$duration}ms"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error warming up cache: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getStats()
    {
        try {
            $stats = $this->homePageService->getCacheStats();

            $hits = count(array_filter($stats, fn($status) => $status === 'HIT'));
            $misses = count(array_filter($stats, fn($status) => $status === 'MISS'));
            $total = count($stats);
            $hitRate = $total > 0 ? round(($hits / $total) * 100, 2) : 0;

            return response()->json([
                'success' => true,
                'data' => [
                    'stats' => $stats,
                    'hits' => $hits,
                    'misses' => $misses,
                    'total' => $total,
                    'hit_rate' => $hitRate
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching stats: ' . $e->getMessage()
            ], 500);
        }
    }

    public function clearAll(Request $request)
    {
        try {
            Cache::flush();

            $warmUp = $request->input('warm_up', false);

            if ($warmUp) {
                $this->homePageService->warmUpCache();
                return response()->json([
                    'success' => true,
                    'message' => 'All caches cleared and homepage cache warmed up successfully'
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'All caches cleared successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error clearing all caches: ' . $e->getMessage()
            ], 500);
        }
    }
}

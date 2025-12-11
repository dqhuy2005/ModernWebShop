<?php

namespace App\Services\impl;

use App\Models\SearchHistory;
use App\Services\ISearchService;
use App\Services\impl\RedisService;
use Illuminate\Support\Facades\Auth;

class SearchService implements ISearchService
{
    protected $redisService;

    public function __construct(RedisService $redisService)
    {
        $this->redisService = $redisService;
    }
    public function formatSearchHistoryResponse(array $history, ?string $etag = null): array
    {
        return [
            'success' => true,
            'type' => 'history',
            'data' => $history,
            'cached' => !empty($history),
            'etag' => $etag ?? md5(json_encode($history))
        ];
    }

    public function searchInHistory(string $keyword, string $sessionId, int $limit = 10): array
    {
        $userId = Auth::id();
        $sanitizedKeyword = $this->sanitizeSearchKeyword($keyword);
        $cacheKey = $this->getSearchCacheKey($userId, $sessionId, $sanitizedKeyword);

        return $this->redisService->remember($cacheKey, 300, function () use ($userId, $sessionId, $sanitizedKeyword, $keyword, $limit) {
            $query = SearchHistory::query();

            if ($userId) {
                $query->where('user_id', $userId);
            } else {
                $query->where('session_id', $sessionId)
                    ->whereNull('user_id');
            }

            $history = $query->where('keyword', 'LIKE', "%{$sanitizedKeyword}%")
                ->select(['id', 'keyword', 'search_count', 'created_at', 'updated_at'])
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get()
                ->toArray();

            return [
                'success' => true,
                'type' => 'history',
                'data' => $history,
                'keyword' => $keyword,
                'count' => count($history)
            ];
        });
    }

    private function getSearchCacheKey(?int $userId, string $sessionId, string $keyword): string
    {
        if ($userId) {
            return "search_query:user:{$userId}:keyword:" . md5($keyword);
        }
        return "search_query:session:{$sessionId}:keyword:" . md5($keyword);
    }

    public function generateETag($data): string
    {
        return md5(json_encode($data));
    }

    public function eTagMatches(?string $requestETag, string $currentETag): bool
    {
        return $requestETag === $currentETag;
    }

    private function sanitizeSearchKeyword(string $keyword): string
    {
        return str_replace(['%', '_'], ['\\%', '\\_'], trim($keyword));
    }

    public function isValidKeyword(string $keyword): bool
    {
        $trimmed = trim($keyword);
        return !empty($trimmed) && strlen($trimmed) >= 1;
    }

    public function getErrorResponse(string $message = 'Không thể tải dữ liệu'): array
    {
        return [
            'success' => false,
            'message' => $message,
            'data' => []
        ];
    }

    public function getEmptyHistoryResponse(string $keyword): array
    {
        return [
            'success' => true,
            'type' => 'history',
            'data' => [],
            'keyword' => $keyword,
            'count' => 0,
            'message' => 'Không có trong lịch sử tìm kiếm'
        ];
    }
}

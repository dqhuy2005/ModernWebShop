<?php

namespace App\Services\impl;

use App\Models\SearchHistory;
use App\Services\ISearchHistoryService;
use App\Services\impl\RedisService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SearchHistoryService implements ISearchHistoryService
{
    protected $redisService;

    public function __construct(RedisService $redisService)
    {
        $this->redisService = $redisService;
    }
    public function saveSearchHistory(string $keyword, ?string $sessionId = null): void
    {
        $keyword = trim($keyword);

        if (empty($keyword) || strlen($keyword) < 1) {
            return;
        }

        $userId = Auth::id();
        $ipAddress = request()->ip();
        $userAgent = request()->userAgent();

        $history = SearchHistory::where(function ($query) use ($userId, $sessionId) {
            if ($userId) {
                $query->where('user_id', $userId);
            } else {
                $query->where('session_id', $sessionId)->whereNull('user_id');
            }
        })
            ->where('keyword', $keyword)
            ->first();

        if ($history) {
            $history->increment('search_count');
            $history->touch();
        } else {
            SearchHistory::create([
                'user_id' => $userId,
                'session_id' => $sessionId,
                'keyword' => $keyword,
                'search_count' => 1,
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
            ]);
        }

        $this->clearCache($userId, $sessionId);
    }

    public function getSearchHistory(?string $sessionId = null, int $limit = 10): array
    {
        $userId = Auth::id();
        $cacheKey = $this->getCacheKey($userId, $sessionId);

        $cacheDuration = $userId ? 300 : 600;

        return $this->redisService->remember($cacheKey, $cacheDuration, function () use ($userId, $sessionId, $limit) {
            $query = SearchHistory::query();

            if ($userId) {
                $query->forUser($userId);
            } else {
                $query->forSession($sessionId);
            }

            return $query->select(['id', 'keyword', 'search_count'])
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get()
                ->toArray();
        });
    }

    public function deleteSearchHistory(int $id, ?string $sessionId = null): bool
    {
        $userId = Auth::id();

        $query = SearchHistory::where('id', $id);

        if ($userId) {
            $query->where('user_id', $userId);
        } else {
            $query->where('session_id', $sessionId)->whereNull('user_id');
        }

        $deleted = $query->delete() > 0;

        if ($deleted) {
            $this->clearCache($userId, $sessionId);
        }

        return $deleted;
    }

    public function clearAllHistory(?string $sessionId = null): int
    {
        $userId = Auth::id();

        $query = SearchHistory::query();

        if ($userId) {
            $query->where('user_id', $userId);
        } else {
            $query->where('session_id', $sessionId)->whereNull('user_id');
        }

        $count = $query->delete();

        $this->clearCache($userId, $sessionId);

        return $count;
    }

    public function migrateSessionToUser(int $userId, string $sessionId): void
    {
        $sessionHistories = SearchHistory::query()->forSession($sessionId)->get();

        foreach ($sessionHistories as $sessionHistory) {
            $userHistory = SearchHistory::query()->forUser($userId)
                ->where('keyword', $sessionHistory->keyword)
                ->first();

            if ($userHistory) {
                $userHistory->search_count += $sessionHistory->search_count;
                $userHistory->save();
                $sessionHistory->delete();
            } else {
                $sessionHistory->update([
                    'user_id' => $userId,
                    'session_id' => null,
                ]);
            }
        }

        $this->clearCache($userId, $sessionId);
    }

    public function getPopularKeywords(int $limit = 10): array
    {
        $cacheKey = 'search_history:popular:' . $limit;

        return $this->redisService->remember($cacheKey, 3600, function () use ($limit) {
            return SearchHistory::select('keyword', DB::raw('SUM(search_count) as total_searches'))
                ->groupBy('keyword')
                ->orderBy('total_searches', 'desc')
                ->limit($limit)
                ->get()
                ->toArray();
        });
    }

    public function clearUserCache(int $userId): void
    {
        $userCacheKey = "search_history:user:{$userId}";
        $this->redisService->forget($userCacheKey);
    }

    public function cleanOldHistories(int $days = 30): int
    {
        return SearchHistory::olderThan($days)->delete();
    }

    private function getCacheKey(?int $userId, ?string $sessionId): string
    {
        if ($userId) {
            return "search_history:user:{$userId}";
        }
        return "search_history:session:{$sessionId}";
    }

    private function clearCache(?int $userId, ?string $sessionId): void
    {
        $cacheKey = $this->getCacheKey($userId, $sessionId);
        $this->redisService->forget($cacheKey);

        if ($userId && $sessionId) {
            $userCacheKey = "search_history:user:{$userId}";
            $sessionCacheKey = "search_history:session:{$sessionId}";
            $this->redisService->forget([$userCacheKey, $sessionCacheKey]);
        }
    }
}

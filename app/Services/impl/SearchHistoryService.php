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
        try {
            DB::transaction(function () use ($userId, $sessionId) {
                $sessionHistories = SearchHistory::query()
                    ->forSession($sessionId)
                    ->select('keyword', DB::raw('SUM(search_count) as total_count'))
                    ->groupBy('keyword')
                    ->get();

                if ($sessionHistories->isEmpty()) {
                    return;
                }

                $keywords = $sessionHistories->pluck('keyword')->toArray();
                $existingUserHistories = SearchHistory::query()
                    ->forUser($userId)
                    ->whereIn('keyword', $keywords)
                    ->get()
                    ->keyBy('keyword');

                $toUpdate = [];
                $toInsert = [];

                foreach ($sessionHistories as $sessionHistory) {
                    $keyword = $sessionHistory->keyword;

                    if ($existingUserHistories->has($keyword)) {
                        $existingId = $existingUserHistories[$keyword]->id;
                        $toUpdate[] = [
                            'id' => $existingId,
                            'search_count' => $existingUserHistories[$keyword]->search_count + $sessionHistory->total_count
                        ];
                    } else {
                        $toInsert[] = [
                            'user_id' => $userId,
                            'session_id' => null,
                            'keyword' => $keyword,
                            'search_count' => $sessionHistory->total_count,
                            'ip_address' => request()->ip(),
                            'user_agent' => request()->userAgent(),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }

                if (!empty($toUpdate)) {
                    foreach ($toUpdate as $update) {
                        SearchHistory::where('id', $update['id'])
                            ->update(['search_count' => $update['search_count']]);
                    }
                }

                if (!empty($toInsert)) {
                    SearchHistory::insert($toInsert);
                }

                SearchHistory::query()->forSession($sessionId)->delete();
            });

            $this->clearCache($userId, $sessionId);

        } catch (\Exception $e) {
            Log::error('Failed to migrate search history', [
                'user_id' => $userId,
                'session_id' => $sessionId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
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

<?php

namespace App\Services\impl;

use App\Models\SearchHistory;
use App\Services\ISearchHistoryService;
use App\Services\impl\RedisService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
            $sessionHistories = SearchHistory::query()
                ->forSession($sessionId)
                ->select('keyword', DB::raw('SUM(search_count) as total_count'), DB::raw('MAX(created_at) as last_search'))
                ->groupBy('keyword')
                ->get();

            if ($sessionHistories->isEmpty()) {
                return;
            }

            $keywords = $sessionHistories->pluck('keyword')->toArray();

            $existingUserHistories = SearchHistory::query()
                ->forUser($userId)
                ->whereIn('keyword', $keywords)
                ->get(['id', 'keyword', 'search_count'])
                ->keyBy('keyword');

            DB::transaction(function () use ($userId, $sessionId, $sessionHistories, $existingUserHistories) {
                $toUpdate = [];
                $toInsert = [];

                foreach ($sessionHistories as $sessionHistory) {
                    $keyword = $sessionHistory->keyword;

                    if ($existingUserHistories->has($keyword)) {
                        $existing = $existingUserHistories[$keyword];
                        $toUpdate[] = [
                            'id' => $existing->id,
                            'search_count' => $existing->search_count + $sessionHistory->total_count
                        ];
                    } else {
                        $toInsert[] = [
                            'user_id' => $userId,
                            'session_id' => null,
                            'keyword' => $keyword,
                            'search_count' => $sessionHistory->total_count,
                            'ip_address' => request()->ip(),
                            'user_agent' => request()->userAgent(),
                            'created_at' => $sessionHistory->last_search ?? now(),
                            'updated_at' => now(),
                        ];
                    }
                }

                if (!empty($toUpdate)) {
                    $cases = [];
                    $ids = [];
                    foreach ($toUpdate as $update) {
                        $ids[] = $update['id'];
                        $cases[] = "WHEN {$update['id']} THEN {$update['search_count']}";
                    }

                    if (!empty($cases)) {
                        $caseString = implode(' ', $cases);
                        $idsString = implode(',', $ids);
                        DB::statement("UPDATE search_histories SET search_count = CASE id {$caseString} END, updated_at = NOW() WHERE id IN ({$idsString})");
                    }
                }

                if (!empty($toInsert)) {
                    foreach (array_chunk($toInsert, 1000) as $chunk) {
                        SearchHistory::insert($chunk);
                    }
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

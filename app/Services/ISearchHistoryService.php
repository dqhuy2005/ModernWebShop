<?php

namespace App\Services;

interface ISearchHistoryService
{
    public function saveSearchHistory(string $keyword, ?string $sessionId = null): void;

    public function getSearchHistory(?string $sessionId = null, int $limit = 10): array;

    public function deleteSearchHistory(int $id, ?string $sessionId = null): bool;

    public function clearAllHistory(?string $sessionId = null): int;

    public function migrateSessionToUser(int $userId, string $sessionId): void;

    public function getPopularKeywords(int $limit = 10): array;

    public function cleanOldHistories(int $days = 30): int;
}

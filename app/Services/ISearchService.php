<?php

namespace App\Services;

interface ISearchService
{
    public function formatSearchHistoryResponse(array $history, ?string $etag = null): array;

    public function searchInHistory(string $keyword, string $sessionId, int $limit = 10): array;

    public function generateETag($data): string;

    public function eTagMatches(?string $requestETag, string $currentETag): bool;

    public function isValidKeyword(string $keyword): bool;

    public function getErrorResponse(string $message = 'Không thể tải dữ liệu'): array;

    public function getEmptyHistoryResponse(string $keyword): array;
}

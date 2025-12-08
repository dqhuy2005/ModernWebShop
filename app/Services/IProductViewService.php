<?php

namespace App\Services;

use App\Models\Product;

interface IProductViewService
{
    public function shouldCountView(int $productId, string $ipAddress, ?int $userId = null): bool;

    public function trackView(Product $product, string $ipAddress, ?string $userAgent = null): void;

    public function getRecentViewCount(int $productId, int $days = 7): int;

    public function getUniqueVisitorsCount(int $productId, int $days = 7): int;
}

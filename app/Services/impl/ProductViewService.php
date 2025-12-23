<?php

namespace App\Services\impl;

use App\Events\ProductViewed;
use App\Models\Product;
use App\Models\ProductView;
use App\Services\impl\RedisService;
use Illuminate\Support\Facades\Auth;
use App\Services\IProductViewService;

class ProductViewService implements IProductViewService
{
    protected RedisService $redis;

    public function __construct(RedisService $redis)
    {
        $this->redis = $redis;
    }

    public function shouldCountView(int $productId, string $ipAddress, ?int $userId = null): bool
    {
        $cacheKey = $this->getViewCacheKey($productId, $ipAddress, $userId);

        if ($this->redis->has($cacheKey)) {
            return false;
        }

        $this->redis->set($cacheKey, true, 120);

        return true;
    }

    public function trackView(Product $product, string $ipAddress, ?string $userAgent = null): void
    {
        $userId = Auth::id();

        if (!$this->shouldCountView($product->id, $ipAddress, $userId)) {
            return;
        }

        event(new ProductViewed($product, $ipAddress, $userAgent, $userId));
    }

    private function getViewCacheKey(int $productId, string $ipAddress, ?int $userId): string
    {
        if ($userId) {
            return "product_view_{$productId}_user_{$userId}";
        }

        return "product_view_{$productId}_ip_" . md5($ipAddress);
    }

    public function getRecentViewCount(int $productId, int $days = 7): int
    {
        return $this->redis->remember(
            "product_{$productId}_views_{$days}days",
            300,
            function () use ($productId, $days) {
                return ProductView::query()->forProduct($productId)
                    ->recent($days)
                    ->count();
            }
        );
    }

    public function getUniqueVisitorsCount(int $productId, int $days = 7): int
    {
        return $this->redis->remember(
            "product_{$productId}_unique_{$days}days",
            600,
            function () use ($productId, $days) {
                return ProductView::query()->forProduct($productId)
                    ->recent($days)
                    ->distinct('ip_address')
                    ->count('ip_address');
            }
        );
    }
}

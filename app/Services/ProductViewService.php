<?php

namespace App\Services;

use App\Events\ProductViewed;
use App\Models\Product;
use App\Models\ProductView;
use App\Services\Cache\RedisService;
use Illuminate\Support\Facades\Auth;

class ProductViewService
{
    protected RedisService $redis;

    public function __construct(RedisService $redis)
    {
        $this->redis = $redis;
    }
    /**
     * Check if view should be counted (anti-spam)
     * Chỉ tính 1 view trong 2 phút cho mỗi IP/User
     */
    public function shouldCountView(int $productId, string $ipAddress, ?int $userId = null): bool
    {
        $cacheKey = $this->getViewCacheKey($productId, $ipAddress, $userId);

        if ($this->redis->has($cacheKey)) {
            return false;
        }

        $this->redis->set($cacheKey, true, 120);

        return true;
    }

    /**
     * Track product view
     */
    public function trackView(Product $product, string $ipAddress, ?string $userAgent = null): void
    {
        $userId = Auth::id();

        if (!$this->shouldCountView($product->id, $ipAddress, $userId)) {
            return;
        }

        event(new ProductViewed($product, $ipAddress, $userAgent, $userId));
    }

    /**
     * Get cache key for view tracking
     */
    private function getViewCacheKey(int $productId, string $ipAddress, ?int $userId): string
    {
        if ($userId) {
            return "product_view_{$productId}_user_{$userId}";
        }

        return "product_view_{$productId}_ip_" . md5($ipAddress);
    }

    /**
     * Get recent view count (7 days) - with cache
     */
    public function getRecentViewCount(int $productId, int $days = 7): int
    {
        return $this->redis->remember(
            "product_{$productId}_views_{$days}days",
            300,
            function () use ($productId, $days) {
                return ProductView::forProduct($productId)
                    ->recent($days)
                    ->count();
            }
        );
    }

    /**
     * Get unique visitors count (7 days)
     */
    public function getUniqueVisitorsCount(int $productId, int $days = 7): int
    {
        return $this->redis->remember(
            "product_{$productId}_unique_{$days}days",
            600,
            function () use ($productId, $days) {
                return ProductView::forProduct($productId)
                    ->recent($days)
                    ->distinct('ip_address')
                    ->count('ip_address');
            }
        );
    }

    /**
     * Get hot products (is_hot = true)
     */
    public function getHotProducts(int $limit = 10)
    {
        return $this->redis->remember(
            "hot_products_{$limit}",
            3600,
            function () use ($limit) {
                return Product::where('is_hot', true)
                    ->orderBy('views', 'desc')
                    ->limit($limit)
                    ->get();
            }
        );
    }
}

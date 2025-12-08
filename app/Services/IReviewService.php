<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\User;

interface IReviewService
{
    public function canUserReviewProduct(User $user, Product $product, Order $order): array;

    public function createReview(array $data, ?array $images = null, ?array $videos = null): ProductReview;

    public function updateReview(ProductReview $review, array $data, ?array $newImages = null, ?array $newVideos = null): ProductReview;

    public function deleteReview(ProductReview $review): bool;

    public function getProductReviewStats(Product $product): array;
}

<?php

namespace App\Services;

interface IHomePageService
{
    public function getHomePageData(): array;

    public function getFeaturedCategories(int $limit = 3);

    public function getNewProducts(int $limit = 8);

    public function getCategoriesWithHotProducts(int $categoryLimit = 5, int $productLimit = 15);

    public function getTopSellingProducts(int $limit = 12);

    public function getHotDeals(int $limit = 8);

    public function getNavigationCategories();

    public function getDisplayCategories();

    public function clearHomePageCache(): void;

    public function warmUpCache(): void;

    public function getCacheStats(): array;
}

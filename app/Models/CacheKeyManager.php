<?php

namespace App\Models;

class CacheKeyManager
{
    // Homepage cache keys
    public const HOME_NEW_PRODUCTS = 'home.new_products';
    public const HOME_TOP_SELLING = 'home.top_selling';
    public const HOME_HOT_DEALS = 'home.hot_deals';
    public const HOME_FEATURED_CATEGORIES = 'home.featured_categories';
    public const HOME_CATEGORIES_WITH_PRODUCTS = 'home.categories_with_products';

    // Product cache keys
    public const PRODUCT_PREFIX = 'product.';
    public const PRODUCT_HOT_LIST = 'products.hot_list';
    public const PRODUCT_NEW_LIST = 'products.new_list';

    // Category cache keys
    public const CATEGORY_PREFIX = 'category.';
    public const CATEGORY_LIST = 'categories.list';
    public const CATEGORY_FEATURED = 'categories.featured';

    // Cache TTL (Time To Live) in seconds
    public const TTL_SHORT = 900;        // 15 minutes
    public const TTL_MEDIUM = 1800;      // 30 minutes
    public const TTL_LONG = 3600;        // 60 minutes
    public const TTL_VERY_LONG = 7200;   // 2 hours

    /**
     * Get cache key for specific product
     */
    public static function product(int $id): string
    {
        return self::PRODUCT_PREFIX . $id;
    }

    /**
     * Get cache key for specific category
     */
    public static function category(int $id): string
    {
        return self::CATEGORY_PREFIX . $id;
    }

    /**
     * Get cache key for category by slug
     */
    public static function categoryBySlug(string $slug): string
    {
        return self::CATEGORY_PREFIX . 'slug.' . $slug;
    }

    /**
     * Get all homepage related cache keys
     */
    public static function homePageKeys(): array
    {
        return [
            self::HOME_NEW_PRODUCTS,
            self::HOME_TOP_SELLING,
            self::HOME_HOT_DEALS,
            self::HOME_FEATURED_CATEGORIES,
            self::HOME_CATEGORIES_WITH_PRODUCTS,
        ];
    }

    /**
     * Get all product related cache keys patterns
     */
    public static function productKeys(): array
    {
        return [
            self::PRODUCT_HOT_LIST,
            self::PRODUCT_NEW_LIST,
            self::HOME_HOT_DEALS,
            self::HOME_NEW_PRODUCTS,
            self::HOME_TOP_SELLING,
            self::HOME_CATEGORIES_WITH_PRODUCTS,
        ];
    }

    /**
     * Get all category related cache keys patterns
     */
    public static function categoryKeys(): array
    {
        return [
            self::CATEGORY_LIST,
            self::CATEGORY_FEATURED,
            self::HOME_FEATURED_CATEGORIES,
            self::HOME_CATEGORIES_WITH_PRODUCTS,
        ];
    }
}

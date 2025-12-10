<?php

namespace App\Services;

interface ICategoryService
{
    /**
     * Get filtered products for a category
     * 
     * @param int $categoryId
     * @param array $filters
     * @param int $perPage
     * @return mixed
     */
    public function getFilteredProducts(int $categoryId, array $filters, int $perPage = 12);

    /**
     * Format AJAX response for category products
     * 
     * @param mixed $products
     * @return array
     */
    public function formatAjaxResponse($products): array;
}

<?php

namespace App\Services;

interface ICategoryService
{
    public function getFilteredProducts(int $categoryId, array $filters, int $perPage = 12);

    public function formatAjaxResponse($products): array;
}

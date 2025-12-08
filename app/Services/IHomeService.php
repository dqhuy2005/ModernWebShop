<?php

namespace App\Services;

interface IHomeService
{
    public function getHomeProducts(int $perPage = 10, int $page = 1): array;

    public function searchProducts(string $keyword, int $perPage = 10): array;
}

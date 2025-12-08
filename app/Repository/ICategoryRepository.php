<?php

namespace App\Repository;

interface ICategoryRepository
{
    public function model();

    public function findBuild();

    public function getFeaturedCategories($limit = 3);

    public function getCategoriesWithHotProducts($limit = 5, $productsLimit = 15);

    public function findBySlug($slug, $columns = ['id', 'name', 'slug']);
}

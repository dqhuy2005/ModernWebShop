<?php

namespace App\Repository;

use App\Models\Category;

class CategoryRepository extends BaseRepository
{
    public function model()
    {
        return Category::class;
    }

    public function findBuild()
    {
        return $this->with(['products']);
    }
}

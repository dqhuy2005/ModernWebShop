<?php

namespace App\Repository;

use App\Models\Product;

class ProductRepository extends BaseRepository
{
    public function model()
    {
        return Product::class;
    }

    public function findBuild()
    {
        return $this->with(['category', 'carts', 'orderDetails']);
    }

    public function findByCategory($categoryId)
    {
        return $this->findWhere(['category_id' => $categoryId]);
    }

    public function findActive()
    {
        return $this->findWhere(['status' => true]);
    }

    public function findByLanguage($language)
    {
        return $this->findWhere(['language' => $language]);
    }

    public function findActiveByCategoryId($categoryId)
    {
        return $this->findWhere([
            'category_id' => $categoryId,
            'status' => true
        ]);
    }
}

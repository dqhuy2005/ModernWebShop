<?php

namespace App\Repository\impl;

use App\Models\OrderDetail;
use App\Repository\IOrderDetailRepository;

class OrderDetailRepository extends BaseRepository implements IOrderDetailRepository
{
    public function model()
    {
        return OrderDetail::class;
    }

    public function findBuild()
    {
        return $this->with(['order', 'product']);
    }
}

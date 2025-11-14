<?php

namespace App\Repository;

use App\Models\OrderDetail;

class OrderDetailRepository extends BaseRepository
{
    public function model()
    {
        return OrderDetail::class;
    }

    public function findBuild()
    {
        return $this->with(['order', 'product']);
    }

    public function calculateOrderTotal($orderId)
    {
        $orderDetails = $this->findByOrder($orderId);
        return $orderDetails->sum('total_price');
    }

    public function findByOrderWithProduct($orderId)
    {
        return $this->scopeQuery(function($query) use ($orderId) {
            return $query->where('order_id', $orderId)->with('product');
        })->all();
    }

    public function getMostSoldProducts($limit = 10)
    {
        return $this->model
            ->selectRaw('product_id, product_name, SUM(quantity) as total_quantity, SUM(total_price) as total_revenue')
            ->groupBy('product_id', 'product_name')
            ->orderByDesc('total_quantity')
            ->limit($limit)
            ->get();
    }
}

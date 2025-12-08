<?php

namespace App\Repository\impl;

use App\Models\Order;

class OrderRepository extends BaseRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Order::class;
    }

    public function findBuild()
    {
        return $this->select('id', 'user_id', 'customer_name', 'customer_email', 'customer_phone', 'total_amount', 'total_items', 'status', 'address', 'note', 'created_at', 'updated_at')
            ->with([
                'user:id,fullname,email,phone',
                'orderDetails' => function ($q) {
                    $q->select('id', 'order_id', 'product_id', 'product_name', 'quantity', 'unit_price', 'total_price', 'product_specifications')
                        ->with([
                            'product:id,name,slug,price',
                            'product.images:id,product_id,path,sort_order'
                        ]);
                }
            ]);
    }

    public function findByUser($userId)
    {
        return $this->select('id', 'user_id', 'customer_name', 'customer_email', 'customer_phone', 'total_amount', 'total_items', 'status', 'address', 'note', 'created_at', 'updated_at')
            ->scopeQuery(function($query) use ($userId) {
                return $query->where('user_id', $userId)->orderBy('created_at', 'desc');
            })->all();
    }

    public function findByStatus($status)
    {
        return $this->findWhere(['status' => $status]);
    }

    public function findPending()
    {
        return $this->findByStatus(Order::STATUS_PENDING);
    }

    public function findProcessing()
    {
        return $this->findByStatus(Order::STATUS_PROCESSING);
    }

    public function findShipped()
    {
        return $this->findByStatus(Order::STATUS_SHIPPED);
    }

    public function findDelivered()
    {
        return $this->findByStatus(Order::STATUS_DELIVERED);
    }

    public function findCancelled()
    {
        return $this->findByStatus(Order::STATUS_CANCELLED);
    }

    public function updateStatus($orderId, $status)
    {
        return $this->update(['status' => $status], $orderId);
    }

    public function findByMinAmount($amount)
    {
        return $this->scopeQuery(function($query) use ($amount) {
            return $query->where('total_amount', '>=', $amount);
        })->all();
    }

    public function findRecent($limit = 10)
    {
        return $this->select('id', 'user_id', 'customer_name', 'customer_email', 'customer_phone', 'total_amount', 'total_items', 'status', 'address', 'note', 'created_at', 'updated_at')
            ->scopeQuery(function($query) use ($limit) {
                return $query->orderBy('created_at', 'desc')->limit($limit);
            })->all();
    }
}

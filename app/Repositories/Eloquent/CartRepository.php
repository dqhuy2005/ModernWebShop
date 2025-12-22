<?php

namespace App\Repositories\Eloquent;

use App\Models\Cart;
use App\Repositories\Contracts\CartRepositoryInterface;

class CartRepository implements CartRepositoryInterface
{
    public function __construct(
        private Cart $model
    ) {
    }

    public function getByUser(int $userId)
    {
        return $this->model->query()
            ->where('user_id', $userId)
            ->with([
                'product:id,name,slug,price,status,category_id',
                'product.images:id,product_id,path,sort_order'
            ])
            ->get();
    }

    public function findByUserAndProduct(int $userId, int $productId)
    {
        return $this->model->query()
            ->where('user_id', $userId)
            ->where('product_id', $productId)
            ->withTrashed()
            ->first();
    }

    public function findByUserAndCart(int $userId, int $cartId)
    {
        return $this->model->query()
            ->where('user_id', $userId)
            ->where('id', $cartId)
            ->withTrashed()
            ->first();
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function updateQuantity(int $id, int $quantity)
    {
        return $this->model->where('id', $id)->update(['quantity' => $quantity]);
    }

    public function update(int $id, array $data)
    {
        return $this->model->where('id', $id)->update($data);
    }

    public function delete(int $id)
    {
        return $this->model->where('id', $id)->delete();
    }

    public function deleteByUser(int $userId)
    {
        return $this->model->where('user_id', $userId)->delete();
    }

    public function deleteSelected(int $userId, array $cartIds)
    {
        return $this->model->query()
            ->where('user_id', $userId)
            ->whereIn('id', $cartIds)
            ->delete();
    }

    public function restore(int $id)
    {
        return $this->model->withTrashed()->where('id', $id)->restore();
    }

    public function getCount(int $userId): int
    {
        return $this->model->query()
            ->where('user_id', $userId)
            ->count();
    }

    public function calculateTotal(int $userId): float
    {
        $cartItems = $this->getByUser($userId);

        return $cartItems->sum(function ($item) {
            return $item->quantity * $item->price;
        });
    }

    public function getByIds(int $userId, array $cartIds)
    {
        return $this->model->query()
            ->where('user_id', $userId)
            ->whereIn('id', $cartIds)
            ->with([
                'product:id,name,slug,price,status,category_id',
                'product.images:id,product_id,path,sort_order'
            ])
            ->get();
    }
}

<?php

namespace App\Repository;

use App\Models\Cart;

class CartRepository extends BaseRepository
{
    public function model()
    {
        return Cart::class;
    }

    public function findBuild()
    {
        return $this->with(['product', 'user']);
    }

    public function findByUser($userId)
    {
        return $this->scopeQuery(function ($query) use ($userId) {
            return $query->where('user_id', $userId)
                ->with('product:id,name,slug,image,price,status,category_id');
        })->all();
    }

    public function findByUserAndProduct($userId, $productId)
    {
        return $this->findWhere([
            'user_id' => $userId,
            'product_id' => $productId
        ])->first();
    }

    public function calculateUserCartTotal($userId)
    {
        $cartItems = $this->findByUser($userId);
        return $cartItems->sum(function ($item) {
            return $item->quantity * $item->price;
        });
    }

    public function clearUserCart($userId)
    {
        return $this->model->where('user_id', $userId)->delete();
    }

    public function updateQuantity($cartId, $quantity)
    {
        return $this->update(['quantity' => $quantity], $cartId);
    }

    public function findByProduct($productId)
    {
        return $this->findWhere(['product_id' => $productId]);
    }
}

<?php

namespace App\Repository\impl;

use App\Models\Cart;
use App\Repository\ICartRepository;

class CartRepository extends BaseRepository implements ICartRepository
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
                ->with([
                    'product:id,name,slug,price,status,category_id',
                    'product.images:id,product_id,path,sort_order'
                ]);
        })->all();
    }

    public function findByUserAndProduct($userId, $productId)
    {
        return $this->model
            ->where('user_id', $userId)
            ->where('product_id', $productId)
            ->withTrashed()
            ->first();
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
}

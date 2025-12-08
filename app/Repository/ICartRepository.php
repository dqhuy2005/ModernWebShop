<?php

namespace App\Repository;

interface ICartRepository
{
    public function model();

    public function findBuild();

    public function findByUser($userId);

    public function findByUserAndProduct($userId, $productId);

    public function calculateUserCartTotal($userId);

    public function clearUserCart($userId);

    public function updateQuantity($cartId, $quantity);

    public function findByProduct($productId);
}

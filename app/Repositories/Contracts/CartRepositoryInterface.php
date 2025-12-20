<?php

namespace App\Repositories\Contracts;

interface CartRepositoryInterface
{
    public function getByUser(int $userId);
    public function findByUserAndProduct(int $userId, int $productId);
    public function create(array $data);
    public function updateQuantity(int $id, int $quantity);
    public function update(int $id, array $data);
    public function delete(int $id);
    public function deleteByUser(int $userId);
    public function deleteSelected(int $userId, array $cartIds);
    public function restore(int $id);
    public function getCount(int $userId): int;
    public function calculateTotal(int $userId): float;
    public function getByIds(int $userId, array $cartIds);
}

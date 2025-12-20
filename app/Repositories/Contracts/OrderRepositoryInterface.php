<?php

namespace App\Repositories\Contracts;

interface OrderRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 15);
    public function find(int $id);
    public function findWithTrashed(int $id);
    public function getByUser(int $userId);
    public function getByStatus(string $status);
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id);
    public function restore(int $id);
    public function updateStatus(int $id, string $status);
    public function getByDateRange(string $dateFrom, string $dateTo);
    public function getByPriceRange(float $priceMin, float $priceMax);
    public function search(string $search);
    public function getAllForExport();
    public function calculateTotalAmount(array $products): float;
    public function calculateTotalItems(array $products): int;
    public function searchCustomers(string $search, int $limit = 15);
    public function getCustomer(int $customerId);
}

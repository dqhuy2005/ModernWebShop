<?php

namespace App\Repository;

interface IOrderDetailRepository
{
    public function model();

    public function findBuild();

    public function calculateOrderTotal($orderId);

    public function findByOrderWithProduct($orderId);

    public function getMostSoldProducts($limit = 10);
}

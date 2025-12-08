<?php

namespace App\Repository;

interface IOrderRepository
{
    public function model();

    public function findBuild();

    public function findByUser($userId);

    public function findByStatus($status);

    public function findPending();

    public function findProcessing();

    public function findShipped();

    public function findDelivered();

    public function findCancelled();

    public function updateStatus($orderId, $status);

    public function findByMinAmount($amount);

    public function findRecent($limit = 10);
}

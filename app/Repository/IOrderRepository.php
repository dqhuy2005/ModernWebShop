<?php

namespace App\Repository;

interface IOrderRepository
{
    public function model();

    public function findBuild();

    public function findByUser($userId);

    public function findByStatus($status);
}

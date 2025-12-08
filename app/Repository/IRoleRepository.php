<?php

namespace App\Repository;

interface IRoleRepository
{
    public function model();

    public function findBySlug(string $slug);
}

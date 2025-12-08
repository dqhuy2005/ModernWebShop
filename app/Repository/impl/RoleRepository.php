<?php

namespace App\Repository\impl;

use App\Models\Role;
use App\Repository\IRoleRepository;

class RoleRepository extends BaseRepository implements IRoleRepository
{
    public function model()
    {
        return Role::class;
    }

    public function findBySlug(string $slug)
    {
        return $this->model->where('slug', $slug)->first();
    }
}

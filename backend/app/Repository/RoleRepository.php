<?php

namespace App\Repositories;

use App\Models\Role;

class RoleRepository extends BaseRepository
{
    public function model()
    {
        return Role::class;
    }

    public function getAllRoles()
    {
        return $this->model->orderBy('name')->get();
    }

    public function findBySlug(string $slug)
    {
        return $this->model->where('slug', $slug)->first();
    }

    public function getAdminRole()
    {
        return $this->findBySlug(Role::ADMIN);
    }

    public function getUserRole()
    {
        return $this->findBySlug(Role::USER);
    }

    public function getRolesWithUserCount()
    {
        return $this->model
            ->withCount('users')
            ->orderBy('name')
            ->get();
    }
}

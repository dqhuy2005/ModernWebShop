<?php

namespace App\Repository;

interface IRoleRepository
{
    public function model();

    public function getAllRoles();

    public function findBySlug(string $slug);

    public function getAdminRole();

    public function getUserRole();

    public function getRolesWithUserCount();
}

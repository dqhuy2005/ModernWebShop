<?php

namespace App\Repository\impl;

use App\Models\User;

class UserRepository extends BaseRepository
{
    public function model()
    {
        return User::class;
    }

    public function findBuild()
    {
        return $this->with(['carts', 'orders']);
    }
}

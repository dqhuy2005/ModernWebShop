<?php

namespace App\Repository\impl;

use App\Models\User;
use App\Repository\IUserRepository;

class UserRepository extends BaseRepository implements IUserRepository
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

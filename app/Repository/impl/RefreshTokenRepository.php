<?php

namespace App\Repository\impl;

use App\Models\RefreshToken;
use App\Repository\IRefreshTokenRepository;

class RefreshTokenRepository extends BaseRepository implements IRefreshTokenRepository
{
    public function model()
    {
        return RefreshToken::class;
    }

    public function findBuild()
    {
        return $this->with(['carts', 'orders']);
    }
}

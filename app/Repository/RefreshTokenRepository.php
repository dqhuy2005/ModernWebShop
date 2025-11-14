<?php

namespace App\Repository;

use App\Models\RefreshToken;

class RefreshTokenRepository extends BaseRepository
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

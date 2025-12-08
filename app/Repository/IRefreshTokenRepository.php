<?php

namespace App\Repository;

interface IRefreshTokenRepository
{
    public function model();

    public function findBuild();
}

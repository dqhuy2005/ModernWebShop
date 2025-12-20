<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface UserRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function find(int $id): ?User;

    public function findWithTrashed(int $id): ?User;

    public function findByEmail(string $email): ?User;

    public function create(array $data): User;

    public function update(User $user, array $data): bool;

    public function delete(User $user): bool;

    public function restore(int $id): bool;

    public function forceDelete(int $id): bool;

    public function toggleStatus(User $user): bool;

    public function getAllForExport(): Collection;

    public function getNonAdminUsers(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function emailExists(string $email, ?int $excludeId = null): bool;
}

<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Models\Role;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class UserRepository implements UserRepositoryInterface
{
    public function __construct(
        private User $model
    ) {}

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model
            ->select('id', 'fullname', 'email', 'phone', 'image', 'status', 'created_at', 'updated_at', 'deleted_at')
            ->withTrashed()
            ->with('role:id,name,slug');

        $this->applyFilters($query, $filters);

        if (!empty($filters['sort_by'])) {
            $sortOrder = $filters['sort_order'] ?? 'desc';
            $query->orderBy($filters['sort_by'], $sortOrder);
        } else {
            $query->orderBy('id', 'desc');
        }

        return $query->paginate($perPage);
    }

    public function find(int $id): ?User
    {
        return $this->model
            ->select('id', 'fullname', 'email', 'phone', 'birthday', 'image', 'address', 'status', 'role_id', 'created_at', 'updated_at', 'deleted_at')
            ->with([
                'role:id,name,slug',
                'carts' => function ($q) {
                    $q->select('id', 'user_id', 'product_id', 'quantity', 'created_at')
                        ->with([
                            'product:id,name,slug,price',
                            'product.images:id,product_id,path,sort_order'
                        ]);
                },
                'orders' => function ($q) {
                    $q->select('id', 'user_id', 'customer_name', 'total_amount', 'total_items', 'status', 'created_at');
                }
            ])
            ->find($id);
    }

    public function findWithTrashed(int $id): ?User
    {
        return $this->model
            ->select('id', 'fullname', 'email', 'phone', 'birthday', 'image', 'address', 'password', 'status', 'role_id', 'created_at', 'updated_at', 'deleted_at')
            ->withTrashed()
            ->find($id);
    }

    public function findByEmail(string $email): ?User
    {
        return $this->model->where('email', $email)->first();
    }

    public function create(array $data): User
    {
        return $this->model->create($data);
    }

    public function update(User $user, array $data): bool
    {
        return $user->update($data);
    }

    public function delete(User $user): bool
    {
        return $user->delete();
    }

    public function restore(int $id): bool
    {
        $user = $this->model->withTrashed()->find($id);

        if (!$user) {
            return false;
        }

        return $user->restore();
    }

    public function forceDelete(int $id): bool
    {
        $user = $this->model->withTrashed()->find($id);

        if (!$user) {
            return false;
        }

        return $user->forceDelete();
    }

    public function toggleStatus(User $user): bool
    {
        $user->status = !$user->status;
        return $user->save();
    }

    public function getAllForExport(): Collection
    {
        return $this->model
            ->select('id', 'fullname', 'email', 'phone', 'status', 'created_at')
            ->with('role:id,name')
            ->get();
    }

    public function getNonAdminUsers(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model
            ->select('id', 'fullname', 'email', 'phone', 'image', 'status', 'created_at', 'updated_at', 'deleted_at')
            ->withTrashed()
            ->with('role:id,name,slug')
            ->whereDoesntHave('role', function ($q) {
                $q->where('slug', Role::ADMIN);
            });

        $this->applyFilters($query, $filters);

        if (!empty($filters['sort_by'])) {
            $sortOrder = $filters['sort_order'] ?? 'desc';
            $query->orderBy($filters['sort_by'], $sortOrder);
        } else {
            $query->orderBy('id', 'desc');
        }

        return $query->paginate($perPage);
    }

    public function emailExists(string $email, ?int $excludeId = null): bool
    {
        $query = $this->model->where('email', $email);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    private function applyFilters($query, array $filters): void
    {
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('fullname', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['role_id'])) {
            $query->where('role_id', $filters['role_id']);
        }

        if (!empty($filters['status'])) {
            switch ($filters['status']) {
                case 'active':
                    $query->where('status', true)->whereNull('deleted_at');
                    break;
                case 'inactive':
                    $query->where('status', false)->whereNull('deleted_at');
                    break;
                case 'deleted':
                    $query->onlyTrashed();
                    break;
            }
        }
    }
}

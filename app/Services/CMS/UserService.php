<?php

namespace App\Services\CMS;

use App\DTOs\UserData;
use App\Models\User;
use App\Models\Role;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Services\impl\ImageService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserService
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private ImageService $imageService
    ) {
    }

    public function createUser(UserData $data): User
    {
        return DB::transaction(function () use ($data) {
            if ($data->password) {
                $data->password = Hash::make($data->password);
            }

            if (!$data->roleId) {
                $userRole = Role::where('slug', 'user')->first();
                if ($userRole) {
                    $data->roleId = $userRole->id;
                }
            } else {
                $selectedRole = Role::find($data->roleId);
                if ($selectedRole && strtolower($selectedRole->name) === 'admin') {
                    $userRole = Role::where('slug', 'user')->first();
                    if ($userRole) {
                        $data->roleId = $userRole->id;
                    }
                }
            }

            return $this->userRepository->create($data->toArray());
        });
    }

    public function updateUser(User $user, UserData $data): User
    {
        return DB::transaction(function () use ($user, $data) {
            $updateData = $data->toArray();

            if ($data->password) {
                $updateData['password'] = Hash::make($data->password);
            } else {
                unset($updateData['password']);
            }

            if (isset($updateData['role_id'])) {
                $currentRole = Role::find($user->role_id);
                $newRole = Role::find($updateData['role_id']);

                if ($currentRole && strtolower($currentRole->name) === 'admin') {
                    unset($updateData['role_id']);
                } elseif ($newRole && strtolower($newRole->name) === 'admin') {
                    $userRole = Role::where('slug', 'user')->first();
                    if ($userRole) {
                        $updateData['role_id'] = $userRole->id;
                    }
                }
            }

            $this->userRepository->update($user, $updateData);

            return $user->fresh();
        });
    }

    public function deleteUser(User $user): bool
    {
        if ($user->id === Auth::id()) {
            throw new \Exception('You cannot delete yourself!');
        }

        return $this->userRepository->delete($user);
    }

    public function restoreUser(int $id): bool
    {
        return $this->userRepository->restore($id);
    }

    public function forceDeleteUser(int $id): bool
    {
        if ($id === Auth::id()) {
            throw new \Exception('You cannot delete yourself!');
        }

        return DB::transaction(function () use ($id) {
            $user = $this->userRepository->findWithTrashed($id);

            if (!$user) {
                throw new \Exception('User not found');
            }

            if ($user->image) {
                $this->imageService->deleteImage($user->image);
            }

            return $this->userRepository->forceDelete($id);
        });
    }

    public function toggleUserStatus(User $user): bool
    {
        if ($user->id === Auth::id()) {
            throw new \Exception('You cannot change your own status!');
        }

        return $this->userRepository->toggleStatus($user);
    }
}

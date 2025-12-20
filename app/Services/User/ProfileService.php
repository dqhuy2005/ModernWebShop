<?php

namespace App\Services\User;

use App\Repositories\Contracts\UserRepositoryInterface;
use App\Services\impl\ImageService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;

class ProfileService
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private ImageService $imageService
    ) {
    }

    public function updateProfile(int $userId, array $data, ?UploadedFile $image = null): array
    {
        try {
            $user = $this->userRepository->find($userId);

            if (!$user) {
                throw new \Exception("User not found");
            }

            $updateData = [
                'fullname' => $data['fullname'],
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? null,
                'birthday' => $data['birthday'] ?? null,
            ];

            if ($image) {
                if (!$this->imageService->validateImage($image)) {
                    throw new \Exception('Invalid image file. Please check size (max 2MB) and format (jpg, png)');
                }

                $updateData['image'] = $this->imageService->uploadAvatar($image, $user->image);
            }

            $this->userRepository->update($user, $updateData);

            $updatedUser = $this->userRepository->find($userId);

            return [
                'success' => true,
                'message' => 'Profile updated successfully',
                'image_url' => $updatedUser->image_url ?? null
            ];

        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function changePassword(int $userId, string $currentPassword, string $newPassword): bool
    {
        $user = $this->userRepository->find($userId);

        if (!$user) {
            throw new \Exception("User not found");
        }

        if (!Hash::check($currentPassword, $user->password)) {
            throw new \Exception("Current password is incorrect");
        }

        return $this->userRepository->update($user, [
            'password' => Hash::make($newPassword)
        ]);
    }
}

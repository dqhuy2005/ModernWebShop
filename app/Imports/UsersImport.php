<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class UsersImport implements ToCollection, WithHeadingRow
{
    /**
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Skip empty rows
            if (empty($row['email'])) {
                continue;
            }

            // Find role by name or use default
            $role = Role::where('name', $row['role'] ?? 'User')->first();
            $roleId = $role ? $role->id : Role::where('name', 'User')->first()->id;

            // Convert status from text to boolean
            $status = in_array(strtolower($row['status'] ?? ''), ['active', '1', 'true']) ? 1 : 0;

            User::updateOrCreate(
                ['email' => $row['email']], // Match by email
                [
                    'fullname' => $row['full_name'] ?? $row['fullname'] ?? null,
                    'phone' => $row['phone'] ?? null,
                    'address' => $row['address'] ?? null,
                    'role_id' => $roleId,
                    'status' => $status,
                    'password' => isset($row['password']) && !empty($row['password'])
                        ? Hash::make($row['password'])
                        : Hash::make('password123'), // Default password
                ]
            );
        }
    }

    /**
     * @return int
     */
    public function headingRow(): int
    {
        return 1;
    }
}

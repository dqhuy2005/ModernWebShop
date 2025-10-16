<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('slug', Role::ADMIN)->first();
        $userRole = Role::where('slug', Role::USER)->first();

        User::create([
            'fullname' => 'Admin User',
            'email' => 'admin@gmail.com',
            'role_id' => $adminRole->id,
            'phone' => '0999999999',
            'password' => Hash::make('12345@54321'),
            'status' => true,
            'language' => 'vi',
            'birthday' => '1990-01-01',
        ]);

        User::create([
            'fullname' => 'John Doe',
            'email' => 'john@gmail.com',
            'role_id' => $userRole->id,
            'phone' => '0987654321',
            'password' => Hash::make('12345@54321'),
            'status' => true,
            'language' => 'en',
            'birthday' => '1985-05-15',
        ]);

        User::create([
            'fullname' => 'Nguyen Van A',
            'email' => 'nguyenvana@gmail.com',
            'role_id' => $userRole->id,
            'phone' => '0909123456',
            'password' => Hash::make('12345@54321'),
            'status' => true,
            'language' => 'vi',
            'birthday' => '1988-12-25',
        ]);
    }
}

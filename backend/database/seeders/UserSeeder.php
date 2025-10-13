<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'fullname' => 'Admin User',
            'email' => 'admin@example.com',
            'phone' => '1234567890',
            'password' => Hash::make('12345@54321'),
            'status' => true,
            'language' => 'en',
            'birthday' => '1990-01-01',
        ]);

        User::create([
            'fullname' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '0987654321',
            'password' => Hash::make('12345@54321'),
            'status' => true,
            'language' => 'en',
            'birthday' => '1985-05-15',
        ]);

        User::create([
            'fullname' => 'Nguyen Van A',
            'email' => 'nguyenvana@example.com',
            'phone' => '0909123456',
            'password' => Hash::make('12345@54321'),
            'status' => true,
            'language' => 'vi',
            'birthday' => '1988-12-25',
        ]);
    }
}

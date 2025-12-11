<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            CategorySeeder::class,
            UserSeeder::class,
            //ProductSeeder::class,
            //ComprehensiveOrderSeeder::class,
            //FakeDataSeeder::class, // Uncomment to seed fake data (5000 users + 2000-5000 products per category)
            NotificationSystemSeeder::class,
        ]);
    }
}

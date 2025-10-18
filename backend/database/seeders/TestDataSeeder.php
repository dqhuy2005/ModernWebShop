<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 Starting to seed test data...');

        // Disable foreign key checks temporarily
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Seed Users
        $this->command->info('👥 Creating users...');

        // Create active users
        User::factory()->count(400)->active()->create();
        $this->command->info('✅ Created 400 active users');

        // Create inactive users
        User::factory()->count(100)->inactive()->create();
        $this->command->info('✅ Created 100 inactive users');

        // Create some deleted users
        User::factory()->count(50)->deleted()->create();
        $this->command->info('✅ Created 50 deleted users');

        $this->command->info('✨ Total users: 550');

        // Seed Products
        $this->command->info('📦 Creating products...');

        // Create active products
        Product::factory()->count(400)->active()->create();
        $this->command->info('✅ Created 400 active products');

        // Create inactive products
        Product::factory()->count(100)->inactive()->create();
        $this->command->info('✅ Created 100 inactive products');

        // Create hot products
        Product::factory()->count(150)->hot()->create();
        $this->command->info('✅ Created 150 hot products');

        // Create popular products
        Product::factory()->count(50)->popular()->create();
        $this->command->info('✅ Created 50 popular products');

        $this->command->info('✨ Total products: 700');

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('🎉 Test data seeding completed successfully!');
        $this->command->info('');
        $this->command->info('📊 Summary:');
        $this->command->info('   - Total Users: 550 (400 active, 100 inactive, 50 deleted)');
        $this->command->info('   - Total Products: 700 (various statuses and hot products)');
        $this->command->info('');
        $this->command->warn('⚠️  Remember: All users have password: "password"');
    }
}

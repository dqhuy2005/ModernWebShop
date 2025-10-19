<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LargeTestDataSeeder extends Seeder
{
    /**
     * Run the database seeds for large dataset (1000+ records)
     */
    public function run(): void
    {
        $this->command->info('🚀 Starting to seed LARGE test data...');
        $this->command->warn('⚠️  This will create 1000+ records. Please wait...');

        // Disable foreign key checks temporarily
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Seed Users (1000 records)
        $this->command->info('👥 Creating 1000 users in batches...');

        $batchSize = 100;
        $totalUsers = 1000;

        for ($i = 0; $i < $totalUsers / $batchSize; $i++) {
            // 70% active
            User::factory()->count(70)->active()->create();
            // 20% inactive
            User::factory()->count(20)->inactive()->create();
            // 10% deleted
            User::factory()->count(10)->deleted()->create();

            $this->command->info('   ✅ Created batch ' . ($i + 1) . ' of ' . ($totalUsers / $batchSize));
        }

        $this->command->info('✨ Total users: 1000');

        // Seed Products (1000 records)
        $this->command->info('📦 Creating 1000 products in batches...');

        $totalProducts = 1000;

        for ($i = 0; $i < $totalProducts / $batchSize; $i++) {
            Product::factory()->count(60)->active()->normal()->create();
            Product::factory()->count(20)->active()->hot()->create();
            Product::factory()->count(10)->inactive()->create();
            Product::factory()->count(10)->popular()->create();

            $this->command->info('   ✅ Created batch ' . ($i + 1) . ' of ' . ($totalProducts / $batchSize));
        }

        $this->command->info('✨ Total products: 1000');
    }
}

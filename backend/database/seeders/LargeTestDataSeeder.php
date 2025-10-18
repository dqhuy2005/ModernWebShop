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
            // 60% active, normal
            Product::factory()->count(60)->active()->normal()->create();
            // 20% active, hot
            Product::factory()->count(20)->active()->hot()->create();
            // 10% inactive
            Product::factory()->count(10)->inactive()->create();
            // 10% popular (high views + hot)
            Product::factory()->count(10)->popular()->create();

            $this->command->info('   ✅ Created batch ' . ($i + 1) . ' of ' . ($totalProducts / $batchSize));
        }

        $this->command->info('✨ Total products: 1000');

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('');
        $this->command->info('🎉 LARGE test data seeding completed successfully!');
        $this->command->info('');
        $this->command->info('📊 Summary:');
        $this->command->info('   - Total Users: 1000 (700 active, 200 inactive, 100 deleted)');
        $this->command->info('   - Total Products: 1000 (various statuses, 300 hot products)');
        $this->command->info('');
        $this->command->warn('⚠️  All users have password: "password"');
        $this->command->info('');
        $this->command->info('💡 Tip: Use pagination to navigate through the data efficiently!');
    }
}

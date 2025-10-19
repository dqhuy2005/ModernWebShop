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
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        User::factory()->count(400)->active()->create();
        User::factory()->count(100)->inactive()->create();
        User::factory()->count(50)->deleted()->create();
        Product::factory()->count(400)->active()->create();
        Product::factory()->count(100)->inactive()->create();
        Product::factory()->count(150)->hot()->create();
        Product::factory()->count(50)->popular()->create();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $categories = [
            // Main Categories
            [
                'id' => 1,
                'name' => 'Laptop',
                'parent_id' => null,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'name' => 'PC Components',
                'parent_id' => null,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'name' => 'Keyboard',
                'parent_id' => null,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 4,
                'name' => 'Mouse',
                'parent_id' => null,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 5,
                'name' => 'Monitor',
                'parent_id' => null,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Laptop Sub-categories
            [
                'id' => 6,
                'name' => 'Gaming Laptop',
                'parent_id' => 1,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 7,
                'name' => 'Business Laptop',
                'parent_id' => 1,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 8,
                'name' => 'Ultrabook',
                'parent_id' => 1,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // PC Components Sub-categories
            [
                'id' => 9,
                'name' => 'CPU',
                'parent_id' => 2,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 10,
                'name' => 'GPU',
                'parent_id' => 2,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 11,
                'name' => 'Mainboard',
                'parent_id' => 2,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 12,
                'name' => 'RAM',
                'parent_id' => 2,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 13,
                'name' => 'Power Supply (PSU)',
                'parent_id' => 2,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 14,
                'name' => 'PC Case',
                'parent_id' => 2,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 15,
                'name' => 'Cooling System',
                'parent_id' => 2,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Keyboard Sub-categories
            [
                'id' => 16,
                'name' => 'Keyboard Mechanical',
                'parent_id' => 3,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 17,
                'name' => 'Keyboard Membrane',
                'parent_id' => 3,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 18,
                'name' => 'Keyboard Wireless',
                'parent_id' => 3,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 19,
                'name' => 'Keyboard Gaming',
                'parent_id' => 3,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 20,
                'name' => 'Keyboard Ergonomic',
                'parent_id' => 3,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Mouse Sub-categories
            [
                'id' => 21,
                'name' => 'Mouse Gaming',
                'parent_id' => 4,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 22,
                'name' => 'Mouse Wired',
                'parent_id' => 4,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 23,
                'name' => 'Mouse Wireless',
                'parent_id' => 4,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 24,
                'name' => 'Mouse Silent',
                'parent_id' => 4,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            // Monitor Sub-categories
            [
                'id' => 25,
                'name' => 'Monitor 60Hz',
                'parent_id' => 5,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 26,
                'name' => 'Monitor 144Hz',
                'parent_id' => 5,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 27,
                'name' => 'Monitor 240Hz',
                'parent_id' => 5,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 28,
                'name' => 'Monitor 2K',
                'parent_id' => 5,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 29,
                'name' => 'Monitor 4K',
                'parent_id' => 5,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('categories')->insert($categories);
    }
}

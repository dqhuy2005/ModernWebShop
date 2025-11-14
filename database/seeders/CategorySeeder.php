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
                'slug' => 'laptop',
                'parent_id' => null,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'name' => 'PC Components',
                'slug' => 'pc-components',
                'parent_id' => null,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'name' => 'Keyboard',
                'slug' => 'keyboard',
                'parent_id' => null,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 4,
                'name' => 'Mouse',
                'slug' => 'mouse',
                'parent_id' => null,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 5,
                'name' => 'Monitor',
                'slug' => 'monitor',
                'parent_id' => null,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Laptop Sub-categories
            [
                'id' => 6,
                'name' => 'Gaming Laptop',
                'slug' => 'gaming-laptop',
                'parent_id' => 1,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 7,
                'name' => 'Business Laptop',
                'slug' => 'business-laptop',
                'parent_id' => 1,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 8,
                'name' => 'Ultrabook',
                'slug' => 'ultrabook',
                'parent_id' => 1,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // PC Components Sub-categories
            [
                'id' => 9,
                'name' => 'CPU',
                'slug' => 'cpu',
                'parent_id' => 2,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 10,
                'name' => 'GPU',
                'slug' => 'gpu',
                'parent_id' => 2,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 11,
                'name' => 'Mainboard',
                'slug' => 'mainboard',
                'parent_id' => 2,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 12,
                'name' => 'RAM',
                'slug' => 'ram',
                'parent_id' => 2,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 13,
                'name' => 'Power Supply (PSU)',
                'slug' => 'power-supply-psu',
                'parent_id' => 2,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 14,
                'name' => 'PC Case',
                'slug' => 'pc-case',
                'parent_id' => 2,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 15,
                'name' => 'Cooling System',
                'slug' => 'cooling-system',
                'parent_id' => 2,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Keyboard Sub-categories
            [
                'id' => 16,
                'name' => 'Keyboard Mechanical',
                'slug' => 'keyboard-mechanical',
                'parent_id' => 3,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 17,
                'name' => 'Keyboard Membrane',
                'slug' => 'keyboard-membrane',
                'parent_id' => 3,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 18,
                'name' => 'Keyboard Wireless',
                'slug' => 'keyboard-wireless',
                'parent_id' => 3,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 19,
                'name' => 'Keyboard Gaming',
                'slug' => 'keyboard-gaming',
                'parent_id' => 3,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 20,
                'name' => 'Keyboard Ergonomic',
                'slug' => 'keyboard-ergonomic',
                'parent_id' => 3,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Mouse Sub-categories
            [
                'id' => 21,
                'name' => 'Mouse Gaming',
                'slug' => 'mouse-gaming',
                'parent_id' => 4,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 22,
                'name' => 'Mouse Wired',
                'slug' => 'mouse-wired',
                'parent_id' => 4,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 23,
                'name' => 'Mouse Wireless',
                'slug' => 'mouse-wireless',
                'parent_id' => 4,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 24,
                'name' => 'Mouse Silent',
                'slug' => 'mouse-silent',
                'parent_id' => 4,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            // Monitor Sub-categories
            [
                'id' => 25,
                'name' => 'Monitor 60Hz',
                'slug' => 'monitor-60hz',
                'parent_id' => 5,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 26,
                'name' => 'Monitor 144Hz',
                'slug' => 'monitor-144hz',
                'parent_id' => 5,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 27,
                'name' => 'Monitor 240Hz',
                'slug' => 'monitor-240hz',
                'parent_id' => 5,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 28,
                'name' => 'Monitor 2K',
                'slug' => 'monitor-2k',
                'parent_id' => 5,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 29,
                'name' => 'Monitor 4K',
                'slug' => 'monitor-4k',
                'parent_id' => 5,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('categories')->insert($categories);
    }
}

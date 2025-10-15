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
                'name' => 'Peripherals',
                'parent_id' => null,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 4,
                'name' => 'Storage',
                'parent_id' => null,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 5,
                'name' => 'Networking',
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
                'name' => 'Processor (CPU)',
                'parent_id' => 2,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 10,
                'name' => 'Graphics Card (GPU)',
                'parent_id' => 2,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 11,
                'name' => 'Motherboard',
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

            // Peripherals Sub-categories
            [
                'id' => 16,
                'name' => 'Keyboard',
                'parent_id' => 3,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 17,
                'name' => 'Mouse',
                'parent_id' => 3,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 18,
                'name' => 'Monitor',
                'parent_id' => 3,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 19,
                'name' => 'Headset',
                'parent_id' => 3,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 20,
                'name' => 'Webcam',
                'parent_id' => 3,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Storage Sub-categories
            [
                'id' => 21,
                'name' => 'SSD',
                'parent_id' => 4,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 22,
                'name' => 'HDD',
                'parent_id' => 4,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 23,
                'name' => 'External Storage',
                'parent_id' => 4,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Networking Sub-categories
            [
                'id' => 24,
                'name' => 'Router',
                'parent_id' => 5,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 25,
                'name' => 'Network Adapter',
                'parent_id' => 5,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('categories')->insert($categories);
    }
}

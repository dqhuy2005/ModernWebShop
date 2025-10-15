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
                'parentId' => null,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'name' => 'PC Components',
                'parentId' => null,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'name' => 'Peripherals',
                'parentId' => null,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 4,
                'name' => 'Storage',
                'parentId' => null,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 5,
                'name' => 'Networking',
                'parentId' => null,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Laptop Sub-categories
            [
                'id' => 6,
                'name' => 'Gaming Laptop',
                'parentId' => 1,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 7,
                'name' => 'Business Laptop',
                'parentId' => 1,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 8,
                'name' => 'Ultrabook',
                'parentId' => 1,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // PC Components Sub-categories
            [
                'id' => 9,
                'name' => 'Processor (CPU)',
                'parentId' => 2,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 10,
                'name' => 'Graphics Card (GPU)',
                'parentId' => 2,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 11,
                'name' => 'Motherboard',
                'parentId' => 2,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 12,
                'name' => 'RAM',
                'parentId' => 2,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 13,
                'name' => 'Power Supply (PSU)',
                'parentId' => 2,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 14,
                'name' => 'PC Case',
                'parentId' => 2,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 15,
                'name' => 'Cooling System',
                'parentId' => 2,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Peripherals Sub-categories
            [
                'id' => 16,
                'name' => 'Keyboard',
                'parentId' => 3,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 17,
                'name' => 'Mouse',
                'parentId' => 3,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 18,
                'name' => 'Monitor',
                'parentId' => 3,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 19,
                'name' => 'Headset',
                'parentId' => 3,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 20,
                'name' => 'Webcam',
                'parentId' => 3,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Storage Sub-categories
            [
                'id' => 21,
                'name' => 'SSD',
                'parentId' => 4,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 22,
                'name' => 'HDD',
                'parentId' => 4,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 23,
                'name' => 'External Storage',
                'parentId' => 4,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Networking Sub-categories
            [
                'id' => 24,
                'name' => 'Router',
                'parentId' => 5,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 25,
                'name' => 'Network Adapter',
                'parentId' => 5,
                'language' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('categories')->insert($categories);
    }
}

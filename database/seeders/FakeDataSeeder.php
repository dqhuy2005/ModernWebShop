<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Cart;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class FakeDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "Starting Fake Data Seeder...\n";
        $startTime = microtime(true);

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Step 1: Create 5000 fake users
        echo "Creating 5,000 users...\n";
        $this->createUsers();

        // Step 2: Get all categories
        $categories = Category::all();
        if ($categories->isEmpty()) {
            echo "No categories found. Please run CategorySeeder first.\n";
            return;
        }

        // Step 3: Create 2000-5000 products per category
        echo "Creating products for each category...\n";
        $this->createProducts($categories);

        // Step 4: Create cart items for random users
        echo "Creating cart items...\n";
        $this->createCartItems();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $endTime = microtime(true);
        $duration = round($endTime - $startTime, 2);

        echo "\n=== Fake Data Seeder Completed ===\n";
        echo "Total Users: " . User::count() . "\n";
        echo "Total Products: " . Product::count() . "\n";
        echo "Total Cart Items: " . Cart::count() . "\n";
        echo "Duration: {$duration} seconds\n";
    }

    /**
     * Create 5000 fake users
     */
    private function createUsers(): void
    {
        $batchSize = 500;
        $totalUsers = 5000;
        $batches = ceil($totalUsers / $batchSize);

        for ($i = 0; $i < $batches; $i++) {
            User::factory()->count($batchSize)->create([
                'role_id' => 2, // Regular user role
            ]);
            echo "  Created batch " . ($i + 1) . "/$batches\n";
        }

        echo "  ✓ {$totalUsers} users created\n";
    }

    /**
     * Create 2000-5000 products per category
     */
    private function createProducts($categories): void
    {
        $batchSize = 500;
        $totalProducts = 0;

        foreach ($categories as $category) {
            // Random number of products per category (50-100)
            $productsCount = rand(25, 50);
            $batches = ceil($productsCount / $batchSize);

            echo "  Category '{$category->name}' (ID: {$category->id}): Creating {$productsCount} products...\n";

            for ($i = 0; $i < $batches; $i++) {
                $currentBatchSize = ($i == $batches - 1)
                    ? ($productsCount - ($i * $batchSize))
                    : $batchSize;

                Product::factory()->count($currentBatchSize)->create([
                    'category_id' => $category->id,
                ]);

                $totalProducts += $currentBatchSize;
                echo "    Batch " . ($i + 1) . "/$batches\n";
            }

            echo "    ✓ {$productsCount} products created for {$category->name}\n";
        }

        echo "  ✓ Total {$totalProducts} products created across all categories\n";
    }

    /**
     * Create cart items for random users
     */
    private function createCartItems(): void
    {
        // Get all users (exclude admins if needed)
        $users = User::where('role_id', 2)->inRandomOrder()->limit(2000)->get();
        $products = Product::where('status', true)->get();

        if ($products->isEmpty()) {
            echo "  No active products found for cart creation.\n";
            return;
        }

        $batchSize = 500;
        $cartItems = [];
        $totalCartItems = 0;

        foreach ($users as $index => $user) {
            // Each user has 1-5 items in cart
            $itemsCount = rand(1, 5);
            $userProducts = $products->random(min($itemsCount, $products->count()));

            foreach ($userProducts as $product) {
                $cartItems[] = [
                    'user_id' => $user->id,
                    'product_id' => $product->id,
                    'quantity' => rand(1, 3),
                    'price' => $product->price,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $totalCartItems++;

                // Insert in batches
                if (count($cartItems) >= $batchSize) {
                    Cart::insert($cartItems);
                    echo "    Inserted batch of " . count($cartItems) . " cart items\n";
                    $cartItems = [];
                }
            }
        }

        // Insert remaining items
        if (!empty($cartItems)) {
            Cart::insert($cartItems);
            echo "    Inserted final batch of " . count($cartItems) . " cart items\n";
        }

        echo "  ✓ {$totalCartItems} cart items created for " . count($users) . " users\n";
    }
}

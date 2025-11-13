<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Seeder;

class ProductImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "Creating default images for products...\n";

        // Get all products that don't have images
        $products = Product::doesntHave('images')->get();

        $count = 0;
        foreach ($products as $product) {
            // Create a default image for each product
            ProductImage::create([
                'product_id' => $product->id,
                'path' => 'default.png', // Using default image
                'alt' => $product->name,
                'sort_order' => 1,
            ]);
            $count++;

            if ($count % 100 == 0) {
                echo "  Created {$count} images...\n";
            }
        }

        echo "✓ Total {$count} default images created\n";
    }
}

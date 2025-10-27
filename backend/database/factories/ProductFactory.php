<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $productNames = [
            'Laptop', 'Smartphone', 'Tablet', 'Headphones', 'Camera',
            'Monitor', 'Keyboard', 'Mouse', 'Speaker', 'Smartwatch',
            'Gaming Console', 'Router', 'External HDD', 'SSD Drive', 'RAM Module',
            'Graphics Card', 'Processor', 'Motherboard', 'Power Supply', 'CPU Cooler',
            'Webcam', 'Microphone', 'USB Cable', 'HDMI Cable', 'Phone Case',
            'Screen Protector', 'Charger', 'Power Bank', 'Smart TV', 'Projector'
        ];

        $brands = [
            'Samsung', 'Apple', 'Sony', 'LG', 'Dell',
            'HP', 'Lenovo', 'Asus', 'Acer', 'MSI',
            'Razer', 'Logitech', 'Corsair', 'Kingston', 'Seagate',
            'Western Digital', 'Intel', 'AMD', 'NVIDIA', 'Canon'
        ];

        $statuses = [true, true, true, false]; // 75% active
        $hotStatuses = [true, false, false, false]; // 25% hot

        $name = fake()->randomElement($brands) . ' ' . fake()->randomElement($productNames) . ' ' . fake()->randomElement(['Pro', 'Max', 'Ultra', 'Plus', 'Elite', 'Premium', '']);
        $slug = Str::slug($name) . '-' . fake()->unique()->numberBetween(1000, 999999);

        // Generate specifications
        $specifications = [
            'Brand' => fake()->randomElement($brands),
            'Model' => strtoupper(fake()->bothify('??-####')),
            'Color' => fake()->randomElement(['Black', 'White', 'Silver', 'Blue', 'Red', 'Gold']),
            'Weight' => fake()->numberBetween(100, 5000) . 'g',
            'Warranty' => fake()->randomElement(['6 months', '1 year', '2 years', '3 years']),
        ];

        // Price ranges based on product type
        $price = fake()->randomElement([
            fake()->numberBetween(500000, 2000000),      // Low range
            fake()->numberBetween(2000000, 10000000),    // Mid range
            fake()->numberBetween(10000000, 30000000),   // High range
            fake()->numberBetween(30000000, 100000000),  // Premium range
        ]);

        return [
            'category_id' => Category::inRandomOrder()->first()?->id ?? 1,
            'name' => $name,
            'slug' => $slug,
            'description' => fake()->optional(0.8)->text(200), // Max 200 chars to fit in VARCHAR(255)
            'specifications' => json_encode($specifications),
            'price' => $price,
            'currency' => 'VND',
            'image' => null, // We'll handle this separately if needed
            'status' => fake()->randomElement($statuses),
            'parent_id' => null,
            'language' => fake()->randomElement(['en', 'vi']),
            'views' => fake()->numberBetween(0, 10000),
            'is_hot' => fake()->randomElement($hotStatuses),
            'created_at' => fake()->dateTimeBetween('-2 years', 'now'),
            'updated_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }

    /**
     * Indicate that the product is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => true,
        ]);
    }

    /**
     * Indicate that the product is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => false,
        ]);
    }

    /**
     * Indicate that the product is hot.
     */
    public function hot(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_hot' => true,
        ]);
    }

    /**
     * Indicate that the product is not hot.
     */
    public function normal(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_hot' => false,
        ]);
    }

    /**
     * Indicate that the product has high views.
     */
    public function popular(): static
    {
        return $this->state(fn (array $attributes) => [
            'views' => fake()->numberBetween(5000, 50000),
            'is_hot' => true,
        ]);
    }
}

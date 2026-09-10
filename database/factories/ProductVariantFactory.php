<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<ProductVariant>
 */
class ProductVariantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $colors = ['Red', 'Blue', 'Black', 'White', 'Navy', 'Grey', 'Green', 'Beige'];
        $sizes = ['S', 'M', 'L', 'XL'];

        $costPrice = fake()->randomFloat(2, 3000, 15000);
        $sellingPrice = $costPrice + fake()->randomFloat(2, 2000, 10000);

        return [
            'product_id' => Product::factory(),
            'sku' => strtoupper(Str::random(10)),
            'color' => fake()->randomElement($colors),
            'size' => fake()->randomElement($sizes),
            'cost_price' => $costPrice,
            'selling_price' => $sellingPrice,
            'stock_quantity' => fake()->numberBetween(10, 100),
        ];
    }
}

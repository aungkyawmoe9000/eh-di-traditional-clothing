<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
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
        $products = [
            'Classic Denim Jacket',
            'Cotton Summer Dress',
            'Slim Fit Chino Pants',
            'Oversized Graphic Tee',
            'Wool Blend Sweater',
            'Athletic Running Shorts',
            'Silk Evening Gown',
            'Casual Linen Shirt',
            'Leather Bomber Jacket',
            'Warm Hooded Parka',
            'Tailored Blazer',
            'Premium Lounge Joggers',
            'V-Neck Cotton T-Shirt',
            'High-Waisted Skinny Jeans',
            'Plaid Flannel Shirt',
            'Waterproof Windbreaker',
        ];

        return [
            'category_id' => Category::factory(),
            'name' => fake()->randomElement($products),
            'description' => fake()->paragraph(2),
        ];
    }
}

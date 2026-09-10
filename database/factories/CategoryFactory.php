<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = [
            'Men\'s Wear',
            'Women\'s Wear',
            'Kids Wear',
            'Accessories',
            'Footwear',
            'Sportswear',
            'Activewear',
            'Summer Collection',
            'Winter Essentials',
            'Traditional Clothing',
        ];

        $name = fake()->unique()->randomElement($categories);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->sentence(),
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Supplier>
 */
class SupplierFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $companies = [
            'Apex Textiles Ltd.',
            'Global Apparel Sourcing',
            'Vogue Fabric Mills',
            'Sovereign Garments Corp.',
            'Pinnacle Thread & Co.',
            'Metro Clothing Distributors',
            'Heritage Weaving Co.',
            'Elite Fashion Suppliers',
        ];

        return [
            'name' => fake()->unique()->randomElement($companies),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
        ];
    }
}

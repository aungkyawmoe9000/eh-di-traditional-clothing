<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create a default Admin user
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Create a Cashier user for standard POS functionality
        User::factory()->create([
            'name' => 'Cashier User',
            'email' => 'cashier@example.com',
            'password' => Hash::make('password'),
            'role' => 'cashier',
        ]);

        // 2. Create 3 to 5 Suppliers
        Supplier::factory()->count(fake()->numberBetween(3, 5))->create();

        // 3. Create 5 Categories
        Category::factory()
            ->count(5)
            ->create()
            ->each(function (Category $category) {
                // 4. For each Category, create 3 to 5 Products
                Product::factory()
                    ->count(fake()->numberBetween(3, 5))
                    ->create([
                        'category_id' => $category->id,
                    ])
                    ->each(function (Product $product) {
                        // 5. For each Product, create 2 to 4 ProductVariants
                        ProductVariant::factory()
                            ->count(fake()->numberBetween(2, 4))
                            ->create([
                                'product_id' => $product->id,
                            ]);
                    });
            });
    }
}

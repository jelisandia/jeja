<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create 1 admin user
        $admin = User::factory()->admin()->create([
            'name' => 'Admin User',
            'email' => 'admin@digitalstore.com',
        ]);

        // Create 10 regular users
        $users = User::factory(10)->create();

        // Create 30 products with mixed pricing
        $products = collect();
        
        // 10 budget products
        $products = $products->merge(Product::factory(10)->budget()->create());
        
        // 15 regular products
        $products = $products->merge(Product::factory(15)->create());
        
        // 5 premium products
        $products = $products->merge(Product::factory(5)->premium()->create());

        // Create some random purchases
        // Each user will have 1-5 random purchases
        foreach ($users as $user) {
            $purchaseCount = fake()->numberBetween(1, 5);
            $randomProducts = $products->random($purchaseCount);
            
            foreach ($randomProducts as $product) {
                Purchase::factory()->create([
                    'user_id' => $user->id,
                    'product_id' => $product->id,
                ]);
            }
        }

        // Admin also makes some purchases
        $adminPurchases = $products->random(3);
        foreach ($adminPurchases as $product) {
            Purchase::factory()->create([
                'user_id' => $admin->id,
                'product_id' => $product->id,
            ]);
        }

        $this->command->info('Database seeded successfully!');
        $this->command->info('Created:');
        $this->command->info('- 1 admin user (admin@digitalstore.com)');
        $this->command->info('- 10 regular users');
        $this->command->info('- 30 products (10 budget, 15 regular, 5 premium)');
        $this->command->info('- Random purchases for all users');
    }
}

<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $productTypes = [
            'Digital Art', 'Photography', 'Illustration', 'Vector Graphics', 
            'UI/UX Design', 'Logo Design', 'Icon Set', 'Background Image',
            'Texture Pack', 'Pattern Design', '3D Model', 'Video Template'
        ];

        $productType = fake()->randomElement($productTypes);
        $name = fake()->words(3, true);
        
        return [
            'name' => ucwords($name) . ' - ' . $productType,
            'description' => fake()->paragraph(3) . "\n\n" . 
                           "Perfect for " . fake()->randomElement(['web design', 'print media', 'social media', 'marketing materials', 'personal projects']) . 
                           ". High quality " . strtolower($productType) . " with " . 
                           fake()->randomElement(['commercial license', 'extended license', 'unlimited usage rights']) . ".",
            'price' => fake()->randomFloat(2, 5, 199.99),
            'original_file_path' => 'products/original/' . fake()->uuid() . '.zip',
            'preview_file_path' => 'products/preview/' . fake()->uuid() . '.jpg',
        ];
    }

    /**
     * Indicate that the product should be premium (higher price).
     */
    public function premium(): static
    {
        return $this->state(fn (array $attributes) => [
            'price' => fake()->randomFloat(2, 100, 499.99),
        ]);
    }

    /**
     * Indicate that the product should be budget (lower price).
     */
    public function budget(): static
    {
        return $this->state(fn (array $attributes) => [
            'price' => fake()->randomFloat(2, 1, 19.99),
        ]);
    }
}

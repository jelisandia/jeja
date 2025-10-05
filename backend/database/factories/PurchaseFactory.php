<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Purchase>
 */
class PurchaseFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Purchase::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'product_id' => Product::factory(),
            'purchase_timestamp' => fake()->dateTimeBetween('-6 months', 'now'),
        ];
    }

    /**
     * Indicate that the purchase should be recent (within last month).
     */
    public function recent(): static
    {
        return $this->state(fn (array $attributes) => [
            'purchase_timestamp' => fake()->dateTimeBetween('-1 month', 'now'),
        ]);
    }

    /**
     * Indicate that the purchase should be old (more than 3 months ago).
     */
    public function old(): static
    {
        return $this->state(fn (array $attributes) => [
            'purchase_timestamp' => fake()->dateTimeBetween('-6 months', '-3 months'),
        ]);
    }
}

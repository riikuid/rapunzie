<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TransactionItem>
 */
class TransactionItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'users_id'=> $this->faker->numberBetween($min = 6, $max = 15),
            'products_id' => $this->faker->numberBetween($min = 14, $max = 20),
            'transactions_id' => $this->faker->numberBetween($min = 12, $max = 14)
        ];
    }
}

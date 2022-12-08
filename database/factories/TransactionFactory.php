<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'users_id'=> $this->faker->numberBetween($min = 5, $max = 16),
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'address' => $this->faker->address(),
            'phone' =>  $this->faker->phoneNumber(),
            'courier' => NULL,
            'payment' => 'MIDTRANS',
            'payment_url' => 'kosong',
            'total_price' => $this->faker->numberBetween($min = 100000, $max = 16000000),
            'status' => 'SUCCESS',
            'created_at' => $this->faker->date($format = '2022-10-d h:i:s', $max = 'now')
        ];
    }
}

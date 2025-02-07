<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Item;
use App\Models\User;
use App\Models\Address;

class PurchaseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'item_id' => Item::inRandomOrder()->first()->id,
            'user_id' => User::factory()->create(),
            'address_id' => Address::factory(),
            'payment_method' => $this->faker->randomElement(['card', 'convenience-store']),
        ];
    }
}

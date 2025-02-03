<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class AddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory()->create(),
            'post_number' => $this->faker->regexify('[0-9]{3}-[0-9]{4}'),
            'address' => $this->faker->address,
            'building' => $this->faker->secondaryAddress,
        ];
    }
}

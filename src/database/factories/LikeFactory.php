<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Item;

class LikeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            // すでに存在するユーザーのIDをランダムに選択する
            'user_id' => User::inRandomOrder()->first()->id ?? \App\Models\User::factory()->create()->id,
            'item_id' => Item::inRandomOrder()->first()->id,
        ];
    }
}

<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Item;

class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        // Fakerのロケールを日本語に設定
        $faker = \Faker\Factory::create('ja_JP');

        // 既存のアイテムをランダムに取得
        $item = Item::inRandomOrder()->first() ?? Item::factory()->create();

        // アイテムに関係したコメントのテンプレート
        $comment = [
            "買うか迷っています",
            "購入しましたが、とても満足です",
            "とても良い品質です",
            "買うか迷っているなら、ぜひお勧めです",
            "思ったより良くなかった",
            "微妙だった",
        ];
        return [
            'user_id' => User::inRandomOrder()->first()->id ?? User::factory()->create()->id,
            'item_id' => Item::inRandomOrder()->first()->id,
            'content' => $this->faker->randomElement($comment),
        ];
    }
}

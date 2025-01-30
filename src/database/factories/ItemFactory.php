<?php

namespace Database\Factories;

use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    protected $model = Item::class;

    public function definition()
    {
        // 固定データのリスト
        $fixedItems = [
            [
                'name' => '腕時計',
                'price' => 15000,
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'image' => 'Clock.jpg',
            ],
            [
                'name' => 'HDD',
                'price' => 5000,
                'description' => '高速で信頼性の高いハードディスク',
                'image' => 'HDD.jpg',
            ],
            [
                'name' => '玉ねぎ3束',
                'price' => 300,
                'description' => '新鮮な玉ねぎ3束のセット',
                'image' => 'Onion.jpg',
            ],
            [
                'name' => '革靴',
                'price' => 4000,
                'description' => 'クラシックなデザインの革靴',
                'image' => 'Shoes.jpg',
            ],
            [
                'name' => 'ノートPC',
                'price' => 45000,
                'description' => '高性能なノートパソコン',
                'image' => 'Laptop.jpg',
            ],
            [
                'name' => 'マイク',
                'price' => 8000,
                'description' => '高音質のレコーディング用マイク',
                'image' => 'Mike.jpg',
            ],
            [
                'name' => 'ショルダーバッグ',
                'price' => 3500,
                'description' => 'おしゃれなショルダーバッグ',
                'image' => 'bag.jpg',
            ],
            [
                'name' => 'タンブラー',
                'price' => 500,
                'description' => '使いやすいタンブラー',
                'image' => 'Tumbler.jpg',
            ],
            [
                'name' => 'コーヒーミル',
                'price' => 4000,
                'description' => '手動のコーヒーミル',
                'image' => 'Coffee.jpg',
            ],
            [
                'name' => 'メイクセット',
                'price' => 2500,
                'description' => '便利なメイクアップセット',
                'image' => 'MakeUp.jpg',
            ],
        ];

        // 固定データからランダムにアイテムを選択
        $item = $this->faker->randomElement($fixedItems);

        // カテゴリーと状態をconfigから取得
        $categories = config('item.categories');
        $conditions = config('item.conditions');

        return [
            'name' => $item['name'],
            'price' => $item['price'],
            'description' => $item['description'],
            'category' => $this->faker->randomElement($categories),
            'condition' => $this->faker->randomElement($conditions),
            'image' => $item['image'],
            'is_sold' => $this->faker->boolean,
            'user_id' => \App\Models\User::factory(),
        ];
    }
}

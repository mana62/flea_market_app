<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $categories = config('item.categories');
        $conditions = config('item.conditions');

        $items = [
            [
                'name' => '腕時計',
                'price' => 15000,
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'condition' => 0,
                'image' => 'Clock.jpg',
            ],
            [
                'name' => 'HDD',
                'price' => 5000,
                'description' => '高速で信頼性の高いハードディスク',
                'condition' => 1,
                'image' => 'HDD.jpg',
            ],
            [
                'name' => '玉ねぎ3束',
                'price' => 300,
                'description' => '新鮮な玉ねぎ3束のセット',
                'condition' => 2,
                'image' => 'Onion.jpg',
            ],
            [
                'name' => '革靴',
                'price' => 4000,
                'description' => 'クラシックなデザインの革靴',
                'condition' => 3,
                'image' => 'Shoes.jpg',
            ],
            [
                'name' => 'ノートPC',
                'price' => 45000,
                'description' => '高性能なノートパソコン',
                'condition' => 0,
                'image' => 'Laptop.jpg',
            ],
            [
                'name' => 'マイク',
                'price' => 8000,
                'description' => '高音質のレコーディング用マイク',
                'condition' => 1,
                'image' => 'Mike.jpg',
            ],
            [
                'name' => 'ショルダーバッグ',
                'price' => 3500,
                'description' => 'おしゃれなショルダーバッグ',
                'condition' => 2,
                'image' => 'bag.jpg',
            ],
            [
                'name' => 'タンブラー',
                'price' => 500,
                'description' => '使いやすいタンブラー',
                'condition' => 3,
                'image' => 'Tumbler.jpg',
            ],
            [
                'name' => 'コーヒーミル',
                'price' => 4000,
                'description' => '手動のコーヒーミル',
                'condition' => 0,
                'image' => 'Coffee.jpg',
            ],
            [
                'name' => 'メイクセット',
                'price' => 2500,
                'description' => '便利なメイクアップセット',
                'condition' => 1,
                'image' => 'MakeUp.jpg',
            ],
        ];

        foreach ($items as $item) {
            Item::create([
                'name' => $item['name'],
                'price' => $item['price'],
                'description' => $item['description'],
                'category' => $categories[array_rand($categories)],
                'condition' => $conditions[array_rand($conditions)],
                'image' => basename($item['image']),
            ]);
        }
    }
}


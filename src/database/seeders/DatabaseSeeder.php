<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\User;
use App\Models\Comment;
use App\Models\Like;

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

        $fixedItems = [
            [
                'name' => '腕時計',
                'price' => 15000,
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'image' => 'Clock.jpg',
                'category' => ['ファッション', 'メンズ'],
                'condition' => '良好',
            ],
            [
                'name' => 'HDD',
                'price' => 5000,
                'description' => '高速で信頼性の高いハードディスク',
                'image' => 'HDD.jpg',
                'category' => '家電',
                'condition' => '目立った傷や汚れなし',
            ],
            [
                'name' => '玉ねぎ3束',
                'price' => 300,
                'description' => '新鮮な玉ねぎ3束のセット',
                'image' => 'Onion.jpg',
                'category' => 'キッチン',
                'condition' => 'やや傷や汚れあり',
            ],
            [
                'name' => '革靴',
                'price' => 4000,
                'description' => 'クラシックなデザインの革靴',
                'image' => 'Shoes.jpg',
                'category' => ['ファッション', 'メンズ'],
                'condition' => '状態が悪い',
            ],
            [
                'name' => 'ノートPC',
                'price' => 45000,
                'description' => '高性能なノートパソコン',
                'image' => 'Laptop.jpg',
                'category' => '家電',
                'condition' => '良好',
            ],
            [
                'name' => 'マイク',
                'price' => 8000,
                'description' => '高音質のレコーディング用マイク',
                'image' => 'Mike.jpg',
                'category' => '家電',
                'condition' => '目立った傷や汚れなし',
            ],
            [
                'name' => 'ショルダーバッグ',
                'price' => 3500,
                'description' => 'おしゃれなショルダーバッグ',
                'image' => 'bag.jpg',
                'category' => ['ファッション', 'レディース'],
                'condition' => 'やや傷や汚れあり',
            ],
            [
                'name' => 'タンブラー',
                'price' => 500,
                'description' => '使いやすいタンブラー',
                'image' => 'Tumbler.jpg',
                'category' => 'キッチン',
                'condition' => '状態が悪い',
            ],
            [
                'name' => 'コーヒーミル',
                'price' => 4000,
                'description' => '手動のコーヒーミル',
                'image' => 'Coffee.jpg',
                'category' => ['キッチン', '家電'],
                'condition' => '良好',
            ],
            [
                'name' => 'メイクセット',
                'price' => 2500,
                'description' => '便利なメイクアップセット',
                'image' => 'MakeUp.jpg',
                'category' => ['コスメ', 'メイク'],
                'condition' => '目立った傷や汚れなし',
            ],
        ];

        User::factory(20)->create();

        foreach ($fixedItems as $item) {
            Item::create([
                'user_id' => User::inRandomOrder()->first()->id,
                'name' => $item['name'],
                'price' => $item['price'],
                'description' => $item['description'],
                'category' => $item['category'],
                'condition' => $item['condition'],
                'image' => $item['image'],
            ]);
        }

        Comment::factory(30)->create();
        Like::factory(50)->create();
    }
}

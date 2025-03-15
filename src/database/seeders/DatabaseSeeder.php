<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\User;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Profile;
use App\Models\Address;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $user1 = User::factory()->create([
            'name' => '山田太郎',
            'email' => 'taro@example.com',
            'password' => Hash::make('password123'),
        ]);
        $user2 = User::factory()->create([
            'name' => '山田二郎',
            'email' => 'jiro@example.com',
            'password' => Hash::make('password456'),
        ]);
        $user3 = User::factory()->create([
            'name' => '山田三郎',
            'email' => 'saburo@example.com',
            'password' => Hash::make('password789'),
        ]);

        Profile::create([
            'user_id' => $user1->id,
            'name' => '山田太郎',
            'image' => null,
        ]);
        Profile::create([
            'user_id' => $user2->id,
            'name' => '山田二郎',
            'image' => null,
        ]);
        Profile::create([
            'user_id' => $user3->id,
            'name' => '山田三郎',
            'image' => null,
        ]);

        Address::create([
            'user_id' => $user1->id,
            'post_number' => '111-1111',
            'address' => '東京都',
            'building' => 'マンション101',
            'is_default' => true,
        ]);
        Address::create([
            'user_id' => $user2->id,
            'post_number' => '222-2222',
            'address' => '沖縄県',
            'building' => 'マンション202',
            'is_default' => true,
        ]);
        Address::create([
            'user_id' => $user3->id,
            'post_number' => '333-3333',
            'address' => '北海道',
            'building' => 'マンション303',
            'is_default' => true
        ]);

        $items = [
            ['id' => 'CO01', 'name' => '腕時計', 'price' => 15000, 'description' => 'スタイリッシュなデザインのメンズ腕時計', 'image' => 'Clock.jpg', 'condition' => '良好', 'category' => 'メンズ'],
            ['id' => 'CO02', 'name' => 'HDD', 'price' => 5000, 'description' => '高速で信頼性の高いハードディスク', 'image' => 'HDD.jpg', 'condition' => '目立った傷や汚れなし', 'category' => '家電'],
            ['id' => 'CO03', 'name' => '玉ねぎ3束', 'price' => 300, 'description' => '新鮮な玉ねぎ3束のセット', 'image' => 'Onion.jpg', 'condition' => 'やや傷や汚れあり', 'category' => 'キッチン'],
            ['id' => 'CO04', 'name' => '革靴', 'price' => 4000, 'description' => 'クラシックなデザインの革靴', 'image' => 'Shoes.jpg', 'condition' => '状態が悪い', 'category' => 'ファッション'],
            ['id' => 'CO05', 'name' => 'ノートPC', 'price' => 45000, 'description' => '高性能なノートパソコン', 'image' => 'Laptop.jpg', 'condition' => '良好', 'category' => '家電'],

            ['id' => 'CO06', 'name' => 'マイク', 'price' => 8000, 'description' => '高音質のレコーディング用マイク', 'image' => 'Mike.jpg', 'condition' => '目立った傷や汚れなし', 'category' => '家電'],
            ['id' => 'CO07', 'name' => 'ショルダーバッグ', 'price' => 3500, 'description' => 'おしゃれなショルダーバッグ', 'image' => 'bag.jpg', 'condition' => 'やや傷や汚れあり', 'category' => 'レディース'],
            ['id' => 'CO08', 'name' => 'タンブラー', 'price' => 500, 'description' => '使いやすいタンブラー', 'image' => 'Tumbler.jpg', 'condition' => '状態が悪い', 'category' => 'キッチン'],
            ['id' => 'CO09', 'name' => 'コーヒーミル', 'price' => 4000, 'description' => '手動のコーヒーミル', 'image' => 'Coffee.jpg', 'condition' => '良好', 'category' => 'キッチン'],
            ['id' => 'CO10', 'name' => 'メイクセット', 'price' => 2500, 'description' => '便利なメイクアップセット', 'image' => 'MakeUp.jpg', 'condition' => '目立った傷や汚れなし', 'category' => 'コスメ'],
        ];

        foreach (array_slice($items, 0, 5) as $item) {
            Item::create([
                'user_id' => $user1->id,
                'name' => $item['name'],
                'price' => $item['price'],
                'description' => $item['description'],
                'category' => $item['category'],
                'condition' => $item['condition'],
                'image' => $item['image'],
            ]);
        }

        foreach (array_slice($items, 5, 5) as $item) {
            Item::create([
                'user_id' => $user2->id,
                'name' => $item['name'],
                'price' => $item['price'],
                'description' => $item['description'],
                'category' => $item['category'],
                'condition' => $item['condition'],
                'image' => $item['image'],
            ]);
        }

        Comment::factory(5)->create();
        Like::factory(5)->create();
    }
}

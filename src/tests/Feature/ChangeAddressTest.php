<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Address;
use App\Models\Item;
use App\Models\Purchase;

class ChangeAddressTest extends TestCase
{
    use RefreshDatabase;

    //送付先住所変更画面にて登録した住所が商品購入画面に反映されている
    public function test_change_address()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        Address::factory()->create([
            'user_id' => $user->id,
            'post_number' => '111-1111',
            'address' => '東京都',
            'building' => '',
            'is_default' => true,
        ]);

        $newAddressData = [
            'post_number' => '222-2222',
            'address' => '大阪府',
            'building' => 'マンション111',
        ];

        $response = $this->actingAs($user)->post(route('change.address', ['item_id' => $item->id]), $newAddressData);
        $response->assertRedirect(route('purchase', ['item_id' => $item->id]));

        $this->assertDatabaseHas('addresses', [
            'user_id' => $user->id,
            'post_number' => '111-1111',
            'is_default' => false,
        ]);

        $this->assertDatabaseHas('addresses', [
            'user_id' => $user->id,
            'post_number' => '222-2222',
            'address' => '大阪府',
            'building' => 'マンション111',
            'is_default' => true,
        ]);

        $response = $this->actingAs($user)->get(route('purchase', ['item_id' => $item->id]));
        $response->assertSee('222-2222');
        $response->assertSee('大阪府');
        $response->assertSee('マンション111');
    }

    //購入した商品に送付先住所が紐づいて登録される
    public function test_purchase_item_with_new_address()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $newAddress = Address::factory()->create([
            'user_id' => $user->id,
            'post_number' => '222-2222',
            'address' => '大阪府',
            'building' => 'マンション111',
            'is_default' => true,
        ]);

        Purchase::factory()->create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'address_id' => $newAddress->id,
        ]);

        $this->actingAs($user)->post(route('item.purchase', ['item_id' => $item->id]));
        $this->assertDatabaseHas('purchases', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'address_id' => $newAddress->id,
        ]);
    }
}
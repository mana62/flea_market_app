<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Address;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    //小計画面で変更が即時反映される
    public function test_payment_method_selection()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        Address::factory()->create([
            'user_id' => $user->id,
            'is_default' => true,
        ]);

        $response = $this->actingAs($user)->get('/purchase/' . $item->id);
        $response->assertStatus(200);
        $response->assertSee('支払い方法');

        $response = $this->actingAs($user)->post('/purchase/' . $item->id, [
            'payment_method' => 'convenience-store',
        ]);

        $response->assertRedirect(route('thanks.buy'));

        $this->assertDatabaseHas('purchases', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'payment_method' => 'convenience-store',
        ]);

        $response = $this->actingAs($user)->get('/purchase/' . $item->id);
        $response->assertStatus(200);
        $response->assertSee('convenience-store');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Models\Item;
use App\Models\Address;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PurchaseRequest;
use App\Http\Requests\AddressRequest;

class PurchaseController extends Controller
{
    //購入ページを表示
    public function showPurchasePage($item_id)
    {
        $item = Item::findOrFail($item_id);
        $user = Auth::user();
        //デフォルト住所を取得
        $address = $user->addresses()->where('is_default', true)->first();

        return view('item_purchase', compact('item', 'address'));
    }

    //購入処理
    public function itemPurchase(PurchaseRequest $request, $item_id)
    {
        $item = Item::findOrFail($item_id);
        $paymentMethod = $request->input('payment_method');
        $address = Auth::user()->addresses()->where('is_default', true)->first();

        switch ($paymentMethod) {
            case 'card':
                $purchase = $this->createPurchase($item, $paymentMethod, $address);
                $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
                $paymentIntent = $stripe->paymentIntents->create([
                    'amount' => $item->price * 100,
                    'currency' => 'jpy',
                    'metadata' => [
                        'item_id' => $item_id,
                        'user_id' => Auth::id(),
                    ],
                ]);

                session([
                    'item_id' => $item->id,
                    'purchase_id' => $purchase->id,
                    'client_secret' => $paymentIntent->client_secret,
                ]);

                return redirect()->route('item.payment.page', [
                    'item_id' => $item_id,
                    'purchase_id' => $purchase->id,
                ]);

            case 'convenience-store':
                $this->createPurchase($item, $paymentMethod, $address);
                return redirect()->route('thanks.buy');
        }
    }

    // 購入データを作成する共通処理
    private function createPurchase(Item $item, string $paymentMethod, Address $address)
{
    $purchase = Purchase::create([
        'item_id' => $item->id,
        'user_id' => Auth::id(),
        'address_id' => $address->id,
        'payment_method' => $paymentMethod,
    ]);

    $item->update(['is_sold' => true]);

    session([
        'item_id' => $item->id,
        'user_id' => Auth::id(),
        'address_id' => $address->id,
        'purchase_id' => $purchase->id,
        'payment_method' => $paymentMethod,
    ]);

    return $purchase;
}


    //購入完了画面を表示
    public function thanksBuy(Request $request)
    {
        $item_id = session('item_id');
        $purchase_id = session('purchase_id');
        $payment_method = session('payment_method');

        return view('thanks_buy', compact('item_id', 'purchase_id', 'payment_method'));
    }

    //配送先変更ページを表示
    public function changeAddressPage($item_id)
    {
        $item = Item::findOrFail($item_id);
        $user = Auth::user();
        return view('change_address', ['item' => $item, 'defaultAddress' => $user->addresses()->where('is_default', true)->first()]);
    }

    //配送先を変更
    public function changeAddress(AddressRequest $request, $item_id)
{
    $user = Auth::user();

    // ① まず、現在のデフォルト住所を `false` に更新
    $user->addresses()->where('is_default', true)->update(['is_default' => false]);

    // ② その後、新しい住所を `is_default = true` で登録
    $address = new Address();
    $address->user_id = $user->id;
    $address->post_number = $request->input('post_number');
    $address->address = $request->input('address');
    $address->building = $request->input('building');
    $address->is_default = true;
    $address->save();
    $user->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
    return redirect()->route('purchase', ['item_id' => $item_id])->with('message', '配送先を変更しました');
}

}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Models\Item;
use App\Models\Address;
use Illuminate\Support\Facades\Auth;

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
    public function itemPurchase(Request $request, $item_id)
    {
        $item = Item::findOrFail($item_id);

        //データベースに保存
        $purchase = Purchase::create([
            'item_id' => $item->id,
            'profile_id' => Auth::id(),
            'payment_method' => $request->input('payment_method'),
        ]);

        //商品をsoldに更新
        $item->update(['is_sold' => true]);

        //セッションにデータを保存
        session([
            'item_id' => $item->id,
            'purchase_id' => $purchase->id,
            'payment_method' => $request->input('payment_method'),
        ]);

        return redirect()->route('thanks.buy')->with('message', '購入が完了しました');
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
    public function changeAddress(Request $request, $item_id)
    {
        $user = Auth::user();

        //データベースに保存
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

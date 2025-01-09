<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Models\Item;
use App\Models\Profile;
use App\Models\Address;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\AddressRequest;

class PurchaseController extends Controller
{
    public function showPurchasePage($item_id)
{
    $item = Item::findOrFail($item_id);

    $profile = Auth::user()->profile;

    // Ensure $profile->addresses is a collection (or empty collection if null)
    $addresses = $profile ? $profile->addresses ?: collect() : collect();

    // Get the default address, if available
    $address = $addresses->where('is_default', 1)->first();
    dd($address);
    // If no default address, initialize a blank address object
    if (!$address) {
        $address = null;  // No address found
    }

    return view('item_purchase', compact('item', 'profile', 'address'));
}


    public function itemPurchase(Request $request, $item_id)
    {
        $item = Item::findOrFail($item_id);
        $address = Address::findOrFail($request->input('address_id'));

        $purchase = new Purchase();
        $purchase->item_id = $item->id;
        $purchase->user_id = Auth::id();
        $purchase->address_id = $address->id;
        $purchase->save();

        $item->is_sold = true;
        $item->save();

        // リダイレクト処理
        return redirect()->route('item_purchase', ['item_id' => $item->id])->with('message', '購入が完了しました');
    }

    public function changeAddressPage($item_id)
    {
        $item = Item::findOrFail($item_id);

        // ログインユーザーのプロフィールを取得、存在しない場合は新規作成
        $user = Auth::user();
        $profile = $user->profile ?? new Profile(['user_id' => $user->id]);

        return view('change_address', compact('item', 'profile'));
    }

    public function changeAddress(AddressRequest $request, $item_id)
    {
        $user = Auth::user();

        // 新しい住所を作成
        $address = new Address();
        $address->user_id = $user->id;
        $address->post_number = $request->input('post_number');
        $address->address = $request->input('address');
        $address->building = $request->input('building');
        $address->is_default = true;
        $address->save();

        // 他の住所をデフォルトから外す
        $user->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);

        return redirect()->route('change.address', ['item_id' => $item_id])->with('message', '配送先住所を変更しました');
    }
}

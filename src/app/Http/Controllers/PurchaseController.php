<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{

    public function itemPurchase(Request $request, $item_id)
    {
        $paymentMethod = $request->input('payment_method');
        $item = Item::findOrFail($item_id);
   
        // 購入済みに変更
        $item->is_sold = true;
        $item->save();
    
        // ログイン中のユーザーのプロフィールを取得
        $user = Auth::user();
        if (!$user->profile) {
            return redirect()->back()->with('error', 'プロフィールが見つかりません');
        }
        // 購入情報を保存
        $purchase = new Purchase();
        $purchase->item_id = $item->id;
        $purchase->profile_id = $user->profile->id;
        $purchase->save();
    
        return redirect()->route('item')->with('success', '購入が完了しました');
    }
}    
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Http\Requests\ProfileRequest;

class ProfileController extends Controller
{
    // マイページ（プロフィール画面または購入・出品商品一覧）を表示
    public function index(Request $request)
{
    $items = Item::with('user.profile')->get();
    $user = Auth::user();
    $tab = $request->query('page'); // クエリパラメータ 'page' を取得
    $profile = $user->profile;

    if ($tab === 'buy') {
        $items = $user->purchasedItems; // 購入商品のリスト
    } elseif ($tab === 'sell') {
        $items = $user->listedItems; // 出品商品のリスト
    } else {
        // クエリがない場合は空のコレクションを渡す
        $items = collect([]);
    }

    return view('mypage', compact('profile', 'items', 'tab'));
}


    // プロフィール編集画面を表示
    public function edit()
    {
        $user = Auth::user();
        $profile = $user->profile;

        // デフォルトの住所を取得
        $defaultAddress = $user->addresses()->where('is_default', true)->first();

        return view('profile', compact('profile', 'defaultAddress'));
    }

   // プロフィールを更新
   public function update(ProfileRequest $request)
   {
       $user = Auth::user();
       $profile = $user->profile;
   
       // プロフィール情報を更新
       $profile->fill($request->only(['name']));
       
       if ($request->hasFile('img')) {
           $path = $request->file('img')->store('public/image/profile_images');
           $profile->image = basename($path);
       }
       $profile->save();
   
       // 初回のみ has_profile フラグを true にする
       if (!$user->has_profile) {
           $user->has_profile = true;
           $user->save();
       }
   
       // プロフィール作成後に item 一覧ページへリダイレクト
       return redirect()->route('item');
   }
}   
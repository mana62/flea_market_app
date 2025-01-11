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
            $originalName = $request->file('img')->getClientOriginalName();
            $fileName = now()->format('Ymd_His') . '_' . $originalName;
            $path = $request->file('img')->storeAs('public/profile_images', $fileName);
            $profile->image = basename($path);
        }
        $profile->save();

        // デフォルト住所を取得、存在しない場合は新規作成
        $address = $user->addresses()->where('is_default', true)->first() ?? new \App\Models\Address(['user_id' => $user->id]);

        // 住所情報を更新
        $address->fill($request->only(['post_number', 'address', 'building']));
        $address->is_default = true;
        $address->save();

        // 初回のみ has_profile フラグを true にする
        if (!$user->has_profile) {
            $user->has_profile = true;
            $user->save();
        }

        return redirect()->route('mypage.profile.update')->with('message', 'プロフィールを更新しました');
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Http\Requests\ProfileRequest;

class ProfileController extends Controller
{
    //マイページを表示
    public function index(Request $request)
    {
        //ログイン中のユーザーを取得
        $user = Auth::user();
        //クエリパラメータでタブを取得
        $tab = $request->query('page');
        //プロフィール情報を取得
        $profile = $user->profile;

        //タブごとに表示するアイテムを切り替え
        $items = match ($tab) {
            'buy' => $user->purchasedItems,
            'sell' => $user->listedItems,
            //デフォルトは空のコレクション
            default => collect([]),
        };

        return view('mypage', compact('profile', 'items', 'tab'));
    }

    //プロフィール編集ページを表示
    public function edit()
    {
        //ログイン中のユーザー、プロフィール、デフォルトのアドレスを取得
        $user = Auth::user();
        $profile = $user->profile;
        $defaultAddress = $user->addresses()->where('is_default', true)->first();

        return view('profile', compact('profile', 'defaultAddress'));
    }

    //プロフィール更新処理
    public function update(ProfileRequest $request)
    {
        $user = Auth::user();
        $profile = $user->profile;

        //プロフィール情報を更新
        $profile->fill($request->only(['name']));
        if ($request->hasFile('img')) {
            $path = $request->file('img')->store('public/profile_images');
            $profile->image = basename($path);
        }
        $profile->save();

        //デフォルト住所を更新
        $address = $user->addresses()->updateOrCreate(
            ['is_default' => true],
            $request->only(['post_number', 'address', 'building'])
        );

        //初回更新時のフラグ設定
        if (!$user->has_profile) {
            $user->update(['has_profile' => true]);
        }

        return redirect()->route('mypage.profile.edit')->with('message', 'プロフィールを更新しました');
    }
}

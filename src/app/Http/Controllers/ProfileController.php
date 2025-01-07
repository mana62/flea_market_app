<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Profile;

class ProfileController extends Controller
{
    // プロフィールを表示する
    public function index()
    {
        $user = Auth::user(); // ログイン中のユーザー情報を取得
        $profile = $user->profile; // ユーザーのプロフィールを取得
    
        return view('mypage', compact('profile')); // プロフィール画面を表示
    }

    // プロフィール編集画面を表示する
    public function edit()
    {
        $user = Auth::user();
        $profile = $user->profile;

        return view('profile', compact('profile'));
    }

    // プロフィールを更新する
    public function update(Request $request)
    {
        $user = Auth::user();
        $profile = $user->profile;

        // 入力された内容が正しいかを確認する（バリデーション）
        $request->validate([
            'name' => 'required|string|max:255',
            'post_number' => 'required|string|max:10',
            'address' => 'required|string|max:255',
            'building' => 'nullable|string|max:255',
            'img' => 'nullable|image|max:2048', // 画像は最大2MBまで
        ]);

        // プロフィール情報を更新する
        $profile->fill($request->only(['name', 'post_number', 'address', 'building']));

        // プロフィール画像を保存する
        if ($request->hasFile('img')) {
            $path = $request->file('img')->store('public/image/profile_images');
            $fileName = now()->format('Ymd_His') . '_' . $path;
            $profile->image = basename($path);
        }

        $profile->save(); // プロフィールを保存する

        return redirect()->route('mypage')->with('success', 'プロフィールを更新しました。');
    }

    public function purchasedItem()
{
    $user = Auth::user();
    $items = $user->purchasedItems; // ユーザーが購入した商品のリレーションを利用
    $tab = 'buy';

    return view('mypage.profile', compact('items', 'tab'));
}

public function listItem()
{
    $user = Auth::user();
    $items = $user->listedItems; // ユーザーが出品した商品のリレーションを利用
    $tab = 'sell';

    return view('mypage.profile', compact('items', 'tab'));
}
}

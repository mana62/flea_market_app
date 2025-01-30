<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ProfileRequest;
use Intervention\Image\Facades\Image;
use App\Models\Profile;

class ProfileController extends Controller
{
    //マイページを表示
    public function index(Request $request)
{
    $user = Auth::user();
    $tab = $request->query('page', 'buy'); // 初期値を 'buy' に統一
    $profile = $user->profile;

    $items = match ($tab) {
        'buy' => $user->purchasedItems,
        'sell' => $user->listedItems,
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

    // プロフィール情報を更新
    $profile->fill($request->only(['name']));

    // Base64形式の画像が送信された場合の処理
    if ($request->filled('img_base64')) {
        $base64Image = $request->input('img_base64');
        $imageData = explode(',', $base64Image)[1];
    
        // Base64デコード
        $imageDecoded = base64_decode($imageData);
        
    
        $imageName = uniqid() . '.png';
        $imagePath = storage_path('app/public/profile_images/' . $imageName);
    
        $profile->image = $imageName;
    }
    
    $profile->save();

    // デフォルト住所を更新
    $user->addresses()->update(['is_default' => false]); // 全てfalseに
$user->addresses()->updateOrCreate(
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
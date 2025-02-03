<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ProfileRequest;
use App\Models\Profile;

class ProfileController extends Controller
{
    //マイページを表示
    public function index(Request $request)
    {
        $user = Auth::user();
        $profile = $user->profile; 
        // クエリパラメータ 'page' がある場合は、その値を使用
        $tab = $request->query('page', 'buy'); // デフォルトは 'buy'
    
        // 'buy' と 'sell' のパラメータに基づいてアイテムを取得
        $items = match ($tab) {
            'buy' => $user->purchasedItems, // 購入商品
            'sell' => $user->listedItems,   // 出品商品
            default => collect([]),         // それ以外は空
        };
    
        return view('mypage', compact('tab', 'items', 'profile'));
    }
    

     //プロフィール編集ページを表示
     public function edit()
     {
         $user = Auth::user(); // 現在のログインユーザーの取得
         $profile = $user->profile ?? new Profile(); // プロフィール取得
         $defaultAddress = $user->addresses()->where('is_default', true)->first(); // デフォルト住所の取得
     
         return view('profile', compact('profile', 'defaultAddress')); // ビューにデータを渡す
     }
     

    //プロフィール更新処理
    public function update(ProfileRequest $request)
    {
        $user = Auth::user();
        $profile = $user->profile;
    
        // プロフィール情報を更新
        $profile->fill($request->only(['name']));
    
        // 画像アップロード処理
        if ($request->filled('img_base64')) {
            $base64Image = $request->input('img_base64');
            $imageData = explode(',', $base64Image)[1];
    
            // Base64デコード
            $imageDecoded = base64_decode($imageData);
    
            // ユニークなファイル名を作成
            $timestamp = now()->format('Y-m-d_H-i-s');
            $originalFileName = uniqid();
            $extension = 'png';
            $imageName = "{$timestamp}_{$originalFileName}.{$extension}";
    
            // 画像を保存
            $imagePath = storage_path("app/public/profile_images/" . $imageName);
            file_put_contents($imagePath, $imageDecoded);
    
            //  プロフィールに画像名を保存
            $profile->image = $imageName;
        }
    
        // プロフィール情報を保存
        $profile->save();
    
        // すべての `is_default = 1` を `is_default = 0` に変更（デフォルト住所を1つにする）
        $user->addresses()->where('is_default', true)->update(['is_default' => false]);
    
        //  `firstOrNew()` を使用し、既存の住所があるか確認
        $address = $user->addresses()->firstOrNew(['user_id' => $user->id]);
    
        // フォームのデータをセット
        $address->fill($request->only(['post_number', 'address', 'building']));
        $address->is_default = true; //  常に1つのデフォルト住所しか持たないように
    
        // ✅ 住所を保存
        $address->save();
    
        // ✅ 初回更新時のフラグ設定
        if (!$user->has_profile) {
            $user->update(['has_profile' => true]);
        }
    
        return redirect()->route('mypage.profile.edit')->with('message', 'プロフィールを更新しました');
    }
}    
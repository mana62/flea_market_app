<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ProfileRequest;
use App\Models\Profile;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $profile = $user->profile;

        $tab = $request->query('page', 'buy');

        $items = match ($tab) {
            'buy' => $user->purchasedItems,
            'sell' => $user->listedItems,
            default => collect([]),
        };

        return view('mypage', compact('tab', 'items', 'profile'));
    }

    public function edit()
    {
        $user = Auth::user();
        $profile = $user->profile ?? new Profile();
        $defaultAddress = $user->addresses()->where('is_default', true)->first();

        return view('profile', compact('profile', 'defaultAddress'));
    }

    public function update(ProfileRequest $request)
    {
        $user = Auth::user();
        $profile = $user->profile ?? new Profile(['user_id' => $user->id]);
        $profile->fill($request->only(['name']));

        if ($request->filled('img_base64')) {
            $base64Image = $request->input('img_base64');
            $imageData = explode(',', $base64Image)[1];
            $imageDecoded = base64_decode($imageData);
            $timestamp = now()->format('Y-m-d_H-i-s');
            $originalFileName = uniqid();
            $extension = 'png';
            $imageName = "{$timestamp}_{$originalFileName}.{$extension}";
            $imagePath = storage_path("app/public/profile_images/" . $imageName);
            file_put_contents($imagePath, $imageDecoded);
            $profile->image = $imageName;
        }
        $profile->save();

        $user->addresses()->where('is_default', true)->update(['is_default' => false]);
        $address = $user->addresses()->firstOrNew(['user_id' => $user->id]);
        $address->fill($request->only(['post_number', 'address', 'building']));
        $address->is_default = true;
        $address->save();

        if (!$user->has_profile) {
            $user->update(['has_profile' => true]);
        }
        return redirect()->route('mypage.profile.edit')->with('message', 'プロフィールを更新しました');
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ProfileRequest;
use App\Models\Profile;
use App\Models\Chat;
use App\Models\ChatRoom;
use App\Models\Rating;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $profile = $user->profile;

        $activeTransactionCount = ChatRoom::where(function ($query) use ($user) {
            $query->where('seller_id', $user->id)
                ->orWhere('buyer_id', $user->id);
        })
            ->whereIn('transaction_status', ['active', 'buyer_rated'])
            ->count();

        $unreadMessageCounts = Chat::whereHas('chatRoom', function ($query) use ($user) {
            $query->where('seller_id', $user->id)
                ->orWhere('buyer_id', $user->id);
        })
            ->where('user_id', '!=', $user->id)
            ->where('read_status', 'unread')
            ->selectRaw('chat_room_id, COUNT(*) as unread_count')
            ->groupBy('chat_room_id')
            ->pluck('unread_count', 'chat_room_id');

        $averageScore = round(Rating::where('rated_id', $user->id)->avg('rating'), 0);

        $tab = $request->query('page', 'buy');
        $items = match ($tab) {
            'buy' => $user->purchasedItems,
            'sell' => $user->listedItems,
            'progress' => $user->progressPurchasedItems()->get()
                ->merge($user->progressListedItems()->get())
                ->load('chatRoom.chats')
                ->filter(fn($item) => in_array(optional($item->chatRoom)->transaction_status, ['active', 'buyer_rated']))
                ->sortByDesc(fn($item) => optional($item->chatRoom->chats->last())->created_at),
            default => collect([]),
        };

        return view('mypage', compact('tab', 'items', 'profile', 'unreadMessageCounts', 'activeTransactionCount', 'averageScore'));
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
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ChatRequest;
use App\Models\Chat;
use App\Models\ChatRoom;
use App\Models\Rating;
use App\Models\Profile;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function storeContent(Request $request)
    {
        $chatRoomId = $request->input('chat_room_id');
        $sessionKey = "stored_content_chatroom_{$chatRoomId}";
        session([$sessionKey => $request->input('content')]);

        return response()->json(['stored_content' => session($sessionKey)]);
    }

    public function index(Request $request, $item_id)
    {
        $user = Auth::user();
        $chatRoom = ChatRoom::where('item_id', $item_id)
            ->where(function ($query) use ($user) {
                $query->where('seller_id', $user->id)
                    ->orWhere('buyer_id', $user->id);
            })
            ->with(['item', 'seller', 'buyer'])
            ->first();

        $chats = Chat::where('chat_room_id', $chatRoom->id)
            ->orderBy('created_at', 'asc')
            ->get();

        optional($chatRoom)->id ? $this->markMessagesAsRead($chatRoom->id, $user->id) : null;

        $sellerName = optional($chatRoom->seller)->name ?? '出品者不明';
        $buyerName = optional($chatRoom->buyer)->name ?? '購入者不明';

        $otherChatRooms = $this->getOtherChatRooms($user, $chatRoom);
        $messages = $chatRoom ? $chatRoom->chats()->orderBy('created_at', 'asc')->get() : collect();
        $storedContent = session("stored_content_chatroom_{$chatRoom->id}", '');

        return view('chat', [
            'chatRoom' => $chatRoom,
            'chats' => $chats,
            'messages' => $messages,
            'profile' => Profile::where('user_id', $user->id)->first(),
            'otherChatRooms' => $otherChatRooms,
            'storedContent' => $storedContent,
            'hasRated' => $this->checkUserRating($chatRoom, $user->id),
            'hasBuyerRated' => $this->checkUserRating($chatRoom, $chatRoom->buyer_id),
            'hasSellerRated' => $this->checkUserRating($chatRoom, $chatRoom->seller_id),
            'sellerName' => $sellerName,
            'buyerName' => $buyerName,
        ]);
    }

    private function markMessagesAsRead($chatRoomId, $userId)
    {
        Chat::where('chat_room_id', $chatRoomId)
            ->where('user_id', '!=', $userId)
            ->where('read_status', 'unread')
            ->update(['read_status' => 'read']);
    }

    public function store(ChatRequest $request)
    {
        $chatRoom = $this->getChatRoom($request->item_id, Auth::user());
        $imagePath = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $timestamp = now()->format('Y-m-d_H-i-s');
            $originalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $image->getClientOriginalExtension();
            $fileName = "{$timestamp}_{$originalName}.{$extension}";
            $imagePath = $image->storeAs('chat_images', $fileName, 'public');
        }

        Chat::create([
            'user_id' => Auth::id(),
            'chat_room_id' => $chatRoom->id,
            'content' => $request->input('content'),
            'image' => $imagePath,
            'read_status' => 'unread',
        ]);

        session()->forget("stored_content_chatroom_{$chatRoom->id}");
        $receiverId = ($chatRoom->buyer_id === Auth::id()) ? $chatRoom->seller_id : $chatRoom->buyer_id;

        return redirect()->route('chat', ['item_id' => $request->item_id])->with('message', 'メッセージを送信しました');
    }

    public function update(Request $request, $id)
    {
        $chat = Chat::findOrFail($id);
        $imagePath = $chat->image;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $timestamp = now()->format('Y-m-d_H-i-s');
            $originalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $image->getClientOriginalExtension();
            $fileName = "{$timestamp}_{$originalName}.{$extension}";

            $imagePath = $image->storeAs('chat_images', $fileName, 'public');
        }

        $chat->update([
            'content' => $request->input('content', ''),
            'image' => $imagePath,
            'read_status' => 'unread',
        ]);

        return redirect()->route('chat', ['item_id' => $chat->chatRoom->item_id])->with('message', 'メッセージを編集しました');
    }

    public function destroy($id)
    {
        $chat = Chat::findOrFail($id);
        $chatRoom = ChatRoom::find($chat->chat_room_id);
        $chat->delete();
        return redirect()->route('chat', ['item_id' => $chatRoom->item_id])
            ->with('message', 'メッセージを削除しました');
    }

    private function getChatRoom($item_id, $user)
    {
        return ChatRoom::where('item_id', $item_id)
            ->where(function ($query) use ($user) {
                $query->where('seller_id', $user->id)
                    ->orWhere('buyer_id', $user->id);
            })
            ->with(['item', 'seller', 'buyer'])
            ->first();
    }

    private function getOtherChatRooms($user, $currentChatRoom)
    {
        return ChatRoom::where(function ($query) use ($user) {
            $query->where('seller_id', $user->id)
                ->orWhere('buyer_id', $user->id);
        })
            ->where('id', '!=', optional($currentChatRoom)->id)
            ->where('transaction_status', 'active')
            ->with([
                'item',
                'chats' => function ($query) {
                    $query->orderByDesc('created_at');
                }
            ])
            ->withCount([
                'chats as unread_count' => function ($query) use ($user) {
                    $query->where('user_id', '!=', $user->id)
                        ->where('read_status', 'unread');
                }
            ])
            ->orderByDesc('unread_count')
            ->orderByDesc(
                Chat::select('created_at')
                    ->whereColumn('chat_rooms.id', 'chats.chat_room_id')
                    ->latest()
                    ->limit(1)
            )
            ->get();
    }

    private function checkUserRating($chatRoom, $userId)
    {
        return Rating::where('chat_room_id', $chatRoom->id)
            ->where('rater_id', $userId)
            ->exists();
    }
}

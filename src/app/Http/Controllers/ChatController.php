<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ChatRequest;
use App\Models\Chat;
use App\Models\ChatRoom;
use App\Models\Rating;
use App\Models\Profile;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    // セッションにメッセージの内容を保存
    public function storeContent(Request $request)
    {
        session(['stored_content' => $request->input('content')]);
        return response()->json(['stored_content' => session('stored_content')]);
    }

    // チャットページ表示
    public function index(Request $request, $item_id)
    {
        $user = Auth::user();
        $chatRoom = ChatRoom::where('item_id', $item_id)
    ->where(function ($query) use ($user) {
        $query->where('seller_id', $user->id)
            ->orWhere('buyer_id', $user->id);
    })
    ->with(['item', 'seller', 'buyer']) // リレーションを追加
    ->first();



        // チャットルームを開いたら未読メッセージを既読にする
        optional($chatRoom)->id ? $this->markMessagesAsRead($chatRoom->id, $user->id) : null;
        // チャット履歴を取得
        $chats = Chat::where('chat_room_id', $chatRoom->id)
        ->orderBy('created_at', 'asc')
        ->get();
        $sellerName = optional($chatRoom->seller)->name ?? '出品者不明';
        $buyerName = optional($chatRoom->buyer)->name ?? '購入者不明';

        $otherChatRooms = $this->getOtherChatRooms($user, $chatRoom);
        $messages = $chatRoom ? $chatRoom->chats()->orderBy('created_at', 'asc')->get() : collect();
        $storedContent = session('stored_content', '');

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

    // 既読処理を追加
    private function markMessagesAsRead($chatRoomId, $userId)
    {
        Chat::where('chat_room_id', $chatRoomId)
            ->where('user_id', '!=', $userId) // 自分が送ったメッセージは除外
            ->where('read_status', 'unread')
            ->update(['read_status' => 'read']);

        Notification::where('item_id', ChatRoom::find($chatRoomId)->item_id)
            ->where('user_id', '!=', $userId)
            ->where('notification_status', 'unread')
            ->update(['notification_status' => 'read']);
    }

    // チャットメッセージの保存
    public function store(ChatRequest $request)
    {
        $chatRoom = $this->getChatRoom($request->item_id, Auth::user());

        $chat = Chat::create([
            'user_id' => Auth::id(),
            'chat_room_id' => $chatRoom->id,
            'content' => $request->input('content'),
            'image' => $request->file('image') ? $request->file('image')->store('chat_images', 'public') : null,
            'read_status' => 'unread',
        ]);

        // セッションの入力内容をクリア
        session()->forget('stored_content');

        // 通知の作成
        $receiverId = ($chatRoom->buyer_id === Auth::id()) ? $chatRoom->seller_id : $chatRoom->buyer_id;

        return redirect()->route('chat', ['item_id' => $request->item_id])->with('message', 'メッセージを送信しました');
    }

    // メッセージの更新
    public function update(Request $request, $id)
    {
        $chat = Chat::findOrFail($id);
        $chat->update([
            'content' => $request->input('content', ''),
            'image' => $request->file('image') ? $request->file('image')->store('chat_images', 'public') : $chat->image,
            'read_status' => 'read',
        ]);

        return redirect()->route('chat', ['item_id' => $chat->chatRoom->item_id])->with('message', 'メッセージを編集しました');
    }

    // メッセージの削除
    public function destroy($id)
{
    $chat = Chat::findOrFail($id);

    // ログインユーザーの ID を取得
    $userId = Auth::id();

    // チャットがログインユーザーに属するものかチェック
    if ($chat->user_id !== $userId) {
        return redirect()->route('chat', ['item_id' => $chat->chat_room_id])
            ->with('message', 'このメッセージを削除する権限がありません');
    }

    $chat->delete();

    return redirect()->route('chat', ['item_id' => $chat->chat_room_id])
        ->with('message', 'メッセージを削除しました');
}


    // 取引を完了させる
    public function closeTrade($chatRoomId)
    {
        $chatRoom = ChatRoom::findOrFail($chatRoomId);
        $chatRoom->update(['transaction_status' => 'buyer_rated']);
        return redirect()->route('chat', ['item_id' => $chatRoom->item_id]);
    }

    // ★★★ 共通メソッド ★★★

    // チャットルームを取得
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

    // 他の取引リストを取得
    private function getOtherChatRooms($user, $currentChatRoom)
    {
        return ChatRoom::where(function ($query) use ($user) {
            $query->where('seller_id', $user->id)
                ->orWhere('buyer_id', $user->id);
        })
            ->where('id', '!=', $currentChatRoom->id)
            ->where('transaction_status', 'active')
            ->with([
                'item',
                'chats' => function ($query) {
                    $query->latest();
                }
            ])
            ->get()
            ->sortByDesc(fn($room) => optional($room->chats->first())->created_at);
    }

    // 購入者、出品者両方共評価したらtrue を返す
    private function checkUserRating($chatRoom, $userId)
    {
        return Rating::where('chat_room_id', $chatRoom->id)
            ->where('rater_id', $userId)
            ->exists();
    }

    // 未読の通知を作成
    private function createNotification($chatRoom, $chat)
    {
        $receiverId = ($chatRoom->seller_id == Auth::id()) ? $chatRoom->buyer_id : $chatRoom->seller_id;

        if (!Chat::where('id', $chat->id)->exists()) {
            return; // `chat_id` が存在しなければ通知を作成しない
        }

        Notification::create([
            'user_id' => $receiverId, //通知を受け取る相手
            'item_id' => $chatRoom->item_id,
            'chat_id' => $chat->id, // チャットルームのIDを使用
            'type' => 'message',
            'notification_status' => 'unread',
        ]);
    }
}

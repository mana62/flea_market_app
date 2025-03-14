<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chat;
use App\Models\ChatRoom;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    // メッセージが読まれたら、通知を減らす（未読を0にする）
    public function readNotice($chatRoomId)
    {
        $user = Auth::user();

        DB::beginTransaction(); // トランザクション開始

        try {
            // ① 未読メッセージを既読にする
            Chat::where('chat_room_id', $chatRoomId)
                ->where('user_id', '!=', $user->id)
                ->where('read_status', 'unread')
                ->update(['read_status' => 'read']);

            // ② 該当する通知を既読にする
            $chatRoom = ChatRoom::find($chatRoomId);
            Notification::where('item_id', $chatRoom->item_id)
                ->where('user_id', '!=', $user->id)
                ->where('notification_status', 'unread')
                ->where('type', 'message')
                ->update(['notification_status' => 'read']);

            // ③ 通知の種類に応じて削除処理を変更
            Notification::where('item_id', $chatRoom->item_id)
                ->where('user_id', '!=', $user->id)
                ->where('notification_status', 'read')
                ->where('type', '!=', 'rating') // 評価通知は削除しない
                ->delete();

            // ④ 未読メッセージ数と未読通知数を取得
            $unreadMessageCount = Chat::whereHas('chatRoom', function ($query) use ($user) {
                $query->where('seller_id', $user->id)
                    ->orWhere('buyer_id', $user->id);
            })
                ->where('user_id', '!=', $user->id)
                ->where('read_status', 'unread')
                ->count();

            $unreadNotificationCount = Notification::where('user_id', $user->id)
                ->where('notification_status', 'unread')
                ->count();

            DB::commit(); // トランザクションコミット

            return response()->json([
                'unread_message_count' => $unreadMessageCount,
                'unread_notification_count' => $unreadNotificationCount,
            ]);
        } catch (\Exception $e) {
            DB::rollBack(); // トランザクションロールバック
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
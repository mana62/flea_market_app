<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rating;
use App\Models\ChatRoom;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class RatingController extends Controller
{
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $user = Auth::user();
            $chatRoom = ChatRoom::findOrFail($request->chat_room_id);

            // すでに評価済みか確認
            $alreadyRated = Rating::where('chat_room_id', $chatRoom->id)
                ->where('rater_id', $user->id)
                ->exists();

            if ($alreadyRated) {
                return response()->json(['success' => false, 'message' => '既に評価済みです']);
            }

            // 購入者が出品者にした評価を保存
            Rating::create([
                'chat_room_id' => $chatRoom->id,
                'rater_id' => $user->id,
                'rated_id' => ($chatRoom->buyer_id === $user->id) ? $chatRoom->seller_id : $chatRoom->buyer_id,
                'rating' => $request->rating,
            ]);

            // 出品者に評価したことの通知を送信
            Notification::create([
                'user_id' => $chatRoom->seller_id,
                'item_id' => $chatRoom->item_id,
                'chat_id' => $chatRoom->id,
                'type' => 'rating',
                'notification_status' => 'unread',
            ]);

            // 双方の評価が完了したか確認
            $hasBuyerRated = Rating::where('chat_room_id', $chatRoom->id)
                ->where('rater_id', $chatRoom->buyer_id)
                ->exists();

            $hasSellerRated = Rating::where('chat_room_id', $chatRoom->id)
                ->where('rater_id', $chatRoom->seller_id)
                ->exists();

            if ($hasBuyerRated && $hasSellerRated) {
                // 両者が評価済みなら取引完了
                $chatRoom->update(['transaction_status' => 'completed']);

                // 購入者に取引完了の通知を送信
                Notification::create([
                    'user_id' => $chatRoom->buyer_id,
                    'item_id' => $chatRoom->item_id,
                    'chat_id' => $chatRoom->id,
                    'type' => 'done',
                    'notification_status' => 'unread',
                ]);

                // 出品者に取引完了の通知を送信
                Notification::create([
                    'user_id' => $chatRoom->seller_id,
                    'item_id' => $chatRoom->item_id,
                    'chat_id' => $chatRoom->id,
                    'type' => 'done',
                    'notification_status' => 'unread',
                ]);

                // 取引関連データの削除
                if (Session::has("progress_{$chatRoom->item_id}")) {
                    Session::forget("progress_{$chatRoom->item_id}");
                }

                // 関連する通知を削除（未読・取引関連の通知）
                Notification::where('chat_id', $chatRoom->id)->delete();
            } else {
                // 購入者が評価したら 'rated' に変更
                if ($chatRoom->buyer_id === $user->id) {
                    $chatRoom->update(['transaction_status' => 'rated']);
                }
            }

            DB::commit();

            return response()->json(['success' => true, 'message' => '評価を送信しました']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'エラーが発生しました', 'error' => $e->getMessage()], 500);
        }
    }
}

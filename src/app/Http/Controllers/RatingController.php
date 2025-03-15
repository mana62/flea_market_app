<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rating;
use App\Models\ChatRoom;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use App\Mail\TransactionCompletedMail;

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
                return redirect()->route('item')->with('error', '既に評価済みです');
            }

            // ユーザーが出品者なのか購入者なのか判定
            $isBuyer = ($chatRoom->buyer_id === $user->id);
            $isSeller = ($chatRoom->seller_id === $user->id);

            // 評価を保存
            Rating::create([
                'chat_room_id' => $chatRoom->id,
                'rater_id' => $user->id,
                'rated_id' => $isBuyer ? $chatRoom->seller_id : $chatRoom->buyer_id,
                'rating' => $request->rating,
            ]);

            // // 通知を送信（評価したことを通知）
            // Notification::create([
            //     'user_id' => $isBuyer ? $chatRoom->seller_id : $chatRoom->buyer_id,
            //     'item_id' => $chatRoom->item_id,
            //     'chat_id' => $chatRoom->id,
            //     'type' => 'rating',
            //     'notification_status' => 'unread',
            // ]);

            // ステータスの更新（購入者が評価したら buyer_rated, 出品者が評価したら completed）
            if ($isBuyer) {
                $chatRoom->update(['transaction_status' => 'buyer_rated']);
                // 🔥 出品者にメール通知（購入者が評価したタイミングで送信）
                Mail::to($chatRoom->seller->email)->queue(new TransactionCompletedMail($chatRoom));
            }

            if ($isSeller && $chatRoom->transaction_status === 'buyer_rated') {
                $chatRoom->update(['transaction_status' => 'completed']);
                // 🔥 取引完了通知

                // 取引完了後、取引中のリストから削除（アイテムは削除しない）
                Session::forget("progress_{$chatRoom->item_id}");
                Notification::where('chat_id', $chatRoom->id)->delete();
            }

            DB::commit();

            return redirect()->route('item')->with('message', '評価を送信しました');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('item')->with('error', 'エラーが発生しました: ' . $e->getMessage());
        }
    }

    /**
     * 取引完了時の通知を送信する
     */
    private function sendTransactionCompletedNotifications($chatRoom)
    {
        // Notification::create([
        //     'user_id' => $chatRoom->buyer_id,
        //     'item_id' => $chatRoom->item_id,
        //     'chat_id' => $chatRoom->id,
        //     'type' => 'done',
        //     'notification_status' => 'unread',
        // ]);

        // Notification::create([
        //     'user_id' => $chatRoom->seller_id,
        //     'item_id' => $chatRoom->item_id,
        //     'chat_id' => $chatRoom->id,
        //     'type' => 'done',
        //     'notification_status' => 'unread',
        // ]);
    }
}

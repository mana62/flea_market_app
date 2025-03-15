<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rating;
use App\Models\ChatRoom;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\RatingRequest;
use App\Mail\TransactionCompletedMail;

class RatingController extends Controller
{
    public function store(RatingRequest $request)
    {
        DB::beginTransaction();

        try {
            $user = Auth::user();
            $chatRoom = ChatRoom::findOrFail($request->chat_room_id);

            $alreadyRated = Rating::where('chat_room_id', $chatRoom->id)
                ->where('rater_id', $user->id)
                ->exists();

            if ($alreadyRated) {
                return redirect()->route('item')->with('message', '既に評価済みです');
            }

            $isBuyer = ($chatRoom->buyer_id === $user->id);
            $isSeller = ($chatRoom->seller_id === $user->id);

            Rating::create([
                'chat_room_id' => $chatRoom->id,
                'rater_id' => $user->id,
                'rated_id' => $isBuyer ? $chatRoom->seller_id : $chatRoom->buyer_id,
                'rating' => $request->rating,
            ]);

            if ($isBuyer) {
                $chatRoom->update(['transaction_status' => 'buyer_rated']);
                Mail::to($chatRoom->seller->email)->queue(new TransactionCompletedMail($chatRoom));
            }

            if ($isSeller && $chatRoom->transaction_status === 'buyer_rated') {
                $chatRoom->update(['transaction_status' => 'completed']);

                Session::forget("progress_{$chatRoom->item_id}");
            }

            DB::commit();

            return redirect()->route('item')->with('message', '評価を送信しました');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('item')->with('message', 'エラーが発生しました: ' . $e->getMessage());
        }
    }
}

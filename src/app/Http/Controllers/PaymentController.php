<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Item;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PaymentRequest;

class PaymentController extends Controller
{
    //支払いページを表示
    public function itemPaymentPage(Request $request, $item_id)
    {
        //商品を取得
        $item = Item::findOrFail($item_id);

        //購入IDを取得
        $purchase_id = $request->query('purchase_id');

        //Stripeの秘密鍵を設定
        Stripe::setApiKey(env('STRIPE_SECRET'));

        //Stripeの支払いIntentを作成
        $paymentIntent = PaymentIntent::create([
            'amount' => $item->price * 100, //金額を設定（円をセンに変換）
            'currency' => 'jpy',           //日本円
            'metadata' => [
                'item_id' => $item_id,
                'user_id' => Auth::id(),
            ],
        ]);

        return view('item_payment', [
            'item' => $item,
            'purchase_id' => $purchase_id,
            'client_secret' => $paymentIntent->client_secret, //クライアントシークレットをビューに渡す
        ]);
    }

    //支払い処理
    public function itemPayment(PaymentRequest $request, $item_id)
    {
        // バリデート済みのデータを取得
        $validated = $request->validated();

        try {
            //支払い情報を保存
            $payment = Payment::create([
                'purchase_id' => $validated['purchase_id'],
                'payment_intent_id' => $validated['payment_intent_id'],
                'amount' => $validated['amount'],
                'currency' => $validated['currency'],
                'status' => 'succeeded',
            ]);

            //正常終了時のレスポンス
            return response()->json(['succeeded' => true, 'message' => '支払いが完了しました'], 200);
        } catch (\Exception $e) {
            //エラー時のレスポンス
            return response()->json(['succeeded' => false, 'message' => '支払いに失敗しました'], 500);
        }
    }
}

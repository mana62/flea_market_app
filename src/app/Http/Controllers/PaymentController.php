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
    // 支払いページを表示
    public function itemPaymentPage(Request $request, $item_id)
    {
        $item = Item::findOrFail($item_id);
        $purchaseId = session('purchase_id');

        Stripe::setApiKey(env('STRIPE_SECRET'));
        $paymentIntent = PaymentIntent::create([
            'amount' => $item->price * 100,
            'currency' => 'jpy',
            'metadata' => [
                'item_id' => $item_id,
                'user_id' => Auth::id(),
            ],
        ]);

        return view('item_payment', [
            'item' => $item,
            'purchase_id' => $purchaseId,
            'client_secret' => $paymentIntent->client_secret,
        ]);
    }

    // 支払い処理
    public function itemPayment(PaymentRequest $request, $item_id)
    {
        if ($request->input('payment_method') === 'convenience-store') {
            return redirect()->route('thanks.buy');
        }
        $validated = $request->validated();

        try {
            Payment::create([
                'purchase_id' => $validated['purchase_id'],
                'payment_intent_id' => $validated['payment_intent_id'],
                'amount' => $validated['amount'],
                'currency' => $validated['currency'],
                'status' => 'succeeded',
            ]);

            return response()->json(['succeeded' => true, 'message' => '支払いが完了しました'], 200);
        } catch (\Exception $e) {
            return response()->json(['succeeded' => false, 'message' => '支払いに失敗しました'], 500);
        }
    }
}
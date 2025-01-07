<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use App\Models\Item;
use Stripe\PaymentIntent;

class PaymentController extends Controller
{
    public function processPayment(Request $request, $item_id)
    {
        $item = Item::findOrFail($item_id);

        Stripe::setApiKey(env('STRIPE_SECRET'));

        $paymentIntent = PaymentIntent::create([
            'amount' => $item->price * 100,
            'currency' => 'jpy',
            'payment_method_types' => ['card', 'convenienceStores'],
            'metadata' => [
                'item_id' => $item_id,
                'user_id' => auth()->id(),
            ],
        ]);

        return redirect()->away($paymentIntent->next_action->redirect_to_url->url);
    }
}

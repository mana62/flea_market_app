<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Models\Item;
use App\Models\ChatRoom;
use App\Models\Address;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PurchaseRequest;
use App\Http\Requests\AddressRequest;

class PurchaseController extends Controller
{
    public function showPurchasePage($item_id)
    {
        $item = Item::findOrFail($item_id);
        $user = Auth::user();
        $address = $user->addresses()->where('is_default', true)->first();

        if (!session()->has('payment_method')) {
            session(['payment_method' => '']);
        }

        return view('item_purchase', compact('item', 'address'));
    }

    public function itemPurchase(PurchaseRequest $request, $item_id)
    {
        $item = Item::findOrFail($item_id);
        $paymentMethod = $request->input('payment_method');

        session(['payment_method' => $paymentMethod]);

        $address = Auth::user()->addresses()->where('is_default', true)->first();

        switch ($paymentMethod) {
            case 'card':
                $purchase = $this->createPurchase($item, $paymentMethod, $address);
                $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
                $paymentIntent = $stripe->paymentIntents->create([
                    'amount' => $item->price * 100,
                    'currency' => 'jpy',
                    'metadata' => [
                        'item_id' => $item_id,
                        'user_id' => Auth::id(),
                    ],
                ]);

                session([
                    'item_id' => $item->id,
                    'purchase_id' => $purchase->id,
                    'client_secret' => $paymentIntent->client_secret,
                ]);

                return redirect()->route('item.payment.page', [
                    'item_id' => $item_id,
                    'purchase_id' => $purchase->id,
                ]);

            case 'convenience-store':
                $this->createPurchase($item, $paymentMethod, $address);
                return redirect()->route('thanks.buy');
        }
    }

    private function createPurchase(Item $item, string $paymentMethod, Address $address)
    {
        $purchase = Purchase::create([
            'item_id' => $item->id,
            'user_id' => Auth::id(),
            'address_id' => $address->id,
            'payment_method' => $paymentMethod,
        ]);

        ChatRoom::create([
            'item_id' => $item->id,
            'seller_id' => $item->user_id,
            'buyer_id' => Auth::id(),
            'transaction_status' => 'active',
        ]);

        if (!$address) {
            throw new \Exception('住所が見つかりません');
        }

        $item->update(['is_sold' => true]);

        session([
            'item_id' => $item->id,
            'user_id' => Auth::id(),
            'address_id' => $address->id,
            'purchase_id' => $purchase->id,
            'payment_method' => $paymentMethod,
        ]);

        return $purchase;
    }

    public function thanksBuy(Request $request)
    {
        $item_id = session('item_id');
        $purchaseId = session('purchase_id');
        $payment_method = session('payment_method');

        return view('thanks_buy', compact('item_id', 'purchaseId', 'payment_method'));
    }

    public function changeAddressPage($item_id)
    {
        $item = Item::findOrFail($item_id);
        $user = Auth::user();
        return view('change_address', ['item' => $item, 'defaultAddress' => $user->addresses()->where('is_default', true)->first()]);
    }

    public function changeAddress(AddressRequest $request, $item_id)
    {
        $user = Auth::user();
        $user->addresses()->where('is_default', true)->update(['is_default' => false]);

        $address = new Address();
        $address->user_id = $user->id;
        $address->post_number = $request->input('post_number');
        $address->address = $request->input('address');
        $address->building = $request->input('building');
        $address->is_default = true;
        $address->save();
        $user->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
        return redirect()->route('purchase', ['item_id' => $item_id])->with([
            'message' => '配送先を変更しました',
            'payment_method' => session('payment_method')
        ]);
    }

}

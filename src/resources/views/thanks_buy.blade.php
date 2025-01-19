@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/thanks_buy.css') }}">
@endsection

@section('content')
    <div class="thanks_buy">
        <p class="thanks_buy__message">ご購入ありがとうございます</p>

        @if ($payment_method === 'card')
            <div class="card-payment__link">
                <a href="{{ route('item.payment.page', ['item_id' => $item_id, 'purchase_id' => $purchase_id]) }}">カード払いはこちら</a>
            </div>
        @endif

        <div class="mypage__link">
            <a href="{{ route('mypage') }}">マイページへ戻る</a>
        </div>
    </div>
@endsection

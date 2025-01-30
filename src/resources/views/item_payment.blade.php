@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/item_payment.css') }}">
    <link rel="stylesheet" href="{{ asset('css/nav.css') }}">
@endsection

@section('nav')
    @include('components.nav')
@endsection

@section('content')
    <h1 class="title">カード情報</h1>
    <div id="paymentMessage"></div>
    <div id="card-errors"></div>
    <form class="payment-form__content" id="payment-form">
        @csrf
        <input type="hidden" name="purchase_id" value="{{ $purchase_id }}">
        <p id="amount" class="payment-input">{{ number_format($item->price, 0) }}円</p>

        <div class="card-element-form" id="card-element"></div>
        <div class="payment-button">
            <button class="payment-button__submit" type="submit">支払う</button>
        </div>
    </form>
@endsection

@section('js')
    <script src="https://js.stripe.com/v3/"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        const stripePublicKey = '{{ env('STRIPE_KEY') }}';
        const clientSecret = '{{ $client_secret }}';
        const purchaseId = '{{ $purchase_id }}';
        const itemId = '{{ $item->id }}';
    </script>
    <script src="{{ asset('js/item_payment.js') }}"></script>
@endsection

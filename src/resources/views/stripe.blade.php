@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/stripe.css') }}">
@endsection

@section('content')
    <a href="{{ route('mypage') }}" class="back-arrow">&lt;</a>
    <h1 class="ttl">Checkout</h1>
    <div class="message" id="payment-result"></div>
    <form class="payment-form-content" id="payment-form">
        @csrf
        <input type="hidden" id="reservation-id" value="{{ $reservation->id }}">
        <input type="number" id="amount" class="payment-input" placeholder="金額を入力 (円)" required min="1" step="1">
        <div class="card-element-form" id="card-element"></div>
        <div class="payment-button">
            <button class="payment-button-submit" type="submit">支払う</button>
        </div>
    </form>
@endsection

@section('js')
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        window.stripePublicKey = "{{ env('STRIPE_KEY') }}";
    </script>
    <script src="{{ asset('js/stripe.js') }}"></script>
@endsection

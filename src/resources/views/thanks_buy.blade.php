@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/thanks_buy.css') }}">
    <link rel="stylesheet" href="{{ asset('css/nav.css') }}">
@endsection

@section('nav')
    @include('components.nav')
@endsection

@section('content')
    <div class="thanks_buy">
        <p class="thanks_buy__message">ご購入ありがとうございます</p>
        @if ($payment_method === 'card')
            <p class="card-payment__message">お支払いが完了しました</p>
        @endif
        <div class="mypage__link">
            <a href="{{ route('mypage') }}">マイページへ戻る</a>
        </div>
    </div>
@endsection

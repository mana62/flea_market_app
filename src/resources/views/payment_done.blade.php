@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/payment_done.css') }}">
@endsection

@section('content')
    <div class="payment_done">
        <p class="payment_done__message">お支払いありがとうございます</p>

        <div class="mypage__link">
            <a href="{{ route('mypage') }}">マイページへ戻る</a>
        </div>
    </div>
@endsection

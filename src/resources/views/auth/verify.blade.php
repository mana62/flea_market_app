@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/verify.css') }}">
@endsection

@section('content')
    <div class="verify-email">
        <h1>メールアドレスの確認が必要です</h1>
        <p>登録したメールアドレスに認証リンクを送信しました</p>
        <p>リンクをクリックして認証を完了してください</p>
        <label for="resend-button" class="label-button">メールが届いていない場合は以下のボタンをクリックしてください</label>
        <form class="verify-email__form" method="post" action="{{ route('verification.send') }}">
            @csrf
            <div class="message">
            @if (session('message'))
                <div class="message-session">
                    {{ session('message') }}
                </div>
            @endif
            </div>
            <div class="verify-email-button">
                <button class="verify-email__button-submit" type="submit">再送信</button>
            </div>
        </form>
    </div>
@endsection

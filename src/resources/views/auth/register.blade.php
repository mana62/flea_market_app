@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/register.css') }}">
@endsection

@section('content')
    <section class="register__content">
        <header class="register-form__heading">
            <h1>会員登録</h1>
        </header>
        <form class="form" action="{{ route('register') }}" method="post" novalidate>
            @csrf
            <div class="form__group">
                <div class="form__group-content">
                    <label for="name">ユーザー名</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" autocomplete="name">
                    @error('name')
                        <div class="form__error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form__group-content">
                    <label for="email">メールアドレス</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" autocomplete="email">
                    @error('email')
                        <div class="form__error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form__group-content">
                    <label for="password">パスワード</label>
                    <input type="password" name="password" id="password">
                    @error('password')
                        <div class="form__error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form__group-content">
                    <label for="password_confirmation">確認用パスワード</label>
                    <input type="password" name="password_confirmation" id="password_confirmation">
                    @error('password_confirmation')
                        <div class="form__error">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="form__button">
                <button class="form__button-submit" type="submit">登録する</button>
            </div>
            <div class="login__link">
                <a href="{{ route('login.view') }}">ログインはこちら</a>
            </div>
        </form>
    </section>
@endsection

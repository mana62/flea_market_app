@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/register.css') }}">
@endsection

@section('content')
    <section class="register-content">
        <header class="register-form__heading">
            <h1>会員登録</h1>
        </header>
        <form class="form" action="{{ route('register') }}" method="post" novalidate>
            @csrf
            <div class="form__group">
                <div class="form__group-content">
                    <label for="name">ユーザー名</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" autocomplete="name">
                    <div class="message">
                        @error('name')
                            <span class="form__error">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form__group-content">
                        <label for="email">メールアドレス</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}"
                            autocomplete="email">
                        <div class="message">
                            @error('email')
                                <span class="form__error">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form__group-content">
                            <div class="password-container">
                                <label for="password">パスワード</label>
                                <input type="password" name="password" id="password">
                                <button type="button" class="toggle-password" data-target="password">🙉</button>
                            </div>
                            <div class="message">
                                @error('password')
                                    <span class="form__error">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form__group-content">
                                <div class="password-container">
                                    <label for="password_confirmation">確認用パスワード</label>
                                    <input type="password" name="password_confirmation" id="password_confirmation">
                                    <button type="button" class="toggle-password"
                                        data-target="password_confirmation">🙉</button>
                                </div>
                                <div class="message">
                                    @error('password_confirmation')
                                        <span class="form__error">{{ $message }}</span>
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

@section('js')
    <script src="{{ asset('js/auth.js') }}"></script>
@endsection

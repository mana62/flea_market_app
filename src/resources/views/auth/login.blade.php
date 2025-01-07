@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection

@section('content')
    <section class="login__content">
        <header class="login-form__heading">
            <h1>ログイン</h1>
        </header>
        <form class="form" action="{{ route('login') }}" method="post">
            @csrf
            <div class="form__group">
                <div class="form__group-content">
                    <label for="name-or-mail">ユーザー名 / メールアドレス</label>
                    <input type="text" name="name-or-mail" id="name-or-mail" value="{{ old('name-or-mail') }}"
                        autocomplete="username">
                    @error('name-or-mail')
                        <div class="form__error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form__group-content">
                    <label for="password">パスワード</label>
                    <input type="password" name="password" id="password" autocomplete="current-password">
                    @error('password')
                        <div class="form__error">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="form__button">
                <button class="form__button-submit" type="submit">ログインする</button>
            </div>
            <div class="register__link">
                <a href="{{ route('register.view') }}">会員登録はこちら</a>
            </div>
        </form>
    </section>
@endsection

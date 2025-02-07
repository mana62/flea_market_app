@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection

@section('content')
    <section class="login-content">
        <header class="login-form__heading">
            <h1>ログイン</h1>
        </header>
        <form class="form" action="{{ route('login') }}" method="post" novalidate>
            @csrf
            <div class="form-group">
                <div class="form__group-content">
                    <label for="email">メールアドレス</label>
                    <input type="text" name="email" id="email" value="{{ old('email') }}" autocomplete="email">

                    <div class="message">
                        @error('email')
                            <span class="form__error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form__group-content">
                        <div class="password-container">
                            <label for="password">パスワード</label>
                            <input type="password" name="password" id="password" autocomplete="current-password">
                            <button type="button" class="toggle-password" data-target="password">🙉</button>
                        </div>

                        <div class="message">
                            @error('password')
                                <span class="form__error">{{ $message }}</span>
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

@section('js')
    <script src="{{ asset('js/login.js') }}"></script>
@endsection

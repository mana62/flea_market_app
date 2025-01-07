@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/change_address.css') }}">
    <link rel="stylesheet" href="{{ asset('css/nav.css') }}">
@endsection

@section('nav')
    @include('components.nav')
@endsection

@section('content')
    <div class="change-address__content">
        <header class="change-address-form__heading">
            <h1>住所の変更</h1>
        </header>

        <form class="form" action="{{ route('') }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="form__group">
                <div class="form__group-content">
                    <label for="post-number">郵便番号
                        <input type="text" name="post-number" id="post-number">
                    </label>
                    <div class="form__error">
                        @error('post-number')
                            {{ $message }}
                        @enderror
                    </div>
                </div>
                <div class="form__group-content">
                    <label for="address">住所
                        <input type="text" name="address" id="address">
                    </label>
                    <div class="form__error">
                        @error('address')
                            {{ $message }}
                        @enderror
                    </div>
                </div>
                <div class="form__group-content">
                    <label for="building">建物名
                        <input type="text" name="building" id="building">
                    </label>
                    <div class="form__error">
                        @error('building')
                            {{ $message }}
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form__button">
                <button class="form__button-update" type="submit">更新する</button>
            </div>
        </form>
    </div>
@endsection

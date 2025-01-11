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
        <form class="form" action="{{ route('change.address', ['item_id' => $item->id]) }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="form__group">
                <div class="form__group-content">
                    <label for="post-number">郵便番号
                        <input type="text" name="post_number" id="post-number"
                            value="{{ old('post_number', $defaultAddress->post_number ?? '') }}">
                    </label>
                    <div class="form__error">
                        @error('post_number')
                            {{ $message }}
                        @enderror
                    </div>
                </div>
                <div class="form__group-content">
                    <label for="address">住所
                        <input type="text" name="address" id="address"
                            value="{{ old('address', $defaultAddress->address ?? '') }}">
                    </label>
                    <div class="form__error">
                        @error('address')
                            {{ $message }}
                        @enderror
                    </div>
                </div>
                <div class="form__group-content">
                    <label for="building">建物名
                        <input type="text" name="building" id="building"
                            value="{{ old('building', $defaultAddress->building ?? '') }}">
                    </label>
                    <div class="form__error">
                        @error('building')
                            {{ $message }}
                        @enderror
                    </div>
                </div>
                <div class="form__button">
                    <button class="form__button-submit" type="submit">更新する</button>
                </div>
            </div>
        </form>
    </div>
@endsection

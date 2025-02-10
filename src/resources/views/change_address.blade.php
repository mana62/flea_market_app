@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/change_address.css') }}">
    <link rel="stylesheet" href="{{ asset('css/nav.css') }}">
@endsection

@section('nav')
    @include('components.nav')
@endsection

@section('content')
    <div class="change-address-content">
        <header class="change-address-form__heading">
            <h1>住所の変更</h1>
        </header>
        <form class="form" action="{{ route('change.address', ['item_id' => $item->id]) }}" method="post"
            enctype="multipart/form-data">
            @csrf
            <div class="form__group">
                <div class="form__group-content">
                    <label for="post_number">郵便番号</label>
                    <input type="text" name="post_number" id="post_number"
                        value="{{ old('post_number', $defaultAddress->post_number ?? '') }}">
                    @error('post_number')
                        <div class="form__error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form__group-content">
                    <label for="address">住所</label>
                    <input type="text" name="address" id="address"
                        value="{{ old('address', $defaultAddress->address ?? '') }}">
                    @error('address')
                        <div class="form__error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form__group-content">
                    <label for="building">建物名</label>
                    <input type="text" name="building" id="building"
                        value="{{ old('building', $defaultAddress->building ?? '') }}">
                    @error('building')
                        <div class="form__error">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="form__button">
                <button class="form__button-submit" type="submit">更新する</button>
            </div>
        </form>
    </div>
@endsection

@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/item_purchase.css') }}">
    <link rel="stylesheet" href="{{ asset('css/nav.css') }}">
@endsection

@section('nav')
    @include('components.nav')
@endsection

@section('content')
    <div class="message">
        @if (session('message'))
            <div class="message-session">{{ session('message') }}</div>
        @endif
    </div>
    <form class="purchase-form" id="purchase-form" method="post">
        @csrf
        <section class="item-purchase__container">
            <div class="item-purchase__left">
                <figure class="item-detail">
                    <div class="item-image">
                        <img src="{{ $item->image ? asset('storage/item_images/' . $item->image) : asset('image/dummy.jpg') }}"
                            alt="{{ $item->name }}">
                    </div>
                    <figcaption class="item-detail__info">
                        <h1 class="item-detail__name" data-item-id="{{ $item->id }}">{{ $item->name }}</h1>
                        <h2 class="items-detail__price" data-price="{{ $item->price }}">
                            ¥{{ number_format($item->price) }}
                        </h2>
                    </figcaption>
                </figure>
                <div class="payment-method__container">
                    <h2>支払い方法</h2>
                    <select id="payment-method" class="payment_method" name="payment_method">
                        <option value="" disabled selected>選択してください</option>
                        <option value="convenience-store">コンビニ払い</option>
                        <option value="card">カード払い</option>
                    </select>
                    @error('payment_method')
                        <span class="form__error">{{ $message }}</span>
                    @enderror
                </div>
                <div class="shipping-address">
                    <header class="shipping-address__row">
                        <h2>配送先</h2>
                        <a href="{{ route('change.address.page', ['item_id' => $item->id]) }}">変更する</a>
                    </header>
                    @if ($address)
                        <div class="shipping-address__detail">
                            <p>〒{{ $address->post_number }}</p>
                            <p>{{ $address->address }}</p>
                            @if ($address->building)
                                <p>{{ $address->building }}</p>
                            @endif
                        </div>
                    @else
                        <div class="shipping-address__detail">
                            <p>住所を登録してください</p>
                        </div>
                    @endif
                </div>
            </div>
            <div class="item-purchase__right">
                <section class="purchase-container">
                    <p class="item-detail__price">商品代金 <span>¥{{ number_format($item->price) }}</span></p>
                    <p class="item-detail__payment_method">支払い方法<span id="itemDetailPaymentMethod">未選択</span></p>
                </section>
                <section class="item-purchase__button">
                    <button type="submit" class="item-purchase__button-submit">購入する</button>
                </section>
            </div>
        </section>
    </form>
@endsection

@section('js')
    <script src="{{ asset('js/item_purchase.js') }}"></script>
@endsection

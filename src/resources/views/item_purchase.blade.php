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
            <div class="message-session">
                {{ session('message') }}
            </div>
        @endif
    </div>

    <section class="item-purchase__container">
        <!-- 左側カラム -->
        <div class="item-purchase__left">
            <figure class="item-detail">
                <div class="item-image">
                    <img src="{{ $item->image }}" alt="{{ $item->name }}" class="item-detail__image">
                </div>
                <figcaption class="item-detail__info">
                    <h1 class="item-detail__name">{{ $item->name }}</h1>
                    <p class="items-detail__price" data-price="{{ $item->price }}">¥{{ number_format($item->price) }}</p>
                </figcaption>
            </figure>

            <div class="payment-method__container">
                <h2>支払い方法</h2>
                <select class="payment_method" name="payment_method">
                    <option value="" disabled selected>選択してください</option>
                    <option value="コンビニ払い">コンビニ払い</option>
                    <option value="カード払い">カード払い</option>
                </select>
            </div>

            <div class="shipping-address">
                <header class="shipping-address__row">
                    <h2>配送先</h2>
                    <a href="{{ route('change.address.page', ['item_id' => $item->id]) }}">変更する</a>
                </header>
                @if ($address && $address->post_number && $address->address)
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

        <!-- 右側カラム -->
        <div class="item-purchase__right">
            <form action="{{ route('purchase', ['item_id' => $item->id]) }}" method="post">
                @csrf
                <section class="purchase-container">
                    <p class="item-detail__price">商品代金 <span>¥{{ number_format($item->price) }}</span></p>
                    <p class="item-detail__payment_method">配送方法 <span>{{ $item->payment_method }}</span></p>
                </section>
                <section class="item-purchase__button">
                    <button type="submit" class="item-purchase__button-submit">購入する</button>
                </section>
            </form>
        </div>
    </section>
@endsection

@section('js')
    <script src="{{ asset('js/payment.js') }}"></script>
@endsection

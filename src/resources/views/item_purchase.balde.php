@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/item_purchase.css') }}">
<link rel="stylesheet" href="{{ asset('css/nav.css') }}">
@endsection

@section('nav')
@include('components.nav')
@endsection

@section('content')

<section class="item-purchase__container">
<form action="{{ route('item.purchase', ['item_id' => $item->id]) }}" method="POST">
    @csrf
    <figure class="item-purchase__left">
      <div class="item-image">
        <img src="{{ $item->image }}" alt="{{ $item->name }}" class="item-detail__image">
      </div>
      <figcaption class="item-detail">
        <h1 class="item-detail__name">{{ $item->name }}</h1>
        <p class="item-detail__price" data-price="{{ $item->price }}">¥{{ number_format($item->price) }}</p>
      </figcaption>

    <div class="payment-method__container">
      <h2>支払い方法</h2>
      <select name="payment_method">
        <option value="コンビニ払い">コンビニ払い</option>
        <option value="カード払い">カード払い</option>
      </select>
    </div>

    <div class="shipping-address">
      <header class="shipping-address__row">
        <h2>配送先</h2>
        <a href="{{ route('change.address', ['item_id' => $item->id]) }}">変更する</a>
      </header>
      @foreach ($profiles as $profile)
      <p>{{ $profile->post_number }}</p>
      <p>{{ $profile->address }}</p>
      @endforeach
    </div>
   </figure>

   <figure class="item-purchase__right">
      <section class="purchase-container">
        <p class="item-detail__price">¥{{ number_format($item->price) }}</p>
        <p class="payment-method">
          <option value="コンビニ払い">コンビニ払い</option>
          <option value="カード払い">カード払い</option>
        </p>
      </section>
   </figure>

  <section class="item-purchase__button">
    <button type="submit" class="item-purchase__button-submit">購入する</button>
  </section>
 </form>
</section>

@endsection

@section('js')
<script src="{{ asset('js/payment.js') }}"></script>
@endsection

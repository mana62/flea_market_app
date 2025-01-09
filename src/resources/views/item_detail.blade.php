@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/item_detail.css') }}">
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
    <div class="item-detail__container">
        <section class="item-detail__left">
            <img src="{{ $item->image }}" alt="{{ $item->name }}" class="item-detail__image">
        </section>

        <section class="item-detail__right">
            <header>
                <h1 class="item-detail__name">{{ $item->name }}</h1>
                <p class="item-detail__brand">{{ $item->brand }}</p>
                <p class="item-detail__price">¥{{ number_format($item->price) }} (税込)</p>
            </header>

            <section class="item-detail__actions">
                @auth
                    <div class="item-detail__likes-count">
                        <button data-item-id="{{ $item->id }}"
                            class="like-btn {{ Auth::user()->likedItems->contains($item->id) ? 'liked' : '' }}">
                            <img src="{{ asset('image/星アイコン8.png') }}" alt="星アイコン">
                        </button>
                        <p id="like-count" data-item-id="{{ $item->id }}">{{ $item->likesCount() }}</p>
                    </div>
                @else
                    <button class="like-btn__disabled" disabled>
                        <img src="{{ asset('image/星アイコン8.png') }}" alt="星アイコン" class="disabled-image">
                    </button>
                    <p id="like-count" data-item-id="{{ $item->id }}">{{ $item->likesCount() }}</p>
                @endauth

                <div class="item-detail__comments-count">
                    <img src="{{ asset('image/ふきだしのアイコン.png') }}" alt="コメントアイコン">
                    <p>{{ $comments->count() }}</p>
                </div>
            </section>

            <form action="{{ route('purchase', ['item_id' => $item->id]) }}" method="get" class="item-detail__purchase-form">
                @csrf
                @if ($item->is_sold)
                    <button class="item-detail__purchase-submit" type="button" disabled>Sold</button>
                @else
                    <button class="item-detail__purchase-submit" type="submit">購入手続きへ</button>
                @endif
            </form>

            <article class="item-detail__info">
                <h2 class="item-detail__section-title">商品説明</h2>
                <p class="item-detail__description">{{ $item->description }}</p>
                <h2 class="item-detail__section-title">商品情報</h2>
                <dl>
                    <dt>カテゴリー</dt>
                    <dd>{{ $item->category }}</dd>
                    <dt>商品の状態</dt>
                    <dd>{{ $item->condition }}</dd>
                </dl>
            </article>

            <article class="item-detail__comments"> <!-- コメント -->
                <h2 class="item-detail__section-title">コメント ({{ $comments->count() }})</h2>
                @foreach ($comments as $comment)
                    <section class="item-detail__comment">
                        <div class="comment-header">
                            @if ($comment->user && $comment->user->profile && $comment->user->profile->image)
                                <img src="{{ $comment->user->profile->image }}" alt="{{ $comment->user->name }}"
                                    class="comment-user-image">
                            @else
                                <div class="comment-user-image default-image"></div>
                            @endif
                            <p class="comment-user">{{ $comment->user->name }}</p>
                        </div>
                        <p class="comment-content">{{ $comment->content }}</p>
                    </section>
                @endforeach
            </article>

            @auth
                <section class="comment-section">
                    <h3>商品へのコメント</h3>
                    <form action="{{ route('item.comment', $item->id) }}" method="post" class="item-detail__comment-form">
                        @csrf
                        @error('comment')
                            <div class="form__error">{{ $message }}</div>
                        @enderror
                        <textarea class="item-detail__text" name="comment" rows="5" cols="60" maxlength="255" required></textarea>
                        <button type="submit" class="item-detail__comment-submit">コメントを送信する</button>
                    </form>
                </section>
            @else
                <p>ログインしてください</p>
            @endauth
        </section>
        </article>
    @endsection

    @section('js')
        <script src="{{ asset('js/item_like.js') }}"></script>
    @endsection

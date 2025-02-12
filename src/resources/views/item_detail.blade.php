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
            <span class="message-session">
                {{ session('message') }}
            </span>
        @endif
    </div>

    <div class="item-detail__container">
        <section class="item-detail__left">
            <img class="item-detail__image"
                src="{{ $item->image ? asset('storage/item_images/' . $item->image) : asset('image/dummy.jpg') }}"
                alt="アイテム画像">
        </section>
        <section class="item-detail__right">
            <header>
                <h1 class="item-detail__name">{{ $item->name }}</h1>
                <p class="item-detail__brand">ブランド名</p>
                <p class="item-detail__price">¥{{ number_format($item->price) }} (税込)</p>
            </header>
            <section class="item-detail__actions">
                <div class="item-detail__likes-count">
                    <button
                        class="like-btn {{ Auth::check() && Auth::user()->likedItems->contains($item->id) ? 'liked' : '' }}"
                        data-item-id="{{ $item->id }}" @if (!Auth::check()) disabled @endif>
                        <img src="{{ asset('image/星アイコン8.png') }}" alt="星アイコン"
                            class="@if (!Auth::check()) disabled-image @endif">
                    </button>
                    <p class="like-count" data-item-id="{{ $item->id }}">{{ $item->likesCount() }}</p>
                </div>
                <div class="item-detail__comments-count">
                    <img src="{{ asset('image/ふきだしのアイコン.png') }}" alt="コメントアイコン">
                    <p>{{ $comments->count() }}</p>
                </div>
            </section>
            <form action="{{ route('purchase', ['item_id' => $item->id]) }}" method="get"
                class="item-detail__purchase-form">
                @if ($item->is_sold)
                    <button class="item-detail__purchase-submit" type="button" disabled>SOLD</button>
                @else
                    <button class="item-detail__purchase-submit" type="submit">購入手続きへ</button>
                @endif
            </form>
            <article class="item-detail__info">
                <h2 class="item-detail__section-title">商品説明</h2>
                <p class="item-detail__description">{{ $item->description }}</p>
                <h2 class="item-detail__section-title">商品情報</h2>
                <ul class="category-list">
                    <dt>カテゴリー</dt>
                    <div class="category-container">
                        @if (is_array($item->category))
                            @foreach ($item->category as $category)
                                <label class="category-item">
                                    <li class="category-label">{{ $category }}</li>
                                </label>
                            @endforeach
                        @else
                            <label class="category-item">
                                <li class="category-label">{{ $item->category }}</li>
                            </label>
                        @endif
                    </div>
                </ul>
                <div class="condition-list">
                    <dt>商品の状態</dt>
                    <dd>{{ $item->condition }}</dd>
                    </dl>
                </div>
            </article>
            <article class="item-detail__comments">
                <h2 class="item-detail__section-title">コメント ({{ $comments->count() }})</h2>
                @foreach ($comments as $comment)
                    <section class="item-detail__comment">
                        <div class="comment-header">
                            @if ($comment->user && $comment->user->profile && $comment->user->profile->image)
                                <div class="user-image-preview" id="userImagePreview">
                                    <img class="comment-user-image"
                                        src="{{ asset('storage/profile_images/' . $comment->user->profile->image) }}"
                                        alt="{{ $comment->user->profile->name }}">
                                </div>
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
                        <textarea class="item-detail__text" name="content" rows="5" cols="60" maxlength="255"></textarea>
                        @error('content')
                        <span class="form__error">{{ $message }}</span>
                    @enderror
                        <button type="submit" class="item-detail__comment-submit">コメントを送信する</button>
                    </form>
                </section>
            @endauth
        </section>
        </article>
    @endsection

    @section('js')
        <script src="{{ asset('js/item_detail.js') }}"></script>
    @endsection

@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/mypage.css') }}">
    <link rel="stylesheet" href="{{ asset('css/nav.css') }}">
@endsection

@section('nav')
    @include('components.nav')
@endsection

@section('content')
    <div class="mypage__content">

        {{-- プロフィール情報の表示 --}}
        <div class="user-detail">
            <div class="user-image-preview" id="userImagePreview">
                <img src="{{ $profile->image ? asset('storage/profile_images/' . $profile->image) : asset('image/default-profile.png') }}" alt="{{ $profile->image }}">
            </div>
            <h1>{{ $profile->name }}</h1>
        </div>
        <div class="profile-link">
            <a href="{{ route('mypage.profile.edit') }}">プロフィールを編集</a>
        </div>
    </div>

    <div class="tabs">
        <a href="{{ url('/mypage?page=buy') }}" class="{{ request('page') === 'buy' ? 'active' : '' }}">購入した商品</a>
        <a href="{{ url('/mypage?page=sell') }}" class="{{ request('page') === 'sell' ? 'active' : '' }}">出品した商品</a>
    </div>

    {{-- 商品リストの表示 --}}
    @if ($items->isEmpty())
        <p class="empty">{{ $tab === 'buy' ? '購入した商品はありません' : '出品した商品はありません' }}</p>
    @else
        <div class="item-list">
            @foreach ($items as $item)
                <div class="item">
                    <div class="item-image">
                        <a href="{{ route('item.detail', ['item_id' => $item->id]) }}">
                            <img class="item-detail__image"
                                src="{{ $item->image ? asset('storage/item_images/' . $item->image) : asset('image/dummy.jpg') }}"
                                alt="アイテム画像">
                        </a>
                    </div>
                    <div class="item-name">{{ $item->name }}</div>
                    @if ($tab === 'sell' && $item->is_sold)
                        <span class="item-status" class="sold-badge">Sold</span>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
@endsection

@section('js')
    <script src="{{ asset('js/mypage.js') }}"></script>
@endsection

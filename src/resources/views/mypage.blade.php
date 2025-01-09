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
        <img src="{{ $profile->image }}" alt="{{ $profile->name }}">
        <h1>{{ $profile->name }}</h1>
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
        <p>{{ $tab === 'buy' ? '購入した商品はありません' : '出品した商品はありません' }}</p>
    @else
        <div class="item-list"> <!-- 修正ポイント -->
            @foreach ($items as $item)
                <div class="item">
                    <div class="item-image">
                    <a href="{{ route('item.detail', ['item_id' => $item->id]) }}">
                        <img src="{{ $item->user->profile->image 
                            ? asset('storage/app/public/profile_images/' . $item->user->profile->image) 
                            : asset('storage/app/public/profile_images/default.png') }}" 
                     alt="{{ $item->name }}">
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

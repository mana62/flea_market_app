@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/mypage.css') }}">
    <link rel="stylesheet" href="{{ asset('css/nav.css') }}">
@endsection

@section('nav')
    @include('components.nav')
@endsection

@section('content')
    <div class="mypage-content">
        <div class="user-detail">
            <div class="user-image-preview">
                @if ($profile->image)
                    <img src="{{ Storage::url('profile_images/' . $profile->image) }}" alt="プロフィール画像">
                @endif
            </div>
            <h1>{{ $profile->name ?? '' }}</h1>
        </div>
        <div class="profile-link">
            <a href="{{ route('mypage.profile.edit') }}">プロフィールを編集</a>
        </div>
    </div>
    <ul class="tabs">
        <li><a href="{{ url('/mypage?page=buy') }}" class="{{ $tab === 'buy' ? 'active' : '' }}">購入した商品</a></li>
        <li><a href="{{ url('/mypage?page=sell') }}" class="{{ $tab === 'sell' ? 'active' : '' }}">出品した商品</a></li>
    </ul>
    @if ($items->isEmpty())
        <p class="empty">
            {{ $tab === 'buy' ? '購入した商品はありません' : '出品した商品はありません' }}
        </p>
    @endif
    <div class="item-list">
        @foreach ($items as $item)
            <div class="item">
                <div class="item-image">
                    <a href="{{ route('item.detail', ['item_id' => $item->id]) }}">
                        <img src="{{ $item->image ? asset('storage/item_images/' . $item->image) : asset('image/dummy.jpg') }}"
                            alt="{{ $item->name }}">
                    </a>
                </div>
                <div class="item-name">{{ $item->name }}</div>
                @if ($tab === 'sell' && $item->is_sold)
                    <span class="item-status sold-badge">Sold</span>
                @endif
            </div>
        @endforeach
    </div>
@endsection

@section('js')
    <script src="{{ asset('js/mypage.js') }}"></script>
@endsection

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

            <div>
                <h1>{{ $profile->name ?? '' }}</h1>
                @if ($averageScore > 0)
                    <div class="review-stars">
                        @for ($i = 1; $i <= 5; $i++)
                            <span class="{{ $i <= $averageScore ? 'selected' : '' }}">★</span>
                        @endfor
                        </span>
                    </div>
                @endif
            </div>
        </div>
        <div class="profile-link">
            <a href="{{ route('mypage.profile.edit') }}">プロフィールを編集</a>
        </div>
    </div>

    <div class="message">
        @if (session('message'))
            <span class="message-session">
                {{ session('message') }}
            </span>
        @endif
    </div>

    <ul class="tabs">
        <li><a href="{{ url('/mypage?page=buy') }}" class="{{ $tab === 'buy' ? 'active' : '' }}">購入した商品</a></li>
        <li><a href="{{ url('/mypage?page=sell') }}" class="{{ $tab === 'sell' ? 'active' : '' }}">出品した商品</a></li>
        <li>
            <a href="{{ url('/mypage?page=progress') }}" class="{{ $tab === 'progress' ? 'active' : '' }}">
                取引中の商品
                {{-- 取引があるアイテムを取得 --}}
                @if ($activeTransactionCount > 0)
                <span class="notification-count">{{ $activeTransactionCount }}</span>
            @endif
            </a>
        </li>
    </ul>
    @if ($items->isEmpty())
        <p class="empty">
            {{ $tab === 'buy' ? '購入した商品はありません' : ($tab === 'sell' ? '出品した商品はありません' : '取引中の商品はありません') }}
        </p>
    @endif
    <div class="item-list">
        @foreach ($items as $item)
            <div class="item">
                <div class="item-image">
                    @if ($tab === 'sell' && $item->is_sold)
                    <span class="sold-label">SOLD</span>
                    @endif

                    @if ($tab === 'progress' && $item->chatRoom)
                        <a href="{{ route('chat', ['item_id' => $item->id]) }}">
                        @else
                            <a href="{{ route('item.detail', ['item_id' => $item->id]) }}">
                    @endif
                    <img src="{{ $item->image ? asset('storage/item_images/' . $item->image) : asset('image/dummy.jpg') }}"
                        alt="{{ $item->name }}">
                    </a>
                    @if ($tab === 'progress' && isset($unreadMessageCounts[$item->chatRoom->id]) && $unreadMessageCounts[$item->chatRoom->id] > 0)
                    <span class="notification-count notification-count__small">
                        {{ $unreadMessageCounts[$item->chatRoom->id] }}
                    </span>
                @endif
                </div>
                <div class="item-name">{{ $item->name }}</div>
            </div>
        @endforeach
    </div>
@endsection

@section('js')
    <script src="{{ asset('js/mypage.js') }}"></script>
@endsection

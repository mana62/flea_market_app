@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/item.css') }}">
    <link rel="stylesheet" href="{{ asset('css/nav.css') }}">
@endsection

@section('nav')
    @include('components.nav')
@endsection

@section('content')
    <div class="tabs">
        <a href="{{ url('/?tab=recommend&search=' . $input) }}" class="{{ $tab === 'recommend' ? 'active' : '' }}">
            おすすめ
        </a>
        <a href="{{ url('/?tab=mylist&search=' . $input) }}" class="{{ $tab === 'mylist' ? 'active' : '' }}">
            マイリスト
        </a>
    </div>

    @if ($tab === 'mylist' && !Auth::check())
        <p class="empty">いいねした商品はありません</p>
    @elseif ($items->isEmpty())
        <p class="empty">
            {{ $tab === 'mylist' ? 'いいねした商品はありません' : 'おすすめ商品はありません' }}
        </p>
    @else
        <div class="item-list">
            @foreach ($items as $item)
                <div class="item">
                    <div class="item-image">
                        <a href="{{ route('item.detail', ['item_id' => $item->id]) }}">
                            @if ($item->image)
                            <img src="{{ asset('storage/item_images/' . $item->image) }}" alt="商品画像">
                    
                        @endif
                        
                        </a>
                    </div>
                    <div class="item-name">{{ $item->name }}</div>
                    @if ($item->is_sold)
                        <span class="item-status">Sold</span>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
@endsection

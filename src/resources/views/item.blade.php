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
        <a href="{{ url('/?tab=recommend&search=' . $input) }}" class="{{ $tab === 'recommend' ? 'active' : '' }}">おすすめ</a>
        <a href="{{ url('/?tab=mylist&search=' . $input) }}" class="{{ $tab === 'mylist' ? 'active' : '' }}">マイリスト</a>
    </div>
    @if ($items->isEmpty())
        <p class="empty">{{ $tab === 'mylist' ? 'いいねした商品はありません' : 'おすすめ商品はありません' }}</p>
    @else
        <div class="item-list">
            @foreach ($items as $item)
                <div class="item">
                    <div class="item-image">
                        <a href="{{ route('item.detail', ['item_id' => $item->id]) }}">
                            <img src="{{ $item->image ? asset('storage/item_images/' . $item->image) : asset('image/dummy.jpg') }}" alt="{{ $item->name }}">
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

@section('js')
<script src="{{ asset('js/item.js') }}"></script>
@endsection
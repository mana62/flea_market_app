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
        <p>{{ $tab === 'mylist' ? 'いいねした商品はありません' : 'おすすめ商品はありません' }}</p>
    @else
        <div class="item-list">
            @foreach ($items as $item)
                <div class="item">
                    <div class="item-image">
                        <a href="{{ url('/item/' . $item->id) }}">
                            <img src="{{ $item->image }}" alt="{{ $item->name }}">
                        </a>
                    </div>
                    <div class="item-name">{{ $item->name }}</div>
                    @if ($item->is_sold)
                        <div class="item-status">Sold</div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
@endsection
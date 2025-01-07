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
        <img src="{{ $profile->image }}" alt="{{ $profile->name }}" class="user-image">
        <form action="{{ route('mypage/profile')}} method='get">
            <button class="">プロフィールを編集</button></form>
    </div>

    <div class="tabs">
        <a href="{{ url('/mypage?page=buy' . $input) }}" class="{{ $tab === 'recommend' ? 'active' : '' }}">購入した商品</a>
        <a href="{{ url('/mypage?page=sell' . $input) }}" class="{{ $tab === 'mylist' ? 'active' : '' }}">出品した商品</a>
    </div>
    @if ($items->isEmpty())
        <p>{{ $tab === 'myitem' ? '購入したした商品はありません' : '出品した商品はありません' }}</p>
    @else
        <div class="my-item">
            @foreach ($items as $item)
                <div class="item">
                    <div class="my-item__image">
                        <a href="{{ url('/item/' . $item->id) }}">
                            <img src="{{ $item->image }}" alt="{{ $item->name }}">
                        </a>
                    </div>
                    <div class="my-item__name">{{ $item->name }}</div>
                    @if ($item->is_sold)
                        <div class="item-status">Sold</div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
@endsection

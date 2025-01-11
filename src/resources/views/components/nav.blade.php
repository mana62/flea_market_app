@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/nav.css') }}">
@endsection

@section('nav')
    <div class="nav__content">
        <div class="search">
            <form action="{{ route('search') }}" method="get">
                <input type="hidden" name="tab" value="{{ $tab ?? 'recommend' }}">
                <input type="text" name="search" placeholder="なにをお探しですか？">
            </form>
        </div>
        <ul class="nav">
            <li class="logout">
                <a href="{{ route('logout') }}"
                    onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                    {{ __('ログアウト') }}
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="post" style="display: none;">
                    @csrf
                </form>
            </li>
            <li class="mypage">
                <a href={{ route('mypage') }}>マイページ</a>
            </li>
            <li class="sell">
                <div class="sell-link">
                <a class="" href={{ route('item.sell.page') }}>出品</a>
            </div>
            </li>
        </ul>
    </div>
@endsection

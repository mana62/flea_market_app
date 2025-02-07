@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/item_sell.css') }}">
    <link rel="stylesheet" href="{{ asset('css/nav.css') }}">
@endsection

@section('nav')
    @include('components.nav')
@endsection

@section('content')
    <div class="sell__content">
        <header class="sell-form__heading">
            <h1>商品の出品</h1>
            <div class="message">
                @if (session('message'))
                    <span class="message-session">{{ session('message') }}</span>
                @endif
            </div>
        </header>

        <form class="form" action="{{ route('item.sell') }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="form__group">
                <!-- 商品画像 -->
                <div class="form__group-content">
                    <h2>商品画像</h2>
                    <div class="image-upload">
                        <div class="item-image-preview" id="itemImagePreview">
                            @if (session('itemImage'))
                                <img src="{{ session('itemImage') }}" alt="選択した画像">
                            @endif
                        </div>
                        <label for="img" class="image-upload__label">
                            画像を選択する
                            <input type="file" name="img" id="img" accept="image/*">
                        </label>
                        <input type="hidden" name="img_base64" id="imgBase64"
                            value="{{ session('itemImage') ?? old('img_base64') }}">
                    </div>

                    <div class="form__error">
                        @error('img_base64')
                            <span class="form__error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- 商品の詳細 -->
                    <div class="form__group-detail">
                        <h2 class="section-title">商品の詳細</h2>

                        <!-- カテゴリー -->
                        <h3>カテゴリー</h3>
                        <ul class="form__group-content category-group">
                            @foreach ($categories as $category)
                                <li>
                                    <label class="category-item">
                                        <input type="checkbox" name="category[]" value="{{ $category }}"
                                            {{ in_array($category, old('category', $item->category ?? [])) ? 'checked' : '' }}>
                                        <span>{{ $category }}</span>
                                    </label>
                                </li>
                            @endforeach
                        </ul>

                        <span class="form__error">
                            @error('category')
                                {{ $message }}
                            @enderror
                        </span>

                        <!-- 商品の状態 -->
                        <h3>商品の状態</h3>
                        <div class="form__group-content">
                            <select name="condition" id="condition">
                                <option value="" disabled
                                    {{ old('condition', $item->condition ?? '') === '' ? 'selected' : '' }}>
                                    選択してください
                                </option>
                                @foreach ($conditions as $condition)
                                    <option value="{{ $condition }}"
                                        {{ old('condition', $item->condition ?? '') === $condition ? 'selected' : '' }}>
                                        {{ $condition }}
                                    </option>
                                @endforeach
                            </select>

                            <span class="form__error">
                                @error('condition')
                                    {{ $message }}
                                @enderror
                            </span>
                        </div>

                        <!-- 商品名と説明 -->
                        <h2 class="section-title">商品名と説明</h2>

                        <!-- 商品名 -->
                        <div class="form__group-content">
                            <label for="name">商品名
                                <input type="text" name="name" id="name"
                                    value="{{ old('name', $item->name ?? '') }}">
                            </label>

                            <span class="form__error">
                                @error('name')
                                    {{ $message }}
                                @enderror
                            </span>
                        </div>

                        <!-- 商品の説明 -->
                        <div class="form__group-content">
                            <label for="description">商品の説明
                                <textarea name="description" id="description">{{ old('description', $item->description ?? '') }}</textarea>
                            </label>

                            <span class="form__error">
                                @error('description')
                                    {{ $message }}
                                @enderror
                            </span>
                        </div>

                        <!-- 販売価格 -->
                        <div class="form__group-content">
                            <label for="price">販売価格
                                <input type="text" name="price" id="price" placeholder="¥"
                                    value="{{ old('price', isset($item->price) ? '¥' . $item->price : '') }}"
                                    oninput="formatPrice(this)">
                            </label>

                            <span class="form__error">
                                @error('price')
                                    {{ $message }}
                                @enderror
                            </span>
                        </div>

                        <!-- 出品ボタン -->
                        <div class="form__button" id="formButton">
                            <button class="form__button-submit" type="submit">出品する</button>
                        </div>
                    </div>
                </div>
        </form>
    </div>
@endsection

@section('js')
    <script src="{{ asset('js/item_sell.js') }}"></script>
@endsection

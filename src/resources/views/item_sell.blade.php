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
                    <div class="message-session">{{ session('message') }}</div>
                @endif
            </div>
        </header>

        <form class="form" action="{{ route('item.sell') }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="form__group">
                <!-- 商品画像 -->
                <div class="form__group-content">
                    <!-- 商品画像 -->
                    <div class="form__group-content">
                        <h3>商品画像</h3>
                        <div class="image-upload">
                            <p id="file-name" class="file-name" style="display: none;"></p> <input type="file"
                                name="img" id="img" accept="image/*" onchange="previewImage(event)"
                                style="display: none;"> <label for="img" class="image-upload__label"
                                id="img-label">画像を選択する</label>
                            @if (isset($imagePath))
                                <img id="img-preview" style="display: none;" src="{{ asset('storage/' . $imagePath) }}"
                                    alt="アイテム画像
                                ">
                            @else
                                <img id="img-preview" style="display: none;" alt="プレビュー画像">
                            @endif
                        </div>
                        <div class="form__error"> @error('img')
                                {{ $message }}
                            @enderror </div>
                    </div>

                    <!-- 商品の詳細 -->
                    <div class="form__group-detail">
                        <h2 class="section-title">商品の詳細</h2>

                        <!-- カテゴリー -->
                        <h3>カテゴリー</h3>
                        <div class="form__group-content category-group">
                            @foreach ($categories as $category)
                                <div>
                                    <label class="category-item">
                                        <input type="checkbox" name="category[]" value="{{ $category }}"
                                            {{ in_array($category, old('category', $item->category ?? [])) ? 'checked' : '' }}>
                                        <span>{{ $category }}</span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        <div class="form__error">
                            @error('category')
                                {{ $message }}
                            @enderror
                        </div>

                        <!-- 商品の状態 -->
                        <div class="form__group-content">
                            <h3>商品の状態</h3>
                            <select name="condition" id="condition">
                                <option value="" disabled
                                    {{ old('condition', $item->condition ?? '') === '' ? 'selected' : '' }}>選択してください
                                </option>
                                @foreach ($conditions as $condition)
                                    <option value="{{ $condition }}"
                                        {{ old('condition', $item->condition ?? '') === $condition ? 'selected' : '' }}>
                                        {{ $condition }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form__error">
                                @error('condition')
                                    {{ $message }}
                                @enderror
                            </div>
                        </div>

                        <!-- 商品名と説明 -->
                        <h2 class="section-title">商品名と説明</h2>

                        <!-- 商品名 -->
                        <div class="form__group-content">
                            <label for="name">商品名
                                <input type="text" name="name" id="name"
                                    value="{{ old('name', $item->name ?? '') }}">
                            </label>
                            <div class="form__error">
                                @error('name')
                                    {{ $message }}
                                @enderror
                            </div>
                        </div>

                        <!-- ブランド名 -->
                        <div class="form__group-content">
                            <label for="brandName">ブランド名
                                <input type="text" name="brandName" id="brandName"
                                    value="{{ old('brandName', $item->brand ?? '') }}">
                            </label>
                            <div class="form__error">
                                @error('brandName')
                                    {{ $message }}
                                @enderror
                            </div>
                        </div>

                        <!-- 商品の説明 -->
                        <div class="form__group-content">
                            <label for="description">商品の説明
                                <textarea name="description" id="description">{{ old('description', $item->description ?? '') }}</textarea>
                            </label>
                            <div class="form__error">
                                @error('description')
                                    {{ $message }}
                                @enderror
                            </div>
                        </div>

                        <!-- 販売価格 -->
                        <div class="form__group-content">
                            <label for="price">販売価格
                                <input type="text" name="price" id="price"
                                    value="{{ old('price', $item->price ?? '') }}">
                            </label>
                            <div class="form__error">
                                @error('price')
                                    {{ $message }}
                                @enderror
                            </div>
                        </div>

                        <!-- 出品ボタン -->
                        <div class="form__button">
                            <button class="form__button-submit" type="submit">出品する</button>
                        </div>
                    </div>
        </form>
    </div>
@endsection

@section('js')
    <script src="{{ asset('js/item_sell.js') }}"></script>
@endsection

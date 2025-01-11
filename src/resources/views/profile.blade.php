@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
    <link rel="stylesheet" href="{{ asset('css/nav.css') }}">
@endsection

@section('nav')
    @include('components.nav')
@endsection

@section('content')
    <div class="profile__content">
        <header class="profile-form__heading">
            <h1>プロフィール設定</h1>

            <div class="message">
                @if (session('message'))
                    <div class="message-session">
                        {{ session('message') }}
                    </div>
                @endif
                </div>
        </header>

        <form class="form" action="{{ route('mypage.profile.update') }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="form__group">
                <div class="form__group-content">
                    <div class="image-upload">
                        <!-- 既存の画像を表示 -->
                        <div class="user-image-preview" id="userImagePreview">
                            @if ($profile->image)
                                <img src="{{ asset('storage/profile_images/' . $profile->image) }}" alt="プロフィール画像">
                            @endif
                        </div>

                        <!-- 画像選択 -->
                        <label for="img" class="image-upload__label">
                            画像を選択する
                            <input type="file" name="img" id="img" accept="image/*"
                                onchange="previewImage(event)">
                        </label>
                    </div>
                    <div class="form__error">
                        @error('image')
                            {{ $message }}
                        @enderror
                    </div>
                </div>
                <div class="form__group-content">
                    <label for="name">ユーザー名
                        <input type="text" name="name" id="name" value="{{ old('name', $profile->name) }}"
                            autocomplete="name">
                    </label>
                    <div class="form__error">
                        @error('name')
                            {{ $message }}
                        @enderror
                    </div>
                </div>
                <div class="form__group-content">
                    <label for="post-number">郵便番号
                        <input type="text" name="post_number" id="post-number"
                            value="{{ old('post_number', $defaultAddress->post_number ?? '') }}">
                    </label>
                    <div class="form__error">
                        @error('post_number')
                            {{ $message }}
                        @enderror
                    </div>
                </div>
                <div class="form__group-content">
                    <label for="address">住所
                        <input type="text" name="address" id="address"
                            value="{{ old('address', $defaultAddress->address ?? '') }}">
                    </label>
                    <div class="form__error">
                        @error('address')
                            {{ $message }}
                        @enderror
                    </div>
                </div>
                <div class="form__group-content">
                    <label for="building">建物名
                        <input type="text" name="building" id="building"
                            value="{{ old('building', $defaultAddress->building ?? '') }}">
                    </label>
                    <div class="form__error">
                        @error('building')
                            {{ $message }}
                        @enderror
                    </div>
                </div>
            </div>
            <div class="form__button">
                <button class="form__button-submit" type="submit">更新する</button>
            </div>
        </form>
    </div>
@endsection

@section('js')
    <script src="{{ asset('js/profile.js') }}"></script>
@endsection

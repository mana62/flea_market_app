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

            <!-- セッションメッセージ -->
            @if (session('message'))
                <div class="message-session">{{ session('message') }}</div>
            @endif
        </header>

        <form class="form" action="{{ route('mypage.profile.update') }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="form__group">
                <!-- プロフィール画像 -->
                <div class="form__group-content">
                    <div class="image-upload">
                        <div class="user-image-preview" id="userImagePreview">
                            @if (old('img_base64')) <!-- old() でBase64データを確認 -->
                                <img src="{{ old('img_base64') }}" alt="プロフィール画像">
                            @elseif ($profile->image) <!-- データベースの画像を表示 -->
                                <img src="{{ asset('storage/profile_images/' . $profile->image) }}" alt="プロフィール画像">
                            @endif
                        </div>
                        <label for="img" class="image-upload__label">
                            画像を選択する
                            <input type="file" name="img" id="img" accept="image/*" onchange="previewImage(event)">
                        </label>
                        
                        <!-- Base64形式の画像データを保持 -->
                        <input type="hidden" name="img_base64" id="imgBase64" value="{{ old('img_base64') }}">
                        @error('img_base64')
                            <div class="form__error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- ユーザー名 -->
                <div class="form__group-content">
                    <label for="name">ユーザー名</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $profile->name ?? '') }}"
                        autocomplete="name">
                    @error('name')
                        <div class="form__error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- 郵便番号 -->
                <div class="form__group-content">
                    <label for="post-number">郵便番号</label>
                    <input type="text" name="post_number" id="post-number"
                        value="{{ old('post_number', $defaultAddress->post_number ?? '') }}">
                    @error('post_number')
                        <div class="form__error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- 住所 -->
                <div class="form__group-content">
                    <label for="address">住所</label>
                    <input type="text" name="address" id="address"
                        value="{{ old('address', $defaultAddress->address ?? '') }}">
                    @error('address')
                        <div class="form__error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- 建物名 -->
                <div class="form__group-content">
                    <label for="building">建物名</label>
                    <input type="text" name="building" id="building"
                        value="{{ old('building', $defaultAddress->building ?? '') }}">
                    @error('building')
                        <div class="form__error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- 更新ボタン -->
            <div class="form__button">
                <button class="form__button-submit" type="submit">更新する</button>
            </div>
        </form>
    </div>
@endsection

@section('js')
    <script src="{{ asset('js/profile.js') }}"></script>
@endsection

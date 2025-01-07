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
        </header>

        <form class="form" action="{{ route('mypage.profile.update') }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="form__group">
                <div class="form__group-content">
                    <div class="image-upload">
                        <div class="user-image-preview" id="userImagePreview"></div>

                        <label for="img" class="image-upload__label">
                            画像を選択する
                            <input type="file" name="image" id="image" accept="image/*"
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
                            value="{{ old('post_number', $profile->post_number) }}">
                    </label>
                    <div class="form__error">
                        @error('post_number')
                            {{ $message }}
                        @enderror
                    </div>
                </div>
                <div class="form__group-content">
                    <label for="address">住所
                        <input type="text" name="address" id="address" value="{{ old('address', $profile->address) }}">
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
                            value="{{ old('building', $profile->building) }}">
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
<script src="image.js"></script>
@endsection

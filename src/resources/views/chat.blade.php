@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/chat.css') }}">
    <link rel="stylesheet" href="{{ asset('css/nav.css') }}">
@endsection

@section('nav')
    @include('components.nav')
@endsection

@section('content')
    <div class="chat-page">
        <div class="left-side">
            <aside>
                <div class="left-side__content">
                    <h2 class="left-side__ttl">その他の取引</h2>
                </div>
                <ul>
                    @foreach ($otherChatRooms as $room)
                        <li>
                            @if ($room->transaction_status === 'active')
                                <a href="{{ route('chat', ['item_id' => $room->item->id]) }}">
                                    {{ $room->item->name ?? '取引商品' }}
                                </a>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </aside>
        </div>

        <div class="right-content">
            <div class="content__row">
                <div class="content__flex">
                    <div class="user__image-preview">
                        @if (isset($chatRoom) && $chatRoom)
                            @php
                                $partner = Auth::id() === $chatRoom->seller_id ? $chatRoom->buyer : $chatRoom->seller;
                            @endphp

                            @if ($partner && $partner->profile && $partner->profile->image)
                                <img src="{{ Storage::url('profile_images/' . $partner->profile->image) }}"
                                    alt="取引相手のプロフィール画像">
                            @endif
                        @endif
                    </div>

                    @if (isset($chatRoom) && $chatRoom)
                        <h1 class="first-content__ttl">
                            {{ Auth::id() === $chatRoom->seller_id ? $chatRoom->buyer->name ?? '購入者不明' : $chatRoom->seller->name ?? '出品者不明' }}さんとの取引画面
                        </h1>
                    @endif
                </div>

                @if ($chatRoom->buyer_id == Auth::id() && $chatRoom->isActive())
                    <button class="first-content__button" onclick="openRatingModal()">取引を完了する</button>
                    <input type="hidden" name="chatRoomId" value="{{ $chatRoom->id }}">
                @endif

                @if ($chatRoom->transaction_status === 'buyer_rated' && Auth::id() === $chatRoom->seller_id)
                    <button class="first-content__button" onclick="openRatingModal()">取引を完了する</button>
                    <input type="hidden" name="chatRoomId" value="{{ $chatRoom->id }}">
                @endif
            </div>

            @if ($chatRoom->buyer_id == Auth::id() && $chatRoom->transaction_status === 'active' && !$hasRated)
                <div id="ratingModal" class="modal">
                    <div class="modal-content">
                        <h2>取引が完了しました。</h2>
                        <form action="{{ route('rating.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="chat_room_id" value="{{ $chatRoom->id }}">
                            <input type="hidden" name="rater_id" value="{{ Auth::id() }}">
                            <input type="hidden" name="rated_id"
                                value="{{ Auth::id() == $chatRoom->seller_id ? $chatRoom->buyer_id : $chatRoom->seller_id }}">
                            <input type="hidden" id="rating-value" name="rating" value="0">
                            <p>今回の取引相手はどうでしたか？</p>
                            <div class="review__stars">
                                @for ($i = 1; $i <= 5; $i++)
                                    <span class="star" data-value="{{ $i }}">★</span>
                                @endfor
                            </div>
                            <div class="review__button">
                                <button type="submit" class="review__button-submit">送信する</button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            @if ($chatRoom->transaction_status === 'buyer_rated' && Auth::id() === $chatRoom->seller_id)
                <div id="ratingModal" class="modal">
                    <div class="modal-content">
                        <h2>取引が完了しました。</h2>
                        <form action="{{ route('rating.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="chat_room_id" value="{{ $chatRoom->id }}">
                            <input type="hidden" name="rater_id" value="{{ Auth::id() }}">
                            <input type="hidden" name="rated_id" value="{{ $chatRoom->buyer_id }}">
                            <input type="hidden" id="rating-value" name="rating" value="0">
                            <p>取引相手の評価をお願いします。</p>
                            <div class="review__stars">
                                @for ($i = 1; $i <= 5; $i++)
                                    <span class="star" data-value="{{ $i }}">★</span>
                                @endfor
                            </div>
                            <div class="review__button">
                                <button type="submit" class="review__button-submit">送信する</button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            <div class="second-content__row">
                <div class="item-image">
                    @if ($chatRoom && $chatRoom->item)
                        <img src="{{ asset('storage/item_images/' . optional($chatRoom->item)->image) }}"
                            alt="{{ optional($chatRoom->item)->name ?? '' }}">
                    @else
                        <img src="{{ asset('image/dummy.jpg') }}" alt="商品画像なし">
                    @endif
                </div>
                <div class="second-content__text">
                    <h2 class="second-content__item">{{ optional($chatRoom->item)->name ?? '' }}</h2>
                    <p class="second-content__price">¥{{ $chatRoom->item->price ? floor($chatRoom->item->price) : '' }}</p>
                </div>
            </div>

            <div class="message-container">
                <div class="message">
                    @if (session('message'))
                        <span class="message-session">
                            {{ session('message') }}
                        </span>
                    @endif
                </div>
                @foreach ($chats as $chat)
                    @if ($chat->user_id == $chatRoom->buyer_id)
                        <div class="message buyer">
                        @else
                            <div class="message seller">
                    @endif
                    <div class="content__flex">
                        <div class="user__image-preview__small">
                            @if (optional($chat->user->profile)->image)
                                <img src="{{ Storage::url('profile_images/' . $chat->user->profile->image) }}"
                                    alt="プロフィール画像">
                            @endif
                        </div>
                        <h3>{{ $chat->user->name }}</h3>
                    </div>
                    <div class="message-content-wrapper">
                        <p class="message-content">{{ $chat->content }}</p>
                        @if ($chat->image)
                            <img src="{{ asset('storage/' . $chat->image) }}" alt="添付画像" style="max-width: 200px;">
                        @endif
                        @if ($chat->user_id == Auth::id())
                            <div class="text">
                                <button class="update__button" onclick="showEditForm({{ $chat->id }})">編集</button>
                                <form action="{{ route('chat.delete', ['id' => $chat->id]) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="delete__button">削除</button>
                                </form>
                            </div>
                        @endif

                        @if ($chat->user_id == Auth::id())
                            <div id="editForm{{ $chat->id }}" style="display: none;" class="edit-form">
                                <form action="{{ route('chat.update', ['id' => $chat->id]) }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    @method('PATCH')
                                    <textarea name="content">{{ old('content', $chat->content) }}</textarea>
                                    <button type="submit">更新</button>
                                    <button type="button" onclick="hideEditForm({{ $chat->id }})">キャンセル</button>
                                </form>
                            </div>
                        @endif
                    </div>
            </div>
            @endforeach
        </div>

        @if ($errors->any())
            <div class="form__error">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('chat.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="item_id" value="{{ $chatRoom->item_id }}">
            <div class="message__input-container">
                <textarea class="message__textarea" name="content" id="chat-input" placeholder="取引メッセージを記入してください"
                    data-chat-room-id="{{ $chatRoom->id }}" data-route="{{ route('chat.storeContent') }}">{{ session("stored_content_chatroom_{$chatRoom->id}", '') }}</textarea>
                <label for="file-upload" class="file__upload" id="preview">画像を追加</label>
                <input type="file" id="file-upload" name="image" style="display: none;">
                <button type="submit" class="message-send__button">
                    <img src="{{ asset('image/button.jpeg') }}" alt="">
                </button>
            </div>
        </form>
    </div>
@endsection

@section('js')
    <script src="{{ asset('js/chat.js') }}"></script>
@endsection

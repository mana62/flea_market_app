<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\NotificationController;

//認証
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', [AuthController::class, 'showVerifyEmail'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
        ->middleware('signed')->name('verification.verify');
    Route::post('/email/resend', [AuthController::class, 'resendVerificationEmail'])
        ->middleware('throttle:6,1')->name('verification.send');
});

//登録・ログイン・ログアウト
Route::get('/register', [AuthController::class, 'registerPage'])->name('register.view');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::get('/login', [AuthController::class, 'loginPage'])->name('login.view');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

//商品関連のルート
Route::get('/search', [SearchController::class, 'searchItem'])->name('search');
Route::get('/', [ItemController::class, 'index'])->name('item');
Route::get('/item/{item_id}', [ItemController::class, 'itemDetail'])->name('item.detail');

Route::middleware(['auth'])->group(function () {
Route::get('/sell', [ItemController::class, 'sellItemPage'])->name('item.sell.page');
Route::post('/sell', [ItemController::class, 'sellItem'])->name('item.sell');
Route::post('/item/{item}/image', [ItemController::class, 'uploadImage'])->name('item.image.upload');
Route::post('/item/{item}/like', [ItemController::class, 'toggleLike'])->name('item.like');
Route::post('/item/{item_id}/comments', [ItemController::class, 'comment'])->name('item.comment');
});

//プロフィール関連
Route::middleware(['auth', 'has.profile'])->group(function () {
    Route::get('/mypage', [ProfileController::class, 'index'])->name('mypage');
});

Route::middleware('auth')->group(function () {
    Route::get('/mypage/profile', [ProfileController::class, 'edit'])->name('mypage.profile.edit');
    Route::put('/mypage/profile', [ProfileController::class, 'update'])->name('mypage.profile.update');
});

//購入・決済関連のルート
Route::middleware('auth')->group(function () {
    Route::get('/purchase/{item_id}', [PurchaseController::class, 'showPurchasePage'])->name('purchase');
    Route::post('/purchase/{item_id}', [PurchaseController::class, 'itemPurchase'])->name('item.purchase');

    Route::get('/purchase/address/{item_id}', [PurchaseController::class, 'changeAddressPage'])->name('change.address.page');
    Route::post('/purchase/address/{item_id}', [PurchaseController::class, 'changeAddress'])->name('change.address');

    Route::get('/payment/{item_id}', [PaymentController::class, 'itemPaymentPage'])->name('item.payment.page');
    Route::post('/payment/{item_id}', [PaymentController::class, 'itemPayment'])->name('item.payment');
    Route::get('/thanks-buy', [PurchaseController::class, 'thanksBuy'])->name('thanks.buy');
});

// チャット
Route::get('/chat/{item_id}', [ChatController::class, 'index'])->name('chat');
Route::post('/chat/store', [ChatController::class, 'store'])->name('chat.store');
Route::patch('/chat/update/{id}', [ChatController::class, 'update'])->name('chat.update');
Route::delete('/chat/delete/{id}', [ChatController::class, 'destroy'])->name('chat.delete');
Route::post('/chat/store-content', [ChatController::class, 'storeContent'])->name('chat.storeContent');

// 評価
Route::post('/rating/store', [RatingController::class, 'store'])->name('rating.store');

// 通知
Route::post('/chat/{chatRoomId}/read', [NotificationController::class, 'readNotice'])->name('chat.read');

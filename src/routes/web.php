<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PaymentController;

//認証
Route::middleware('auth')->group(function () {
    //メール認証
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
//商品検索
Route::get('/search', [SearchController::class, 'searchItem'])->name('search');
//商品一覧
Route::get('/', [ItemController::class, 'index'])->name('item');
//商品詳細
Route::get('/item/{item_id}', [ItemController::class, 'itemDetail'])->name('item.detail');
Route::middleware(['auth'])->group(function () {

//出品ページ
Route::get('/sell', [ItemController::class, 'sellItemPage'])->name('item.sell.page');
//出品処理
Route::post('/sell', [ItemController::class, 'sellItem'])->name('item.sell');
//画像アップロード
Route::post('/item/{item}/image', [ItemController::class, 'uploadImage'])->name('item.image.upload');
//いいね機能
Route::post('/item/{item}/like', [ItemController::class, 'toggleLike'])->name('item.like');
//コメント投稿
Route::post('/item/{item_id}/comments', [ItemController::class, 'comment'])->name('item.comment');
});
//プロフィール関連
Route::middleware(['auth', 'has.profile'])->group(function () {
    Route::get('/mypage', [ProfileController::class, 'index'])->name('mypage');
});

Route::middleware('auth')->group(function () {
    //プロフィール編集ページ
    Route::get('/mypage/profile', [ProfileController::class, 'edit'])->name('mypage.profile.edit');
    //プロフィール更新
    Route::put('/mypage/profile', [ProfileController::class, 'update'])->name('mypage.profile.update');
});

//購入・決済関連のルート
Route::middleware('auth')->group(function () {
    //購入ページ
    Route::get('/purchase/{item_id}', [PurchaseController::class, 'showPurchasePage'])->name('purchase');
    Route::post('/purchase/{item_id}', [PurchaseController::class, 'itemPurchase'])->name('item.purchase');

    //配送先変更
    Route::get('/purchase/address/{item_id}', [PurchaseController::class, 'changeAddressPage'])->name('change.address.page');
    Route::post('/purchase/address/{item_id}', [PurchaseController::class, 'changeAddress'])->name('change.address');

    //Stripe決済
    Route::get('/payment/{item_id}', [PaymentController::class, 'itemPaymentPage'])->name('item.payment.page');
    Route::post('/payment/{item_id}', [PaymentController::class, 'itemPayment'])->name('item.payment');
    //購入完了
    Route::get('/thanks-buy', [PurchaseController::class, 'thanksBuy'])->name('thanks.buy');
});

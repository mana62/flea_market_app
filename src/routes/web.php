<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ProfileController;

Route::middleware('auth')->group(function () {
    Route::get('/email/verify', [AuthController::class, 'showVerifyEmail'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
        ->middleware('signed')->name('verification.verify');
    Route::post('/email/resend', [AuthController::class, 'resendVerificationEmail'])
        ->middleware('throttle:6,1')->name('verification.send');
});

Route::get('/register', [AuthController::class, 'registerPage'])->name('register.view');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::get('/login', [AuthController::class, 'loginPage'])->name('login.view');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/search', [SearchController::class, 'searchItem'])->name('search');
Route::get('/', [ItemController::class, 'index'])->name('item');
Route::get('/item/{item_id}', [ItemController::class, 'itemDetail'])->name('item.detail');
Route::get('/sell', [ItemController::class, 'sellItemPage'])->name('item.sell.page');
Route::post('/sell', [ItemController::class, 'sellItem'])->name('item.sell');
Route::post('/item/{item}/image', [ItemController::class, 'uploadImage'])->name('item.image.upload');
Route::post('/item/{item}/like', [ItemController::class, 'toggleLike'])->name('item.like');
Route::post('/item/{item_id}/comments', [ItemController::class, 'comment'])->name('item.comment');

// プロフィール設定が完了していない場合、リダイレクトするミドルウェアを適用
Route::middleware(['auth', 'has.profile'])->group(function () {
    Route::get('/mypage', [ProfileController::class, 'index'])->name('mypage');
});

// プロフィール編集と更新は has.profile ミドルウェアを適用しない
Route::middleware(['auth'])->group(function () {
    Route::get('/mypage/profile', [ProfileController::class, 'edit'])->name('mypage.profile.edit');
    Route::put('/mypage/profile', [ProfileController::class, 'update'])->name('mypage.profile.update');
});

Route::middleware(['auth'])->group(function () {
    // 購入ページの表示（GET）
    Route::get('/purchase/{item_id}', [PurchaseController::class, 'showPurchasePage'])->name('purchase');
 
    // 購入処理の実行（POST）
    Route::post('/purchase/{item_id}', [PurchaseController::class, 'itemPurchase'])->name('item.purchase');
 
    // 配送先変更ページの表示（GET）
    Route::get('/purchase/address/{item_id}', [PurchaseController::class, 'changeAddressPage'])->name('change.address.page');
 
    // 配送先変更処理の実行（POST）
    Route::post('/purchase/address/{item_id}', [PurchaseController::class, 'changeAddress'])->name('change.address');
 });
 
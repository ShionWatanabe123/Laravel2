<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Auth;

// ホームページのルート設定
Route::get('/', function () {
    if (Auth::check()) {
        // ログイン状態ならば商品一覧ページへリダイレクト
        return redirect()->route('products.index');
    } else {
        // ログイン状態でなければログイン画面へリダイレクト
        return redirect()->route('login');
    }
});

Auth::routes();

// 認証が必要なルート設定
Route::group(['middleware' => 'auth'], function () {
    Route::resource('products', ProductController::class);
});

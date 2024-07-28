<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SalesController;
use Illuminate\Support\Facades\Auth;

// ルート '/' の設定
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('products.index');
    } else {
        return redirect()->route('login');
    }
});

// 認証ルートの設定
Auth::routes();

// 認証が必要なルートのグループ
Route::middleware(['auth'])->group(function () {
    // 商品関連のルート
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
    Route::get('/products/search', [ProductController::class, 'search'])->name('products.search');
    
    // 購入処理のルート
    Route::post('/purchase', [SalesController::class, 'purchase'])->name('purchase');
});

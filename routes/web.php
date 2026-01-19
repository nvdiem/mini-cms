<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\SiteController;

// Frontend
Route::get('/', [SiteController::class, 'index'])->name('site.home');
Route::get('/posts/{slug}', [SiteController::class, 'show'])->name('site.posts.show');

// Auth
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Admin
Route::prefix('admin')->middleware('auth')->name('admin.')->group(function () {
    Route::get('/', fn() => redirect()->route('admin.posts.index'));

    Route::get('posts/{post}/preview', [PostController::class, 'preview'])->name('posts.preview');
    Route::post('posts/{id}/restore', [PostController::class, 'restore'])->name('posts.restore');
    Route::post('posts/bulk', [PostController::class, 'bulk'])->name('posts.bulk');
    Route::resource('posts', PostController::class);

    Route::resource('categories', CategoryController::class)->only(['index','store','update','destroy']);
    Route::resource('media', MediaController::class)->only(['index','store','destroy']);
});

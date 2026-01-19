<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\LeadController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\UserController;

use App\Http\Controllers\SiteController;


use App\Http\Controllers\ContactController;

// Auth
Route::get('login', [AuthController::class, 'showLogin'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Admin
Route::prefix('admin')->name('admin.')->middleware('auth')->group(function(){
    Route::get('/', function(){ return redirect()->route('admin.posts.index'); })->name('dashboard');

    // Posts
    Route::resource('posts', PostController::class);
    Route::post('posts/bulk', [PostController::class, 'bulk'])->name('posts.bulk');
    Route::post('posts/{id}/restore', [PostController::class, 'restore'])->name('posts.restore');
    Route::get('posts/{post}/preview', [PostController::class, 'preview'])->name('posts.preview');

    // Pages
    Route::resource('pages', PageController::class);
    Route::post('pages/bulk', [PageController::class, 'bulk'])->name('pages.bulk');
    Route::post('pages/{id}/restore', [PageController::class, 'restore'])->name('pages.restore');
    Route::get('pages/{page}/preview', [PageController::class, 'preview'])->name('pages.preview');

    // Categories & Tags
    Route::resource('categories', CategoryController::class)->except(['create','edit','show']);
    Route::resource('tags', TagController::class)->except(['create','edit','show']);

    // Review Queue
    Route::get('review', [ReviewController::class, 'index'])->name('review.index');
    Route::post('review/{post}/publish', [ReviewController::class, 'publish'])->name('review.publish');

    // Media
    Route::resource('media', MediaController::class)->only(['index','store','destroy']);

    // Leads
    Route::get('leads', [LeadController::class, 'index'])->name('leads.index');
    Route::get('leads/{lead}', [LeadController::class, 'show'])->name('leads.show');
    Route::post('leads/{lead}/status', [LeadController::class, 'updateStatus'])->name('leads.status');
    Route::post('leads/bulk', [LeadController::class, 'bulk'])->name('leads.bulk');

    // Admin Only
    Route::middleware('admin')->group(function(){
        // Settings
        Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::post('settings', [SettingsController::class, 'update'])->name('settings.update');

        // Users
        Route::resource('users', UserController::class)->except(['show']);
        Route::post('users/{user}/toggle', [UserController::class, 'toggleActive'])->name('users.toggle');
    });
});

// Site
Route::get('/', [SiteController::class, 'home'])->name('site.home');
Route::get('/posts/{slug}', [SiteController::class, 'show'])->name('site.posts.show');
Route::get('/p/{slug}', [SiteController::class, 'showPage'])->name('site.pages.show');
Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

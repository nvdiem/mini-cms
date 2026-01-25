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
use App\Http\Controllers\Admin\PageBuilderController;
use App\Http\Controllers\InstallerController;

use App\Http\Controllers\SiteController;
use App\Http\Controllers\PublicLeadController;
use App\Http\Controllers\PublicPageBuilderController;


use App\Http\Controllers\ContactController;

// Installer Routes (must be before other routes)
Route::prefix('install')->name('installer.')->group(function() {
    Route::get('/', [InstallerController::class, 'requirements'])->name('requirements');
    Route::get('/database', [InstallerController::class, 'database'])->name('database');
    Route::post('/database', [InstallerController::class, 'databaseStore'])->name('database.store');
    Route::get('/admin', [InstallerController::class, 'admin'])->name('admin');
    Route::post('/admin', [InstallerController::class, 'adminStore'])->name('admin.store');
    Route::get('/complete', [InstallerController::class, 'complete'])->name('complete');
});

// Auth (rate-limited to prevent brute force)
Route::get('login', [AuthController::class, 'showLogin'])->name('login');
Route::post('login', [AuthController::class, 'login'])->middleware('throttle:5,1');
Route::post('logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// SEO Routes
Route::get('/sitemap.xml', [App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', [App\Http\Controllers\SitemapController::class, 'robots'])->name('robots');

// Admin
Route::prefix('admin')->name('admin.')->middleware('auth')->group(function(){
    // Dashboard
    Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

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
    Route::post('media/folders', [\App\Http\Controllers\Admin\MediaFolderController::class, 'store'])->name('media.folders.store');
    Route::put('media/folders/{folder}', [\App\Http\Controllers\Admin\MediaFolderController::class, 'update'])->name('media.folders.update');
    Route::delete('media/folders/{folder}', [\App\Http\Controllers\Admin\MediaFolderController::class, 'destroy'])->name('media.folders.destroy');
    Route::resource('media', MediaController::class)->parameters(['media' => 'media']);

    // Leads
    Route::get('leads', [LeadController::class, 'index'])->name('leads.index');
    Route::get('leads/{lead}', [LeadController::class, 'show'])->name('leads.show');
    Route::post('leads/{lead}/status', [LeadController::class, 'updateStatus'])->name('leads.status');
    Route::post('leads/bulk', [LeadController::class, 'bulk'])->name('leads.bulk');

    // Page Builder
    Route::get('page-builder', [PageBuilderController::class, 'index'])->name('page-builder.index');
    Route::get('page-builder/create', [PageBuilderController::class, 'create'])->name('page-builder.create');
    Route::post('page-builder', [PageBuilderController::class, 'store'])->name('page-builder.store');
    Route::get('page-builder/{id}', [PageBuilderController::class, 'show'])->name('page-builder.show');
    Route::post('page-builder/{id}/activate', [PageBuilderController::class, 'activate'])->name('page-builder.activate');

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

// Search
Route::get('/search', [App\Http\Controllers\SearchController::class, 'index'])->name('site.search');

// Page Builder Public Routes
Route::post('/lead', [PublicLeadController::class, 'store'])
    ->middleware('throttle:30,1')
    ->name('lead.store');
Route::get('/b/{slug}', [PublicPageBuilderController::class, 'show'])->name('pagebuilder.show');

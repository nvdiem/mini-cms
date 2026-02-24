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
use App\Http\Controllers\PublicSupportController;

use App\Http\Controllers\ContactController;
use App\Http\Controllers\Admin\SupportController;

// Shop
use App\Http\Controllers\ShopController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\Admin\Shop\ProductController as ShopProductController;
use App\Http\Controllers\Admin\Shop\VariantController;
use App\Http\Controllers\Admin\Shop\OrderController;
use App\Http\Controllers\Admin\Shop\ShopSettingsController;

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

// Public Support (no CSRF, with throttle + honeypot)
Route::post('/support/first-message', [PublicSupportController::class, 'firstMessage'])
    ->middleware('throttle:100,1')
    ->name('support.first-message');
Route::post('/support/messages', [PublicSupportController::class, 'sendMessage'])
    ->middleware('throttle:120,1')
    ->name('support.send');
Route::get('/support/messages', [PublicSupportController::class, 'pollMessages'])
    ->middleware('throttle:600,1')
    ->name('support.poll');
// SSE Stream
Route::get('/support/stream', [PublicSupportController::class, 'stream'])
    ->middleware('throttle:600,1')
    ->name('support.stream');
// New Enhancements (Sprint 9.2)
Route::post('/support/mark-read', [PublicSupportController::class, 'markRead'])
    ->middleware('throttle:600,1')
    ->name('support.mark-read');
Route::post('/support/typing', [PublicSupportController::class, 'typing'])
    ->middleware('throttle:100,1')
    ->name('support.typing');

// Admin
Route::prefix('admin')->name('admin.')->middleware(['auth', 'active_user'])->group(function(){
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

    // Support
    Route::get('support', [SupportController::class, 'index'])->name('support.index');
    Route::get('support/unread-count', [SupportController::class, 'unreadCount'])->name('support.unread-count'); // Moved up
    Route::get('support/{id}', [SupportController::class, 'show'])->name('support.show');
    Route::get('support/{id}/messages', [SupportController::class, 'pollMessages'])->name('support.poll');
    Route::get('support/{id}/stream', [SupportController::class, 'stream'])->name('support.stream');
    Route::post('support/{id}/messages', [SupportController::class, 'reply'])->name('support.reply');
    Route::post('support/{id}/status', [SupportController::class, 'updateStatus'])->name('support.status');
    // Enhancements (Sprint 9.2)
    Route::post('support/{id}/mark-read', [SupportController::class, 'markRead'])->name('support.mark-read');
    Route::post('support/{id}/typing', [SupportController::class, 'typing'])
        ->middleware('throttle:300,1')
        ->name('support.typing');

    // Page Builder
    Route::get('page-builder', [PageBuilderController::class, 'index'])->name('page-builder.index');
    Route::get('page-builder/create', [PageBuilderController::class, 'create'])->name('page-builder.create');
    Route::post('page-builder', [PageBuilderController::class, 'store'])->name('page-builder.store');
    Route::get('page-builder/{id}', [PageBuilderController::class, 'show'])->name('page-builder.show');
    Route::put('page-builder/{id}', [PageBuilderController::class, 'update'])->name('page-builder.update');
    Route::delete('page-builder/{id}', [PageBuilderController::class, 'destroy'])->name('page-builder.destroy');
    Route::post('page-builder/{id}/activate', [PageBuilderController::class, 'activate'])->name('page-builder.activate');

    // ── Shop Module ──
    Route::prefix('shop')->name('shop.')->group(function(){
        // Products (editor + admin)
        Route::get('products', [ShopProductController::class, 'index'])->name('products.index');
        Route::get('products/create', [ShopProductController::class, 'create'])->name('products.create');
        Route::post('products', [ShopProductController::class, 'store'])->name('products.store');
        Route::get('products/{product}/edit', [ShopProductController::class, 'edit'])->name('products.edit');
        Route::put('products/{product}', [ShopProductController::class, 'update'])->name('products.update');
        Route::post('products/{product}/publish', [ShopProductController::class, 'publish'])->name('products.publish');
        Route::delete('products/{product}', [ShopProductController::class, 'destroy'])->name('products.destroy');
        Route::post('products/{id}/restore', [ShopProductController::class, 'restore'])->name('products.restore');

        // Variants
        Route::post('products/{product}/variants/generate', [VariantController::class, 'generate'])->name('variants.generate');
        Route::put('products/{product}/variants', [VariantController::class, 'update'])->name('variants.update');
        Route::post('variants/{variant}/toggle', [VariantController::class, 'toggle'])->name('variants.toggle');
        Route::post('variants/{variant}/stock-adjust', [VariantController::class, 'stockAdjust'])->name('variants.stock-adjust');

        // Orders & Settings (admin only)
        Route::middleware('admin')->group(function(){
            Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
            Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
            Route::post('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status');

            Route::get('settings', [ShopSettingsController::class, 'index'])->name('settings.index');
            Route::post('settings', [ShopSettingsController::class, 'update'])->name('settings.update');
        });
    });

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
Route::get('/b/{slug}/{path?}', [PublicPageBuilderController::class, 'serve'])
    ->where('path', '.*')
    ->name('pagebuilder.show');

// ── Public Shop ──
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/shop/{slug}', [ShopController::class, 'show'])->name('shop.show');
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/order/{order_no}/thank-you', [CheckoutController::class, 'thankYou'])->name('checkout.thank-you');

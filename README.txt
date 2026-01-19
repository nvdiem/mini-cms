Laravel Mini CMS Patch v4
========================
Adds requested features:
- Sidebar menus: Pages/Leads/Analytics placeholders + active menus for Posts/Categories/Media.
- Header shows logged-in email + "Visit site" link to frontend homepage.
- Frontend module:
  - Homepage lists published posts
  - Post detail by slug (only published)
  - Admin preview for any post (draft/review/published) at /admin/posts/{post}/preview
  - Admin actions menu includes Preview (and View for published)

Copy into project (overwrite):
- app/Models/Post.php
- app/Http/Controllers/Admin/PostController.php
- app/Http/Controllers/SiteController.php
- resources/views/admin/layout.blade.php
- resources/views/admin/posts/index.blade.php
- resources/views/site/*

Routes (routes/web.php)
----------------------
Add at top:
use App\Http\Controllers\SiteController;

Public frontend:
Route::get('/', [SiteController::class, 'index'])->name('site.home');
Route::get('/posts/{slug}', [SiteController::class, 'show'])->name('site.posts.show');

Admin (inside admin group):
Route::get('posts/{post}/preview', [PostController::class, 'preview'])->name('posts.preview');

Note:
- Frontend shows only posts where status=published and published_at <= now (or null).
- Preview shows any post regardless of status, behind auth.

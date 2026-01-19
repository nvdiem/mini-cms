# Mini CMS (Laravel + Blade, No NPM) — Project Status & Handoff (as of 2026-01-19)

This document summarizes the current state of the project and provides a handoff prompt so another AI can continue the work seamlessly.

---

## 0) Environment & Constraints
- **Stack:** Laravel + MySQL (Laragon on Windows)
- **Views:** Blade only
- **Frontend:** Tailwind via **CDN** (no Vite, no npm)
- **Auth:** Custom (no Breeze)
- **UI Base:** `mini_cms_templates_v2` (calm theme tokens, admin layout, responsive list)
- **Must keep:** confirm modal, toast + undo, mobile card view, calm colors for long usage

---

## 1) Implemented Features (Done)

### 1.1 Authentication (custom)
- `/login` (GET) shows login form
- `/login` (POST) authenticates using session
- `/logout` (POST) logs out
- `auth` middleware protects `/admin/*`

Demo seed user:
- `admin@local.test`
- `123456`

### 1.2 Admin UI
- Admin layout uses a calm token-based theme and Tailwind utilities.
- Sidebar menus:
  - **Posts** (active)
  - **Categories** (active)
  - **Media Library** (active)
  - **Pages / Leads / Analytics** (placeholders labeled “Soon”)
  - **Settings** (placeholder)
- Header:
  - **Visit site** link to frontend homepage
  - Logged-in user **email shown** with initial avatar
  - Logout button
  - Dark mode toggle (optional test toggle)

### 1.3 Posts module (CRUD + workflow)
- List view:
  - search `q`
  - filter by `status` (draft/review/published)
  - trash view toggle (`trash=1`)
  - desktop table view + mobile card view
- Post editor:
  - title, slug, excerpt, content
  - status (draft/review/published)
  - published_at (optional)
  - categories sync (many-to-many)
  - featured_image_id selection from media
- Actions:
  - Soft delete (trash) + restore
  - Bulk actions (move to trash / restore)
  - Confirm modal before trash
  - Toast notifications and **Undo** support after trash

### 1.4 Categories module
- CRUD lite: index/store/update/destroy
- Used in Post editor (checkbox list)

### 1.5 Media module
- Upload images (stored under `storage/app/public`)
- Media library index (grid/list)
- Delete
- Requirement: `php artisan storage:link`

### 1.6 Frontend module (public site)
- `GET /` homepage: lists **published** posts only
- `GET /posts/{slug}`: post detail for **published** posts only
- Published rule:
  - `status = published`
  - `published_at` is NULL OR `<= now()`
- Admin preview:
  - `GET /admin/posts/{post}/preview` shows any post (draft/review/published) behind auth
  - Shows preview banner + “Back to editor”

---

## 2) Current Routes (web.php)
Frontend:
- `GET /` → `site.home`
- `GET /posts/{slug}` → `site.posts.show`

Auth:
- `GET /login` → login form
- `POST /login` → login submit
- `POST /logout` → logout (auth protected)

Admin (auth middleware):
- `GET /admin` → redirect to posts
- `resource /admin/posts`
- `POST /admin/posts/{id}/restore`
- `POST /admin/posts/bulk`
- `GET /admin/posts/{post}/preview`
- `resource /admin/categories` (index/store/update/destroy)
- `resource /admin/media` (index/store/destroy)

---

## 3) Code/Folder Notes (important for continuing)
- `x-admin.layout` is implemented as an anonymous component under:
  - `resources/views/components/admin/layout.blade.php`
- `x-site.layout` is implemented as an anonymous component under:
  - `resources/views/components/site/layout.blade.php`
- Frontend views live under:
  - `resources/views/site/`
- Admin views live under:
  - `resources/views/admin/`
- Models include `Post::scopePublished()` for frontend filtering.

---

## 4) Next Requested Features (Not Implemented Yet)
You requested the next phase:
1) **Pages module**
   - Admin CRUD for static pages (About/Policy/etc.)
   - Frontend route: `/p/{slug}`
2) **Review Queue + quick publish**
   - Admin page listing `status=review`
   - One-click publish action (sets status to published + optionally published_at = now)
3) **Tags module**
   - Tags CRUD
   - Assign tags to posts (many-to-many)
   - Filter posts list by tag
   - Show tag chips on frontend detail

---

## 5) How to Create a “Patch Zip” for AI Analysis
Include:
- `app/`
- `resources/views/`
- `routes/web.php`
- `database/migrations/`
- `database/seeders/`
- `composer.json`, `composer.lock`
- `.env.example`
- (optional) `config/`

Exclude:
- `vendor/`
- `node_modules/`
- `storage/logs/`, `storage/framework/`
- `.env`

---

# Handoff Prompt for Another AI (copy/paste)

**PROMPT START**

You are continuing a Laravel mini CMS project. Constraints:
- Laravel + MySQL (Laragon Windows)
- Blade only, Tailwind via CDN (NO npm, NO Vite)
- Custom auth (no Breeze)
- UI must remain consistent with `mini_cms_templates_v2`: calm color tokens, confirm modal, toast+undo, responsive (desktop table + mobile card view).
- Admin layout is an anonymous Blade component at `resources/views/components/admin/layout.blade.php`.
- Site layout is an anonymous Blade component at `resources/views/components/site/layout.blade.php`.

Current implemented modules:
- Admin: Posts (CRUD, soft delete+restore, bulk actions, confirm modal, toast+undo, category sync, featured image), Categories (CRUD lite), Media (upload/library/delete), Admin Preview (`/admin/posts/{post}/preview`).
- Frontend: Homepage `/` lists published posts only (status=published and published_at<=now or null), Post detail `/posts/{slug}` for published only.
- Admin header shows logged in user email and has “Visit site” link.

TASK (next phase):
Implement **Pages + Review Queue (quick publish) + Tags**:
1) Pages module:
   - DB: pages table (title, slug, excerpt, content, status, published_at, author_id, featured_image_id, timestamps, soft deletes if needed)
   - Admin: `/admin/pages` list + create/edit; re-use the same UI patterns as Posts.
   - Frontend: `/p/{slug}` shows published pages only.
2) Review Queue:
   - Admin page: `/admin/review` (or `/admin/posts?status=review` with a dedicated UI) listing posts in review.
   - Quick publish action: POST endpoint that sets status=published and published_at=now if empty.
   - Show toast on success.
3) Tags module:
   - DB: tags table + pivot table post_tag
   - Admin: tags CRUD page
   - Post editor: assign tags (checkbox list or simple comma input)
   - Post list: filter by tag
   - Frontend: show tag chips on post detail.

Output:
- Provide patch zip changes (files to copy) and exact routes/web.php additions.
- No npm. Keep UI consistent with existing tokens/components.
- Use confirm modal + toast pattern for destructive actions.

**PROMPT END**

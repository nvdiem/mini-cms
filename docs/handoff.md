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
  - **Pages** (active)
  - **Categories** (active)
  - **Tags** (active)
  - **Media Library** (active)
  - **Review Queue** (active)
  - **Leads** (active)
  - **Settings** (active)
- Header:
  - **Visit site** link to frontend homepage
  - Logged-in user **email shown** with initial avatar
  - Logout button

### 1.3 Posts module (CRUD + workflow)
- List view: search, filters (status, tag), trash toggle, mobile card view
- Editor: title, slug, excerpt, content, status, published_at, featured_image
- **SEO Settings**: meta title, meta description, keywords
- **Categories & Tags**: Many-to-many sync

### 1.4 Pages module
- Full CRUD, same structure as Posts but standalone
- **SEO Settings**: meta title, meta description, keywords
- Frontend route: `/p/{slug}`

### 1.5 Taxonomy modules
- **Categories**: CRUD lite
- **Tags**: CRUD lite, used in Post editor and Frontend

### 1.6 Review Queue module
- Dedicated page at `/admin/review`
- Quick Publish button for posts in "Review" status

### 1.7 Media module
- Upload images to `storage/app/public`
- Media library index (grid/list)

### 1.8 Settings Module
- Admin page at `/admin/settings` (Tabbed interface)
- Config: Site Name, Tagline, Logo, Default Post Status, Posts Per Page, SEO Defaults
- Cached `setting($key)` helper

### 1.9 Leads / Contact Module
- Frontend: `/contact` form with validation and SaaS-style design
- Backend: `/admin/leads` list view with Status workflow (New/Handled/Spam)

### 1.10 Frontend Redesign (SaaS / Minimal Japanese) ✨ NEW
- **Aesthetic**: Minimalist, whitespace-driven, Slate-50 palette, Inter font
- **Layout**: Clean top nav, progress bar, sticky header
- **Home**: Hero section, Featured Post split-card, Grid layout, CTA
- **Post**: Tailwind Typography (prose), Meta header, Related Posts, Prev/Next Navigation
- **Responsive**: Fully optimized for mobile

### 1.11 Demo Content ✨ NEW
- `DemoContentSeeder`: Generates realistic posts (Strategy, Design, Engineering), pages, and leads.
- **Images**: Supports local demo images in `public/demo/posts/`

---

## 2) Current Routes (web.php)

### Frontend:
- `GET /` → `site.home`
- `GET /posts/{slug}` → `site.posts.show`
- `GET /p/{slug}` → `site.pages.show`
- `GET /contact` → `contact.index`
- `POST /contact` → `contact.store`

### Auth:
- `GET /login`, `POST /login`, `POST /logout`

### Admin (auth middleware):
- Resources: `posts`, `pages`, `categories`, `tags`, `media`
- **Review**: `GET /admin/review`, `POST /publish`
- **Leads**: `GET /admin/leads`, `POST /status`, `POST /bulk`
- **Settings**: `GET /admin/settings`, `POST /update`

---

## 3) Database Schema

### Tables:
- `users`, `posts`, `pages`, `categories`, `tags`, `media`
- `settings` (key, value)
- `leads` (name, email, phone, message, status, source)
- Pivot: `category_post`, `post_tag`

---

## 4) Code/Folder Notes

### Views:
- Frontend: `resources/views/site/` (Modern SaaS design)
- Admin: `resources/views/admin/` (Consolidated layout)
- Layouts: `components/site/layout.blade.php` (Frontend master)

### Seeders:
- `DemoContentSeeder`: Main seeder for realistic data
- `SettingsSeeder`: Default configuration

---

## 5) Handoff Prompt for Another AI

**PROMPT START**

You are continuing a Laravel mini CMS project. Constraints:
- Laravel + MySQL (Laragon Windows)
- Blade only, Tailwind via CDN (NO npm, NO Vite)
- Custom auth (no Breeze)
- UI: "Minimal Japanese / SaaS" aesthetic for Frontend, "Calm Admin" for Backend.

**Current implemented modules:**
- **Core**: Posts & Pages (CRUD, SEO, Soft Deletes).
- **Taxonomies**: Categories & Tags.
- **Media**: Library & Uploads.
- **Workflow**: Review Queue.
- **Settings**: Site-wide config via `setting()` helper.
- **Leads**: Contact form & Admin management.
- **Frontend**: Full redesign complete. Minimalist hero, grid layout, typography-focused post view, related posts, mobile responsive.
- **Demo Data**: `DemoContentSeeder` generates realistic content.

**Database**:
- Tables: users, posts, pages, categories, tags, media, settings, leads.

**NEXT TASK SUGGESTIONS:**
- User management (CRUD for admin users)
- Analytics dashboard (simple stats chart)
- Post scheduling (publish at specific time via scheduler)
- Comment system
- Newsletter subscription (integrate with external provider or local)

**PROMPT END**

# Mini CMS (Laravel + Blade, No NPM) — Project Status & Handoff (as of 2026-01-20)

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

### 1.1 Authentication & Roles (Sprint 2) ✨
- **Roles**: `admin` (Full access), `editor` (Content only).
- **Status**: `is_active` check. Disabled users cannot login.
- **Middleware**: `admin` middleware protects sensitive routes.
- **Demo Users**:
  - `admin@local.test` / `123456`
  - `editor@local.test` / `123456`

### 1.2 User Management Module (Sprint 2) ✨
- Admin-only CRUD: `/admin/users`
- Security: Admins cannot disable themselves.
- UI: Badge indicators for Roles/Status.

### 1.3 Analytics & Dashboard (Sprint 3) ✨
- **Dashboard UI**: `/admin` now shows a comprehensive SaaS-style dashboard.
- **Charts**: Chart.js integration showing Daily Views vs Leads (Last 7/14/30 days).
- **KPI Cards**: Total Views, New Leads, Content Stats (Published, Draft, Review).
- **Activity Feed**: Timeline of recent system actions with clickable links.
- **Top Content**: Table showing best-performing posts by views.

### 1.4 Refined Activity Logging System (Sprint 3) ✨
- **Robustness**: `activity_logs` table with `meta` (JSON) support for rich context.
- **Helper**: Global `activity_log()` function auto-detects user, subject, and generates links.
- **Integration**:
  - **Posts/Pages**: Created, Updated, Trashed, Restored, Bulk actions, Quick Publish.
  - **Leads**: Created (Guest), Status Changed.
  - **Settings**: Configuration updates.

### 1.5 Media Module Overhaul (Sprint 4 & Phases A/B/C) 🚀
- **Safe Delete**: Prevents deletion of media used in Posts/Pages.
- **Metadata**: `alt_text`, `caption`, `width`, `height` stored in DB.
- **Detail View**: Dedicated page `/admin/media/{id}` to edit metadata, see usage stats, and copy URL.
- **Folders**:
  - Sidebar with "All", "Unsorted", and Custom Folders with live counts.
  - New Folder modal & Rename Folder modal.
  - Sidebar counts respect Search Query (e.g. searching "logo" shows counts per folder).
- **Upload**: Auto-captures image dimensions.

### 1.6 SEO Features (Sprint 4) 🚀
- **Sitemap**: `/sitemap.xml` generated dynamically for Posts & Pages.
- **Robots**: `/robots.txt` generated dynamically.
- **Meta Tags**: Canonical URLs and Noindex for non-public pages.

### 1.7 Admin UI
- **Sidebar**: Dashboard link added. Role-based visibility.
- **Header**: Shows User Badge (ADM/EDT).
- **Layout**: Calm theme, responsive sidebar.

### 1.8 Core Modules
- **Posts**: CRUD, SEO, Tags/Cats, Review Workflow, **View Tracking**.
- **Pages**: CRUD, SEO, **Activity Logging**.
- **Taxonomies**: Categories, Tags.
- **Media**: Library & Uploads & Folders.
- **Leads**: Contact form submissions + Status workflow + **Logging**.
- **Settings**: Site configurations (Global) + **Logging**.

### 1.9 Frontend Redesign
- **Aesthetic**: Minimalist, whitespace-driven, Slate-50 palette.
- **Components**: Hero, Featured Post, Grid, Related Posts, Contact Form.
- **Optimized**: Mobile responsive, sticky header, progress bar.

---

## 2) Current Routes (web.php)

### Frontend:
- `GET /` → `site.home`
- `GET /posts/{slug}` → `site.posts.show` (Tracks unique daily view)
- `GET /p/{slug}` → `site.pages.show`
- `GET /contact` → `contact.index`
- `POST /contact` → `contact.store`
- `GET /sitemap.xml`, `GET /robots.txt`

### Auth:
- `GET /login`, `POST /login`, `POST /logout`

### Admin (Shared):
- `dashboard` (`/admin`) - Analytics Home
- `posts`, `pages`, `categories`, `tags`, `leads`, `review`
- `media` (Now full resource with folders support)

### Admin (Protected - Admin Only):
- `settings`: Index, Update
- `users`: Index, Create, Store, Edit, Update, Toggle

---

## 3) Database Schema

### Tables:
- `users` (id, name, email, password, **role**, **is_active**)
- `posts`, `pages` (featured_image_id, etc)
- `categories`, `tags`, `pivot_tables`
- `media` (id, path, **folder_id**, **alt_text**, **caption**, **width**, **height**, etc)
- `media_folders` (id, name)
- `settings` (key, value)
- `leads` (name, email, phone, message, status, source)
- `post_view_stats` (post_id, date, views) [Unique(post_id, date)]
- `activity_logs` (user_id, type, subject_id, subject_type, message, **meta**)

---

## 4) Code/Folder Notes

- **Helpers**: `app/helpers.php` contains `setting()`, `setting_set()`, and `activity_log()`.
- **Models**: `PostViewStat` (Analytic), `ActivityLog` (History), `Media`, `MediaFolder`.
- **Migrations**: Latest include `create_post_view_stats`, `create_activity_logs`, `add_metadata_to_media_table`, `create_media_folders_table`.
- **Views**: `resources/views/admin/media/index.blade.php` (Sidebar + Grid), `show.blade.php` (Detail).

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
- **Analytics**: Dashboard with Chart.js, KPI cards, Post View tracking (`post_view_stats`).
- **Activity**: System-wide logging (`activity_logs` + `meta`) for audit trail.
- **Media**: 
  - **Library**: Folders (Sidebar), Search, Pagination.
  - **Safety**: Prevent delete if in use.
  - **Metadata**: Alt/Caption/Dimensions.
  - **Details**: Dedicated view for management.
- **SEO**: Sitemap, Robots, Canonical.
- **Taxonomies**: Categories & Tags.
- **Settings**: Site-wide config via `setting()` helper.
- **Leads**: Contact form & Admin management.
- **User Management**: RBAC (Admin/Editor), User CRUD.
- **Frontend**: Full redesign complete. Minimalist.

**Database**:
- Tables included: users, posts, pages, cat, tag, media, media_folders, settings, leads, post_view_stats, activity_logs.

**NEXT TASK SUGGESTIONS:**
- **Phase D (Media)**: Thumbnails generation (optimization).
- **Post Scheduling**: Implement accurate scheduling (currently just `published_at` field, needs automated publisher or scope adjustment).
- **Comment System**: Add comments to posts with moderation queue.
- **Newsletter**: Simple subscription form & email list management.
- **Search**: Enhance frontend search (currently basic).

**PROMPT END**

# Mini CMS (Laravel + Blade, No NPM) — Project Status & Handoff (as of 2026-01-28)

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
- **Media Picker Modal**: Reusable component `<x-media-picker>` with:
  - Grid layout, Search, Folder Filter.
  - **Smart Resizing**: Width/Height inputs with auto-calculation (aspect ratio lock).
  - Integration with TinyMCE (auto-inserts `<img>` with dimensions).
  - Integration with Featured Image selection.

### 1.6 SEO Features (Sprint 4) 🚀
- **Sitemap**: `/sitemap.xml` generated dynamically for Posts & Pages.
- **Robots**: `/robots.txt` generated dynamically.
- **Meta Tags**: Canonical URLs and Noindex for non-public pages.

### 1.7 WordPress-Style Editor Layout (Sprint 5) 🎨
- **Posts & Pages**: Redesigned create/edit forms with 2-column layout
  - **Left Column (8/12)**: Title, Permalink, Content (TinyMCE), Excerpt, SEO Settings (collapsible)
  - **Right Sidebar (4/12)**: Publish box, Categories (Posts only), Tags (Posts only), Featured Image
- **Icons**: Material Icons for visual hierarchy (Publish, Categories, Tags, Featured Image)
- **Responsive**: Mobile-friendly (stacks vertically on small screens)

### 1.8 TinyMCE Rich Text Editor (Sprint 5) ✨
- **Integration**: TinyMCE 6 (GPL license) for Posts & Pages content editing
- **Location**: `public/js/tinymce/tinymce.min.js`
- **Configuration**:
  - Height: 600px
  - Plugins: lists, link, code, table, fullscreen, image
  - Toolbar: undo/redo, blocks, bold/italic, lists, link, table, media, code, fullscreen
  - Custom "Media Library" button (currently opens new tab - **needs modal implementation**)

### 1.9 Admin UI Improvements
- **Sidebar**: Dashboard link added. Role-based visibility.
- **Header**: Shows User Badge (ADM/EDT).
- **Layout**: Calm theme, responsive sidebar.
- **Toolbar Consolidation**: Posts & Pages index now have single-row toolbar
  - **Left**: Bulk Actions dropdown + Apply button
  - **Center**: Vertical divider
  - **Right**: Search + Filters + Filter/Clear buttons + Item count

### 1.10 Core Modules
- **Posts**: CRUD, SEO, Tags/Cats, Review Workflow, **View Tracking**, **TinyMCE Editor**.
- **Pages**: CRUD, SEO, **Activity Logging**, **TinyMCE Editor**.
- **Taxonomies**: Categories, Tags.
- **Media**: Library & Uploads & Folders.
- **Leads**: Contact form submissions + Status workflow + **Logging**.
- **Settings**: Site configurations (Global) + **Logging**.

### 1.17 Chat Support Module (Sprint 9, 9.1 & 9.2) 💬
- **Guest Widget**: Floating chat button (bottom-right) on all public pages.
  - First message form (name, optional email, message).
  - **Real-time**: Near-instant message delivery via **SSE** (Server-Sent Events).
  - **Fallback**: Automatically degrades to polling (4s) if connection fails.
  - **Typing Indicators**: "Support is typing..." (Real-time).
  - **Unread Badge**: Red counter on widget toggle when minimized.
  - Token stored in localStorage for session persistence.
- **Admin Inbox**: `/admin/support` - List all conversations with search/filter.
  - Status filter (Open/Pending/Closed).
  - **Notifications**:
    - **Sidebar Badge**: Red counter in sidebar menu.
    - **Global Bell**: Navbar notification with total unread count (polls every 5s).
    - **Table Badge**: Numeric badge (99+ cap) on conversation list.
  - Responsive table/card views.
- **Conversation View**: `/admin/support/{id}` - Chat timeline with reply box.
  - **Real-time**: SSE integration for messages and status updates.
  - **Typing Indicators**: "Visitor is typing..." (Real-time).
  - **Sound**: Ping sound on new message arrival.
  - Status auto-updates (e.g. if visitor replies, status changes to Open instantly).
- **Status Rules**:
  - Visitor message → `open`
  - Agent reply → `pending`
  - Agent can manually set `closed`.
  - Visitor message while closed → auto reopen to `open`.
- **Session Reset**: 72h timeout adds system message "--- New session started ---".
- **Security**:
  - CSRF exempt for public endpoints.
  - Throttle: first-message (100/min), send (120/min), poll (600/min), stream (600/min).
  - Honeypot field protection.
- **Activity Logging**: All key actions logged.

### 1.11 Frontend Redesign
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
- `GET /b/{slug}` → `pagebuilder.show` (Serve static page packages)
- `POST /lead` → `lead.store` (PageBuilder forms, throttle:30,1)
- `POST /support/first-message` → Guest starts chat (throttle:100,1)
- `POST /support/messages` → Guest sends message (throttle:120,1)
- `GET /support/messages` → Guest polls messages (throttle:600,1)
- `GET /support/stream` → SSE Stream (throttle:600,1)
- `POST /support/typing` → Typing Signal (throttle:100,1)

### Auth:
- `GET /login`, `POST /login`, `POST /logout`

### Admin (Shared):
- `dashboard` (`/admin`) - Analytics Home
- `posts`, `pages`, `categories`, `tags`, `leads`, `review`
- `media` (Full resource with folders support + detail view)
- `page-builder` (Upload, List, Show, Activate static HTML packages)

### Admin (Protected - Admin Only):
- `settings`: Index, Update
- `users`: Index, Create, Store, Edit, Update, Toggle

### Admin (Support - Admin/Editor):
- `GET /admin/support` → Inbox list
- `GET /admin/support/unread-count` → Global Badge Poll
- `GET /admin/support/{id}` → Conversation view
- `GET /admin/support/{id}/messages` → Poll new messages
- `GET /admin/support/{id}/stream` → SSE Stream
- `POST /admin/support/{id}/messages` → Agent reply
- `POST /admin/support/{id}/status` → Update status
- `POST /admin/support/{id}/typing` → Typing Signal

---

## 3) Database Schema

### Tables:
- `users` (id, name, email, password, **role**, **is_active**)
- `posts`, `pages` (featured_image_id, meta_title, meta_description, meta_keywords, etc)
- `categories`, `tags`, `pivot_tables`
- `media` (id, path, **folder_id**, **alt_text**, **caption**, **width**, **height**, etc)
- `media_folders` (id, name)
- `settings` (key, value)
- `leads` (name, email, phone, message, status, source)
- `post_view_stats` (post_id, date, views) [Unique(post_id, date)]
- `activity_logs` (user_id, type, subject_id, subject_type, message, **meta**)
- `page_packages` (id, name, slug, zip_path, public_dir, version, entry_file, is_active, wire_contact, wire_selector, created_by, timestamps)
- `support_conversations` (id, visitor_token, name, email, status, assigned_to, last_message_at, source_url, referrer, meta)
- `support_messages` (id, conversation_id, sender_type, user_id, message, read_at, timestamps)

---

## 4) Code/Folder Notes

- **Helpers**: `app/helpers.php` contains `setting()`, `setting_set()`, and `activity_log()`.
- **Models**: `PostViewStat` (Analytic), `ActivityLog` (History), `Media`, `MediaFolder`, `PagePackage`, `SupportConversation`, `SupportMessage`.
- **Services**: `ZipExtractService` (safe ZIP extraction), `PublishService` (publish to public + inject JS).
- **Controllers**: `PublicSupportController` (guest endpoints), `Admin\SupportController` (admin inbox).
- **Migrations**: Latest include `create_post_view_stats`, `create_activity_logs`, `add_metadata_to_media_table`, `create_media_folders_table`, `create_page_packages_table`, `create_support_conversations_table`, `create_support_messages_table`.
- **Views**: 
  - `resources/views/admin/media/index.blade.php` (Sidebar + Grid)
  - `resources/views/admin/media/show.blade.php` (Detail view)
  - `resources/views/admin/posts/_form.blade.php` (WordPress-style layout + TinyMCE)
  - `resources/views/admin/pages/_form.blade.php` (WordPress-style layout + TinyMCE)
  - `resources/views/admin/page-builder/` (index, create, show)
- **TinyMCE**: `public/js/tinymce/tinymce.min.js` (GPL license)
- **Public Assets**: `public/pagebuilder/{slug}/{version}/` (Published static sites)
- **Support Views**: `resources/views/admin/support/` (index, show)
- **Chat Widget**: `resources/views/components/site/support-widget.blade.php`

---

## 5) Known Issues & Next Steps

### 1.12 Media Picker Modal (Sprint 6) ✅
- **Component**: Reusable `<x-media-picker>` modal.
- **Features**:
  - **Smart Resize**: Auto-calculates height from width (and vice versa) using aspect ratio.
  - **Legacy Support**: JS auto-detects dimensions for old images without DB metadata.
  - **Integration**: Works seamlessly with TinyMCE and Featured Image selection.

### 1.13 Frontend Search (Sprint 6) 🔍
- **Route**: `/search?q=...`
- **Logic**: Searches Title (priority), Excerpt, Content.
- **Features**:
  - **Highlighting**: Keyword matches highlighted in yellow in Title and Snippets.
  - **Snippets**: Auto-generated text window around keyword.
  - **Header**: Integrated search bar in site navigation.
  - **Security**: Only shows published posts.

  - **Security**: Only shows published posts.

### 1.14 Frontend SEO & Enhancements (Sprint 6) 🚀
- **Breadcrumbs**: Reusable component `<x-breadcrumbs>` integrated into Post/Page views.
- **Related Posts**: Advanced logic (Tags > Categories > Latest) ensuring 4 items always show.
- **Schema.org**: JSON-LD (Article/WebPage) injected via Controller/Layout.

### 1.15 Page Builder (Sprint 7) 🎨
- **Upload & Publish**: Admin can upload ZIP files containing static HTML/CSS/JS sites.
- **Safe Extraction**: Comprehensive security (MIME validation, extension allowlist/blocklist, path traversal prevention, size limits).
- **Contact Form Wiring**: Automatic JavaScript injection to connect static forms to CMS leads.
- **Public Serving**: Static sites served at `/b/{slug}` with version management.
- **Lead Integration**: Public endpoint `/lead` (no CSRF) with rate limiting + honeypot protection.
- **Admin UI**: List, Upload, Detail views with status badges (Active, Wired).
- **Activity Logging**: All package actions logged with metadata.

### 1.16 Production Readiness (Sprint 8) 📦
- **Web Installer**: 4-step installation wizard at `/install`:
  - Step 1: Server requirements check (PHP, extensions, writable dirs)
  - Step 2: Database configuration with connection test
  - Step 3: Admin account and site settings creation
  - Step 4: Complete with credentials display
  - Auto-generates `.env` file with APP_KEY
  - Locks installer after completion (`storage/installed`)
- **Documentation**:
  - `README.md`: Product-focused overview
  - `INSTALL.md`: Detailed installation guide (shared hosting, VPS)
  - `CHANGELOG.md`: Version history
  - `LICENSE.md`: Commercial license terms
- **Build System**: PowerShell script `build.ps1` creates release ZIP


### 📋 **Planned Features**
- **Phase D (Media)**: Thumbnails generation (optimization)
- **Post Scheduling**: Implement accurate scheduling (currently just `published_at` field)
- **Comment System**: Add comments to posts with moderation queue
- **Newsletter**: Simple subscription form & email list management
- **Comment System**: Add comments to posts with moderation queue
- **Newsletter**: Simple subscription form & email list management


---

## 6) Handoff Prompt for Another AI

**PROMPT START**

You are continuing a Laravel mini CMS project. Constraints:
- Laravel + MySQL (Laragon Windows)
- Blade only, Tailwind via CDN (NO npm, NO Vite)
- Custom auth (no Breeze)
- UI: "Minimal Japanese / SaaS" aesthetic for Frontend, "Calm Admin" for Backend.

**Current implemented modules:**
- **Core**: Posts & Pages (CRUD, SEO, Soft Deletes, **TinyMCE Editor**, **WordPress-style Layout**).
- **Analytics**: Dashboard with Chart.js, KPI cards, Post View tracking (`post_view_stats`).
- **Activity**: System-wide logging (`activity_logs` + `meta`) for audit trail.
- **Media**: 
  - **Library**: Folders (Sidebar), Search, Pagination.
  - **Safety**: Prevent delete if in use.
  - **Metadata**: Alt/Caption/Dimensions.
  - **Details**: Dedicated view for management.
  - **Picker**: **Reusable Modal** with Smart Resizing for TinyMCE & Featured Image.
- **SEO**: Sitemap, Robots, Canonical.
- **Taxonomies**: Categories & Tags.
- **Settings**: Site-wide config via `setting()` helper.
- **Leads**: Contact form & Admin management.
- **User Management**: RBAC (Admin/Editor), User CRUD.
- **Frontend**: Full redesign complete. Minimalist.
  - **Search**: Advanced search with highlighting & snippets.
- **Editor**: TinyMCE 6 integrated (GPL) at `public/js/tinymce/`.
- **Page Builder**: Upload static HTML sites (ZIP), auto-inject contact forms, serve at `/b/{slug}`.
  - **Security**: Safe extraction (allowlist, blocklist, path traversal prevention, size limits).
  - **Lead Integration**: Public endpoint `/lead` with rate limiting + honeypot.
- **Chat Support**: Real-time (SSE) Guest/Admin chat, **Typing Indicators**, **Unread Badges**, **Sound Notifications**, **10x Scalable Limits**.

- Tables included: users, posts, pages, categories, tags, media, media_folders, settings, leads, post_view_stats, activity_logs, page_packages, support_conversations, support_messages.

**IMMEDIATE NEXT TASK:**
- **Phase D (Media)**: Thumbnails generation (optimization).
- **Post Scheduling**: Implement accurate scheduling.

**OTHER SUGGESTIONS:**
- **Phase D (Media)**: Thumbnails generation (optimization).
- **Post Scheduling**: Implement accurate scheduling.
- **Comment System**: Add comments to posts with moderation queue.
- **Newsletter**: Simple subscription form & email list management.

**PROMPT END**

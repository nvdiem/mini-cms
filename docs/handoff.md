  Mini CMS (Laravel + Blade, No NPM) — Project Status & Handoff (Master)

  As of: 2026-01-31

  0) Environment & Constraints

  Stack: Laravel + MySQL (Laragon Windows)

  Views: Blade only

  Frontend: Tailwind via CDN (no Vite, no npm)

  Auth: Custom (no Breeze)

  UI Base: mini_cms_templates_v2 (calm theme tokens, admin layout, responsive list)

  Must keep: confirm modal, toast + undo, mobile card view, calm colors for long usage

  1) Implemented Features (Done)
  1.1 Authentication & Roles

  Roles: admin (full), editor (content only)

  is_active gate (disabled users cannot login)

  Admin middleware protects sensitive routes

  Demo users:

  admin@local.test / 123456

  editor@local.test / 123456

  1.2 User Management (Admin only)

  CRUD: /admin/users

  Security: admin cannot disable self

  UI badges for role/status

  1.3 Analytics Dashboard

  /admin SaaS-style dashboard

  Chart.js: Daily Views vs Leads (7/14/30 days)

  KPI cards + Activity feed + Top content table

  1.4 Activity Logging

  activity_logs table with meta JSON

  Helper activity_log() auto-detects user/subject + generates links

  Integrated for: posts/pages/leads/settings (incl bulk/restore/publish actions)

  1.5 Media Module (Phases A/B/C)

  Safe delete: prevent removing media used in posts/pages

  Metadata: alt_text, caption, width, height

  Media detail page: /admin/media/{id}

  Folders with sidebar counts (counts respect current search)

  Upload captures image dimensions

  Reusable <x-media-picker> modal:

  search + folder filter + grid

  smart resizing (aspect ratio lock)

  integrates with TinyMCE + featured image

  1.6 SEO

  /sitemap.xml dynamic for posts/pages

  /robots.txt dynamic

  Canonical + Noindex for non-public pages

  1.7 Editor UI (WordPress-style)

  Posts/Pages create/edit: 2-column layout

  Right sidebar: publish box, taxonomy (posts), featured image

  Responsive on mobile

  1.8 TinyMCE 6

  Location: public/js/tinymce/tinymce.min.js (GPL)

  Plugins: lists, link, code, table, fullscreen, image

  Toolbar: undo/redo, blocks, bold/italic, lists, link, table, media, code, fullscreen

  1.9 Core Modules

  Posts: CRUD, SEO, tags/cats, review workflow, view tracking (post_view_stats)

  Pages: CRUD, SEO, logging

  Taxonomies: categories, tags

  Leads: public submit + admin management (status workflow + logging)

  Settings: global config via setting()/setting_set() + logging

  Page Builder: upload ZIP static sites, safe extract, auto-wire contact forms, public serve /b/{slug}

  2) Chat Support Module (Sprints 9 / 9.1 / 9.2) 💬
  2.1 Core UX

  Guest floating widget on all public pages

  First message: name + optional email + message

  Session persistence: visitor_token in localStorage

  72h idle: system message --- New session started ---

  2.2 Realtime & Reliability

  Primary: Pusher Channels (WebSocket via SaaS)

  Fallback: polling (4s) if Pusher connection fails

  Events:
    - SupportMessageCreated: broadcast on message create
    - SupportTyping: broadcast typing indicators

  Channel: support.conversation.{conversation_id} (public)

  Config: BROADCAST_DRIVER=pusher in .env (requires PUSHER_APP_ID, KEY, SECRET, CLUSTER)

  2.3 Message Types (IMPORTANT — keep consistent)

  support_messages.sender_type values used in logic:

  visitor (guest)

  agent (admin/editor)

  system (session reset markers)

  Unread logic depends on these values.

  2.4 Unread System (read_at)

  Each message has read_at

  Admin unread: visitor messages where read_at IS NULL

  Guest unread: agent messages where read_at IS NULL

  Mark-read rules:

  Admin opens /admin/support/{id} → mark visitor msgs read

  Guest opens widget → mark agent msgs read

  2.5 Typing Indicators (No WebSocket)

  Guest typing → cache key support:typing:guest:{conversation_id}

  Admin typing → cache key support:typing:admin:{conversation_id}

  typing=true if timestamp within last 3 seconds

  2.6 Notifications & UI (Admin)

  Inbox /admin/support:

  per-conversation numeric badge (99+ cap)

  Sidebar “Support” menu:

  subtle red dot if total unread > 0 (no number)

  Navbar bell:

  numeric total unread badge (99+ cap)

  title sync (n) ... and sound ping on new message (audio unlock pattern)

  2.7 Status Rules

  Visitor message → open

  Agent reply → pending

  Agent can set closed

  Visitor message while closed → auto reopen open

  2.8 E-commerce Module (Phase 1 MVP) 🛒
  2.8.1 Architecture & Models
  - Products: title, slug, description (TinyMCE), SEO, featured_image_id.
  - Options & Variants: Up to 3 options (e.g. Size, Color). Cartesian product generation.
  - Inventory: variant level stock_qty with transaction safety (lockForUpdate).
  - Orders: Unique order_no (ORD-YYYYMMDD-XXXXX), status workflow.
  - Snapshots: items table stores price/title/signature at time of order.

  2.8.2 Cart & Checkout
  - Cart: Session-based (`shop_cart`). Stock clamping on add/update.
  - Checkout: COD only. Note/Address/Phone validation.
  - Business Rules: Soft deletes on products/variants. Active/Published gates.

  2.8.3 Admin UI
  - Products Index: Filters (status, stock), mobile results.
  - Product Editor: 2-column layout, options builder, variant matrix (inline edit).
  - Orders: Status transitions (new → confirmed → packed → shipped → completed).
  - Shop Settings: Fixed shipping fee, COD instructions.

  3) Current Routes (web.php)
  Frontend

  GET / → site.home

  GET /posts/{slug} → post show (unique daily view tracking)

  GET /p/{slug} → page show

  GET /contact, POST /contact

  GET /sitemap.xml, GET /robots.txt

  GET /b/{slug}/{path?} → pagebuilder serve

  POST /lead → pagebuilder leads

  Public Shop
  GET /shop → catalog list
  GET /shop/{slug} → product detail
  GET /cart → cart view
  POST /cart/add, POST /cart/update, POST /cart/remove
  GET /checkout, POST /checkout
  GET /order/{order_no}/thank-you


  Public Support

  POST /support/first-message

  POST /support/messages

  GET /support/messages (poll)

  GET /support/stream (SSE)

  POST /support/typing

  POST /support/mark-read ✅

  Admin Support (Admin/Editor)

  GET /admin/support

  GET /admin/support/unread-count (lightweight poll for global badge)

  GET /admin/support/{id}

  GET /admin/support/{id}/messages (poll)

  GET /admin/support/{id}/stream (SSE)

  POST /admin/support/{id}/messages (reply)

  POST /admin/support/{id}/status

  POST /admin/support/{id}/typing

  POST /admin/support/{id}/mark-read ✅

  4) Database Schema (Key Tables)

  users (role, is_active)

  posts, pages (SEO fields, featured_image_id)

  media, media_folders

  leads

  post_view_stats (unique per post/day)

  activity_logs (meta JSON)

  page_packages

  support_conversations

  support_messages (sender_type, read_at)

  products, product_options, product_option_values
  product_variants, product_variant_values
  orders, order_items


  5) Code / Folder Notes

  Helpers: app/helpers.php → setting(), setting_set(), activity_log()

  Controllers:

  PublicSupportController

  Admin\SupportController

  Views:

  Admin support: resources/views/admin/support/index.blade.php, show.blade.php

  Guest widget: resources/views/components/site/support-widget.blade.php

  TinyMCE: public/js/tinymce/tinymce.min.js

  6) Throttling Notes

  Current limits may be tuned for test/staging. For production (shared hosting) consider lowering:

  first-message: 10/min

  send: 12/min

  poll: 60/min

  stream: 60/min
  Typing: 10–30/min is sufficient.

  7) Regression Checklist (Chat Support)

  SSE keeps alive > 5 minutes (heartbeat)

  Network drop + reconnect → missed messages delivered (Last-Event-ID resume)

  Unread counts correct for admin & guest (read_at, mark-read)

  Typing works both directions (<=3s window)

  Audio ping only after user interaction (no autoplay errors)

  Title (n) sync updates immediately on mark-read

  9) Project Status
  - **Core Features**: Complete & Stable.
  - **Chat Support**: Complete (Sprint 9.2).
  - **E-commerce**: Phase 1 MVP Complete (+ Đã có bộ dữ liệu QA qua `ShopSeeder`).
  - **Next Steps**: Maintenance & stability.


  9) Handoff Prompt (for another AI)

  PROMPT START
  You are continuing a Laravel mini CMS project. Constraints:

  Laravel + MySQL (Laragon Windows)

  Blade only, Tailwind via CDN (NO npm, NO Vite)

  Custom auth (no Breeze)

  UI: Minimal Japanese/SaaS frontend, Calm Admin backend.

  Implemented modules:

  Posts/Pages CRUD + SEO + Soft deletes + TinyMCE

  Analytics dashboard (Chart.js)

  Activity logs with meta JSON

  Media library + folders + safe delete + metadata + media picker modal

  Leads + settings + user management

  Page Builder (ZIP upload, safe extract, serve static sites)

  E-commerce (Phase 1):
  - Product/Variant CRUD + Cartesian generation + Stock management
  - Session-based cart + COD Checkout
  - Order workflow (new → confirmed → packed → shipped → completed)
  - Activity logging for all shop actions
  - QA Test Data: Run `php artisan db:seed --class=ShopSeeder` to populate variants and active orders matrix.

  Chat Support:


  SSE real-time + polling fallback

  Last-Event-ID resume + heartbeat

  Unread via support_messages.read_at + mark-read endpoints

  Typing indicators via cache keys

  Admin UI notifications: sidebar dot, navbar bell badge, per-conversation badges, sound (audio unlock) + title sync.

  Current State:
  Project core features are complete. Focus is now on maintenance and stability.

  SCOPE NOTE (IMPORTANT):
  - Do NOT implement or suggest: media thumbnails, post scheduling, comments, newsletter.
  - Only work on tasks explicitly requested by the project owner.
  PROMPT END
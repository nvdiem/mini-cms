# Changelog

All notable changes to Mini CMS will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).

---

## [1.0.0] - 2026-01-25

### Added

#### Core Features
- Posts & Pages with CRUD, SEO meta, soft deletes
- WordPress-style editor layout (2-column)
- TinyMCE 6 rich text editor (GPL license)
- Categories & Tags taxonomy system
- Media Library with folders, search, metadata
- Media Picker modal with smart image resizing
- Safe delete prevention (media in use)

#### Page Builder
- Upload ZIP packages containing static HTML/CSS/JS sites
- Safe extraction with security validations
- Automatic contact form wiring (JS injection)
- Public serving at `/b/{slug}`
- Lead integration with rate limiting

#### Leads & Contact
- Contact form with Lead management
- Lead status workflow (New → Handled/Spam)
- Activity logging for all Lead actions
- Public `/lead` endpoint for PageBuilder forms

#### Analytics & Dashboard
- Dashboard with Chart.js visualizations
- Daily views vs leads chart (7/14/30 days)
- KPI cards (Views, Leads, Content stats)
- Activity feed with clickable links
- Top performing posts table
- Post view tracking (unique daily)

#### User Management
- Role-based access (Admin/Editor)
- User CRUD with status toggle
- Active/disabled user enforcement

#### SEO
- Dynamic sitemap.xml
- Dynamic robots.txt
- Canonical URLs
- Schema.org JSON-LD (Article/WebPage)
- Breadcrumbs component

#### Frontend
- Minimalist design (Slate-50 palette)
- Hero, Featured Post, Grid layouts
- Advanced search with highlighting
- Mobile responsive

#### Admin UI
- Calm theme with dark mode
- Toast notifications with undo
- Bulk actions
- Responsive sidebar

#### System
- Web installer wizard (4 steps)
- Activity logging system
- Settings management
- Custom auth (no Breeze)

### Security
- CSRF protection (except public lead endpoint)
- Rate limiting on login/contact
- Honeypot for spam prevention
- Safe ZIP extraction (path traversal prevention)
- Extension allowlist/blocklist

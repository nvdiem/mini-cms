You are continuing a Laravel mini CMS project. I uploaded a zip of the project source (no vendor, no node_modules).

CONSTRAINTS
- Laravel + MySQL (Laragon Windows)
- Blade only, Tailwind via CDN (NO npm, NO Vite)
- Custom auth (no Breeze)
- Keep calm long-session-friendly UI
- Use existing patterns: toast notifications + confirm modal + responsive (desktop table + mobile card view)
- Do not refactor widely; prefer minimal diffs.

WHAT’S ALREADY DONE (CURRENT STATE)
- Admin modules: Posts (CRUD, soft delete+restore, bulk actions, preview), Pages, Review Queue (quick publish), Tags, Categories, Media (upload/library/delete), Leads, Settings, Users+Roles (admin/editor).
- Frontend: Minimal Japanese + SaaS CTA, homepage + post detail + contact + pages; demo seed data exists.
- Analytics Dashboard “SaaS feel”: KPI cards + chart via Chart.js CDN, top posts, recent activity concept.

KNOWN ISSUES / GAPS (IMPORTANT)
- Media improvements: 
  - Upload is partial (missing dimensions)
  - Delete must be safe (block deleting media if used by posts/pages)
  - No media detail/edit view (alt_text, caption, copy URL)
  - No orphans tool
- I now want to implement “Media folders (1-level) + sidebar folder list + count badges” in /admin/media.

YOUR TASK
1) Read the project code from the uploaded zip and confirm:
   - Current routes for /admin/media
   - MediaController and Media model fields
   - Database schema/migrations for media
2) Implement Media Folders (1-level):
   - Add media_folders table + media.folder_id (nullable, FK set null on delete)
   - Add CRUD folders (create/rename/delete) and “move media to folder” (bulk)
   - Add folder filter via query string: folder=none or folder={id}
3) Add Sidebar folder list + count badges in /admin/media:
   - All Media, Unsorted, each folder with count badge
   - Active state highlight
   - Preserve search query when clicking
4) Output patch-style changes (full file contents, migrations, routes)
5) Provide commands to run (migrate, view clear)

DELIVERABLE FORMAT
- A short summary
- A file-by-file patch list with full contents
- Any notes for Laragon Windows (storage:link etc.)

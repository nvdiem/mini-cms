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
Implement Pages + Review Queue (quick publish) + Tags:
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

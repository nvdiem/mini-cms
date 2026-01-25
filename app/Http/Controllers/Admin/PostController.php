<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Category;
use App\Models\Media;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $status = $request->query('status', '');
        $trash = $request->query('trash', '');
        $tagSlug = $request->query('tag', '');

        $query = Post::query()
            ->with(['author','categories','featuredImage','tags'])
            ->withCount('categories');

        if ($trash === '1') {
            $query->onlyTrashed();
        }

        if ($q !== '') {
            $query->where(function($sub) use ($q) {
                $sub->where('title', 'like', '%' . $q . '%')
                    ->orWhere('slug', 'like', '%' . $q . '%');
            });
        }

        if (in_array($status, ['draft','review','published'], true)) {
            $query->where('status', $status);
        }

        if ($tagSlug !== '') {
            $query->whereHas('tags', function($q) use ($tagSlug) {
                $q->where('slug', $tagSlug);
            });
        }

        $posts = $query->orderByDesc('updated_at')->paginate(10)->withQueryString();

        $tags = Tag::orderBy('name')->get();

        return view('admin.posts.index', [
            'posts' => $posts,
            'q' => $q,
            'status' => $status,
            'trash' => $trash,
            'tagSlug' => $tagSlug,
            'tags' => $tags,
        ]);
    }

    public function create()
    {
        $post = new Post(['status' => 'draft']);
        $categories = Category::orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();
        $media = Media::orderByDesc('id')->limit(50)->get();
        
        // For media picker modal
        $mediaFolders = \App\Models\MediaFolder::orderBy('name')->get();
        $allMedia = Media::orderByDesc('id')->get();

        return view('admin.posts.create', compact('post','categories','tags','media','mediaFolders','allMedia'));
    }

    public function store(Request $request)
    {
        $data = $this->validatePost($request);

        $post = new Post();
        $post->title = $data['title'];
        $post->slug = $this->uniqueSlug($data['slug'] ?: Str::slug($data['title']));
        $post->excerpt = $data['excerpt'] ?? null;
        // PR-03: Sanitize HTML content to prevent XSS
        $post->content = app(\App\Services\HtmlSanitizerService::class)->sanitize($data['content'] ?? '');
        $post->status = $data['status'];
        $post->published_at = $data['published_at'] ?? null;
        $post->author_id = Auth::id();
        $post->featured_image_id = $data['featured_image_id'] ?? null;
        $post->meta_title = $data['meta_title'] ?: $data['title'];
        $post->meta_description = $data['meta_description'] ?: ($data['excerpt'] ?: Str::limit(strip_tags($data['content'] ?? ''), 160));
        $post->meta_keywords = $data['meta_keywords'] ?? null;
        $post->save();

        $post->categories()->sync($data['category_ids'] ?? []);
        $post->tags()->sync($data['tag_ids'] ?? []);

        activity_log('created', $post, "Created post '{$post->title}'");

        return redirect()
            ->route('admin.posts.edit', $post)
            ->with('toast', ['tone' => 'success', 'title' => 'Saved', 'message' => 'Post created successfully.']);
    }

    public function edit(Post $post)
    {
        $post->load(['categories','featuredImage','tags']);
        $categories = Category::orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();
        $media = Media::orderByDesc('id')->limit(50)->get();

        // For media picker modal
        $mediaFolders = \App\Models\MediaFolder::orderBy('name')->get();
        $allMedia = Media::orderByDesc('id')->get();

        return view('admin.posts.edit', compact('post','categories','tags','media','mediaFolders','allMedia'));
    }

    public function update(Request $request, Post $post)
    {
        $data = $this->validatePost($request, $post->id);

        $post->title = $data['title'];
        $post->slug = $this->uniqueSlug($data['slug'] ?: Str::slug($data['title']), $post->id);
        $post->excerpt = $data['excerpt'] ?? null;
        // PR-03: Sanitize HTML content to prevent XSS
        $post->content = app(\App\Services\HtmlSanitizerService::class)->sanitize($data['content'] ?? '');
        $post->status = $data['status'];
        $post->published_at = $data['published_at'] ?? null;
        $post->featured_image_id = $data['featured_image_id'] ?? null;
        $post->meta_title = $data['meta_title'] ?: $data['title'];
        $post->meta_description = $data['meta_description'] ?: ($data['excerpt'] ?: Str::limit(strip_tags($data['content'] ?? ''), 160));
        $post->meta_keywords = $data['meta_keywords'] ?? null;
        $post->save();

        $post->categories()->sync($data['category_ids'] ?? []);
        $post->tags()->sync($data['tag_ids'] ?? []);

        activity_log('updated', $post, "Updated post '{$post->title}'");

        return back()->with('toast', ['tone' => 'success', 'title' => 'Saved', 'message' => 'Changes saved.']);
    }

    public function preview(Post $post)
    {
        $post->load(['author','categories','featuredImage','tags']);
        return view('site.post', [
            'post' => $post,
            'isPreview' => true,
            'backUrl' => route('admin.posts.edit', $post),
        ]);
    }

    public function destroy(Post $post)
    {
        $post->delete();
        activity_log('trashed', $post, "Moved post '{$post->title}' to trash");

        return redirect()
            ->route('admin.posts.index')
            ->with('toast', [
                'tone' => 'danger',
                'title' => 'Moved to trash',
                'message' => 'Post was moved to trash.',
                'undo' => route('admin.posts.restore', $post->id),
            ]);
    }

    public function restore($id)
    {
        $post = Post::withTrashed()->findOrFail($id);
        $post->restore();
        activity_log('restored', $post, "Restored post '{$post->title}'");

        return redirect()
            ->route('admin.posts.index')
            ->with('toast', ['tone' => 'success', 'title' => 'Restored', 'message' => 'Post restored.']);
    }

    public function bulk(Request $request)
    {
        $action = $request->input('action');
        $ids = $request->input('ids', []);

        if (!is_array($ids) || count($ids) === 0) {
            return back()->with('toast', ['tone' => 'danger', 'title' => 'No selection', 'message' => 'Select at least one item.']);
        }

        if ($action === 'delete') {
            Post::whereIn('id', $ids)->delete();
            activity_log('bulk_trash', null, "Moved " . count($ids) . " posts to trash");
            return back()->with('toast', ['tone' => 'danger', 'title' => 'Moved to trash', 'message' => 'Selected posts moved to trash.']);
        }

        if ($action === 'restore') {
            Post::withTrashed()->whereIn('id', $ids)->restore();
            activity_log('bulk_restore', null, "Restored " . count($ids) . " posts");
            return back()->with('toast', ['tone' => 'success', 'title' => 'Restored', 'message' => 'Selected posts restored.']);
        }

        return back()->with('toast', ['tone' => 'danger', 'title' => 'Invalid action', 'message' => 'Please choose a valid bulk action.']);
    }

    private function validatePost(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'status' => ['required', 'in:draft,review,published'],
            'published_at' => ['nullable', 'date'],
            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => ['integer'],
            'tag_ids' => ['nullable', 'array'],
            'tag_ids.*' => ['integer'],
            'featured_image_id' => ['nullable', 'integer'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
        ]);
    }

    private function uniqueSlug(string $slug, ?int $ignoreId = null): string
    {
        $base = $slug ?: Str::random(8);
        $candidate = $base;
        $i = 2;

        while (
            Post::withTrashed()
                ->where('slug', $candidate)
                ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $candidate = $base . '-' . $i;
            $i++;
        }

        return $candidate;
    }
}

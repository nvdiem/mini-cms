<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class PageController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $status = $request->query('status', '');
        $trash = $request->query('trash', '');

        $query = Page::query()
            ->with(['author','featuredImage']);

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

        $pages = $query->orderByDesc('updated_at')->paginate(10)->withQueryString();

        return view('admin.pages.index', [
            'pages' => $pages,
            'q' => $q,
            'status' => $status,
            'trash' => $trash,
        ]);
    }

    public function create()
    {
        $page = new Page(['status' => 'draft']);
        $media = Media::orderByDesc('id')->limit(50)->get();

        return view('admin.pages.create', compact('page','media'));
    }

    public function store(Request $request)
    {
        $data = $this->validatePage($request);

        $page = new Page();
        $page->title = $data['title'];
        $page->slug = $this->uniqueSlug($data['slug'] ?: Str::slug($data['title']));
        $page->excerpt = $data['excerpt'] ?? null;
        $page->content = $data['content'] ?? '';
        $page->status = $data['status'];
        $page->published_at = $data['published_at'] ?? null;
        $page->author_id = Auth::id();
        $page->featured_image_id = $data['featured_image_id'] ?? null;
        $page->save();

        return redirect()
            ->route('admin.pages.edit', $page)
            ->with('toast', ['tone' => 'success', 'title' => 'Saved', 'message' => 'Page created successfully.']);
    }

    public function edit(Page $page)
    {
        $page->load(['featuredImage']);
        $media = Media::orderByDesc('id')->limit(50)->get();

        return view('admin.pages.edit', compact('page','media'));
    }

    public function update(Request $request, Page $page)
    {
        $data = $this->validatePage($request, $page->id);

        $page->title = $data['title'];
        $page->slug = $this->uniqueSlug($data['slug'] ?: Str::slug($data['title']), $page->id);
        $page->excerpt = $data['excerpt'] ?? null;
        $page->content = $data['content'] ?? '';
        $page->status = $data['status'];
        $page->published_at = $data['published_at'] ?? null;
        $page->featured_image_id = $data['featured_image_id'] ?? null;
        $page->save();

        return back()->with('toast', ['tone' => 'success', 'title' => 'Saved', 'message' => 'Changes saved.']);
    }

    public function preview(Page $page)
    {
        $page->load(['author','featuredImage']);
        return view('site.page', [
            'page' => $page,
            'isPreview' => true,
            'backUrl' => route('admin.pages.edit', $page),
        ]);
    }

    public function destroy(Page $page)
    {
        $page->delete();

        return redirect()
            ->route('admin.pages.index')
            ->with('toast', [
                'tone' => 'danger',
                'title' => 'Moved to trash',
                'message' => 'Page was moved to trash.',
                'undo' => route('admin.pages.restore', $page->id),
            ]);
    }

    public function restore($id)
    {
        $page = Page::withTrashed()->findOrFail($id);
        $page->restore();

        return redirect()
            ->route('admin.pages.index')
            ->with('toast', ['tone' => 'success', 'title' => 'Restored', 'message' => 'Page restored.']);
    }

    public function bulk(Request $request)
    {
        $action = $request->input('action');
        $ids = $request->input('ids', []);

        if (!is_array($ids) || count($ids) === 0) {
            return back()->with('toast', ['tone' => 'danger', 'title' => 'No selection', 'message' => 'Select at least one item.']);
        }

        if ($action === 'delete') {
            Page::whereIn('id', $ids)->delete();
            return back()->with('toast', ['tone' => 'danger', 'title' => 'Moved to trash', 'message' => 'Selected pages moved to trash.']);
        }

        if ($action === 'restore') {
            Page::withTrashed()->whereIn('id', $ids)->restore();
            return back()->with('toast', ['tone' => 'success', 'title' => 'Restored', 'message' => 'Selected pages restored.']);
        }

        return back()->with('toast', ['tone' => 'danger', 'title' => 'Invalid action', 'message' => 'Please choose a valid bulk action.']);
    }

    private function validatePage(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'status' => ['required', 'in:draft,review,published'],
            'published_at' => ['nullable', 'date'],
            'featured_image_id' => ['nullable', 'integer'],
        ]);
    }

    private function uniqueSlug(string $slug, ?int $ignoreId = null): string
    {
        $base = $slug ?: Str::random(8);
        $candidate = $base;
        $i = 2;

        while (
            Page::withTrashed()
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

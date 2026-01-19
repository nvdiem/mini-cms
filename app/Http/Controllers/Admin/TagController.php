<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TagController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $tags = Tag::query()
            ->withCount('posts')
            ->when($q !== '', fn($qr) => $qr->where('name','like',"%{$q}%")->orWhere('slug','like',"%{$q}%"))
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('admin.tags.index', compact('tags','q'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:100'],
            'slug' => ['nullable','string','max:120'],
        ]);

        $slug = $data['slug'] ?: Str::slug($data['name']);
        $slug = $this->uniqueSlug($slug);

        Tag::create(['name' => $data['name'], 'slug' => $slug]);

        return back()->with('toast', ['tone'=>'success','title'=>'Saved','message'=>'Tag created.']);
    }

    public function update(Request $request, Tag $tag)
    {
        $data = $request->validate([
            'name' => ['required','string','max:100'],
            'slug' => ['nullable','string','max:120'],
        ]);

        $slug = $data['slug'] ?: Str::slug($data['name']);
        $tag->name = $data['name'];
        $tag->slug = $this->uniqueSlug($slug, $tag->id);
        $tag->save();

        return back()->with('toast', ['tone'=>'success','title'=>'Saved','message'=>'Tag updated.']);
    }

    public function destroy(Tag $tag)
    {
        $tag->delete();
        return back()->with('toast', ['tone'=>'danger','title'=>'Deleted','message'=>'Tag deleted.']);
    }

    private function uniqueSlug(string $slug, ?int $ignoreId = null): string
    {
        $base = $slug ?: Str::random(8);
        $candidate = $base;
        $i = 2;

        while (
            Tag::where('slug', $candidate)
                ->when($ignoreId, fn($q) => $q->where('id','!=',$ignoreId))
                ->exists()
        ) {
            $candidate = $base . '-' . $i;
            $i++;
        }

        return $candidate;
    }
}

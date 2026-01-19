<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $categories = Category::query()
            ->when($q !== '', fn($qr) => $qr->where('name','like',"%{$q}%")->orWhere('slug','like',"%{$q}%"))
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('admin.categories.index', compact('categories','q'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:100'],
            'slug' => ['nullable','string','max:120'],
        ]);

        $slug = $data['slug'] ?: Str::slug($data['name']);
        $slug = $this->uniqueSlug($slug);

        Category::create(['name' => $data['name'], 'slug' => $slug]);

        return back()->with('toast', ['tone'=>'success','title'=>'Saved','message'=>'Category created.']);
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => ['required','string','max:100'],
            'slug' => ['nullable','string','max:120'],
        ]);

        $slug = $data['slug'] ?: Str::slug($data['name']);
        $category->name = $data['name'];
        $category->slug = $this->uniqueSlug($slug, $category->id);
        $category->save();

        return back()->with('toast', ['tone'=>'success','title'=>'Saved','message'=>'Category updated.']);
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return back()->with('toast', ['tone'=>'danger','title'=>'Deleted','message'=>'Category deleted.']);
    }

    private function uniqueSlug(string $slug, ?int $ignoreId = null): string
    {
        $base = $slug ?: Str::random(8);
        $candidate = $base;
        $i = 2;

        while (
            Category::where('slug', $candidate)
                ->when($ignoreId, fn($q) => $q->where('id','!=',$ignoreId))
                ->exists()
        ) {
            $candidate = $base . '-' . $i;
            $i++;
        }

        return $candidate;
    }
}

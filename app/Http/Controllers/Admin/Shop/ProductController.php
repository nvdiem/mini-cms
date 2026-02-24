<?php

namespace App\Http\Controllers\Admin\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::withTrashed()->with('featuredImage')
            ->withCount(['variants', 'activeVariants']);

        // Search
        if ($s = $request->input('search')) {
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                  ->orWhere('slug', 'like', "%{$s}%");
            });
        }

        // Status filter
        if ($request->input('status') === 'active') {
            $query->where('is_active', true)->whereNull('deleted_at');
        } elseif ($request->input('status') === 'inactive') {
            $query->where('is_active', false)->whereNull('deleted_at');
        } elseif ($request->input('status') === 'trashed') {
            $query->whereNotNull('deleted_at');
        } else {
            $query->whereNull('deleted_at');
        }

        // Out of stock filter
        if ($request->boolean('out_of_stock')) {
            $query->whereHas('variants', function ($q) {
                $q->where('is_active', true)->where('stock_qty', '<=', 0);
            });
        }

        $products = $query->latest()->paginate(20)->appends($request->query());

        return view('admin.shop.products.index', compact('products'));
    }

    public function create()
    {
        $media = Media::latest()->take(50)->get();
        return view('admin.shop.products.create', compact('media'));
    }

    public function store(Request $request)
    {
        $data = $this->validateProduct($request);

        $product = new Product();
        $product->title              = $data['title'];
        $product->slug               = $this->uniqueSlug($data['slug'] ?: Str::slug($data['title']));
        $product->excerpt            = $data['excerpt'] ?? null;
        $product->description_html   = $data['description_html'] ?? '';
        $product->is_active          = $request->boolean('is_active');
        $product->published_at       = $data['published_at'] ?? null;
        $product->featured_image_id  = $data['featured_image_id'] ?? null;
        $product->seo_title          = $data['seo_title'] ?: $data['title'];
        $product->seo_description    = $data['seo_description'] ?? null;
        $product->canonical_url      = $data['canonical_url'] ?? null;
        $product->is_noindex         = $request->boolean('is_noindex');
        $product->save();

        // Save options & values
        $this->syncOptions($product, $request);

        activity_log('product.created', $product, "Created product '{$product->title}'");

        return redirect()
            ->route('admin.shop.products.edit', $product)
            ->with('toast', ['tone' => 'success', 'title' => 'Saved', 'message' => 'Product created successfully.']);
    }

    public function edit(Product $product)
    {
        $product->load(['options.values', 'variants.optionValues', 'featuredImage']);
        $media = Media::latest()->take(50)->get();

        return view('admin.shop.products.edit', compact('product', 'media'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $this->validateProduct($request, $product->id);

        $product->title              = $data['title'];
        $product->slug               = $this->uniqueSlug($data['slug'] ?: Str::slug($data['title']), $product->id);
        $product->excerpt            = $data['excerpt'] ?? null;
        $product->description_html   = $data['description_html'] ?? '';
        $product->is_active          = $request->boolean('is_active');
        $product->published_at       = $data['published_at'] ?? null;
        $product->featured_image_id  = $data['featured_image_id'] ?? null;
        $product->seo_title          = $data['seo_title'] ?: $data['title'];
        $product->seo_description    = $data['seo_description'] ?? null;
        $product->canonical_url      = $data['canonical_url'] ?? null;
        $product->is_noindex         = $request->boolean('is_noindex');
        $product->save();

        // Sync options & values
        $this->syncOptions($product, $request);

        activity_log('product.updated', $product, "Updated product '{$product->title}'");

        return redirect()
            ->route('admin.shop.products.edit', $product)
            ->with('toast', ['tone' => 'success', 'title' => 'Saved', 'message' => 'Product updated successfully.']);
    }

    public function publish(Product $product)
    {
        $product->is_active = !$product->is_active;
        $product->save();

        $state = $product->is_active ? 'published' : 'unpublished';
        activity_log("product.{$state}", $product, ucfirst($state) . " product '{$product->title}'");

        return redirect()->back()
            ->with('toast', ['tone' => 'success', 'title' => ucfirst($state), 'message' => "Product {$state}."]);
    }

    public function destroy(Product $product)
    {
        $product->delete();
        activity_log('product.trashed', $product, "Moved product '{$product->title}' to trash");

        return redirect()
            ->route('admin.shop.products.index')
            ->with('toast', [
                'tone'    => 'danger',
                'title'   => 'Moved to trash',
                'message' => 'Product was moved to trash.',
                'undo'    => route('admin.shop.products.restore', $product->id),
            ]);
    }

    public function restore($id)
    {
        $product = Product::withTrashed()->findOrFail($id);
        $product->restore();
        activity_log('product.restored', $product, "Restored product '{$product->title}'");

        return redirect()
            ->route('admin.shop.products.index')
            ->with('toast', ['tone' => 'success', 'title' => 'Restored', 'message' => 'Product restored.']);
    }

    /* ── Private helpers ── */

    private function syncOptions(Product $product, Request $request): void
    {
        $optionsInput = $request->input('options', []);

        // Collect IDs to keep
        $keepOptionIds = [];
        $keepValueIds  = [];

        foreach ($optionsInput as $i => $opt) {
            if (empty($opt['name']) && empty($opt['label'])) continue;

            $option = $product->options()->updateOrCreate(
                ['id' => $opt['id'] ?? null],
                [
                    'name'       => Str::slug($opt['label'] ?? $opt['name'], '_'),
                    'label'      => $opt['label'] ?? $opt['name'],
                    'sort_order' => $i,
                ]
            );
            $keepOptionIds[] = $option->id;

            $values = $opt['values'] ?? [];
            foreach ($values as $vi => $val) {
                if (empty(trim($val['value'] ?? ''))) continue;
                $ov = $option->values()->updateOrCreate(
                    ['id' => $val['id'] ?? null],
                    ['value' => trim($val['value']), 'sort_order' => $vi]
                );
                $keepValueIds[] = $ov->id;
            }
            // Remove values not in the list
            $option->values()->whereNotIn('id', $keepValueIds)->delete();
            $keepValueIds = []; // reset per option
        }

        // Remove options not in the list
        $product->options()->whereNotIn('id', $keepOptionIds)->delete();
    }

    private function validateProduct(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'title'             => ['required', 'string', 'max:255'],
            'slug'              => ['nullable', 'string', 'max:255'],
            'excerpt'           => ['nullable', 'string'],
            'description_html'  => ['nullable', 'string'],
            'is_active'         => ['nullable'],
            'published_at'      => ['nullable', 'date'],
            'featured_image_id' => ['nullable', 'integer', 'exists:media,id'],
            'seo_title'         => ['nullable', 'string', 'max:255'],
            'seo_description'   => ['nullable', 'string'],
            'canonical_url'     => ['nullable', 'url', 'max:255'],
            'is_noindex'        => ['nullable'],
        ]);
    }

    private function uniqueSlug(string $slug, ?int $ignoreId = null): string
    {
        $original = $slug;
        $count    = 1;
        while (Product::withTrashed()
            ->where('slug', $slug)
            ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->exists()
        ) {
            $slug = "{$original}-{$count}";
            $count++;
        }
        return $slug;
    }
}

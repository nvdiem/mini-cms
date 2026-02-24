<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::published()
            ->with('featuredImage')
            ->withCount('activeVariants');

        // Search
        if ($s = $request->input('q')) {
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                  ->orWhere('excerpt', 'like', "%{$s}%");
            });
        }

        $products = $query->latest()->paginate(12);

        // Attach price range to each product
        $products->getCollection()->transform(function ($product) {
            $range = $product->priceRange();
            $product->min_price = $range['min'];
            $product->max_price = $range['max'];
            return $product;
        });

        return view('site.shop.index', compact('products'));
    }

    public function show(string $slug)
    {
        $product = Product::published()
            ->where('slug', $slug)
            ->with(['options.values', 'variants.featuredImage', 'featuredImage'])
            ->firstOrFail();

        $activeVariants = $product->variants
            ->where('is_active', true)
            ->values();

        // Build variants JSON for JS
        $variantsJson = $activeVariants->map(fn($v) => [
            'id'               => $v->id,
            'signature'        => $v->option_signature,
            'label'            => $v->signatureLabel(),
            'price'            => (float) $v->price,
            'compare_at_price' => $v->compare_at_price ? (float) $v->compare_at_price : null,
            'stock_qty'        => $v->stock_qty,
            'in_stock'         => $v->inStock(),
            'sku'              => $v->sku,
            'image'            => $v->featuredImage ? asset('storage/' . $v->featuredImage->path) : null,
        ]);

        return view('site.shop.show', compact('product', 'activeVariants', 'variantsJson'));
    }
}

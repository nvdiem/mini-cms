<?php

namespace App\Http\Controllers;

use App\Models\ProductVariant;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart  = $this->getCart();
        $items = $this->resolveCartItems($cart);

        $subtotal = collect($items)->sum('line_total');

        return view('site.shop.cart', compact('items', 'subtotal'));
    }

    public function add(Request $request)
    {
        $data = $request->validate([
            'variant_id' => ['required', 'integer', 'exists:product_variants,id'],
            'qty'        => ['required', 'integer', 'min:1'],
        ]);

        $variant = ProductVariant::with('product')->findOrFail($data['variant_id']);

        // Must be active & in stock
        if (!$variant->is_active || !$variant->product->is_active) {
            return back()->with('toast', [
                'tone' => 'danger', 'title' => 'Unavailable',
                'message' => 'This product variant is not available.',
            ]);
        }

        $cart = $this->getCart();
        $key  = (string) $variant->id;

        $currentQty = $cart[$key]['qty'] ?? 0;
        $newQty     = $currentQty + $data['qty'];

        // Clamp to stock
        if ($newQty > $variant->stock_qty) {
            $newQty = $variant->stock_qty;
            session()->flash('toast', [
                'tone' => 'info', 'title' => 'Adjusted',
                'message' => "Quantity adjusted to available stock ({$variant->stock_qty}).",
            ]);
        }

        if ($newQty < 1) {
            return back()->with('toast', [
                'tone' => 'danger', 'title' => 'Out of Stock',
                'message' => 'This variant is currently out of stock.',
            ]);
        }

        $cart[$key] = [
            'variant_id' => $variant->id,
            'qty'        => $newQty,
        ];

        $this->saveCart($cart);

        return redirect()->route('cart.index')->with('toast', [
            'tone' => 'success', 'title' => 'Added to Cart',
            'message' => "'{$variant->product->title}' added to your cart.",
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'variant_id' => ['required', 'integer'],
            'qty'        => ['required', 'integer', 'min:0'],
        ]);

        $cart = $this->getCart();
        $key  = (string) $data['variant_id'];

        if ($data['qty'] <= 0) {
            unset($cart[$key]);
        } else {
            $variant = ProductVariant::find($data['variant_id']);
            if ($variant) {
                $qty = min($data['qty'], $variant->stock_qty);
                $cart[$key] = ['variant_id' => (int) $data['variant_id'], 'qty' => $qty];
            }
        }

        $this->saveCart($cart);

        return redirect()->route('cart.index')->with('toast', [
            'tone' => 'success', 'title' => 'Cart Updated',
            'message' => 'Your cart has been updated.',
        ]);
    }

    public function remove(Request $request)
    {
        $data = $request->validate([
            'variant_id' => ['required', 'integer'],
        ]);

        $cart = $this->getCart();
        unset($cart[(string) $data['variant_id']]);
        $this->saveCart($cart);

        return redirect()->route('cart.index')->with('toast', [
            'tone' => 'success', 'title' => 'Removed',
            'message' => 'Item removed from cart.',
        ]);
    }

    /* ── Session Helpers ── */

    private function getCart(): array
    {
        return session('shop_cart', []);
    }

    private function saveCart(array $cart): void
    {
        session(['shop_cart' => $cart]);
    }

    /**
     * Resolve cart keys into full item data for display.
     */
    public static function resolveCartItems(array $cart): array
    {
        if (empty($cart)) return [];

        $variantIds = collect($cart)->pluck('variant_id');
        $variants   = ProductVariant::with(['product.featuredImage', 'featuredImage'])
            ->whereIn('id', $variantIds)
            ->get()
            ->keyBy('id');

        $items = [];
        foreach ($cart as $entry) {
            $variant = $variants->get($entry['variant_id']);
            if (!$variant || !$variant->product) continue;

            $qty = min($entry['qty'], $variant->stock_qty);

            $items[] = [
                'variant_id'  => $variant->id,
                'product'     => $variant->product,
                'variant'     => $variant,
                'title'       => $variant->product->title,
                'signature'   => $variant->signatureLabel(),
                'sku'         => $variant->sku,
                'price'       => (float) $variant->price,
                'qty'         => max(1, $qty),
                'stock_qty'   => $variant->stock_qty,
                'line_total'  => (float) $variant->price * max(1, $qty),
                'image'       => $variant->featuredImage ?? $variant->product->featuredImage,
            ];
        }

        return $items;
    }
}

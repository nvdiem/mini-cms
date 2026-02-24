<?php

namespace App\Http\Controllers\Admin\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductVariantValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VariantController extends Controller
{
    /**
     * Generate all variant combinations from product options.
     */
    public function generate(Request $request, Product $product)
    {
        $product->load('options.values');

        if ($product->options->isEmpty()) {
            return back()->with('toast', [
                'tone' => 'danger', 'title' => 'Error',
                'message' => 'Add options and values before generating variants.'
            ]);
        }

        // Build cartesian product of option values
        $optionValueSets = $product->options->map(fn($opt) =>
            $opt->values->map(fn($v) => [
                'option_name' => $opt->name,
                'value_id'    => $v->id,
                'value'       => $v->value,
            ])->toArray()
        )->toArray();

        $combos = $this->cartesian($optionValueSets);
        $created = 0;

        DB::transaction(function () use ($product, $combos, &$created) {
            foreach ($combos as $combo) {
                // Build signature: "size:S|color:Red"
                $sigParts = [];
                foreach ($combo as $item) {
                    $sigParts[] = $item['option_name'] . ':' . $item['value'];
                }
                $signature = implode('|', $sigParts);

                // Skip if variant with this signature already exists
                $exists = $product->variants()
                    ->where('option_signature', $signature)
                    ->withTrashed()
                    ->exists();

                if ($exists) continue;

                $variant = $product->variants()->create([
                    'option_signature' => $signature,
                    'price'            => 0,
                    'stock_qty'        => 0,
                    'is_active'        => true,
                ]);

                // Link option values
                $valueIds = collect($combo)->pluck('value_id')->toArray();
                $variant->optionValues()->sync($valueIds);
                $created++;
            }
        });

        activity_log('variant.generated', $product, "Generated {$created} variants for '{$product->title}'", [
            'product_id' => $product->id,
            'count'      => $created,
        ]);

        return back()->with('toast', [
            'tone' => 'success', 'title' => 'Variants Generated',
            'message' => "{$created} new variant(s) created."
        ]);
    }

    /**
     * Bulk update variant prices/stock.
     */
    public function update(Request $request, Product $product)
    {
        $variantsData = $request->validate([
            'variants'               => ['required', 'array'],
            'variants.*.id'          => ['required', 'integer', 'exists:product_variants,id'],
            'variants.*.sku'         => ['nullable', 'string', 'max:100'],
            'variants.*.price'       => ['required', 'numeric', 'min:0'],
            'variants.*.compare_at_price' => ['nullable', 'numeric', 'min:0'],
            'variants.*.stock_qty'   => ['required', 'integer', 'min:0'],
            'variants.*.is_active'   => ['nullable'],
            'variants.*.featured_image_id' => ['nullable', 'integer'],
        ]);

        $updated = 0;
        DB::transaction(function () use ($variantsData, $product, &$updated) {
            foreach ($variantsData['variants'] as $vData) {
                $variant = ProductVariant::where('id', $vData['id'])
                    ->where('product_id', $product->id)
                    ->first();

                if (!$variant) continue;

                $before = ['price' => $variant->price, 'stock' => $variant->stock_qty];

                $variant->update([
                    'sku'              => $vData['sku'] ?? null,
                    'price'            => $vData['price'],
                    'compare_at_price' => $vData['compare_at_price'] ?? null,
                    'stock_qty'        => $vData['stock_qty'],
                    'is_active'        => isset($vData['is_active']),
                    'featured_image_id' => $vData['featured_image_id'] ?? null,
                ]);
                $updated++;

                activity_log('variant.updated', $variant, "Updated variant '{$variant->option_signature}'", [
                    'product_id' => $product->id,
                    'variant_id' => $variant->id,
                    'before'     => $before,
                    'after'      => ['price' => $variant->price, 'stock' => $variant->stock_qty],
                ]);
            }
        });

        return back()->with('toast', [
            'tone' => 'success', 'title' => 'Variants Updated',
            'message' => "{$updated} variant(s) updated."
        ]);
    }

    /**
     * Toggle variant active state.
     */
    public function toggle(ProductVariant $variant)
    {
        $variant->is_active = !$variant->is_active;
        $variant->save();

        $state = $variant->is_active ? 'activated' : 'deactivated';
        activity_log("variant.{$state}", $variant, "Variant '{$variant->option_signature}' {$state}", [
            'product_id' => $variant->product_id,
            'variant_id' => $variant->id,
        ]);

        return back()->with('toast', [
            'tone' => 'success', 'title' => ucfirst($state),
            'message' => "Variant {$state}."
        ]);
    }

    /**
     * Quick stock adjustment.
     */
    public function stockAdjust(Request $request, ProductVariant $variant)
    {
        $data = $request->validate([
            'adjustment' => ['required', 'integer'],
        ]);

        $before = $variant->stock_qty;
        $variant->stock_qty = max(0, $variant->stock_qty + $data['adjustment']);
        $variant->save();

        activity_log('variant.stock_adjust', $variant, "Stock adjusted for '{$variant->option_signature}'", [
            'product_id' => $variant->product_id,
            'variant_id' => $variant->id,
            'before'     => $before,
            'after'      => $variant->stock_qty,
            'adjustment' => $data['adjustment'],
        ]);

        return back()->with('toast', [
            'tone' => 'success', 'title' => 'Stock Updated',
            'message' => "Stock: {$before} → {$variant->stock_qty}."
        ]);
    }

    /* ── Cartesian product helper ── */

    private function cartesian(array $sets): array
    {
        if (empty($sets)) return [[]];

        $first = array_shift($sets);
        $rest  = $this->cartesian($sets);
        $result = [];

        foreach ($first as $item) {
            foreach ($rest as $combo) {
                $result[] = array_merge([$item], $combo);
            }
        }

        return $result;
    }
}

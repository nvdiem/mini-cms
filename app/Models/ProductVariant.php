<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductVariant extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'product_id', 'sku', 'price', 'compare_at_price',
        'stock_qty', 'is_active', 'featured_image_id', 'option_signature',
    ];

    protected $casts = [
        'price'            => 'decimal:2',
        'compare_at_price' => 'decimal:2',
        'stock_qty'        => 'integer',
        'is_active'        => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function optionValues()
    {
        return $this->belongsToMany(
            ProductOptionValue::class,
            'product_variant_values',
            'variant_id',
            'option_value_id'
        );
    }

    public function featuredImage()
    {
        return $this->belongsTo(Media::class, 'featured_image_id');
    }

    /* ── Helpers ── */

    /**
     * Human-readable signature: "Size M / Color Red"
     */
    public function signatureLabel(): string
    {
        $parts = explode('|', $this->option_signature);
        $labels = [];
        foreach ($parts as $part) {
            $kv = explode(':', $part, 2);
            if (count($kv) === 2) {
                $labels[] = ucfirst($kv[0]) . ' ' . $kv[1];
            }
        }
        return implode(' / ', $labels);
    }

    public function inStock(): bool
    {
        return $this->is_active && $this->stock_qty > 0;
    }
}

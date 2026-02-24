<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title', 'slug', 'excerpt', 'description_html', 'is_active',
        'published_at', 'featured_image_id',
        'seo_title', 'seo_description', 'canonical_url', 'is_noindex',
    ];

    protected $casts = [
        'is_active'    => 'boolean',
        'is_noindex'   => 'boolean',
        'published_at' => 'datetime',
    ];

    /* ── Relationships ── */

    public function options()
    {
        return $this->hasMany(ProductOption::class)->orderBy('sort_order');
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function activeVariants()
    {
        return $this->hasMany(ProductVariant::class)->where('is_active', true);
    }

    public function featuredImage()
    {
        return $this->belongsTo(Media::class, 'featured_image_id');
    }

    /* ── Scopes ── */

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('is_active', true);
    }

    public function scopePublished(Builder $q): Builder
    {
        return $q->where('is_active', true)
                 ->where(function ($sub) {
                     $sub->whereNull('published_at')
                         ->orWhere('published_at', '<=', now());
                 });
    }

    /* ── Helpers ── */

    public function priceRange(): array
    {
        $variants = $this->activeVariants ?? $this->activeVariants()->get();
        $prices   = $variants->pluck('price')->filter();

        return [
            'min' => $prices->min() ?? 0,
            'max' => $prices->max() ?? 0,
        ];
    }
}

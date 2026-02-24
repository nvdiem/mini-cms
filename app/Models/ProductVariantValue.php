<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ProductVariantValue extends Pivot
{
    public $timestamps   = false;
    public $incrementing = false;
    protected $table     = 'product_variant_values';

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    public function optionValue()
    {
        return $this->belongsTo(ProductOptionValue::class, 'option_value_id');
    }
}

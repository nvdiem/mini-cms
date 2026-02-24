<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'order_id', 'product_id', 'variant_id',
        'product_title_snapshot', 'variant_signature_snapshot', 'sku_snapshot',
        'unit_price_snapshot', 'qty', 'line_total',
    ];

    protected $casts = [
        'unit_price_snapshot' => 'decimal:2',
        'line_total'          => 'decimal:2',
        'qty'                 => 'integer',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id')->withTrashed();
    }
}

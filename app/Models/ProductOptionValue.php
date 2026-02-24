<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductOptionValue extends Model
{
    public $timestamps = false;

    protected $fillable = ['product_option_id', 'value', 'sort_order'];

    public function option()
    {
        return $this->belongsTo(ProductOption::class, 'product_option_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $fillable = [
        'name', 'email', 'phone', 'message', 'source', 'status'
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function scopeStatus($query, $status)
    {
        if (in_array($status, ['new', 'handled', 'spam'], true)) {
            return $query->where('status', $status);
        }
        return $query;
    }
}

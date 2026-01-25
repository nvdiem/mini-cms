<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PagePackage extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'zip_path',
        'public_dir',
        'version',
        'entry_file',
        'is_active',
        'wire_contact',
        'wire_selector',
        'created_by'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'wire_contact' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $attributes = [
        'entry_file' => 'index.html',
        'is_active' => false,
        'wire_contact' => true,
        'wire_selector' => '[data-contact-form],#contactForm,.js-contact',
    ];

    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getPublicUrlAttribute()
    {
        return url("/b/{$this->slug}");
    }

    public function getFullPublicPathAttribute()
    {
        return public_path($this->public_dir);
    }
}

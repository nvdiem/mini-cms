<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    protected $fillable = ['disk','path','original_name','mime','size','uploaded_by', 'alt_text', 'caption', 'width', 'height'];

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function url(): string
    {
        return Storage::disk($this->disk)->url($this->path);
    }

    public function posts()
    {
        return $this->hasMany(Post::class, 'featured_image_id');
    }

    public function pages()
    {
        return $this->hasMany(Page::class, 'featured_image_id');
    }
}

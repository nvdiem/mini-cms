<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MediaFolder extends Model
{
    protected $fillable = ['name'];

    public function media()
    {
        return $this->hasMany(Media::class, 'folder_id');
    }
}

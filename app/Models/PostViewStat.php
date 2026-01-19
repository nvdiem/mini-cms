<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostViewStat extends Model
{
    protected $guarded = [];
    public $timestamps = false; // Using date column, simple aggregation

    protected $casts = [
        'date' => 'date',
    ];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * Increment view count for a post on a given date (default today).
     */
    public static function incrementFor($post_id, $date = null)
    {
        $date = $date ?: now()->format('Y-m-d');
        
        // Upsert logic: simple firstOrCreate then increment, or raw upsert
        // Simplest portable way given constraints:
        $stat = self::firstOrCreate(
            ['post_id' => $post_id, 'date' => $date],
            ['views' => 0]
        );

        $stat->increment('views');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportConversation extends Model
{
    protected $fillable = [
        'visitor_token',
        'name',
        'email',
        'status',
        'assigned_to',
        'last_message_at',
        'source_url',
        'referrer',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
        'last_message_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get all messages for this conversation
     */
    public function messages(): HasMany
    {
        return $this->hasMany(SupportMessage::class, 'conversation_id');
    }

    /**
     * Get the assigned agent
     */
    public function assignedAgent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Scope to filter by status
     */
    public function scopeStatus($query, $status)
    {
        if (in_array($status, ['open', 'pending', 'closed'], true)) {
            return $query->where('status', $status);
        }
        return $query;
    }

    /**
     * Get the latest message for this conversation
     */
    public function latestMessage(): HasMany
    {
        return $this->hasMany(SupportMessage::class, 'conversation_id')->latest()->limit(1);
    }

    /**
     * Get unread messages count (for agent)
     */
    public function getUnreadCountAttribute(): int
    {
        return $this->messages()->whereNull('read_at')->where('sender_type', 'visitor')->count();
    }

    /**
     * Check if conversation was closed more than given hours ago
     */
    public function isClosedForMoreThan(int $hours): bool
    {
        if ($this->status !== 'closed') {
            return false;
        }
        return $this->updated_at->diffInHours(now()) >= $hours;
    }
}

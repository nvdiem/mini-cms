<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportMessage extends Model
{
    protected $fillable = [
        'conversation_id',
        'sender_type',
        'user_id',
        'message',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the conversation this message belongs to
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(SupportConversation::class, 'conversation_id');
    }

    /**
     * Get the user who sent this message (for agent messages)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if this message is from a visitor
     */
    public function isFromVisitor(): bool
    {
        return $this->sender_type === 'visitor';
    }

    /**
     * Check if this message is from an agent
     */
    public function isFromAgent(): bool
    {
        return $this->sender_type === 'user';
    }

    /**
     * Check if this is a system message
     */
    public function isSystem(): bool
    {
        return $this->sender_type === 'system';
    }
}

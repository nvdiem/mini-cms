<?php

namespace App\Events;

use App\Models\SupportMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SupportMessageCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $id;
    public int $conversation_id;
    public string $sender_type;
    public string $message;
    public string $created_at;
    public ?string $read_at;
    public ?string $user_name;

    /**
     * Create a new event instance.
     */
    public function __construct(SupportMessage $message)
    {
        $this->id = $message->id;
        $this->conversation_id = $message->conversation_id;
        $this->sender_type = $message->sender_type;
        $this->message = $message->message;
        $this->created_at = $message->created_at->toISOString();
        $this->read_at = $message->read_at?->toISOString();
        $this->user_name = $message->user?->name ?? $message->user?->email ?? null;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('support.conversation.' . $this->conversation_id),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'support.message.created';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->id,
            'conversation_id' => $this->conversation_id,
            'sender_type' => $this->sender_type,
            'message' => $this->message,
            'created_at' => $this->created_at,
            'read_at' => $this->read_at,
            'user_name' => $this->user_name,
        ];
    }
}

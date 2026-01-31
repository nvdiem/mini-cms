<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportConversation;
use App\Models\SupportMessage;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    /**
     * Display inbox list of all conversations
     */
    /**
     * Display inbox list of all conversations
     */
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $status = $request->query('status', '');

        $query = SupportConversation::query()
            ->with(['latestMessage', 'assignedAgent'])
            ->withCount(['messages as unread_count' => function($q) {
                $q->where('sender_type', 'visitor')->whereNull('read_at');
            }]);

        if ($q) {
            $query->where(function($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhereHas('messages', function($msgQuery) use ($q) {
                        $msgQuery->where('message', 'like', "%{$q}%");
                    });
            });
        }

        if ($status) {
            $query->status($status);
        }

        $conversations = $query->orderByDesc('last_message_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.support.index', compact('conversations', 'q', 'status'));
    }

    /**
     * Display a single conversation with all messages
     */
    public function show($id)
    {
        $conversation = SupportConversation::with(['messages.user', 'assignedAgent'])
            ->findOrFail($id);
            
        // We'll mark read via JS or separate call to ensure accurate UI state
        // But keeping server-side mark-read on load is good practice too
        SupportMessage::where('conversation_id', $id)
            ->where('sender_type', 'visitor')
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return view('admin.support.show', compact('conversation'));
    }

    /**
     * Mark visitor messages as read manually (AJAX)
     */
    public function markRead(Request $request, $id)
    {
        $count = SupportMessage::where('conversation_id', $id)
            ->where('sender_type', 'visitor')
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['ok' => true, 'marked' => $count]);
    }

    /**
     * Signal that admin is typing
     */
    public function typing(Request $request, $id)
    {
        \Illuminate\Support\Facades\Cache::put("support:typing:admin:{$id}", time(), 10);
        return response()->json(['ok' => true]);
    }

// ... reply and updateStatus unchanged ...

    /**
     * Send an agent reply to the conversation
     */
    public function reply(Request $request, $id)
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'min:1', 'max:2000'],
        ]);

        $conversation = SupportConversation::findOrFail($id);

        // Create agent message
        $message = SupportMessage::create([
            'conversation_id' => $conversation->id,
            'sender_type' => 'user',
            'user_id' => auth()->id(),
            'message' => $validated['message'],
        ]);

        // Update conversation
        $updates = [
            'status' => 'pending',
            'last_message_at' => now(),
        ];
        
        // Assign agent if not yet assigned
        if (!$conversation->assigned_to) {
            $updates['assigned_to'] = auth()->id();
        }
        
        $conversation->update($updates);

        activity_log(
            'support.message.agent',
            $conversation,
            "Agent replied to conversation",
            [
                'message_id' => $message->id,
                'user_id' => auth()->id(),
            ]
        );

        if ($request->wantsJson()) {
            return response()->json([
                'ok' => true,
                'message' => [
                    'id' => $message->id,
                    'sender_type' => $message->sender_type,
                    'message' => $message->message,
                    'created_at' => $message->created_at->toISOString(),
                    'user_name' => auth()->user()->name ?? auth()->user()->email,
                ],
            ]);
        }

        return redirect()->back()->with('toast', [
            'tone' => 'success',
            'title' => 'Reply sent',
            'message' => 'Your reply has been sent to the visitor.',
        ]);
    }

    /**
     * Update conversation status
     */
    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:open,pending,closed'],
        ]);

        $conversation = SupportConversation::findOrFail($id);
        $oldStatus = $conversation->status;

        $conversation->update(['status' => $validated['status']]);

        activity_log(
            'support.status.changed',
            $conversation,
            "Conversation status changed from {$oldStatus} to {$validated['status']}",
            [
                'from' => $oldStatus,
                'to' => $validated['status'],
            ]
        );

        return redirect()->back()->with('toast', [
            'tone' => 'success',
            'title' => 'Status updated',
            'message' => "Conversation marked as {$validated['status']}.",
        ]);
    }

    /**
     * Poll for new messages (AJAX endpoint for admin)
     */
    public function pollMessages(Request $request, $id)
    {
        $afterId = (int) $request->query('after_id', 0);

        $conversation = SupportConversation::findOrFail($id);

        $messages = SupportMessage::where('conversation_id', $id)
            ->where('id', '>', $afterId)
            ->with('user')
            ->orderBy('id', 'asc')
            ->get()
            ->map(function ($msg) {
                return [
                    'id' => $msg->id,
                    'sender_type' => $msg->sender_type,
                    'message' => $msg->message,
                    'created_at' => $msg->created_at->toISOString(),
                    'user_name' => $msg->user?->name ?? $msg->user?->email ?? null,
                ];
            });

        // Mark new visitor messages as read
        SupportMessage::where('conversation_id', $id)
            ->where('id', '>', $afterId)
            ->where('sender_type', 'visitor')
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $lastMessageId = $messages->isNotEmpty() 
            ? $messages->last()['id'] 
            : $afterId;

        // Check Guest typing
        $guestTypingTimestamp = \Illuminate\Support\Facades\Cache::get("support:typing:guest:{$id}");
        $isGuestTyping = $guestTypingTimestamp && (time() - $guestTypingTimestamp <= 3);

        return response()->json([
            'ok' => true,
            'messages' => $messages->values(),
            'last_message_id' => $lastMessageId,
            'conversation_status' => $conversation->fresh()->status,
            'typing' => ['guest' => $isGuestTyping],
        ]);
    }

    /**
     * Stream new messages and status updates via SSE
     */
    /**
     * Stream new messages and status updates via SSE
     */
    public function stream(Request $request, $id)
    {
        $afterId = (int) $request->query('after_id', 0);

        // Support native EventSource resume
        $lastEventId = $request->header('Last-Event-ID');
        if ($lastEventId !== null && is_numeric($lastEventId)) {
            $afterId = max($afterId, (int)$lastEventId);
        }
        
        // Disable buffering
        if (function_exists('apache_setenv')) {
            @apache_setenv('no-gzip', 1);
        }
        ini_set('output_buffering', 'off');
        ini_set('zlib.output_compression', false);

        $headers = [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no',
        ];

        return response()->stream(function () use ($id, $afterId) {
            if (session()->isStarted()) {
                session_write_close();
            }

            $conversation = SupportConversation::find($id);
            if (!$conversation) {
                echo "event: error\ndata:Conversation not found\n\n";
                ob_flush();
                flush();
                return;
            }

            $lastId = $afterId;
            $currentStatus = $conversation->status;
            $startTime = time();
            $lastKeepAlive = time();
            $lastTypingState = false;

            while (true) {
                if (connection_aborted()) break;
                if (time() - $startTime > 60) break;

                $packetSent = false;

                // 1. Check for new messages
                $messages = SupportMessage::where('conversation_id', $conversation->id)
                    ->where('id', '>', $lastId)
                    ->with('user')
                    ->orderBy('id', 'asc')
                    ->get();

                foreach ($messages as $msg) {
                    $payload = [
                        'id' => $msg->id,
                        'sender_type' => $msg->sender_type,
                        'message' => $msg->message,
                        'created_at' => $msg->created_at->toISOString(),
                        'user_name' => $msg->user?->name ?? $msg->user?->email ?? null,
                    ];
                    
                    echo "id: {$msg->id}\n";
                    echo "event: message.created\n";
                    echo "data: " . json_encode($payload) . "\n\n";
                    
                    $lastId = $msg->id;
                    $packetSent = true;

                    // Mark visitor messages as read if admin is viewing stream
                    if ($msg->sender_type === 'visitor' && !$msg->read_at) {
                        try {
                            $msg->update(['read_at' => now()]);
                        } catch (\Throwable $e) {}
                    }
                }

                // 2. Check for status change
                $freshConversation = $conversation->fresh();
                if ($freshConversation->status !== $currentStatus) {
                    $payload = [
                        'status_from' => $currentStatus,
                        'status_to' => $freshConversation->status,
                        'updated_at' => now()->toISOString(),
                    ];
                    
                    echo "event: conversation.status_changed\n";
                    echo "data: " . json_encode($payload) . "\n\n";
                    
                    $currentStatus = $freshConversation->status;
                    $packetSent = true;
                }

                // 3. Check for typing (Guest)
                $guestTypingTimestamp = \Illuminate\Support\Facades\Cache::get("support:typing:guest:{$conversation->id}");
                $isGuestTyping = $guestTypingTimestamp && (time() - $guestTypingTimestamp <= 3);

                if ($isGuestTyping !== $lastTypingState) {
                     echo "event: typing\n";
                     echo "data: " . json_encode(['guest' => $isGuestTyping]) . "\n\n";
                     $lastTypingState = $isGuestTyping;
                     $packetSent = true;
                }

                if ($packetSent) {
                    ob_flush();
                    flush();
                }

                // 4. Heartbeat
                if (time() - $lastKeepAlive >= 20) {
                    echo "event: ping\n";
                    echo "data: {\"t\": " . time() . "}\n\n";
                    ob_flush();
                    flush();
                    $lastKeepAlive = time();
                }

                usleep(500000); // 0.5s
            }
        }, 200, $headers);
    }
    /**
     * Get global unread count
     */
    public function unreadCount()
    {
        $count = SupportMessage::where('sender_type', 'visitor')
            ->whereNull('read_at')
            ->count();
            
        return response()->json(['count' => $count]);
    }
}

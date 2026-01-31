<?php

namespace App\Http\Controllers;

use App\Models\SupportConversation;
use App\Models\SupportMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PublicSupportController extends Controller
{
    /**
     * Session reset timeout in hours
     */
    private const SESSION_RESET_HOURS = 72;

    /**
     * Handle the first message from a visitor
     * Creates token if absent, creates conversation if not exists
     */
    public function firstMessage(Request $request)
    {
        // Honeypot check - website field must be empty
        if ($request->filled('website')) {
            return response()->json([
                'ok' => true,
                'message' => 'Thank you for your message.'
            ]);
        }

        // Validate input
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:60'],
            'email' => ['nullable', 'email', 'max:255'],
            'message' => ['required', 'string', 'min:1', 'max:2000'],
            'visitor_token' => ['nullable', 'string', 'max:64'],
        ]);

        // Generate or use existing token
        $visitorToken = !empty($validated['visitor_token']) 
            ? $validated['visitor_token'] 
            : Str::random(32);

        // Find or create conversation
        $conversation = SupportConversation::where('visitor_token', $visitorToken)->first();
        
        $isNewConversation = false;
        $sessionReset = false;

        if (!$conversation) {
            // Create new conversation
            $conversation = SupportConversation::create([
                'visitor_token' => $visitorToken,
                'name' => $validated['name'],
                'email' => $validated['email'] ?? null,
                'status' => 'open',
                'last_message_at' => now(),
                'source_url' => $request->header('Referer'),
                'referrer' => $request->input('referrer'),
                'meta' => [
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'utm_source' => $request->input('utm_source'),
                    'utm_medium' => $request->input('utm_medium'),
                    'utm_campaign' => $request->input('utm_campaign'),
                    'session_seq' => 1,
                ],
            ]);
            $isNewConversation = true;

            activity_log(
                'support.conversation.created',
                $conversation,
                "New support conversation created",
                [
                    'visitor_token' => $visitorToken,
                    'source_url' => $conversation->source_url,
                    'ip' => $request->ip(),
                ]
            );
        } else {
            // Update existing conversation
            // Check if session reset is needed (closed for more than 72 hours)
            if ($conversation->isClosedForMoreThan(self::SESSION_RESET_HOURS)) {
                $sessionReset = true;
                $meta = $conversation->meta ?? [];
                $meta['session_seq'] = ($meta['session_seq'] ?? 1) + 1;
                $meta['last_reset_at'] = now()->toISOString();
                
                // Add system message for session separator
                SupportMessage::create([
                    'conversation_id' => $conversation->id,
                    'sender_type' => 'system',
                    'message' => '--- New session started ---',
                ]);

                $conversation->update(['meta' => $meta]);

                activity_log(
                    'support.session.reset',
                    $conversation,
                    "Support session reset after timeout",
                    [
                        'session_seq' => $meta['session_seq'],
                        'last_reset_at' => $meta['last_reset_at'],
                    ]
                );
            }

            // Fill in missing name/email if provided
            $updates = ['status' => 'open', 'last_message_at' => now()];
            if (empty($conversation->name) && !empty($validated['name'])) {
                $updates['name'] = $validated['name'];
            }
            if (empty($conversation->email) && !empty($validated['email'])) {
                $updates['email'] = $validated['email'];
            }
            $conversation->update($updates);
        }

        // Create the visitor message
        $message = SupportMessage::create([
            'conversation_id' => $conversation->id,
            'sender_type' => 'visitor',
            'message' => $validated['message'],
        ]);

        activity_log(
            'support.message.visitor',
            $conversation,
            "Visitor sent a message",
            ['message_id' => $message->id]
        );

        return response()->json([
            'ok' => true,
            'visitor_token' => $visitorToken,
            'conversation_status' => $conversation->status,
            'last_message_id' => $message->id,
            'is_new' => $isNewConversation,
            'session_reset' => $sessionReset,
        ]);
    }

    /**
     * Send a message for an existing conversation
     */
    public function sendMessage(Request $request)
    {
        // Honeypot check
        if ($request->filled('website')) {
            return response()->json([
                'ok' => true,
                'message' => 'Message sent.'
            ]);
        }

        $validated = $request->validate([
            'visitor_token' => ['required', 'string', 'max:64'],
            'message' => ['required', 'string', 'min:1', 'max:2000'],
        ]);

        $conversation = SupportConversation::where('visitor_token', $validated['visitor_token'])->first();

        if (!$conversation) {
            return response()->json([
                'ok' => false,
                'message' => 'Conversation not found. Please refresh the page.'
            ], 404);
        }

        // Check for session reset (closed for more than 72 hours)
        if ($conversation->isClosedForMoreThan(self::SESSION_RESET_HOURS)) {
            $meta = $conversation->meta ?? [];
            $meta['session_seq'] = ($meta['session_seq'] ?? 1) + 1;
            $meta['last_reset_at'] = now()->toISOString();
            
            // Add system message for session separator
            SupportMessage::create([
                'conversation_id' => $conversation->id,
                'sender_type' => 'system',
                'message' => '--- New session started ---',
            ]);

            $conversation->update(['meta' => $meta]);

            activity_log(
                'support.session.reset',
                $conversation,
                "Support session reset after timeout",
                [
                    'session_seq' => $meta['session_seq'],
                    'last_reset_at' => $meta['last_reset_at'],
                ]
            );
        }

        // Create the message
        $message = SupportMessage::create([
            'conversation_id' => $conversation->id,
            'sender_type' => 'visitor',
            'message' => $validated['message'],
        ]);

        // Update conversation status and timestamp
        $conversation->update([
            'status' => 'open',
            'last_message_at' => now(),
        ]);

        activity_log(
            'support.message.visitor',
            $conversation,
            "Visitor sent a message",
            ['message_id' => $message->id]
        );

        return response()->json([
            'ok' => true,
            'last_message_id' => $message->id,
            'conversation_status' => 'open',
        ]);
    }

    /**
     * Poll for new messages
     */
    /**
     * Mark messages as read by visitor
     */
    public function markRead(Request $request)
    {
        $validated = $request->validate([
            'visitor_token' => ['required', 'string', 'max:64'],
        ]);

        $conversation = SupportConversation::where('visitor_token', $validated['visitor_token'])->first();

        if ($conversation) {
            // Mark all agent messages as read
            $count = SupportMessage::where('conversation_id', $conversation->id)
                ->where('sender_type', '!=', 'visitor') // agent or system
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
            
            return response()->json(['ok' => true, 'marked' => $count]);
        }

        return response()->json(['ok' => false], 404);
    }

    /**
     * Signal that visitor is typing
     */
    public function typing(Request $request)
    {
        $validated = $request->validate([
            'visitor_token' => ['required', 'string', 'max:64'],
        ]);

        $conversation = SupportConversation::where('visitor_token', $validated['visitor_token'])->first();

        if ($conversation) {
            \Illuminate\Support\Facades\Cache::put("support:typing:guest:{$conversation->id}", time(), 10);
            return response()->json(['ok' => true]);
        }

        return response()->json(['ok' => false], 404);
    }

    /**
     * Poll for new messages
     */
    public function pollMessages(Request $request)
    {
        $validated = $request->validate([
            'visitor_token' => ['required', 'string', 'max:64'],
            'after_id' => ['nullable', 'integer', 'min:0'],
        ]);

        $conversation = SupportConversation::where('visitor_token', $validated['visitor_token'])->first();

        if (!$conversation) {
            return response()->json([
                'ok' => false,
                'messages' => [],
                'last_message_id' => 0,
            ]);
        }

        $afterId = $validated['after_id'] ?? 0;

        $messages = SupportMessage::where('conversation_id', $conversation->id)
            ->where('id', '>', $afterId)
            ->orderBy('id', 'asc')
            ->get()
            ->map(function ($msg) {
                return [
                    'id' => $msg->id,
                    'sender_type' => $msg->sender_type,
                    'message' => $msg->message,
                    'created_at' => $msg->created_at->toISOString(),
                    'user_name' => $msg->user?->name ?? null,
                ];
            });

        $lastMessageId = $messages->isNotEmpty() 
            ? $messages->last()['id'] 
            : $afterId;

        // Check Admin typing status
        $adminTypingTimestamp = \Illuminate\Support\Facades\Cache::get("support:typing:admin:{$conversation->id}");
        $isAdminTyping = $adminTypingTimestamp && (time() - $adminTypingTimestamp <= 3);

        // Count unread agent messages
        $unreadCount = SupportMessage::where('conversation_id', $conversation->id)
            ->where('sender_type', '!=', 'visitor')
            ->whereNull('read_at')
            ->count();

        return response()->json([
            'ok' => true,
            'messages' => $messages->values(),
            'last_message_id' => $lastMessageId,
            'conversation_status' => $conversation->status,
            'typing' => [
                'admin' => $isAdminTyping,
            ],
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * Stream new messages via SSE
     */
    public function stream(Request $request)
    {
        $request->validate([
            'visitor_token' => ['required', 'string', 'max:64'],
            'after_id' => ['nullable', 'integer', 'min:0'],
        ]);

        $token = $request->query('visitor_token');
        $afterId = (int) $request->query('after_id', 0);

        // Disable buffering for real-time delivery
        if (function_exists('apache_setenv')) {
            @apache_setenv('no-gzip', 1);
        }
        ini_set('output_buffering', 'off');
        ini_set('zlib.output_compression', false);

        // Prepare headers
        $headers = [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no', // For Nginx
        ];

        return response()->stream(function () use ($token, $afterId) {
            // Close session to prevent locking
            if (session()->isStarted()) {
                session_write_close();
            }

            $conversation = SupportConversation::where('visitor_token', $token)->first();
            
            if (!$conversation) {
                echo "event: error\ndata:Conversation not found\n\n";
                ob_flush();
                flush();
                return;
            }

            $lastId = $afterId;
            $startTime = time();
            $lastKeepAlive = time();
            $lastTypingState = false;

            // Run for max 60 seconds (client will reconnect) to prevent zombies
            while (true) {
                if (connection_aborted()) break;
                if (time() - $startTime > 60) break;

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
                        'user_name' => $msg->user?->name ?? null,
                    ];
                    
                    echo "id: {$msg->id}\n";
                    echo "event: message.created\n";
                    echo "data: " . json_encode($payload) . "\n\n";
                    
                    $lastId = $msg->id;
                }
                
                // If we found messages, flush immediately
                if ($messages->isNotEmpty()) {
                    ob_flush();
                    flush();
                }

                // 2. Check for typing status
                $adminTypingTimestamp = \Illuminate\Support\Facades\Cache::get("support:typing:admin:{$conversation->id}");
                $isAdminTyping = $adminTypingTimestamp && (time() - $adminTypingTimestamp <= 3);

                if ($isAdminTyping !== $lastTypingState) {
                     echo "event: typing\n";
                     echo "data: " . json_encode(['admin' => $isAdminTyping]) . "\n\n";
                     $lastTypingState = $isAdminTyping;
                     ob_flush();
                     flush();
                }

                // 3. Check for keepalive
                if (time() - $lastKeepAlive >= 15) {
                    echo ": keepalive\n\n";
                    ob_flush();
                    flush();
                    $lastKeepAlive = time();
                }

                // Sleep minimal amount
                usleep(500000); // 0.5s
            }
        }, 200, $headers);
    }
}

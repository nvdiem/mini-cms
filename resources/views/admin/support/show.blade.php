<x-admin.layout :title="'Conversation · Mini CMS'" :crumb="'Support'">
  <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
      <h1 class="text-2xl font-semibold text-text-strong dark:text-white tracking-tight">
        Conversation with {{ $conversation->name }}
      </h1>
      <p class="text-sm text-text-muted dark:text-slate-400 mt-1">
        Started {{ $conversation->created_at->format('M j, Y \a\t g:i A') }}
      </p>
    </div>
    <a class="btn-ghost" href="{{ route('admin.support.index') }}">← Back to Inbox</a>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    {{-- Chat Panel --}}
    <div class="lg:col-span-2 space-y-4">
      <div class="card">
        {{-- Messages Container --}}
        <div id="messagesContainer" class="h-[500px] overflow-y-auto p-4 space-y-3 bg-slate-50/50 dark:bg-slate-900/30">
          @foreach($conversation->messages as $msg)
            @if($msg->sender_type === 'system')
              <div class="flex justify-center">
                <div class="text-xs text-slate-400 dark:text-slate-500 bg-slate-100 dark:bg-slate-800 px-3 py-1 rounded-full">
                  {{ $msg->message }}
                </div>
              </div>
            @elseif($msg->sender_type === 'visitor')
              <div class="flex justify-start">
                <div class="max-w-[80%] bg-white dark:bg-slate-800 border border-border-light dark:border-border-dark rounded-xl rounded-tl-sm px-4 py-2 shadow-sm">
                  <div class="text-xs text-slate-400 dark:text-slate-500 mb-1">{{ $conversation->name }}</div>
                  <p class="text-sm text-text-strong dark:text-white whitespace-pre-wrap break-words">{{ $msg->message }}</p>
                  <div class="text-[10px] text-slate-400 mt-1">{{ $msg->created_at->format('M j, g:i A') }}</div>
                </div>
              </div>
            @else
              <div class="flex justify-end">
                <div class="max-w-[80%] bg-primary text-white rounded-xl rounded-tr-sm px-4 py-2 shadow-sm">
                  <div class="text-xs text-blue-200 mb-1">{{ $msg->user?->email ?? 'Agent' }}</div>
                  <p class="text-sm whitespace-pre-wrap break-words">{{ $msg->message }}</p>
                  <div class="text-[10px] text-blue-200 mt-1">{{ $msg->created_at->format('M j, g:i A') }}</div>
                </div>
              </div>
            @endif
          @endforeach
        </div>

        {{-- Reply Box --}}
        <div class="p-4 border-t border-border-light dark:border-border-dark">
          <form id="replyForm" class="flex gap-2">
            @csrf
            <textarea 
              id="replyMessage" 
              name="message" 
              class="input flex-1 resize-none" 
              rows="2" 
              placeholder="Type your reply..." 
              required 
              maxlength="2000"
            ></textarea>
            <button type="submit" id="sendBtn" class="btn-primary px-4 self-end">
              <span class="material-icons-outlined text-[18px]" aria-hidden="true">send</span>
              Send
            </button>
          </form>
        </div>
      </div>
    </div>

    {{-- Context Panel --}}
    <div class="space-y-4">
      {{-- Status Card --}}
      <div class="card p-6">
        <div class="text-sm font-semibold text-text-strong dark:text-white">Status</div>
        <div class="mt-4">
          @php
            $badgeClass = match($conversation->status) {
              'open' => 'badge-draft',
              'pending' => 'bg-amber-100 text-amber-700 border-amber-200 dark:bg-amber-900/20 dark:text-amber-300 dark:border-amber-900/40',
              'closed' => 'badge-pub',
            };
          @endphp
          <span id="statusBadge" class="badge {{ $badgeClass }}">{{ ucfirst($conversation->status) }}</span>
        </div>

        <div class="mt-6 space-y-2">
          @if($conversation->status !== 'closed')
            <form method="POST" action="{{ route('admin.support.status', $conversation->id) }}">
              @csrf
              <input type="hidden" name="status" value="closed" />
              <button class="btn-primary w-full" type="submit">
                <span class="material-icons-outlined text-[18px]" aria-hidden="true">check_circle</span>
                Close Conversation
              </button>
            </form>
          @endif

          @if($conversation->status !== 'open')
            <form method="POST" action="{{ route('admin.support.status', $conversation->id) }}">
              @csrf
              <input type="hidden" name="status" value="open" />
              <button class="btn-ghost w-full" type="submit">Reopen as Open</button>
            </form>
          @endif

          @if($conversation->status !== 'pending')
            <form method="POST" action="{{ route('admin.support.status', $conversation->id) }}">
              @csrf
              <input type="hidden" name="status" value="pending" />
              <button class="btn-ghost w-full" type="submit">Set as Pending</button>
            </form>
          @endif
        </div>
      </div>

      {{-- Visitor Info Card --}}
      <div class="card p-6">
        <div class="text-sm font-semibold text-text-strong dark:text-white">Visitor Info</div>
        <div class="mt-4 space-y-3 text-sm">
          <div>
            <div class="text-xs font-semibold text-text-muted dark:text-slate-400 uppercase tracking-wider">Name</div>
            <div class="mt-1 text-text-strong dark:text-white">{{ $conversation->name }}</div>
          </div>

          <div>
            <div class="text-xs font-semibold text-text-muted dark:text-slate-400 uppercase tracking-wider">Email</div>
            <div class="mt-1">
              @if($conversation->email)
                <a href="mailto:{{ $conversation->email }}" class="text-primary hover:underline">{{ $conversation->email }}</a>
              @else
                <span class="text-slate-400">Not provided</span>
              @endif
            </div>
          </div>

          @if($conversation->source_url)
            <div>
              <div class="text-xs font-semibold text-text-muted dark:text-slate-400 uppercase tracking-wider">Source URL</div>
              <div class="mt-1 text-slate-600 dark:text-slate-300 text-xs truncate" title="{{ $conversation->source_url }}">
                {{ $conversation->source_url }}
              </div>
            </div>
          @endif

          @if($conversation->referrer)
            <div>
              <div class="text-xs font-semibold text-text-muted dark:text-slate-400 uppercase tracking-wider">Referrer</div>
              <div class="mt-1 text-slate-600 dark:text-slate-300 text-xs truncate" title="{{ $conversation->referrer }}">
                {{ $conversation->referrer }}
              </div>
            </div>
          @endif

          @if($conversation->assignedAgent)
            <div>
              <div class="text-xs font-semibold text-text-muted dark:text-slate-400 uppercase tracking-wider">Assigned To</div>
              <div class="mt-1 text-text-strong dark:text-white">{{ $conversation->assignedAgent->email }}</div>
            </div>
          @endif

          <div class="flex justify-between pt-2 border-t border-border-light dark:border-border-dark">
            <span class="text-text-muted dark:text-slate-400">ID</span>
            <span class="text-text-strong dark:text-white font-medium">#{{ $conversation->id }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <x-slot:scripts>
    <script>
    (function(){
      const conversationId = {{ $conversation->id }};
      const messagesContainer = document.getElementById('messagesContainer');
      const replyForm = document.getElementById('replyForm');
      const replyMessage = document.getElementById('replyMessage');
      const sendBtn = document.getElementById('sendBtn');
      const statusBadge = document.getElementById('statusBadge');
      const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
      
      let lastMessageId = {{ $conversation->messages->last()?->id ?? 0 }};
      let eventSource = null;
      let usePollingFallback = false;
      let pollTimer = null;
      let lastTypingSent = 0;
      let unreadInSession = 0;
      
      // Audio
      const audio = new Audio('/sounds/ping.mp3');
      let audioUnlocked = false;

      // Audio Unlock on first interaction
      function unlockAudio() {
        if (audioUnlocked) return;
        audio.play().then(() => {
          audio.pause();
          audio.currentTime = 0;
          audioUnlocked = true;
        }).catch(() => {}); // Expected if no interaction yet
      }
      document.body.addEventListener('click', unlockAudio, { once: true });
      document.body.addEventListener('keydown', unlockAudio, { once: true });

      // Title Sync
      const originalTitle = document.title;
      function updateTitle() {
        if (unreadInSession > 0) {
            document.title = `(${unreadInSession}) ${originalTitle}`;
        } else {
            document.title = originalTitle;
        }
      }

      // Reset unread on focus
      window.addEventListener('focus', function() {
        if (unreadInSession > 0) {
            unreadInSession = 0;
            updateTitle();
        }
      });

      // Scroll to bottom initially
      messagesContainer.scrollTop = messagesContainer.scrollHeight;
      
      // Check if user is near bottom
      function isNearBottom() {
        return messagesContainer.scrollHeight - messagesContainer.scrollTop - messagesContainer.clientHeight < 100;
      }
      
      // Update badge UI
      function updateStatusUI(status) {
        if (!statusBadge) return;
        statusBadge.textContent = status.charAt(0).toUpperCase() + status.slice(1);
        
        // Reset classes
        statusBadge.className = 'badge';
        if (status === 'open') statusBadge.classList.add('badge-draft');
        else if (status === 'closed') statusBadge.classList.add('badge-pub');
        else statusBadge.classList.add('bg-amber-100', 'text-amber-700', 'border-amber-200', 'dark:bg-amber-900/20', 'dark:text-amber-300', 'dark:border-amber-900/40');
      }
      
      // Render typing indicator
      function updateTypingIndicator(isGuestTyping) {
        const existing = document.getElementById('typing-indicator-row');
        if (isGuestTyping) {
            if (!existing) {
                const html = `<div id="typing-indicator-row" class="flex justify-start">
                    <div class="bg-white dark:bg-slate-800 border border-border-light dark:border-border-dark rounded-xl rounded-tl-sm px-4 py-2 shadow-sm">
                        <div class="text-xs text-slate-400 dark:text-slate-500 mb-1">Visitor is typing...</div>
                         <div class="flex space-x-1 items-center h-4">
                           <div class="w-1.5 h-1.5 bg-slate-400 rounded-full animate-bounce"></div>
                           <div class="w-1.5 h-1.5 bg-slate-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                           <div class="w-1.5 h-1.5 bg-slate-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                         </div>
                    </div>
                </div>`;
                messagesContainer.insertAdjacentHTML('beforeend', html);
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            }
        } else {
            if (existing) existing.remove();
        }
      }

      // Append a message to the container
      function appendMessage(msg) {
        // Deduplicate
        if (msg.id && msg.id <= lastMessageId) return;
        if (msg.id) lastMessageId = msg.id;

        // Remove typing indicator
        const typingRow = document.getElementById('typing-indicator-row');
        if (typingRow) typingRow.remove();

        const wasNearBottom = isNearBottom();
        let html = '';
        
        if (msg.sender_type === 'system') {
          html = `<div class="flex justify-center">
            <div class="text-xs text-slate-400 dark:text-slate-500 bg-slate-100 dark:bg-slate-800 px-3 py-1 rounded-full">
              ${escapeHtml(msg.message)}
            </div>
          </div>`;
        } else if (msg.sender_type === 'visitor') {
          const time = new Date(msg.created_at).toLocaleString('en-US', { month: 'short', day: 'numeric', hour: 'numeric', minute: '2-digit' });
          html = `<div class="flex justify-start">
            <div class="max-w-[80%] bg-white dark:bg-slate-800 border border-border-light dark:border-border-dark rounded-xl rounded-tl-sm px-4 py-2 shadow-sm">
              <div class="text-xs text-slate-400 dark:text-slate-500 mb-1">{{ $conversation->name }}</div>
              <p class="text-sm text-text-strong dark:text-white whitespace-pre-wrap break-words">${escapeHtml(msg.message)}</p>
              <div class="text-[10px] text-slate-400 mt-1">${time}</div>
            </div>
          </div>`;
          
          // Sound for visitor message & Title Update
          try { audio.play().catch(()=>{}); } catch(e){}
          
          if (document.hidden) {
            unreadInSession++;
            updateTitle();
          }

        } else {
          const time = new Date(msg.created_at).toLocaleString('en-US', { month: 'short', day: 'numeric', hour: 'numeric', minute: '2-digit' });
          html = `<div class="flex justify-end">
            <div class="max-w-[80%] bg-primary text-white rounded-xl rounded-tr-sm px-4 py-2 shadow-sm">
              <div class="text-xs text-blue-200 mb-1">${escapeHtml(msg.user_name || 'Agent')}</div>
              <p class="text-sm whitespace-pre-wrap break-words">${escapeHtml(msg.message)}</p>
              <div class="text-[10px] text-blue-200 mt-1">${time}</div>
            </div>
          </div>`;
        }
        
        messagesContainer.insertAdjacentHTML('beforeend', html);
        
        if (wasNearBottom) {
          messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
      }
      
      function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
      }
      
      // Typing Outbound
      replyMessage.addEventListener('input', function() {
        const now = Date.now();
        if (now - lastTypingSent > 4000) {
            lastTypingSent = now;
            fetch(`/admin/support/${conversationId}/typing`, {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json', 
                    'X-CSRF-TOKEN': csrfToken, 
                    'Accept': 'application/json' 
                },
            }).catch(()=>{});
        }
      });

      // SSE / Polling Logic
      function startStream() {
        if (usePollingFallback) {
            startPolling();
            return;
        }
        if (eventSource) return;

        const url = `/admin/support/${conversationId}/stream?after_id=${lastMessageId}`;
        eventSource = new EventSource(url, { withCredentials: true });
        
        eventSource.addEventListener('message.created', e => {
           try {
               const msg = JSON.parse(e.data);
               appendMessage(msg);
           } catch(err) { console.error(err); }
        });
        
        eventSource.addEventListener('conversation.status_changed', e => {
           try {
               const data = JSON.parse(e.data);
               updateStatusUI(data.status_to);
               if (window.showToast) showToast({ tone: 'info', title: 'Status Update', message: `Conversation is now ${data.status_to}` });
           } catch(err) { console.error(err); }
        });
        
        eventSource.addEventListener('typing', e => {
           try {
               const data = JSON.parse(e.data);
               updateTypingIndicator(data.guest);
           } catch(err) {} 
        });
        
        eventSource.onerror = () => {
            console.warn('SSE Disconnected. Switching to polling.');
            eventSource.close();
            eventSource = null;
            usePollingFallback = true;
            startPolling();
        };
      }

      function stopStream() {
          if (eventSource) {
              eventSource.close();
              eventSource = null;
          }
          stopPolling();
      }

      // Poll as fallback
      async function pollMessages() {
        try {
          const response = await fetch(`/admin/support/${conversationId}/messages?after_id=${lastMessageId}`, {
            credentials: 'same-origin',
            headers: { 'Accept': 'application/json' }
          });
          
          if (response.ok) {
            const data = await response.json();
            if (data.messages && data.messages.length > 0) {
              data.messages.forEach(msg => appendMessage(msg));
              // lastMessageId updated in appendMessage
            }
            if (data.conversation_status) {
                updateStatusUI(data.conversation_status);
            }
            if (data.typing) {
                updateTypingIndicator(data.typing.guest);
            }
          }
        } catch (e) {
          console.error('Poll error:', e);
        }
      }
      
      function startPolling() {
          if (pollTimer) return;
          pollMessages();
          pollTimer = setInterval(pollMessages, 4000);
      }
      
      function stopPolling() {
          if (pollTimer) {
              clearInterval(pollTimer);
              pollTimer = null;
          }
      }
      
      // Start
      startStream();
      
      // Handle reply form submit
      replyForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const message = replyMessage.value.trim();
        if (!message) return;
        
        sendBtn.disabled = true;
        sendBtn.innerHTML = '<span class="material-icons-outlined text-[18px] animate-spin" aria-hidden="true">refresh</span> Sending...';
        
        try {
          const response = await fetch(`/admin/support/${conversationId}/messages`, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
              'Content-Type': 'application/json',
              'Accept': 'application/json',
              'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ message })
          });
          
          if (response.ok) {
            const data = await response.json();
            if (data.message) {
              appendMessage(data.message);
            }
            replyMessage.value = '';
            showToast({ tone: 'success', title: 'Sent', message: 'Reply sent successfully.' });
          } else {
            const errorData = await response.text();
            console.error('Reply error:', response.status, errorData);
            showToast({ tone: 'danger', title: 'Error', message: 'Failed to send reply. Check console.' });
          }
        } catch (e) {
          console.error('Network error:', e);
          showToast({ tone: 'danger', title: 'Error', message: 'Network error. Try again.' });
        }
        
        sendBtn.disabled = false;
        sendBtn.innerHTML = '<span class="material-icons-outlined text-[18px]" aria-hidden="true">send</span> Send';
      });
      
      // Handle Enter to submit (Shift+Enter for new line)
      replyMessage.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
          e.preventDefault();
          replyForm.dispatchEvent(new Event('submit'));
        }
      });
    })();
    </script>
  </x-slot:scripts>
</x-admin.layout>

<x-admin.layout :title="'Conversation · Mini CMS'" :crumb="'Support'">
  <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div class="flex flex-col">
      <div class="flex items-center gap-3">
        <a class="btn bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 transition-colors shadow-sm rounded-lg px-3 py-2 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-slate-200" href="{{ route('admin.support.index') }}">
          <span class="material-icons-outlined text-[18px] align-middle">arrow_back</span>
        </a>
        <h1 class="text-2xl font-semibold text-slate-900 dark:text-white tracking-tight">
          Conversation with {{ $conversation->name }}
        </h1>
      </div>
      <p class="mt-1 text-sm text-slate-600 ml-[3.25rem]">
        Started {{ $conversation->created_at->format('M j, Y \a\t g:i A') }}
      </p>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
    {{-- Chat Panel --}}
    <div class="lg:col-span-8 flex flex-col">
      <div class="card flex flex-col rounded-xl border border-slate-200 shadow-sm overflow-hidden bg-white dark:bg-slate-900 dark:border-slate-700">
        
        {{-- Chat Header --}}
        <div class="px-4 py-3 bg-gradient-to-r from-slate-50 to-slate-100/80 border-b border-slate-200 dark:from-slate-800 dark:to-slate-800/80 dark:border-slate-700 flex justify-between items-center">
             <div class="flex items-center gap-2">
               <span class="text-sm font-semibold text-slate-900 dark:text-white">Messages</span>
               <span class="inline-flex items-center justify-center px-2 py-0.5 text-[10px] font-bold bg-slate-200 text-slate-600 rounded-full dark:bg-slate-700 dark:text-slate-300">{{ $conversation->messages->count() }}</span>
             </div>
             @php
                $statusPill = match($conversation->status) {
                  'open' => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
                  'pending' => 'bg-amber-50 text-amber-700 border border-amber-200',
                  'closed' => 'bg-slate-100 text-slate-700 border border-slate-200',
                };
             @endphp
             <span class="text-xs rounded-full px-2 py-0.5 font-medium {{ $statusPill }}">
                {{ ucfirst($conversation->status) }}
             </span>
        </div>

        {{-- Messages Container with subtle gradient bg --}}
        <div id="messagesContainer" class="h-[calc(100vh-320px)] min-h-[420px] overflow-y-auto p-4 space-y-3 bg-gradient-to-b from-white via-slate-50/30 to-white dark:from-slate-900 dark:via-slate-800/30 dark:to-slate-900 scroll-smooth relative">
          @if($conversation->messages->count() === 0)
            <div class="flex flex-col items-center justify-center h-full text-slate-400">
              <span class="material-icons-outlined text-4xl mb-2 opacity-20">chat_bubble_outline</span>
              <p class="text-sm">No messages yet.</p>
            </div>
          @endif

          @foreach($conversation->messages as $msg)
            @if($msg->sender_type === 'system')
              <div class="flex items-center gap-3 my-4">
                <div class="h-px bg-slate-100 flex-1 dark:bg-slate-800"></div>
                <div class="text-[10px] uppercase tracking-wider text-slate-400 font-medium">
                  {{ $msg->message }}
                </div>
                <div class="h-px bg-slate-100 flex-1 dark:bg-slate-800"></div>
              </div>
            @elseif($msg->sender_type === 'visitor')
              <div class="flex justify-start animate-fade-in">
                <div class="max-w-[75%] group flex gap-2">
                  {{-- Avatar Initial --}}
                  <div class="flex-shrink-0 w-8 h-8 rounded-full bg-gradient-to-br from-slate-200 to-slate-300 dark:from-slate-600 dark:to-slate-700 flex items-center justify-center text-xs font-bold text-slate-600 dark:text-slate-200 shadow-sm">
                    {{ strtoupper(substr($conversation->name, 0, 1)) }}
                  </div>
                  <div>
                    <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl rounded-tl-md px-4 py-2.5 shadow-sm text-slate-900 dark:text-slate-100 transition-all hover:shadow-md">
                      <p class="text-sm whitespace-pre-wrap break-words leading-relaxed">{{ $msg->message }}</p>
                    </div>
                    <div class="mt-1 ml-1 text-[11px] text-slate-400 dark:text-slate-500">
                      {{ $msg->created_at->format('M j, g:i A') }}
                    </div>
                  </div>
                </div>
              </div>
            @else
              <div class="flex justify-end animate-fade-in">
                <div class="max-w-[75%] group">
                  <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-2xl rounded-tr-md px-4 py-2.5 shadow-md hover:shadow-lg transition-all">
                     <p class="text-sm whitespace-pre-wrap break-words leading-relaxed">{{ $msg->message }}</p>
                  </div>
                  <div class="mt-1 mr-1 text-right text-[11px] text-slate-400 dark:text-slate-500">
                     {{ $msg->created_at->format('M j, g:i A') }}
                  </div>
                </div>
              </div>
            @endif
          @endforeach
        </div>

        {{-- Typing Indicator Area --}}
        {{-- Typing Indicator Area --}}
        <div id="typingContainer" class="min-h-[28px] px-4 py-2 border-t border-slate-100 text-sm text-slate-500 empty:hidden"></div>

        {{-- Reply Box with gradient border --}}
        <div class="bg-gradient-to-r from-white via-slate-50/50 to-white border-t border-slate-200 px-4 py-3 sticky bottom-0 z-10 transition-all dark:from-slate-900 dark:via-slate-800/50 dark:to-slate-900 dark:border-slate-700">
          <form id="replyForm" class="flex gap-3 items-end">
            @csrf
            <textarea 
              id="replyMessage" 
              name="message" 
              class="input flex-1 resize-none rounded-xl border-slate-200 focus:ring-2 focus:ring-slate-200 focus:border-slate-300 dark:bg-slate-800 dark:border-slate-700 min-h-[2.75rem]" 
              rows="1" 
              placeholder="Type your reply..." 
              required 
              maxlength="2000"
              oninput="this.style.height = ''; this.style.height = Math.min(this.scrollHeight, 150) + 'px'"
            ></textarea>
            <button type="submit" id="sendBtn" class="btn bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white rounded-xl px-5 py-2 shadow-md hover:shadow-lg transition-all flex items-center gap-2 font-medium focus:ring-2 focus:ring-blue-200 items-center justify-center h-[42px] hover:scale-[1.02] active:scale-[0.98]">
              <span class="material-icons-outlined text-[18px]" aria-hidden="true">send</span>
              <span>Send</span>
            </button>
          </form>
        </div>
      </div>
    </div>

    {{-- Context Panel --}}
    <div class="lg:col-span-4 space-y-6">
      {{-- Status Card --}}
      <div class="card p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm bg-white dark:bg-slate-900 hover:shadow-md transition-shadow">
        <div class="text-sm font-semibold text-slate-900 dark:text-white flex items-center gap-2">
          <span class="material-icons-outlined text-lg text-primary">flag</span>
          Current Status
        </div>
        <div class="mt-4">
          @php
            $badgeClass = match($conversation->status) {
              'open' => 'bg-white text-slate-700 border border-slate-200',
              'pending' => 'bg-amber-50 text-amber-700 border border-amber-200 dark:bg-amber-900/20 dark:text-amber-300',
              'closed' => 'bg-slate-100 text-slate-600 border border-slate-200',
            };
          @endphp
          <span id="statusBadge" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badgeClass }}">
            {{ ucfirst($conversation->status) }}
          </span>
        </div>

        <div class="mt-6 space-y-3">
          @if($conversation->status !== 'closed')
            <form method="POST" action="{{ route('admin.support.status', $conversation->id) }}">
              @csrf
              <input type="hidden" name="status" value="closed" />
              <button class="btn w-full bg-blue-600 hover:bg-blue-700 text-white rounded-lg py-2 text-sm font-medium shadow-sm transition-colors focus:ring-2 focus:ring-blue-200" type="submit">
                Close Conversation
              </button>
            </form>
          @endif

          @if($conversation->status !== 'open')
            <form method="POST" action="{{ route('admin.support.status', $conversation->id) }}">
              @csrf
              <input type="hidden" name="status" value="open" />
              <button class="btn w-full bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 rounded-lg py-2 text-sm font-medium shadow-sm transition-colors focus:ring-2 focus:ring-slate-200" type="submit">Reopen as Open</button>
            </form>
          @endif

          @if($conversation->status !== 'pending')
            <form method="POST" action="{{ route('admin.support.status', $conversation->id) }}">
              @csrf
              <input type="hidden" name="status" value="pending" />
              <button class="btn w-full bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 rounded-lg py-2 text-sm font-medium shadow-sm transition-colors focus:ring-2 focus:ring-slate-200" type="submit">Set as Pending</button>
            </form>
          @endif
        </div>
      </div>

      {{-- Visitor Info Card --}}
      <div class="card p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm bg-white dark:bg-slate-900 hover:shadow-md transition-shadow">
        <div class="text-sm font-semibold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
          <span class="material-icons-outlined text-lg text-emerald-500">person</span>
          Visitor Info
        </div>
        <div class="space-y-4 text-sm">
          <div>
            <div class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-1 flex items-center gap-1">
              <span class="material-icons-outlined text-[12px]">badge</span>
              Name
            </div>
            <div class="font-medium text-slate-900 dark:text-white">{{ $conversation->name }}</div>
          </div>

          <div>
            <div class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-1 flex items-center gap-1">
              <span class="material-icons-outlined text-[12px]">mail</span>
              Email
            </div>
            <div>
              @if($conversation->email)
                <a href="mailto:{{ $conversation->email }}" class="text-blue-600 hover:underline font-medium">{{ $conversation->email }}</a>
              @else
                <span class="text-slate-400 italic">Not provided</span>
              @endif
            </div>
          </div>

          @if($conversation->source_url)
            <div>
              <div class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-1">Source URL</div>
              <a href="{{ $conversation->source_url }}" target="_blank" class="block text-blue-600 hover:underline text-xs truncate" title="{{ $conversation->source_url }}">
                {{ $conversation->source_url }}
              </a>
            </div>
          @endif

          @if($conversation->referrer)
            <div>
              <div class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-1">Referrer</div>
              <div class="text-slate-600 dark:text-slate-300 text-xs truncate" title="{{ $conversation->referrer }}">
                {{ $conversation->referrer }}
              </div>
            </div>
          @endif

          @if($conversation->assignedAgent)
            <div>
              <div class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-1">Assigned To</div>
              <div class="text-slate-900 dark:text-white font-medium">{{ $conversation->assignedAgent->email }}</div>
            </div>
          @endif

          <div class="flex justify-between pt-3 border-t border-slate-100 dark:border-slate-800 mt-2">
            <span class="text-slate-500 text-xs">ID</span>
            <span class="text-slate-700 dark:text-slate-300 font-mono text-xs">#{{ $conversation->id }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <x-slot:scripts>
    <style>
      @keyframes fade-in {
        from { opacity: 0; transform: translateY(8px); }
        to { opacity: 1; transform: translateY(0); }
      }
      .animate-fade-in {
        animation: fade-in 0.3s ease-out;
      }
    </style>
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
      
      const typingContainer = document.getElementById('typingContainer');

      // Update badge UI
      function updateStatusUI(status) {
        if (!statusBadge) return;
        statusBadge.textContent = status.charAt(0).toUpperCase() + status.slice(1);
        
        // Reset classes
        statusBadge.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium';
        if (status === 'open') statusBadge.classList.add('bg-emerald-50', 'text-emerald-700', 'border', 'border-emerald-200');
        else if (status === 'closed') statusBadge.classList.add('bg-slate-100', 'text-slate-700', 'border', 'border-slate-200');
        else statusBadge.classList.add('bg-amber-50', 'text-amber-700', 'border', 'border-amber-200');
      }
      
      // Render typing indicator
      function updateTypingIndicator(isGuestTyping) {
        if (!typingContainer) return;
        if (isGuestTyping) {
            typingContainer.classList.remove('hidden');
            typingContainer.innerHTML = `<div class="flex items-center gap-1.5 animate-pulse">
               <span>Visitor is typing</span>
               <div class="flex space-x-0.5">
                  <div class="w-1 h-1 bg-slate-400 rounded-full"></div>
                  <div class="w-1 h-1 bg-slate-400 rounded-full"></div>
                  <div class="w-1 h-1 bg-slate-400 rounded-full"></div>
               </div>
            </div>`;
        } else {
            typingContainer.innerHTML = '';
            typingContainer.classList.add('hidden');
        }
      }

      // Append a message to the container
      function appendMessage(msg) {
        // Deduplicate
        if (msg.id && msg.id <= lastMessageId) return;
        if (msg.id) lastMessageId = msg.id;

        // Remove typing indicator logic (handled by updateTypingIndicator)
        updateTypingIndicator(false);

        const wasNearBottom = isNearBottom();
        const html = createMessageHtml(msg);
        
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

      function createMessageHtml(msg) {
        const visitorInitial = '{{ strtoupper(substr($conversation->name, 0, 1)) }}';
        if (msg.sender_type === 'system') {
          return `<div class="flex items-center gap-3 my-4 animate-fade-in">
            <div class="h-px bg-slate-100 flex-1 dark:bg-slate-800"></div>
              <div class="text-[10px] uppercase tracking-wider text-slate-400 font-medium">
                ${escapeHtml(msg.message)}
              </div>
            <div class="h-px bg-slate-100 flex-1 dark:bg-slate-800"></div>
          </div>`;
        } else if (msg.sender_type === 'visitor') {
          const time = new Date(msg.created_at).toLocaleString('en-US', { month: 'short', day: 'numeric', hour: 'numeric', minute: '2-digit' });
          return `<div class="flex justify-start animate-fade-in">
            <div class="max-w-[75%] group flex gap-2">
              <div class="flex-shrink-0 w-8 h-8 rounded-full bg-gradient-to-br from-slate-200 to-slate-300 dark:from-slate-600 dark:to-slate-700 flex items-center justify-center text-xs font-bold text-slate-600 dark:text-slate-200 shadow-sm">
                ${visitorInitial}
              </div>
              <div>
                <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl rounded-tl-md px-4 py-2.5 shadow-sm text-slate-900 dark:text-slate-100 transition-all hover:shadow-md">
                  <p class="text-sm whitespace-pre-wrap break-words leading-relaxed">${escapeHtml(msg.message)}</p>
                </div>
                <div class="mt-1 ml-1 text-[11px] text-slate-400 dark:text-slate-500">
                  ${time}
                </div>
              </div>
            </div>
          </div>`;
        } else {
          const time = new Date(msg.created_at).toLocaleString('en-US', { month: 'short', day: 'numeric', hour: 'numeric', minute: '2-digit' });
          return `<div class="flex justify-end animate-fade-in">
            <div class="max-w-[75%] group">
              <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-2xl rounded-tr-md px-4 py-2.5 shadow-md hover:shadow-lg transition-all">
                 <p class="text-sm whitespace-pre-wrap break-words leading-relaxed">${escapeHtml(msg.message)}</p>
              </div>
              <div class="mt-1 mr-1 text-right text-[11px] text-slate-400 dark:text-slate-500">
                 ${time}
              </div>
            </div>
          </div>`;
        }
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

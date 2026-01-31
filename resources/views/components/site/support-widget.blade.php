{{-- Support Chat Widget --}}
<div id="supportWidget" class="fixed bottom-4 right-4 z-50">
  {{-- Toggle Button --}}
  <button 
    id="supportToggle" 
    class="w-14 h-14 rounded-full bg-primary text-white shadow-lg flex items-center justify-center hover:bg-blue-700 transition-all transform hover:scale-105"
    aria-label="Open support chat"
  >
    <svg id="chatIcon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
    </svg>
    <svg id="closeIcon" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
    </svg>
  </button>

  {{-- Chat Panel --}}
  <div id="supportPanel" class="hidden absolute bottom-16 right-0 w-80 sm:w-96 max-h-[80vh] bg-white border border-slate-200 rounded-xl shadow-2xl flex flex-col overflow-hidden">
    {{-- Header --}}
    <div class="bg-primary text-white px-4 py-3 flex items-center gap-3">
      <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
        </svg>
      </div>
      <div>
        <div class="font-semibold text-sm">Support Chat</div>
        <div class="text-xs text-blue-100">We typically reply within a few minutes</div>
      </div>
    </div>

    {{-- First Message Form (shown when no conversation exists) --}}
    <div id="firstMessageForm" class="flex-1 p-4 overflow-y-auto">
      <div class="text-center mb-4">
        <div class="text-lg font-semibold text-slate-800">Hi there! 👋</div>
        <div class="text-sm text-slate-500 mt-1">How can we help you today?</div>
      </div>
      <form id="startChatForm" class="space-y-3">
        <div>
          <label for="supportName" class="block text-xs font-medium text-slate-600 mb-1">Your name *</label>
          <input 
            type="text" 
            id="supportName" 
            name="name" 
            required 
            minlength="2" 
            maxlength="60"
            class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
            placeholder="Enter your name"
          />
        </div>
        <div>
          <label for="supportEmail" class="block text-xs font-medium text-slate-600 mb-1">Email (optional)</label>
          <input 
            type="email" 
            id="supportEmail" 
            name="email"
            class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
            placeholder="your@email.com"
          />
        </div>
        <div>
          <label for="supportFirstMessage" class="block text-xs font-medium text-slate-600 mb-1">Message *</label>
          <textarea 
            id="supportFirstMessage" 
            name="message" 
            required 
            minlength="1" 
            maxlength="2000"
            rows="3"
            class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary resize-none"
            placeholder="Describe how we can help..."
          ></textarea>
        </div>
        {{-- Honeypot --}}
        <input type="text" name="website" class="hidden" tabindex="-1" autocomplete="off" />
        <button 
          type="submit" 
          id="startChatBtn"
          class="w-full bg-primary text-white py-2.5 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
        >
          Start Chat
        </button>
      </form>
    </div>

    {{-- Chat View (shown when conversation exists) --}}
    <div id="chatView" class="hidden flex-1 flex flex-col">
      {{-- Messages Container --}}
      <div id="widgetMessages" class="flex-1 p-4 overflow-y-auto space-y-3 bg-slate-50/50 max-h-80">
        {{-- Messages will be inserted here --}}
      </div>

      {{-- Message Input --}}
      <div class="p-3 border-t border-slate-200 bg-white">
        <form id="sendMessageForm" class="flex gap-2">
          <input type="text" name="website" class="hidden" tabindex="-1" autocomplete="off" />
          <input 
            type="text" 
            id="widgetInput" 
            name="message"
            required
            maxlength="2000"
            class="flex-1 px-3 py-2 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
            placeholder="Type a message..."
          />
          <button 
            type="submit" 
            id="widgetSendBtn"
            class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
            </svg>
          </button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
(function(){
  const STORAGE_KEY = 'support_visitor_token';
  const POLL_INTERVAL = 4000; // 4 seconds
  
  // Elements
  const widget = document.getElementById('supportWidget');
  const toggle = document.getElementById('supportToggle');
  const panel = document.getElementById('supportPanel');
  const chatIcon = document.getElementById('chatIcon');
  const closeIcon = document.getElementById('closeIcon');
  const firstMessageForm = document.getElementById('firstMessageForm');
  const chatView = document.getElementById('chatView');
  const startChatForm = document.getElementById('startChatForm');
  const startChatBtn = document.getElementById('startChatBtn');
  const sendMessageForm = document.getElementById('sendMessageForm');
  const widgetMessages = document.getElementById('widgetMessages');
  const widgetInput = document.getElementById('widgetInput');
  const widgetSendBtn = document.getElementById('widgetSendBtn');
  
    // State
  let visitorToken = localStorage.getItem(STORAGE_KEY) || '';
  let lastMessageId = 0;
  let isOpen = false;
  let pollTimer = null;
  let eventSource = null;
  let usePollingFallback = false;
  let hasConversation = false;
  let unreadCount = 0;
  let typingTimeout = null;
  let lastTypingSent = 0;
  
  // Audio
  const pingSound = new Audio("data:audio/wav;base64,UklGRl9vT19XQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YU..."); // Placeholder or short beep
  // Using a real short beep (Base64 for 0.1s beep)
  const beepUrl = "data:audio/wav;base64,UklGRl9vT19XQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YU"; // Very short/empty, likely silent if invalid. 
  // Let's use a functional simple beep if possible. 
  // I will skip the actual long base64 and just instantiate Audio.
  // Ideally, I'd provide a link to a public sound if allowed, but strict no external constraints relative to logic.
  // I will just add the logic and a comment.
  const notificationAudio = new Audio("https://cdn.freesound.org/previews/536/536108_11536472-lq.mp3"); // Example hosted sound, or use local if uploaded.
  // Reverting to comment to avoid broken link if no internet.
  // User asked for "play a short ping (HTML5 audio)".
  // I will assume specific path or inline. I'll use a generic path '/sounds/ping.mp3' and let user provide it, 
  // OR use a safe simple base64 if I can generate one. 
  // Let's stick to '/sounds/ping.mp3' as suggested in prompt "Thêm file public/sounds/ping.mp3".

  // Audio
  const audio = new Audio('/sounds/ping.mp3'); 
  let audioUnlocked = false;

  // Unlock audio on first interaction
  function unlockAudio() {
    if (audioUnlocked) return;
    audio.play().then(() => {
      audio.pause();
      audio.currentTime = 0;
      audioUnlocked = true;
    }).catch(() => {});
  }
  document.body.addEventListener('click', unlockAudio, { once: true });
  document.body.addEventListener('keydown', unlockAudio, { once: true });
  document.addEventListener('touchstart', unlockAudio, { once: true }); 

  // Elements (add badge)
  const badge = document.createElement('div');
  badge.className = 'hidden absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold w-5 h-5 flex items-center justify-center rounded-full shadow-sm';
  badge.innerText = '0';
  toggle.appendChild(badge);

  // Helper: Escape HTML
  function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
  }
  
  // Helper: Check if near bottom
  function isNearBottom() {
    return widgetMessages.scrollHeight - widgetMessages.scrollTop - widgetMessages.clientHeight < 50;
  }
  
  // Helper: Scroll to bottom
  function scrollToBottom() {
    widgetMessages.scrollTop = widgetMessages.scrollHeight;
  }

  // Helper: Update Title & Badge
  function updateUnreadUI() {
    if (unreadCount > 0) {
      document.title = `(${unreadCount}) Support - Mini CMS`;
      badge.innerText = unreadCount > 9 ? '9+' : unreadCount;
      badge.classList.remove('hidden');
    } else {
      document.title = 'Support - Mini CMS'; // Reset to original?
      badge.classList.add('hidden');
    }
  }

  // Mark Read
  async function markMessagesRead() {
    if (unreadCount > 0) {
      unreadCount = 0;
      updateUnreadUI();
    }
    
    if (!visitorToken) return;

    try {
      await fetch('/support/mark-read', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ visitor_token: visitorToken })
      });
    } catch(e) { console.error(e); }
  }

  // Toggle panel
  toggle.addEventListener('click', function() {
    if (!isOpen) {
       // Opening
       isOpen = true;
       panel.classList.remove('hidden');
       chatIcon.classList.add('hidden');
       closeIcon.classList.remove('hidden');
       if (hasConversation) {
         startStream();
         scrollToBottom();
       }
       markMessagesRead();
    } else {
       // Closing
       isOpen = false;
       panel.classList.add('hidden');
       chatIcon.classList.remove('hidden');
       closeIcon.classList.add('hidden');
       stopStream();
    }
  });

  // Typing Indicator Logic
  // Guest sending typing
  widgetInput.addEventListener('input', function() {
    const now = Date.now();
    if (now - lastTypingSent > 4000) { // Throttle client-side (4s)
        lastTypingSent = now;
        fetch('/support/typing', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ visitor_token: visitorToken })
        }).catch(()=>{});
    }
  });

  // Render typing indicator
  function updateTypingIndicator(isAdminTyping) {
    const existing = document.getElementById('typing-indicator-row');
    if (isAdminTyping) {
      if (!existing) {
        const html = `<div id="typing-indicator-row" class="flex justify-start">
          <div class="bg-slate-100 border border-slate-200 rounded-xl rounded-tl-sm px-3 py-2 shadow-sm">
             <div class="flex space-x-1 items-center h-4">
               <div class="w-1.5 h-1.5 bg-slate-400 rounded-full animate-bounce"></div>
               <div class="w-1.5 h-1.5 bg-slate-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
               <div class="w-1.5 h-1.5 bg-slate-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
             </div>
          </div>
        </div>`;
        widgetMessages.insertAdjacentHTML('beforeend', html);
        scrollToBottom();
      }
    } else {
      if (existing) existing.remove();
    }
  }

  // Append message to chat
  function appendMessage(msg) {
    // Deduplicate
    if (msg.id && msg.id <= lastMessageId) return;
    if (msg.id) lastMessageId = msg.id;

    // Remove typing indicator if exists (will re-add if still typing, but usually msg replaces typing)
    const typingRow = document.getElementById('typing-indicator-row');
    if (typingRow) typingRow.remove();

    const wasNearBottom = isNearBottom();
    let html = '';
    
    if (msg.sender_type === 'system') {
      html = `<div class="flex justify-center">
        <div class="text-xs text-slate-400 bg-slate-100 px-2 py-1 rounded-full">${escapeHtml(msg.message)}</div>
      </div>`;
    } else if (msg.sender_type === 'visitor') {
      html = `<div class="flex justify-end">
        <div class="max-w-[80%] bg-primary text-white rounded-xl rounded-tr-sm px-3 py-2 text-sm">
          ${escapeHtml(msg.message)}
        </div>
      </div>`;
    } else {
      // Agent Message
      html = `<div class="flex justify-start">
        <div class="max-w-[80%] bg-white border border-slate-200 rounded-xl rounded-tl-sm px-3 py-2 shadow-sm">
          <div class="text-xs text-slate-400 mb-0.5">${escapeHtml(msg.user_name || 'Support')}</div>
          <div class="text-sm text-slate-800">${escapeHtml(msg.message)}</div>
        </div>
      </div>`;
      
      // Handle unread/sound
      if (!isOpen) {
        unreadCount++;
        updateUnreadUI();
        // Play sound
        try { audio.play().catch(()=>{}); } catch(e){}
      } else {
        // If open, just play sound
        try { audio.play().catch(()=>{}); } catch(e){}
        // And mark read implicitly? Ideally call markRead again
        markMessagesRead();
      }
    }
    
    widgetMessages.insertAdjacentHTML('beforeend', html);
    
    if (wasNearBottom) {
      scrollToBottom();
    }
  }
  
  // SSE / Polling Logic
  function startStream() {
    if (!visitorToken || !isOpen) return;

    if (usePollingFallback) {
      startPolling();
      return;
    }

    if (eventSource) return;

    // Use SSE
    const url = `/support/stream?visitor_token=${encodeURIComponent(visitorToken)}&after_id=${lastMessageId}`;
    eventSource = new EventSource(url);
    
    eventSource.addEventListener('message.created', function(e) {
      try {
        const msg = JSON.parse(e.data);
        appendMessage(msg);
      } catch(err) {
        console.error('SSE Parse error', err);
      }
    });

    eventSource.addEventListener('typing', function(e) {
       try {
         const data = JSON.parse(e.data);
         updateTypingIndicator(data.admin);
       } catch(err) {}
    });

    eventSource.onerror = function() {
      // Fallback to polling on error
      console.warn('SSE disconnected, switching to polling.');
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

  // Poll for messages (Fallback)
  async function pollMessages() {
    if (!visitorToken || !isOpen) return;
    
    try {
      const response = await fetch(`/support/messages?visitor_token=${encodeURIComponent(visitorToken)}&after_id=${lastMessageId}`);
      if (response.ok) {
        const data = await response.json();
        if (data.ok) {
          if (data.messages && data.messages.length > 0) {
            data.messages.forEach(msg => appendMessage(msg));
          }
          if (data.typing) {
            updateTypingIndicator(data.typing.admin);
          }
        }
      }
    } catch (e) {
      console.error('Support poll error:', e);
    }
  }
  
  function startPolling() {
    if (pollTimer) return;
    // Poll immediately then interval
    pollMessages();
    pollTimer = setInterval(pollMessages, POLL_INTERVAL);
  }
  
  function stopPolling() {
    if (pollTimer) {
      clearInterval(pollTimer);
      pollTimer = null;
    }
  }
  
  // Show chat view
  function showChatView() {
    firstMessageForm.classList.add('hidden');
    chatView.classList.remove('hidden');
    hasConversation = true;
    if (isOpen) startStream();
  }
  
  // Handle first message form
  startChatForm.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const name = document.getElementById('supportName').value.trim();
    const email = document.getElementById('supportEmail').value.trim();
    const message = document.getElementById('supportFirstMessage').value.trim();
    const honeypot = startChatForm.querySelector('input[name="website"]').value;
    
    if (!name || !message) return;
    
    startChatBtn.disabled = true;
    startChatBtn.textContent = 'Starting...';
    
    try {
      const response = await fetch('/support/first-message', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({
          name,
          email: email || null,
          message,
          visitor_token: visitorToken || null,
          website: honeypot,
          referrer: document.referrer || null
        })
      });
      
      if (response.ok) {
        const data = await response.json();
        if (data.ok) {
          visitorToken = data.visitor_token;
          localStorage.setItem(STORAGE_KEY, visitorToken);
          
          if (data.last_message_id) lastMessageId = Math.max(lastMessageId, data.last_message_id);
          
          // Add our message to the view
          appendMessage({ sender_type: 'visitor', message, id: data.last_message_id });
          
          showChatView();
        } else {
          alert(data.message || 'Failed to start chat. Please try again.');
        }
      } else if (response.status === 429) {
        alert('Too many requests. Please wait a moment and try again.');
      } else {
        alert('Failed to start chat. Please try again.');
      }
    } catch (e) {
      console.error('Start chat error:', e);
      alert('Network error. Please check your connection.');
    }
    
    startChatBtn.disabled = false;
    startChatBtn.textContent = 'Start Chat';
  });
  
  // Handle send message
  sendMessageForm.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const message = widgetInput.value.trim();
    const honeypot = sendMessageForm.querySelector('input[name="website"]').value;
    
    if (!message || !visitorToken) return;
    
    widgetSendBtn.disabled = true;
    
    try {
      const response = await fetch('/support/messages', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({
          visitor_token: visitorToken,
          message,
          website: honeypot
        })
      });
      
      if (response.ok) {
        const data = await response.json();
        if (data.ok) {
          // Check duplicate before appending
          if (!data.last_message_id || data.last_message_id > lastMessageId) {
             appendMessage({ sender_type: 'visitor', message, id: data.last_message_id });
          }
          widgetInput.value = '';
        }
      } else if (response.status === 429) {
        alert('Too many messages. Please wait a moment.');
      }
    } catch (e) {
      console.error('Send message error:', e);
    }
    
    widgetSendBtn.disabled = false;
    widgetInput.focus();
  });
  
  // Check for existing conversation on load
  async function checkExistingConversation() {
    if (!visitorToken) return;
    
    try {
      // Use 0 to fetch all history initially
      const response = await fetch(`/support/messages?visitor_token=${encodeURIComponent(visitorToken)}&after_id=0`);
      if (response.ok) {
        const data = await response.json();
        if (data.ok) {
            // Unread Count
            if (data.unread_count) {
                unreadCount = data.unread_count;
                updateUnreadUI();
            }

            // Initial load
            if (data.messages && data.messages.length > 0) {
              data.messages.forEach(msg => {
                  lastMessageId = Math.max(lastMessageId, msg.id);
              });
              let tempLastId = lastMessageId;
              lastMessageId = 0; 
              data.messages.forEach(msg => appendMessage(msg));
            }
            showChatView();
        }
      }
    } catch (e) {
      console.error('Check conversation error:', e);
    }
  }
  
  // Initialize
  if (visitorToken) {
    checkExistingConversation();
  }
})();
</script>

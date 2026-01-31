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
  
  // Toggle panel
  toggle.addEventListener('click', function() {
    isOpen = !isOpen;
    panel.classList.toggle('hidden', !isOpen);
    chatIcon.classList.toggle('hidden', isOpen);
    closeIcon.classList.toggle('hidden', !isOpen);
    
    if (isOpen && hasConversation) {
      startStream();
      widgetMessages.scrollTop = widgetMessages.scrollHeight;
    } else {
      stopStream();
    }
  });
  
  // Append message to chat
  function appendMessage(msg) {
    // Deduplicate
    if (msg.id && msg.id <= lastMessageId) return;
    if (msg.id) lastMessageId = msg.id;

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
      html = `<div class="flex justify-start">
        <div class="max-w-[80%] bg-white border border-slate-200 rounded-xl rounded-tl-sm px-3 py-2 shadow-sm">
          <div class="text-xs text-slate-400 mb-0.5">${escapeHtml(msg.user_name || 'Support')}</div>
          <div class="text-sm text-slate-800">${escapeHtml(msg.message)}</div>
        </div>
      </div>`;
    }
    
    widgetMessages.insertAdjacentHTML('beforeend', html);
    
    if (wasNearBottom) {
      widgetMessages.scrollTop = widgetMessages.scrollHeight;
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
        if (data.ok && data.messages && data.messages.length > 0) {
          data.messages.forEach(msg => appendMessage(msg));
          // lastMessageId is updated in appendMessage
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
            // Initial load
            if (data.messages && data.messages.length > 0) {
              data.messages.forEach(msg => {
                  lastMessageId = Math.max(lastMessageId, msg.id);
                  // Manually append (ignoring checks since it's init) or use appendMessage
                  // Better to use appendMessage logic properly
                  // Reset lastMessageId to 0 before appending?
                  // Logic: appendMessage checks <= lastMessageId.
                  // So we must handle this carefully.
                  // Simplified: we trust history call.
                  // But wait appendMessage sets lastMessageId=msg.id.
                  // So we can just call it sequentially.
              });
              // Reset lastID to allow filling from 0
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

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
  <meta name="csrf-token" content="{{ csrf_token() }}"/>
  <title>{{ $title ?? 'Mini CMS' }}</title>

  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin=""/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet"/>

  <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
  <script>
    tailwind.config = {
      darkMode: "class",
      theme: {
        extend: {
          colors: {
            primary: "rgb(var(--c-primary) / <alpha-value>)",
            "background-light": "rgb(var(--c-bg) / <alpha-value>)",
            "background-dark": "rgb(var(--c-bg-dark) / <alpha-value>)",
            "surface-light": "rgb(var(--c-surface) / <alpha-value>)",
            "surface-dark": "rgb(var(--c-surface-dark) / <alpha-value>)",
            "border-light": "rgb(var(--c-border) / <alpha-value>)",
            "border-dark": "rgb(var(--c-border-dark) / <alpha-value>)",
            "text-strong": "rgb(var(--c-text-strong) / <alpha-value>)",
            "text": "rgb(var(--c-text) / <alpha-value>)",
            "text-muted": "rgb(var(--c-text-muted) / <alpha-value>)",
          },
          fontFamily: { sans: ["Inter","sans-serif"] },
          boxShadow: {
            soft: "0 10px 30px rgba(2,6,23,.06)",
            soft2: "0 4px 10px rgba(2,6,23,.06)"
          }
        }
      }
    };
  </script>

  <style>
    :root{
      --c-primary: 37 99 235;
      --c-bg: 247 248 251;
      --c-surface: 255 255 255;
      --c-border: 230 232 240;
      --c-text-strong: 15 23 42;
      --c-text: 51 65 85;
      --c-text-muted: 100 116 139;

      --c-bg-dark: 11 18 32;
      --c-surface-dark: 17 27 46;
      --c-border-dark: 31 42 68;
    }
    html{ scroll-behavior:smooth; }
    summary::-webkit-details-marker{ display:none; }
    summary{ list-style:none; }
  </style>

  <style type="text/tailwindcss">
    @layer base { body { @apply bg-background-light text-text dark:bg-background-dark dark:text-slate-200 antialiased; } }
    @layer components {
      .card { @apply bg-surface-light dark:bg-surface-dark border border-border-light dark:border-border-dark rounded-xl shadow-soft; }
      .card-hd { @apply px-6 py-4 border-b border-border-light dark:border-border-dark; }

      .btn { @apply inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition;
             @apply focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/25; }
      .btn-primary { @apply btn bg-primary text-white hover:bg-blue-700 shadow-soft2; }
      .btn-ghost { @apply btn bg-transparent border border-border-light dark:border-border-dark hover:bg-slate-50 dark:hover:bg-slate-800; }
      .btn-soft { @apply btn bg-primary/10 text-primary hover:bg-primary/15; }
      .btn-danger { @apply btn text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 border border-transparent; }

      .input { @apply w-full bg-surface-light dark:bg-surface-dark border border-border-light dark:border-border-dark rounded-lg px-3 py-2 text-sm;
               @apply focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary; }
      .select { @apply input appearance-none pr-10 cursor-pointer; }

      .badge { @apply inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold tracking-wide border; }
      .badge-draft { @apply bg-slate-100 text-slate-600 border-slate-200 dark:bg-slate-800 dark:text-slate-200 dark:border-slate-700; }
      .badge-pub { @apply bg-emerald-100 text-emerald-700 border-emerald-200 dark:bg-emerald-900/20 dark:text-emerald-300 dark:border-emerald-900/40; }
      .badge-soon { @apply bg-slate-100 text-slate-500 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700; }

      .chk { @apply h-4 w-4 rounded-[4px] border-slate-300 text-primary focus:ring-2 focus:ring-primary/30 dark:bg-slate-800 dark:border-slate-600 cursor-pointer; }

      .table { @apply w-full text-left border-collapse; }
      .th { @apply py-3 px-4 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider; }
      .td { @apply py-3 px-4 text-sm; }
      .row { @apply hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors; }

      .toast { @apply flex items-start gap-3 p-4 rounded-xl border shadow-soft2 bg-surface-light dark:bg-surface-dark border-border-light dark:border-border-dark; }
      .toast-title { @apply text-sm font-semibold text-slate-900 dark:text-white; }
      .toast-msg { @apply text-sm text-slate-600 dark:text-slate-300; }

      .modal-backdrop { @apply fixed inset-0 bg-slate-900/40 backdrop-blur-[2px] z-50; }
      .modal { @apply fixed inset-0 z-50 flex items-center justify-center p-4; }
      .modal-panel { @apply card w-full max-w-md p-6; }
    }
  </style>
</head>

<body class="font-sans transition-colors duration-200">
<div class="flex h-screen overflow-hidden">
  <aside class="w-64 bg-surface-light dark:bg-surface-dark border-r border-border-light dark:border-border-dark flex-shrink-0 hidden md:flex flex-col z-20">
    <div class="h-16 flex items-center px-6 border-b border-border-light dark:border-border-dark">
      <a class="flex items-center gap-2 font-bold text-lg tracking-tight text-text-strong dark:text-white hover:text-primary transition-colors" href="{{ route('admin.dashboard') }}">
        <span class="material-icons-outlined text-primary" aria-hidden="true">all_inclusive</span>
        PointOne
      </a>
    </div>

    <nav class="flex-1 overflow-y-auto py-5 px-3 space-y-1">
      <a class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-primary/10 text-primary dark:bg-primary/15' : '' }}" href="{{ route('admin.dashboard') }}">
        <span class="material-icons-outlined mr-3 text-[20px] {{ request()->routeIs('admin.dashboard') ? 'text-primary' : 'text-slate-400 group-hover:text-slate-500 dark:text-slate-500 dark:group-hover:text-slate-300' }}" aria-hidden="true">dashboard</span>
        Dashboard
      </a>

      <p class="px-3 text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider mt-4">Content</p>

      <a class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors {{ request()->is('admin/posts*') ? 'bg-primary/10 text-primary dark:bg-primary/15' : '' }}" href="{{ route('admin.posts.index') }}">
        <span class="material-icons-outlined mr-3 text-[20px] {{ request()->is('admin/posts*') ? 'text-primary' : 'text-slate-400 group-hover:text-slate-500 dark:text-slate-500 dark:group-hover:text-slate-300' }}" aria-hidden="true">article</span>
        Posts
      </a>

      <a class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors {{ request()->is('admin/pages*') ? 'bg-primary/10 text-primary dark:bg-primary/15' : '' }}" href="{{ route('admin.pages.index') }}">
        <span class="material-icons-outlined mr-3 text-[20px] {{ request()->is('admin/pages*') ? 'text-primary' : 'text-slate-400 group-hover:text-slate-500 dark:text-slate-500 dark:group-hover:text-slate-300' }}" aria-hidden="true">description</span>
        Pages
      </a>

      <a class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors {{ request()->is('admin/categories*') ? 'bg-primary/10 text-primary dark:bg-primary/15' : '' }}" href="{{ route('admin.categories.index') }}">
        <span class="material-icons-outlined mr-3 text-[20px] {{ request()->is('admin/categories*') ? 'text-primary' : 'text-slate-400 group-hover:text-slate-500 dark:text-slate-500 dark:group-hover:text-slate-300' }}" aria-hidden="true">label</span>
        Categories
      </a>

      <a class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors {{ request()->is('admin/tags*') ? 'bg-primary/10 text-primary dark:bg-primary/15' : '' }}" href="{{ route('admin.tags.index') }}">
        <span class="material-icons-outlined mr-3 text-[20px] {{ request()->is('admin/tags*') ? 'text-primary' : 'text-slate-400 group-hover:text-slate-500 dark:text-slate-500 dark:group-hover:text-slate-300' }}" aria-hidden="true">local_offer</span>
        Tags
      </a>

      <a class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors {{ request()->is('admin/media*') ? 'bg-primary/10 text-primary dark:bg-primary/15' : '' }}" href="{{ route('admin.media.index') }}">
        <span class="material-icons-outlined mr-3 text-[20px] {{ request()->is('admin/media*') ? 'text-primary' : 'text-slate-400 group-hover:text-slate-500 dark:text-slate-500 dark:group-hover:text-slate-300' }}" aria-hidden="true">image</span>
        Media Library
      </a>

      <a class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors {{ request()->is('admin/review*') ? 'bg-primary/10 text-primary dark:bg-primary/15' : '' }}" href="{{ route('admin.review.index') }}">
        <span class="material-icons-outlined mr-3 text-[20px] {{ request()->is('admin/review*') ? 'text-primary' : 'text-slate-400 group-hover:text-slate-500 dark:text-slate-500 dark:group-hover:text-slate-300' }}" aria-hidden="true">rate_review</span>
        Review Queue
      </a>

      <p class="px-3 text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider mt-6">Leads</p>
      <a class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors {{ request()->is('admin/leads*') ? 'bg-primary/10 text-primary dark:bg-primary/15' : '' }}" href="{{ route('admin.leads.index') }}">
        <span class="material-icons-outlined mr-3 text-[20px] {{ request()->is('admin/leads*') ? 'text-primary' : 'text-slate-400 group-hover:text-slate-500 dark:text-slate-500 dark:group-hover:text-slate-300' }}" aria-hidden="true">people</span>
        Leads
      </a>



      @if(auth()->user()->role === 'admin')
      <p class="px-3 text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider mt-6">System</p>
      
      <a class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors {{ request()->is('admin/users*') ? 'bg-primary/10 text-primary dark:bg-primary/15' : '' }}" href="{{ route('admin.users.index') }}">
        <span class="material-icons-outlined mr-3 text-[20px] {{ request()->is('admin/users*') ? 'text-primary' : 'text-slate-400 group-hover:text-slate-500 dark:text-slate-500 dark:group-hover:text-slate-300' }}" aria-hidden="true">people_outline</span>
        Users
      </a>

      <a class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors {{ request()->is('admin/settings*') ? 'bg-primary/10 text-primary dark:bg-primary/15' : '' }}" href="{{ route('admin.settings.index') }}">
        <span class="material-icons-outlined mr-3 text-[20px] {{ request()->is('admin/settings*') ? 'text-primary' : 'text-slate-400 group-hover:text-slate-500 dark:text-slate-500 dark:group-hover:text-slate-300' }}" aria-hidden="true">settings</span>
        Settings
      </a>
      @endif
    </nav>
  </aside>

  <main class="flex-1 overflow-y-auto relative">
    <header class="h-16 flex justify-between items-center px-6 sm:px-8 bg-surface-light/80 dark:bg-surface-dark/80 backdrop-blur-md sticky top-0 z-10 border-b border-border-light dark:border-border-dark">
      <div class="flex items-center gap-2 text-sm text-text-muted dark:text-slate-400">
        <span>Dashboard</span>
        <span class="text-slate-300 dark:text-slate-600" aria-hidden="true">/</span>
        <span class="font-medium text-primary" aria-current="page">{{ $crumb ?? 'Admin' }}</span>
      </div>

      <div class="flex items-center gap-2">
        <a class="btn-soft px-3 py-2" href="{{ route('site.home') }}" target="_blank" rel="noopener">
          <span class="material-icons-outlined text-[18px]" aria-hidden="true">open_in_new</span>
          Visit site
        </a>

        <button class="btn-soft px-3 py-2" type="button" onclick="document.body.classList.toggle('dark')">
          <span class="material-icons-outlined text-[18px]" aria-hidden="true">dark_mode</span>
          Toggle
        </button>

        <div class="hidden sm:flex items-center gap-2 px-3 py-2 rounded-lg border border-border-light dark:border-border-dark bg-surface-light dark:bg-surface-dark">
          <div class="h-7 w-7 rounded-full bg-primary/10 flex items-center justify-center text-primary text-xs font-bold">
            {{ strtoupper(substr(auth()->user()->email ?? 'U', 0, 1)) }}
          </div>
          <div class="text-sm text-slate-700 dark:text-slate-200 max-w-[220px] truncate" title="{{ auth()->user()->email ?? '' }}">
            {{ auth()->user()->email ?? '' }}
            <span class="ml-1 text-[10px] uppercase font-bold px-1.5 py-0.5 rounded {{ auth()->user()->role === 'admin' ? 'bg-violet-100 text-violet-700' : 'bg-slate-100 text-slate-500' }}">
              {{ auth()->user()->role === 'admin' ? 'ADM' : 'EDT' }}
            </span>
          </div>
        </div>

        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button class="btn-ghost px-3 py-2" type="submit" title="Log out">
            <span class="material-icons-outlined text-[18px]" aria-hidden="true">logout</span>
          </button>
        </form>
      </div>
    </header>

    <div class="p-6 sm:p-8 max-w-7xl mx-auto">
      {{ $slot }}
    </div>
  </main>
</div>

<div id="toastHost" class="fixed bottom-4 right-4 z-50 flex flex-col gap-3 w-[360px] max-w-[90vw]"></div>

<script>
(function(){
  const host = document.getElementById('toastHost');
  function toneToIcon(tone){
    if(tone === "success") return ["check_circle","text-emerald-600"];
    if(tone === "danger") return ["error","text-red-600"];
    return ["info","text-primary"];
  }

  window.showToast = function(opts){
    opts = opts || {};
    const title = opts.title || "Saved";
    const message = opts.message || "Your changes were saved.";
    const tone = opts.tone || "info";
    const action = opts.action;

    const iconInfo = toneToIcon(tone);
    const icon = iconInfo[0], iconCls = iconInfo[1];

    const el = document.createElement("div");
    el.className = "toast";
    el.innerHTML =
      '<span class="material-icons-outlined ' + iconCls + '" aria-hidden="true">' + icon + '</span>' +
      '<div class="min-w-0">' +
        '<div class="toast-title">' + title + '</div>' +
        '<div class="toast-msg">' + message + '</div>' +
        (action && action.label ? '<button class="mt-2 btn-soft px-3 py-1.5" data-toast-action="1">' + action.label + '</button>' : '') +
      '</div>' +
      '<button class="ml-auto btn-ghost px-2 py-2" aria-label="Close toast">' +
        '<span class="material-icons-outlined text-[18px]">close</span>' +
      '</button>';

    el.querySelector('button[aria-label="Close toast"]').addEventListener("click", function(){ el.remove(); });

    const actBtn = el.querySelector('[data-toast-action="1"]');
    if(actBtn && action && typeof action.onClick === "function"){
      actBtn.addEventListener("click", function(){
        try { action.onClick(); } catch(e){}
        el.remove();
      });
    }

    host.appendChild(el);
    const ttl = typeof opts.ttl === "number" ? opts.ttl : 4500;
    setTimeout(function(){ if(el.isConnected) el.remove(); }, ttl);
  };

  @if(session('toast'))
    (function(){
      const t = @json(session('toast'));
      if(t && t.undo){
        showToast({
          tone: t.tone || 'info',
          title: t.title || 'Notice',
          message: t.message || '',
          ttl: 6000,
          action: {
            label: 'Undo',
            onClick: function(){
              const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
              fetch(t.undo, { method: 'POST', headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' } })
                .then(() => window.location.reload())
                .catch(() => window.location.reload());
            }
          }
        });
      } else {
        showToast({ tone: t.tone || 'info', title: t.title || 'Notice', message: t.message || '' });
      }
    })();
  @endif
})();
</script>

{{ $scripts ?? '' }}
</body>
</html>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  
  <title>{{ $meta_title ?? ($title ?? (setting('seo_default_title') ?: (setting('site_name') ?: 'Mini CMS'))) }}</title>
  
  <meta name="description" content="{{ $meta_description ?? (setting('seo_default_description') ?: '') }}">
  <meta name="keywords" content="{{ $meta_keywords ?? (setting('seo_default_keywords') ?: '') }}">

  <script src="https://cdn.tailwindcss.com?plugins=typography"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
  <style>
    body { font-family: 'Inter', sans-serif; }
    /* Scroll progress bar */
    #progress-bar {
      width: 0%;
      height: 3px;
      transition: width 0.1s;
    }
  </style>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: '#2563EB',
            surface: '#F8FAFC',
          }
        }
      }
    }
    // Scroll progress script
    window.addEventListener('scroll', () => {
      const winScroll = document.body.scrollTop || document.documentElement.scrollTop;
      const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
      const scrolled = (winScroll / height) * 100;
      const bar = document.getElementById('progress-bar');
      if (bar) bar.style.width = scrolled + "%";
    });
  </script>
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen flex flex-col selection:bg-primary/20 selection:text-primary">

  <!-- Progress Bar container (fixed top) -->
  <div class="fixed top-0 left-0 right-0 z-50 h-[3px] bg-transparent pointer-events-none">
    <div id="progress-bar" class="bg-primary h-full"></div>
  </div>

  @if(!empty($isPreview))
    <div class="sticky top-0 z-40 bg-amber-50 border-b border-amber-200">
      <div class="max-w-5xl mx-auto px-4 py-2 flex items-center justify-between gap-3">
        <div class="flex items-center gap-2 text-sm text-amber-900">
          <span class="material-icons-outlined text-[18px]">info</span>
          <span class="font-medium">Preview Mode</span>
        </div>
        @if(!empty($backUrl))
          <a href="{{ $backUrl }}" class="text-xs font-semibold uppercase tracking-wider text-amber-900 hover:text-amber-700 hover:underline">Back to Editor</a>
        @endif
      </div>
    </div>
  @endif

  <!-- Navigation -->
  <nav class="sticky top-0 z-30 bg-white/80 backdrop-blur-md border-b border-slate-200">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
      <!-- Brand -->
      <a href="{{ route('site.home') }}" class="flex items-center gap-2 font-bold text-lg tracking-tight text-slate-900 hover:opacity-80 transition">
        {{ setting('site_name') ?: 'PointOne' }}
      </a>

      <!-- Links -->
      <div class="flex items-center gap-6 text-sm font-medium">
        <a href="{{ route('site.home') }}" class="text-slate-600 hover:text-primary transition">Home</a>
        <a href="{{ route('site.home') }}#latest" class="hidden sm:block text-slate-600 hover:text-primary transition">Blog</a>
        <a href="{{ route('contact.index') }}" class="text-slate-600 hover:text-primary transition">Contact</a>
        
        @auth
          <a href="{{ route('admin.dashboard') }}" class="text-slate-900 hover:text-primary transition flex items-center gap-1">
            <span>Admin</span>
            <span class="material-icons-outlined text-[16px]">arrow_forward</span>
          </a>
        @else
          <!-- Optional login link or CTA if desired -->
        @endauth
        
        <a href="{{ route('contact.index') }}" class="hidden sm:inline-flex items-center justify-center px-4 py-2 bg-slate-900 text-white rounded-lg hover:bg-slate-800 transition shadow-sm text-xs font-semibold uppercase tracking-wide">
          Get in touch
        </a>
      </div>
    </div>
  </nav>

  <!-- Main Content -->
  <main class="flex-grow">
    {{ $slot }}
  </main>

  <!-- Footer -->
  <footer class="bg-white border-t border-slate-200 py-12 mt-12">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 flex flex-col md:flex-row justify-between items-center gap-6">
      <div class="text-center md:text-left">
        <div class="font-bold text-slate-900 text-lg">{{ setting('site_name') ?: 'PointOne' }}</div>
        <div class="text-slate-500 text-sm mt-1">{{ setting('tagline') }}</div>
      </div>
      <div class="flex flex-wrap justify-center gap-6 text-sm text-slate-500">
        <a href="{{ route('site.home') }}" class="hover:text-slate-900 transition">Home</a>
        <a href="{{ route('contact.index') }}" class="hover:text-slate-900 transition">Contact</a>
        <a href="/login" class="hover:text-slate-900 transition">Sign In</a>
      </div>
      <div class="text-xs text-slate-400">
        &copy; {{ date('Y') }} {{ setting('site_name') ?: 'PointOne' }}. All rights reserved.
      </div>
    </div>
  </footer>

  <!-- Toasts -->
  @if(session('toast'))
    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show" transition class="fixed bottom-4 right-4 z-50">
      @php $toast = session('toast'); @endphp
      <div class="bg-slate-900 text-white px-4 py-3 rounded-lg shadow-lg flex items-center gap-3">
        @if(($toast['tone'] ?? 'info') === 'success')
          <span class="material-icons-outlined text-green-400">check_circle</span>
        @elseif(($toast['tone'] ?? 'info') === 'danger')
          <span class="material-icons-outlined text-red-400">error</span>
        @else
          <span class="material-icons-outlined text-blue-400">info</span>
        @endif
        <div>
          <div class="font-medium text-sm">{{ $toast['title'] ?? 'Notification' }}</div>
          @if(isset($toast['message']))
            <div class="text-xs text-slate-300">{{ $toast['message'] }}</div>
          @endif
        </div>
      </div>
    </div>
  @endif

  <!-- Simple text-replacement JS for X-data simulation since we don't have Alpine loaded but need simple fade -->
  <script>
    // Minimal toast handler if Alpine isn't desired (Constraints said Tailwind CDN, didn't specify Alpine explicitly but it's common)
    // Adding simple timeout removal for toasts if they exist
    const toast = document.querySelector('.fixed.bottom-4.right-4');
    if(toast) {
      setTimeout(() => {
        toast.style.transition = 'opacity 0.5s';
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 500);
      }, 4000);
    }
  </script>
</body>
</html>

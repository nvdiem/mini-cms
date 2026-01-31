<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>{{ $title ?? 'Blog' }}</title>

  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin=""/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>

  <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: { primary: "#2563EB" },
          fontFamily: { sans: ["Inter","sans-serif"] }
        }
      }
    };
  </script>

  <style>
    html{ scroll-behavior:smooth; }
  </style>
</head>
<body class="bg-slate-50 text-slate-800 font-sans">
  @if(!empty($isPreview))
    <div class="sticky top-0 z-50 bg-amber-50 border-b border-amber-200">
      <div class="max-w-5xl mx-auto px-4 py-2 flex items-center justify-between gap-3">
        <div class="text-sm text-amber-900">
          <b>Preview mode</b> · This post may be unpublished.
        </div>
        <div class="flex items-center gap-2">
          @if(!empty($backUrl))
            <a href="{{ $backUrl }}" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-white border border-amber-200 text-sm font-medium hover:bg-amber-50">
              ← Back to editor
            </a>
          @endif
          <a href="{{ route('site.home') }}" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-primary text-white text-sm font-medium hover:bg-blue-700">
            Home
          </a>
        </div>
      </div>
    </div>
  @endif

  <header class="border-b border-slate-200 bg-white/80 backdrop-blur">
    <div class="max-w-5xl mx-auto px-4 py-4 flex items-center justify-between gap-3">
      <a href="{{ route('site.home') }}" class="font-bold text-lg tracking-tight">PointOne Blog</a>
      <div class="text-sm text-slate-500">
        @auth
          <a href="{{ route('admin.posts.index') }}" class="text-primary font-medium hover:underline">Admin</a>
        @endauth
      </div>
    </div>
  </header>

  <main class="max-w-5xl mx-auto px-4 py-8">
    {{ $slot }}
  </main>

  <footer class="border-t border-slate-200 bg-white">
    <div class="max-w-5xl mx-auto px-4 py-6 text-sm text-slate-500">
      © {{ date('Y') }} PointOne
    </div>
  </footer>

  {{-- Support Chat Widget --}}
  <x-site.support-widget />
</body>
</html>

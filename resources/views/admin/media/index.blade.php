<x-admin.layout :title="'Media · Mini CMS'" :crumb="'Media'">
  <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
      <h1 class="text-2xl font-semibold text-text-strong dark:text-white tracking-tight">Media Library</h1>
      <p class="text-sm text-text-muted dark:text-slate-400 mt-1">Upload images and reuse them as featured images.</p>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    <div class="card p-6">
      <div class="text-sm font-semibold text-text-strong dark:text-white">Upload</div>

      @if ($errors->any())
        <div class="mt-4 p-3 rounded-lg border border-red-200 bg-red-50 text-sm text-red-700">
          {{ $errors->first() }}
        </div>
      @endif

      <form class="mt-4 space-y-3" method="POST" action="{{ route('admin.media.store') }}" enctype="multipart/form-data">
        @csrf
        <div>
          <label class="text-sm font-medium">File</label>
          <input class="mt-2 block w-full text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-primary/10 file:text-primary hover:file:bg-primary/15"
                 type="file" name="file" accept="image/*" required/>
          <div class="text-xs text-text-muted dark:text-slate-400 mt-2">Max 4MB. jpg/png/webp/gif.</div>
        </div>
        <button class="btn-primary w-full" type="submit">Upload</button>
      </form>

      <div class="mt-4 text-xs text-text-muted dark:text-slate-400">
        Run once: <code class="px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800">php artisan storage:link</code>
      </div>
    </div>

    <div class="card lg:col-span-2 overflow-hidden">
      <div class="card-hd">
        <form class="flex gap-3 items-center" method="GET" action="{{ route('admin.media.index') }}">
          <div class="relative w-full sm:w-80">
            <input class="input pr-10" name="q" value="{{ $q }}" placeholder="Search media..." />
            <span class="material-icons-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400" aria-hidden="true">search</span>
          </div>
          <button class="btn-ghost" type="submit">Search</button>
          <a class="btn-soft px-3 py-2" href="{{ route('admin.media.index') }}">Clear</a>
        </form>
      </div>

      @if($items->count() === 0)
        <div class="p-10 text-center">
          <div class="mx-auto h-12 w-12 rounded-2xl bg-primary/10 flex items-center justify-center">
            <span class="material-icons-outlined text-primary" aria-hidden="true">photo_library</span>
          </div>
          <h2 class="mt-4 text-lg font-semibold text-text-strong dark:text-white">No media files</h2>
          <p class="mt-1 text-sm text-text-muted dark:text-slate-400">Upload an image to get started.</p>
        </div>
      @else
        <div class="p-4 sm:p-6 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
          @foreach($items as $m)
            <div class="group relative border border-border-light dark:border-border-dark rounded-xl overflow-hidden bg-surface-light dark:bg-surface-dark">
              <div class="aspect-[4/3] bg-slate-100 dark:bg-slate-800 hover:opacity-90 transition">
                <a href="{{ route('admin.media.show', $m) }}" class="block w-full h-full">
                   <img src="{{ $m->url() }}" alt="{{ $m->original_name }}" class="h-full w-full object-cover"/>
                </a>
              </div>
              <div class="p-2">
                <div class="text-xs font-medium text-slate-700 dark:text-slate-200 truncate">
                  <a href="{{ route('admin.media.show', $m) }}" class="hover:text-primary hover:underline">{{ $m->original_name }}</a>
                </div>
                <div class="text-[11px] text-slate-500 dark:text-slate-400 truncate">#{{ $m->id }} · {{ number_format($m->size/1024, 0) }} KB</div>
              </div>

              <form method="POST" action="{{ route('admin.media.destroy', $m) }}" class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition">
                @csrf
                @method('DELETE')
                <button class="inline-flex items-center justify-center h-9 w-9 rounded-lg bg-white/90 hover:bg-white border border-border-light text-red-600 shadow-soft2" type="submit" title="Delete">
                  <span class="material-icons-outlined text-[20px]" aria-hidden="true">delete</span>
                </button>
              </form>
            </div>
          @endforeach
        </div>

        <div class="flex items-center justify-between px-6 py-4 border-t border-border-light dark:border-border-dark bg-slate-50/70 dark:bg-slate-800/30">
          <div class="text-xs text-slate-500 dark:text-slate-400">
            Showing <span class="font-medium">{{ $items->firstItem() ?? 0 }}</span> to <span class="font-medium">{{ $items->lastItem() ?? 0 }}</span> of <span class="font-medium">{{ $items->total() }}</span> results
          </div>
          <div class="text-sm">{{ $items->links() }}</div>
        </div>
      @endif
    </div>
  </div>
</x-admin.layout>

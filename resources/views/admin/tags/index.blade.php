<x-admin.layout :title="'Tags · Mini CMS'" :crumb="'Tags'">
  <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
      <h1 class="text-2xl font-semibold text-text-strong dark:text-white tracking-tight">Tags</h1>
      <p class="text-sm text-text-muted dark:text-slate-400 mt-1">Create and manage tags for your posts.</p>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    <div class="card p-6">
      <div class="text-sm font-semibold text-text-strong dark:text-white">New tag</div>
      <form class="mt-4 space-y-3" method="POST" action="{{ route('admin.tags.store') }}">
        @csrf
        <div>
          <label class="text-sm font-medium">Name</label>
          <input class="input mt-1" name="name" value="{{ old('name') }}" placeholder="Laravel" required/>
          @error('name') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
        </div>
        <div>
          <label class="text-sm font-medium">Slug (optional)</label>
          <input class="input mt-1" name="slug" value="{{ old('slug') }}" placeholder="laravel"/>
          @error('slug') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
          <div class="text-xs text-text-muted dark:text-slate-400 mt-1">Leave empty to auto-generate.</div>
        </div>
        <button class="btn-primary w-full" type="submit">Create</button>
      </form>
    </div>

    <div class="card lg:col-span-2 overflow-hidden">
      <div class="card-hd">
        <form class="flex gap-3 items-center" method="GET" action="{{ route('admin.tags.index') }}">
          <div class="relative w-full sm:w-80">
            <input class="input pr-10" name="q" value="{{ $q }}" placeholder="Search tags..." />
            <span class="material-icons-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400" aria-hidden="true">search</span>
          </div>
          <button class="btn-ghost" type="submit">Search</button>
          <a class="btn-soft px-3 py-2" href="{{ route('admin.tags.index') }}">Clear</a>
        </form>
      </div>

      @if($tags->count() === 0)
        <div class="p-10 text-center">
          <div class="mx-auto h-12 w-12 rounded-2xl bg-primary/10 flex items-center justify-center">
            <span class="material-icons-outlined text-primary" aria-hidden="true">label_off</span>
          </div>
          <h2 class="mt-4 text-lg font-semibold text-text-strong dark:text-white">No tags yet</h2>
          <p class="mt-1 text-sm text-text-muted dark:text-slate-400">Create your first tag using the form.</p>
        </div>
      @else
        <div class="overflow-x-auto">
          <table class="table">
            <thead>
              <tr class="bg-slate-50/70 dark:bg-slate-800/40 border-b border-border-light dark:border-border-dark">
                <th class="th">Name</th>
                <th class="th">Slug</th>
                <th class="th">Posts</th>
                <th class="th text-right">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-border-light dark:divide-border-dark">
              @foreach($tags as $tag)
                <tr class="row">
                  <td class="td">
                    <div class="font-medium text-text-strong dark:text-white">{{ $tag->name }}</div>
                    <div class="text-xs text-text-muted dark:text-slate-400">#{{ $tag->id }}</div>
                  </td>
                  <td class="td text-slate-600 dark:text-slate-300">{{ $tag->slug }}</td>
                  <td class="td text-slate-600 dark:text-slate-300">{{ $tag->posts_count ?? 0 }}</td>
                  <td class="td text-right">
                    <details class="relative inline-block">
                      <summary class="inline-flex items-center justify-center h-9 w-9 rounded-lg border border-border-light dark:border-border-dark bg-surface-light dark:bg-surface-dark hover:bg-slate-50 dark:hover:bg-slate-800 cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/25" aria-label="Open actions menu">
                        <span class="material-icons-outlined text-[20px] text-slate-500 dark:text-slate-300" aria-hidden="true">more_horiz</span>
                      </summary>
                      <div class="absolute right-0 mt-2 w-72 rounded-xl border border-border-light dark:border-border-dark bg-surface-light dark:bg-surface-dark shadow-soft2 overflow-hidden z-10 p-3">
                        <form class="space-y-2" method="POST" action="{{ route('admin.tags.update', $tag) }}">
                          @csrf
                          @method('PUT')
                          <div>
                            <label class="text-xs font-medium text-slate-600 dark:text-slate-300">Name</label>
                            <input class="input mt-1" name="name" value="{{ $tag->name }}"/>
                          </div>
                          <div>
                            <label class="text-xs font-medium text-slate-600 dark:text-slate-300">Slug</label>
                            <input class="input mt-1" name="slug" value="{{ $tag->slug }}"/>
                          </div>
                          <div class="flex gap-2">
                            <button class="btn-soft px-3 py-2" type="submit">Save</button>
                            <button class="btn-danger px-3 py-2" type="button" onclick="if(confirm('Delete this tag?')) { const form = document.createElement('form'); form.method = 'POST'; form.action = '{{ route('admin.tags.destroy', $tag) }}'; const csrf = document.createElement('input'); csrf.type = 'hidden'; csrf.name = '_token'; csrf.value = '{{ csrf_token() }}'; const method = document.createElement('input'); method.type = 'hidden'; method.name = '_method'; method.value = 'DELETE'; form.appendChild(csrf); form.appendChild(method); document.body.appendChild(form); form.submit(); }">Delete</button>
                          </div>
                        </form>
                      </div>
                    </details>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        <div class="flex items-center justify-between px-6 py-4 border-t border-border-light dark:border-border-dark bg-slate-50/70 dark:bg-slate-800/30">
          <div class="text-xs text-slate-500 dark:text-slate-400">
            Showing <span class="font-medium">{{ $tags->firstItem() ?? 0 }}</span> to <span class="font-medium">{{ $tags->lastItem() ?? 0 }}</span> of <span class="font-medium">{{ $tags->total() }}</span> results
          </div>
          <div class="text-sm">{{ $tags->links() }}</div>
        </div>
      @endif
    </div>
  </div>
</x-admin.layout>

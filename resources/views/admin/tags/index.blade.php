<x-admin.layout :title="'Tags · Mini CMS'" :crumb="'Tags'">
  <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
      <h1 class="text-2xl font-semibold text-slate-900 dark:text-white tracking-tight">Tags</h1>
      <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">Organize your posts with tags.</p>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="rounded-xl border border-slate-200 bg-white shadow-sm p-5 self-start">
      <div class="text-sm font-semibold text-slate-900">New tag</div>
      <form class="mt-4 space-y-3" method="POST" action="{{ route('admin.tags.store') }}">
        @csrf
        <div>
          <label class="block text-sm font-medium text-slate-900 mb-1">Name</label>
          <input class="h-10 w-full rounded-lg border border-slate-200 bg-white px-3 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-200 focus:border-slate-300" name="name" value="{{ old('name') }}" placeholder="Laravel" required/>
          @error('name') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-900 mb-1">Slug (optional)</label>
          <input class="h-10 w-full rounded-lg border border-slate-200 bg-white px-3 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-200 focus:border-slate-300" name="slug" value="{{ old('slug') }}" placeholder="laravel"/>
          @error('slug') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
          <div class="text-xs text-slate-500 mt-1">Leave empty to auto-generate.</div>
        </div>
        <button class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-200 w-full" type="submit">Create tag</button>
      </form>
    </div>

    <div class="rounded-xl border border-slate-200 bg-white shadow-sm lg:col-span-2 overflow-hidden">
      <div class="px-4 sm:px-5 py-3 bg-slate-50/70 dark:bg-slate-800/30 border-b border-slate-200 dark:border-slate-700">
        <form class="flex flex-col sm:flex-row gap-3 items-center" method="GET" action="{{ route('admin.tags.index') }}">
          <div class="relative w-full sm:w-80">
            <input class="h-10 w-full rounded-lg border border-slate-200 bg-white pl-3 pr-10 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-200 focus:border-slate-300" name="q" value="{{ $q }}" placeholder="Search tags..." />
            <span class="material-icons-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400" aria-hidden="true">search</span>
          </div>
          <div class="flex gap-2 w-full sm:w-auto">
            <button class="inline-flex items-center justify-center gap-2 rounded-lg bg-white px-4 py-2 text-sm font-medium text-slate-700 border border-slate-200 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-200 flex-1 sm:flex-none" type="submit">Search</button>
            <a class="inline-flex items-center justify-center gap-2 rounded-lg px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-slate-200" href="{{ route('admin.tags.index') }}">Clear</a>
          </div>
        </form>
      </div>

      @if($tags->count() === 0)
        <div class="py-14 text-center">
          <div class="mx-auto h-12 w-12 rounded-2xl bg-blue-50 flex items-center justify-center">
            <span class="material-icons-outlined text-blue-600" aria-hidden="true">label_off</span>
          </div>
          <h2 class="mt-4 text-sm font-semibold text-slate-900">No tags yet</h2>
          <p class="mt-1 text-sm text-slate-600">Create your first tag using the form.</p>
        </div>
      @else
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-600 border-b border-slate-200">
                <th class="px-4 py-3">Name</th>
                <th class="px-4 py-3">Slug</th>
                <th class="px-4 py-3">Posts</th>
                <th class="px-4 py-3 text-right">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
              @foreach($tags as $tag)
                <tr class="hover:bg-slate-50 transition-colors">
                  <td class="px-4 py-4">
                    <div class="font-medium text-slate-900">{{ $tag->name }}</div>
                    <div class="text-[12px] text-slate-400">#{{ $tag->id }}</div>
                  </td>
                  <td class="px-4 py-4 text-sm text-slate-600">{{ $tag->slug }}</td>
                  <td class="px-4 py-4 text-sm text-slate-600">{{ $tag->posts_count ?? 0 }}</td>
                  <td class="px-4 py-4 text-right">
                    <details class="relative inline-block text-left">
                      <summary class="inline-flex items-center justify-center h-8 w-8 rounded-lg border border-slate-200 bg-white text-slate-500 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-200 cursor-pointer" aria-label="Open actions menu">
                        <span class="material-icons-outlined text-[18px]" aria-hidden="true">more_horiz</span>
                      </summary>
                      <div class="absolute right-0 mt-2 w-72 rounded-xl border border-slate-200 bg-white shadow-lg overflow-hidden z-20 p-4">
                        <form class="space-y-3" method="POST" action="{{ route('admin.tags.update', $tag) }}">
                          @csrf
                          @method('PUT')
                          <div>
                            <label class="block text-xs font-medium text-slate-700 mb-1">Name</label>
                            <input class="h-9 w-full rounded-lg border border-slate-200 bg-white px-2 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-200 focus:border-slate-300" name="name" value="{{ $tag->name }}"/>
                          </div>
                          <div>
                            <label class="block text-xs font-medium text-slate-700 mb-1">Slug</label>
                            <input class="h-9 w-full rounded-lg border border-slate-200 bg-white px-2 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-200 focus:border-slate-300" name="slug" value="{{ $tag->slug }}"/>
                          </div>
                          <div class="flex gap-2 pt-1">
                            <button class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-medium text-white shadow-sm hover:bg-blue-700 flex-1" type="submit">Save</button>
                            <button class="inline-flex items-center justify-center gap-2 rounded-lg bg-white px-3 py-1.5 text-xs font-medium text-red-600 border border-slate-200 hover:bg-red-50 flex-1" type="button" onclick="if(confirm('Delete this tag?')) { const form = document.createElement('form'); form.method = 'POST'; form.action = '{{ route('admin.tags.destroy', $tag) }}'; const csrf = document.createElement('input'); csrf.type = 'hidden'; csrf.name = '_token'; csrf.value = '{{ csrf_token() }}'; const method = document.createElement('input'); method.type = 'hidden'; method.name = '_method'; method.value = 'DELETE'; form.appendChild(csrf); form.appendChild(method); document.body.appendChild(form); form.submit(); }">Delete</button>
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

        <div class="flex items-center justify-between px-4 py-3 border-t border-slate-200 bg-slate-50 rounded-b-xl">
          <div class="text-xs text-slate-500">
            Showing <span class="font-medium">{{ $tags->firstItem() ?? 0 }}</span> to <span class="font-medium">{{ $tags->lastItem() ?? 0 }}</span> of <span class="font-medium">{{ $tags->total() }}</span> results
          </div>
          <div class="text-sm">{{ $tags->links() }}</div>
        </div>
      @endif
    </div>
  </div>
</x-admin.layout>

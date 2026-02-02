<x-admin.layout :title="'Pages · Mini CMS'" :crumb="'Pages'">
  <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
      <h1 class="text-2xl font-semibold text-slate-900 dark:text-white tracking-tight">Pages</h1>
      <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">Create, manage, and publish static pages.</p>
    </div>
    <div class="flex flex-wrap gap-2">
      <a class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-200" href="{{ route('admin.pages.create') }}">
        <span class="material-icons-outlined text-[18px]" aria-hidden="true">add</span>
        Add New Page
      </a>
      <a class="inline-flex items-center justify-center gap-2 rounded-lg px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-slate-200" href="{{ route('admin.pages.index', array_merge(request()->query(), ['trash' => request('trash')==='1' ? null : 1])) }}">
        <span class="material-icons-outlined text-[18px]" aria-hidden="true">delete</span>
        {{ request('trash')==='1' ? 'Back to list' : 'Trash' }}
      </a>
    </div>
  </div>

  <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
    <div class="px-4 sm:px-5 py-3 bg-white border-b border-slate-200 rounded-t-xl">
      <div class="flex flex-col lg:flex-row gap-3 lg:items-center">
        {{-- Bulk Actions --}}
        <form id="bulkForm" class="flex flex-col sm:flex-row gap-2 sm:items-center" method="POST" action="{{ route('admin.pages.bulk') }}">
          @csrf
          <input type="hidden" name="trash" value="{{ request('trash') }}"/>

          <div class="relative w-full sm:w-48">
            <select class="h-10 w-full rounded-lg border border-slate-200 bg-white px-3 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-200 focus:border-slate-300" name="action" required>
              <option value="">{{ request('trash')==='1' ? 'Bulk Actions (Trash)' : 'Bulk Actions' }}</option>
              @if(request('trash')==='1')
                <option value="restore">Restore</option>
              @else
                <option value="delete">Move to trash</option>
              @endif
            </select>
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-500">
              <span class="material-icons-outlined text-sm" aria-hidden="true">expand_more</span>
            </div>
          </div>

          <button class="inline-flex items-center justify-center gap-2 rounded-lg px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-slate-200 sm:w-auto" type="submit">Apply</button>
        </form>

        {{-- Divider --}}
        <div class="hidden lg:block h-6 w-px bg-slate-200 mx-1"></div>

        {{-- Search & Filters --}}
        <form class="flex flex-col sm:flex-row gap-2 sm:items-center flex-1" method="GET" action="{{ route('admin.pages.index') }}">
          <input type="hidden" name="trash" value="{{ request('trash') }}"/>

          <div class="relative w-full sm:w-64">
            <input class="h-10 w-full rounded-lg border border-slate-200 bg-white pl-3 pr-10 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-200 focus:border-slate-300" name="q" value="{{ $q }}" placeholder="Search pages..." />
            <span class="material-icons-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400" aria-hidden="true">search</span>
          </div>

          <div class="relative w-full sm:w-36">
            <select class="h-10 w-full rounded-lg border border-slate-200 bg-white px-3 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-200 focus:border-slate-300" name="status">
              <option value="">All status</option>
              <option value="draft" {{ $status==='draft' ? 'selected' : '' }}>Draft</option>
              <option value="review" {{ $status==='review' ? 'selected' : '' }}>Review</option>
              <option value="published" {{ $status==='published' ? 'selected' : '' }}>Published</option>
            </select>
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-500">
              <span class="material-icons-outlined text-sm" aria-hidden="true">expand_more</span>
            </div>
          </div>

          <button class="inline-flex items-center justify-center gap-2 rounded-lg bg-white px-4 py-2 text-sm font-medium text-slate-700 border border-slate-200 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-200 sm:w-auto" type="submit">Filter</button>
          <a class="inline-flex items-center justify-center gap-2 rounded-lg px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-slate-200" href="{{ route('admin.pages.index', ['trash' => request('trash')]) }}">Clear</a>
        </form>

        {{-- Item Count --}}
        <div class="text-sm text-slate-500">
          {{ $pages->total() }} items
        </div>
      </div>
    </div>

    @if($pages->count() === 0)
      <div class="py-14 text-center">
        <div class="mx-auto h-12 w-12 rounded-2xl bg-blue-50 flex items-center justify-center">
          <span class="material-icons-outlined text-blue-600" aria-hidden="true">description</span>
        </div>
        <h2 class="mt-4 text-sm font-semibold text-slate-900">No pages found</h2>
        <p class="mt-1 text-sm text-slate-600">Adjust filters or create a new page.</p>
        <div class="mt-5 flex justify-center gap-2">
          <a class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-200" href="{{ route('admin.pages.create') }}">Create Page</a>
          <a class="inline-flex items-center justify-center gap-2 rounded-lg px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-slate-200" href="{{ route('admin.pages.index', ['trash' => request('trash')]) }}">Reset</a>
        </div>
      </div>
    @else
      <div class="sm:hidden p-4 space-y-3 bg-slate-50">
        @foreach($pages as $page)
          <article class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm" data-page="pg{{ $page->id }}">
            <div class="flex items-start gap-3">
              <input class="mt-1 h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-200 row-chk" type="checkbox" value="{{ $page->id }}" aria-label="Select page {{ $page->id }}"/>
              <div class="min-w-0 flex-1">
                <div class="flex items-center gap-2">
                  <div class="font-medium text-slate-900 truncate">{{ $page->title }}</div>
                   @php
                    $badgeClass = match($page->status) {
                      'published' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                      'draft' => 'bg-slate-100 text-slate-700 border-slate-200',
                      default => 'bg-slate-100 text-slate-700 border-slate-200'
                    };
                   @endphp
                  <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium border {{ $badgeClass }}">{{ ucfirst($page->status) }}</span>
                </div>

                <div class="mt-1 text-sm text-slate-500">
                  {{ $page->author?->name ?? '—' }}
                  · Updated {{ $page->updated_at->format('M j, Y') }}
                </div>

                <div class="mt-3 flex flex-wrap items-center gap-2">
                  <a class="inline-flex items-center justify-center gap-2 rounded-lg bg-white px-3 py-1.5 text-sm font-medium text-slate-700 border border-slate-200 hover:bg-slate-50" href="{{ route('admin.pages.edit', $page) }}">Edit</a>
                  <a class="inline-flex items-center justify-center gap-2 rounded-lg px-3 py-1.5 text-sm font-medium text-slate-700 hover:bg-slate-100" href="{{ route('admin.pages.preview', $page) }}" target="_blank" rel="noopener">Preview</a>

                  @if($page->status==='published')
                    <a class="inline-flex items-center justify-center gap-2 rounded-lg px-3 py-1.5 text-sm font-medium text-slate-700 hover:bg-slate-100" href="{{ route('site.pages.show', $page->slug) }}" target="_blank" rel="noopener">View</a>
                  @endif

                  @if(request('trash')==='1')
                    <form method="POST" action="{{ route('admin.pages.restore', $page->id) }}">
                      @csrf
                      <button class="inline-flex items-center justify-center gap-2 rounded-lg px-3 py-1.5 text-sm font-medium text-slate-700 hover:bg-slate-100" type="submit">Restore</button>
                    </form>
                  @else
                    <button class="inline-flex items-center justify-center gap-2 rounded-lg px-3 py-1.5 text-sm font-medium text-red-600 hover:bg-red-50" data-trash="pg{{ $page->id }}">Trash</button>
                  @endif
                </div>
              </div>
            </div>

            @if(request('trash')!=='1')
              <form id="delForm_pg{{ $page->id }}" method="POST" action="{{ route('admin.pages.destroy', $page) }}" class="hidden">
                @csrf @method('DELETE')
              </form>
            @endif
          </article>
        @endforeach
      </div>

      <div class="hidden sm:block overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-600 border-b border-slate-200">
              <th class="px-4 py-3 w-10"><input id="chkAll" class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-200" type="checkbox" aria-label="Select all"/></th>
              <th class="px-4 py-3">Title</th>
              <th class="px-4 py-3">Author</th>
              <th class="px-4 py-3">Status</th>
              <th class="px-4 py-3">Updated</th>
              <th class="px-4 py-3 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-200 bg-white">
            @foreach($pages as $page)
              <tr class="hover:bg-slate-50 transition-colors group" data-page="pg{{ $page->id }}">
                <td class="px-4 py-4"><input class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-200 row-chk" type="checkbox" value="{{ $page->id }}" aria-label="Select page {{ $page->id }}"/></td>
                <td class="px-4 py-4">
                  <div class="flex items-center gap-3">
                    @if($page->featuredImage)
                      <img src="{{ $page->featuredImage->url() }}" alt="" class="h-9 w-12 object-cover rounded-md border border-slate-200"/>
                    @else
                      <div class="h-9 w-12 rounded-lg border border-slate-200 bg-slate-50"></div>
                    @endif
                    <div class="min-w-0">
                      <div class="text-sm font-medium text-slate-900 truncate">{{ $page->title }}</div>
                      <div class="text-xs text-slate-400 mt-0.5 truncate">/{{ $page->slug }}</div>
                    </div>
                  </div>
                </td>
                <td class="px-4 py-4 text-sm text-slate-600">{{ $page->author?->name ?? '—' }}</td>
                <td class="px-4 py-4">
                   @php
                    $badgeClass = match($page->status) {
                      'published' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                      'draft' => 'bg-slate-100 text-slate-700 border-slate-200',
                      default => 'bg-slate-100 text-slate-700 border-slate-200'
                    };
                   @endphp
                  <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium border {{ $badgeClass }}">{{ ucfirst($page->status) }}</span>
                </td>
                <td class="px-4 py-4 text-sm text-slate-600">{{ $page->updated_at->format('M j, Y') }}</td>
                <td class="px-4 py-4 text-right">
                  @if(request('trash')==='1')
                    <form method="POST" action="{{ route('admin.pages.restore', $page->id) }}" class="inline">
                      @csrf
                      <button class="inline-flex items-center justify-center gap-2 rounded-lg px-2 py-1 text-sm font-medium text-slate-600 hover:bg-slate-100" type="submit">Restore</button>
                    </form>
                  @else
                    <details class="relative inline-block text-left">
                      <summary class="inline-flex items-center justify-center h-8 w-8 rounded-lg border border-slate-200 bg-white text-slate-500 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-200 cursor-pointer" aria-label="Open actions menu">
                        <span class="material-icons-outlined text-[18px]" aria-hidden="true">more_horiz</span>
                      </summary>
                      <div class="absolute right-0 mt-2 w-48 rounded-xl border border-slate-200 bg-white shadow-lg overflow-hidden z-10 py-1">
                        <a href="{{ route('admin.pages.edit', $page) }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">Edit</a>
                        <a href="{{ route('admin.pages.preview', $page) }}" target="_blank" rel="noopener" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">Preview</a>
                        @if($page->status==='published')
                          <a href="{{ route('site.pages.show', $page->slug) }}" target="_blank" rel="noopener" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">View on site</a>
                        @endif
                        <button class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50" type="button" data-trash="pg{{ $page->id }}">Trash</button>
                      </div>
                    </details>
                    <form id="delForm_pg{{ $page->id }}" method="POST" action="{{ route('admin.pages.destroy', $page) }}" class="hidden">
                      @csrf @method('DELETE')
                    </form>
                  @endif
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>

        <div class="flex items-center justify-between px-4 py-3 border-t border-slate-200 bg-slate-50 rounded-b-xl">
          <div class="text-xs text-slate-500">
            Showing <span class="font-medium">{{ $pages->firstItem() ?? 0 }}</span> to <span class="font-medium">{{ $pages->lastItem() ?? 0 }}</span> of <span class="font-medium">{{ $pages->total() }}</span> results
          </div>
          <div class="text-sm">{{ $pages->links() }}</div>
        </div>
      </div>
    @endif
  </div>

  <div id="modalBackdrop" class="modal-backdrop hidden" aria-hidden="true"></div>
  <div id="confirmModal" class="modal hidden" role="dialog" aria-modal="true" aria-labelledby="modalTitle" aria-describedby="modalDesc">
    <div class="modal-panel">
      <div class="flex items-start gap-3">
        <div class="h-10 w-10 rounded-xl bg-red-50 dark:bg-red-900/20 flex items-center justify-center">
          <span class="material-icons-outlined text-red-600" aria-hidden="true">delete_outline</span>
        </div>
        <div class="min-w-0">
          <div id="modalTitle" class="text-base font-semibold text-text-strong dark:text-white">Move to trash?</div>
          <div id="modalDesc" class="mt-1 text-sm text-text-muted dark:text-slate-400">You can undo this action for a short time.</div>
        </div>
        <button class="ml-auto btn-ghost px-2 py-2" aria-label="Close modal" type="button" onclick="closeConfirm()">
          <span class="material-icons-outlined text-[18px]" aria-hidden="true">close</span>
        </button>
      </div>

      <div class="mt-5 flex items-center justify-end gap-2">
        <button class="btn-ghost" type="button" onclick="closeConfirm()">Cancel</button>
        <button id="confirmBtn" class="btn-danger px-4 py-2" type="button">Trash</button>
      </div>
    </div>
  </div>

  @php
    $scripts = <<<'HTML'
<script>
  const bulkForm = document.getElementById('bulkForm');
  function syncBulkHiddenInputs(){
    bulkForm.querySelectorAll('input[name="ids[]"]').forEach(el => el.remove());
    document.querySelectorAll('.row-chk:checked').forEach(chk => {
      const inp = document.createElement('input');
      inp.type = 'hidden';
      inp.name = 'ids[]';
      inp.value = chk.value;
      bulkForm.appendChild(inp);
    });
  }
  document.querySelectorAll('.row-chk').forEach(chk => chk.addEventListener('change', syncBulkHiddenInputs));
  const chkAll = document.getElementById('chkAll');
  if(chkAll){
    chkAll.addEventListener('change', () => {
      document.querySelectorAll('.row-chk').forEach(chk => chk.checked = chkAll.checked);
      syncBulkHiddenInputs();
    });
  }
  bulkForm.addEventListener('submit', (e) => {
    syncBulkHiddenInputs();
    if(bulkForm.querySelectorAll('input[name="ids[]"]').length === 0){
      e.preventDefault();
      showToast({ tone: 'danger', title: 'No selection', message: 'Select at least one item.' });
    }
  });

  let pendingFormId = null;
  const modal = document.getElementById('confirmModal');
  const backdrop = document.getElementById('modalBackdrop');
  const confirmBtn = document.getElementById('confirmBtn');

  function openConfirm(formId){
    pendingFormId = formId;
    backdrop.classList.remove('hidden');
    modal.classList.remove('hidden');
    setTimeout(() => confirmBtn && confirmBtn.focus(), 0);
  }
  function closeConfirm(){
    pendingFormId = null;
    backdrop.classList.add('hidden');
    modal.classList.add('hidden');
  }
  window.closeConfirm = closeConfirm;

  backdrop.addEventListener('click', closeConfirm);
  document.addEventListener('keydown', (e) => {
    if(e.key === 'Escape' && !modal.classList.contains('hidden')) closeConfirm();
  });

  document.querySelectorAll('[data-trash]').forEach(btn => {
    btn.addEventListener('click', () => {
      const id = btn.getAttribute('data-trash');
      openConfirm('delForm_' + id);
    });
  });

  confirmBtn.addEventListener('click', () => {
    if(!pendingFormId) return;
    const form = document.getElementById(pendingFormId);
    if(form) form.submit();
  });
</script>
HTML;
  @endphp
  <x-slot:scripts>{!! $scripts !!}</x-slot:scripts>
</x-admin.layout>

<x-admin.layout :title="'Pages · Mini CMS'" :crumb="'Pages'">
  <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
      <h1 class="text-2xl font-semibold text-text-strong dark:text-white tracking-tight">Pages</h1>
      <p class="text-sm text-text-muted dark:text-slate-400 mt-1">Manage static pages for your site.</p>
    </div>
    <div class="flex flex-wrap gap-2">
      <a class="btn-primary" href="{{ route('admin.pages.create') }}">
        <span class="material-icons-outlined text-[18px]" aria-hidden="true">add</span>
        Add New Page
      </a>
      <a class="btn-ghost" href="{{ route('admin.pages.index', array_merge(request()->query(), ['trash' => request('trash')==='1' ? null : 1])) }}">
        <span class="material-icons-outlined text-[18px]" aria-hidden="true">delete</span>
        {{ request('trash')==='1' ? 'Back to list' : 'Trash' }}
      </a>
    </div>
  </div>

  <div class="card overflow-hidden">
    <div class="px-4 sm:px-6 py-3 bg-slate-50/70 dark:bg-slate-800/30 border-b border-border-light dark:border-border-dark space-y-3">
      <form class="flex flex-col sm:flex-row gap-3 sm:items-center" method="GET" action="{{ route('admin.pages.index') }}">
        <input type="hidden" name="trash" value="{{ request('trash') }}"/>

        <div class="relative w-full sm:w-80">
          <input class="input pr-10" name="q" value="{{ $q }}" placeholder="Search pages..." />
          <span class="material-icons-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400" aria-hidden="true">search</span>
        </div>

        <div class="relative w-full sm:w-44">
          <select class="select" name="status">
            <option value="">All status</option>
            <option value="draft" {{ $status==='draft' ? 'selected' : '' }}>Draft</option>
            <option value="review" {{ $status==='review' ? 'selected' : '' }}>Review</option>
            <option value="published" {{ $status==='published' ? 'selected' : '' }}>Published</option>
          </select>
          <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-500">
            <span class="material-icons-outlined text-sm" aria-hidden="true">expand_more</span>
          </div>
        </div>

        <button class="btn-ghost" type="submit">Filter</button>
        <a class="btn-soft px-3 py-2" href="{{ route('admin.pages.index', ['trash' => request('trash')]) }}">Clear</a>

        <div class="sm:ml-auto text-sm text-slate-500 dark:text-slate-400">
          {{ $pages->total() }} items
        </div>
      </form>

      <form id="bulkForm" class="flex flex-col sm:flex-row gap-3 sm:items-center" method="POST" action="{{ route('admin.pages.bulk') }}">
        @csrf
        <input type="hidden" name="trash" value="{{ request('trash') }}"/>

        <div class="relative w-full sm:w-60">
          <select class="select" name="action" required>
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

        <button class="btn-ghost" type="submit">Apply</button>
        <div class="text-xs text-slate-500 dark:text-slate-400">Select rows then apply.</div>
      </form>
    </div>

    @if($pages->count() === 0)
      <div class="p-10 text-center">
        <div class="mx-auto h-12 w-12 rounded-2xl bg-primary/10 flex items-center justify-center">
          <span class="material-icons-outlined text-primary" aria-hidden="true">inbox</span>
        </div>
        <h2 class="mt-4 text-lg font-semibold text-text-strong dark:text-white">No pages found</h2>
        <p class="mt-1 text-sm text-text-muted dark:text-slate-400">Adjust filters or create a new page.</p>
        <div class="mt-5 flex justify-center gap-2">
          <a class="btn-primary" href="{{ route('admin.pages.create') }}">Create Page</a>
          <a class="btn-ghost" href="{{ route('admin.pages.index', ['trash' => request('trash')]) }}">Reset</a>
        </div>
      </div>
    @else
      <div class="sm:hidden p-4 space-y-3">
        @foreach($pages as $page)
          <article class="card p-4" data-page="pg{{ $page->id }}">
            <div class="flex items-start gap-3">
              <input class="chk mt-1 row-chk" type="checkbox" value="{{ $page->id }}" aria-label="Select page {{ $page->id }}"/>
              <div class="min-w-0 flex-1">
                <div class="flex items-center gap-2">
                  <div class="font-medium text-text-strong dark:text-white truncate">{{ $page->title }}</div>
                  <span class="badge {{ $page->status==='published' ? 'badge-pub' : 'badge-draft' }}">{{ ucfirst($page->status) }}</span>
                </div>

                <div class="mt-1 text-sm text-text-muted dark:text-slate-400">
                  {{ $page->author?->name ?? '—' }}
                  · Updated {{ $page->updated_at->format('M j, Y') }}
                </div>

                <div class="mt-3 flex flex-wrap items-center gap-2">
                  <a class="btn-soft px-3 py-1.5" href="{{ route('admin.pages.edit', $page) }}">Edit</a>
                  <a class="btn-ghost px-3 py-1.5" href="{{ route('admin.pages.preview', $page) }}" target="_blank" rel="noopener">Preview</a>

                  @if($page->status==='published')
                    <a class="btn-ghost px-3 py-1.5" href="{{ route('site.pages.show', $page->slug) }}" target="_blank" rel="noopener">View</a>
                  @endif

                  @if(request('trash')==='1')
                    <form method="POST" action="{{ route('admin.pages.restore', $page->id) }}">
                      @csrf
                      <button class="btn-ghost px-3 py-1.5" type="submit">Restore</button>
                    </form>
                  @else
                    <button class="btn-danger px-3 py-1.5" data-trash="pg{{ $page->id }}">Trash</button>
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
        <table class="table">
          <thead>
            <tr class="bg-slate-50/70 dark:bg-slate-800/40 border-b border-border-light dark:border-border-dark">
              <th class="p-4 w-10"><input id="chkAll" class="chk" type="checkbox" aria-label="Select all"/></th>
              <th class="th">Title</th>
              <th class="th">Author</th>
              <th class="th">Status</th>
              <th class="th">Updated</th>
              <th class="th text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-border-light dark:divide-border-dark">
            @foreach($pages as $page)
              <tr class="row group" data-page="pg{{ $page->id }}">
                <td class="p-4"><input class="chk row-chk" type="checkbox" value="{{ $page->id }}" aria-label="Select page {{ $page->id }}"/></td>
                <td class="td">
                  <div class="flex items-center gap-3">
                    @if($page->featuredImage)
                      <img src="{{ $page->featuredImage->url() }}" alt="" class="h-9 w-12 object-cover rounded-md border border-border-light dark:border-border-dark"/>
                    @else
                      <div class="h-9 w-12 rounded-md border border-border-light dark:border-border-dark bg-slate-100 dark:bg-slate-800"></div>
                    @endif
                    <div class="min-w-0">
                      <div class="font-medium text-text-strong dark:text-white truncate">{{ $page->title }}</div>
                      <div class="text-xs text-text-muted dark:text-slate-400 truncate">/p/{{ $page->slug }}</div>
                    </div>
                  </div>
                </td>
                <td class="td text-slate-600 dark:text-slate-300">{{ $page->author?->name ?? '—' }}</td>
                <td class="td"><span class="badge {{ $page->status==='published' ? 'badge-pub' : 'badge-draft' }}">{{ ucfirst($page->status) }}</span></td>
                <td class="td text-slate-500 dark:text-slate-400">{{ $page->updated_at->format('M j, Y') }}</td>
                <td class="td text-right">
                  @if(request('trash')==='1')
                    <form method="POST" action="{{ route('admin.pages.restore', $page->id) }}" class="inline">
                      @csrf
                      <button class="btn-soft px-3 py-1.5" type="submit">Restore</button>
                    </form>
                  @else
                    <details class="relative inline-block">
                      <summary class="inline-flex items-center justify-center h-9 w-9 rounded-lg border border-border-light dark:border-border-dark bg-surface-light dark:bg-surface-dark hover:bg-slate-50 dark:hover:bg-slate-800 cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/25" aria-label="Open actions menu">
                        <span class="material-icons-outlined text-[20px] text-slate-500 dark:text-slate-300" aria-hidden="true">more_horiz</span>
                      </summary>
                      <div class="absolute right-0 mt-2 w-44 rounded-xl border border-border-light dark:border-border-dark bg-surface-light dark:bg-surface-dark shadow-soft2 overflow-hidden z-10">
                        <a href="{{ route('admin.pages.edit', $page) }}" class="block px-3 py-2 text-sm hover:bg-slate-50 dark:hover:bg-slate-800">Edit</a>
                        <a href="{{ route('admin.pages.preview', $page) }}" target="_blank" rel="noopener" class="block px-3 py-2 text-sm hover:bg-slate-50 dark:hover:bg-slate-800">Preview</a>
                        @if($page->status==='published')
                          <a href="{{ route('site.pages.show', $page->slug) }}" target="_blank" rel="noopener" class="block px-3 py-2 text-sm hover:bg-slate-50 dark:hover:bg-slate-800">View</a>
                        @endif
                        <button class="w-full text-left px-3 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20" type="button" data-trash="pg{{ $page->id }}">Trash</button>
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

        <div class="flex items-center justify-between px-6 py-4 border-t border-border-light dark:border-border-dark bg-slate-50/70 dark:bg-slate-800/30">
          <div class="text-xs text-slate-500 dark:text-slate-400">
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

<x-admin.layout :title="'Leads · Mini CMS'" :crumb="'Leads'">
  <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
      <h1 class="text-2xl font-semibold text-slate-900 dark:text-white tracking-tight">Leads</h1>
      <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">Review and manage contact form submissions.</p>
    </div>
  </div>

  <div class="card overflow-hidden">
    <div class="px-4 sm:px-6 py-3 bg-slate-50/70 dark:bg-slate-800/30 border-b border-border-light dark:border-border-dark">
      <div class="flex flex-col lg:flex-row gap-3 lg:items-center">
        {{-- Bulk Actions --}}
        <form id="bulkForm" class="flex flex-col sm:flex-row gap-2 sm:items-center" method="POST" action="{{ route('admin.leads.bulk') }}">
          @csrf
          <div class="relative w-full sm:w-48">
            <select class="select text-sm focus:outline-none focus:ring-0 focus-visible:ring-2 focus-visible:ring-slate-200 focus:border-slate-300" name="action" required>
              <option value="">Bulk Actions</option>
              <option value="new">Mark as New</option>
              <option value="handled">Mark as Handled</option>
              <option value="spam">Mark as Spam</option>
              <option value="delete">Delete</option>
            </select>
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-500">
              <span class="material-icons-outlined text-sm" aria-hidden="true">expand_more</span>
            </div>
          </div>

          <button class="btn-ghost sm:w-auto" type="submit">Apply</button>
        </form>

        {{-- Divider --}}
        <div class="hidden lg:block h-8 w-px bg-border-light dark:bg-border-dark"></div>

        {{-- Search & Filters --}}
        <form class="flex flex-col sm:flex-row gap-2 sm:items-center flex-1" method="GET" action="{{ route('admin.leads.index') }}">
          <div class="relative w-full sm:w-64">
            <input class="input pr-10 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-200 focus:border-slate-300" name="q" value="{{ $q }}" placeholder="Search leads..." />
            <span class="material-icons-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400" aria-hidden="true">search</span>
          </div>

          <div class="relative w-full sm:w-40">
            <select class="select focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-200 focus:border-slate-300" name="status">
              <option value="">All status</option>
              <option value="new" {{ $status==='new' ? 'selected' : '' }}>New</option>
              <option value="handled" {{ $status==='handled' ? 'selected' : '' }}>Handled</option>
              <option value="spam" {{ $status==='spam' ? 'selected' : '' }}>Spam</option>
            </select>
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-500">
              <span class="material-icons-outlined text-sm" aria-hidden="true">expand_more</span>
            </div>
          </div>

          <button class="btn bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm sm:w-auto focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-200 focus:border-slate-300" type="submit">Filter</button>
          <a class="px-2 py-1 rounded-md text-sm text-slate-600 hover:text-slate-900 hover:underline dark:text-slate-400 dark:hover:text-slate-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-200 transition-colors" href="{{ route('admin.leads.index') }}">Clear</a>
        </form>

        {{-- Item Count --}}
        <div class="text-sm text-slate-500 dark:text-slate-400">
          {{ $leads->total() }} items
        </div>
      </div>
    </div>

    @if($leads->count() === 0)
      <div class="p-10 text-center">
        <div class="mx-auto h-12 w-12 rounded-2xl bg-primary/10 flex items-center justify-center">
          <span class="material-icons-outlined text-primary" aria-hidden="true">inbox</span>
        </div>
        <h2 class="mt-4 text-lg font-semibold text-slate-900 dark:text-white">No leads found</h2>
        <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">Adjust filters or wait for new submissions.</p>
      </div>
    @else
      <div class="sm:hidden p-4 space-y-3">
        @foreach($leads as $lead)
          <article class="card p-4">
            <div class="flex items-start gap-3">
              <input class="chk mt-1 row-chk" type="checkbox" value="{{ $lead->id }}" aria-label="Select lead" />
              <div class="min-w-0 flex-1">
                <div class="flex items-center gap-2">
                  <div class="font-medium text-slate-900 dark:text-white truncate">{{ $lead->name }}</div>
                  <span class="badge {{ $lead->status==='handled' ? 'badge-pub' : ($lead->status==='spam' ? 'badge-danger' : 'badge-draft') }}">{{ ucfirst($lead->status) }}</span>
                </div>

                <div class="mt-1 text-sm text-slate-600 dark:text-slate-400">
                  {{ $lead->email }}
                  @if($lead->phone) · {{ $lead->phone }} @endif
                  · {{ $lead->created_at->format('M j, Y') }}
                </div>

                <div class="mt-3 flex flex-wrap items-center gap-2">
                  <a class="btn-soft px-3 py-1.5" href="{{ route('admin.leads.show', $lead) }}">View</a>
                </div>
              </div>
            </div>
          </article>
        @endforeach
      </div>

      <div class="hidden sm:block overflow-x-auto">
        <table class="table">
          <thead>
            <tr class="bg-slate-50/70 dark:bg-slate-800/40 border-b border-border-light dark:border-border-dark">
              <th class="p-4 w-10"><input id="chkAll" class="chk" type="checkbox" aria-label="Select all"/></th>
              <th class="th">Name</th>
              <th class="th">Email</th>
              <th class="th">Status</th>
              <th class="th">Date</th>
              <th class="th text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-border-light dark:divide-border-dark">
            @foreach($leads as $lead)
              <tr class="row group hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                <td class="p-4"><input class="chk row-chk" type="checkbox" value="{{ $lead->id }}" aria-label="Select lead"/></td>
                <td class="td">
                  <div class="font-medium text-slate-900 dark:text-white">{{ $lead->name }}</div>
                  @if($lead->phone)<div class="text-xs text-slate-400 leading-4">{{ $lead->phone }}</div>@endif
                </td>
                <td class="td text-slate-600 dark:text-slate-300">{{ $lead->email }}</td>
                <td class="td"><span class="badge {{ $lead->status==='handled' ? 'badge-pub' : ($lead->status==='spam' ? 'badge-danger' : 'badge-draft') }}">{{ ucfirst($lead->status) }}</span></td>
                <td class="td text-slate-500 dark:text-slate-400">{{ $lead->created_at->format('M j, Y') }}</td>
                <td class="td text-right">
                  <details class="relative inline-block">
                    <summary class="inline-flex items-center justify-center h-9 w-9 rounded-lg border border-border-light dark:border-border-dark bg-surface-light dark:bg-surface-dark hover:bg-slate-50 dark:hover:bg-slate-800 cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/25" aria-label="Open actions menu">
                      <span class="material-icons-outlined text-[20px] text-slate-500 dark:text-slate-300" aria-hidden="true">more_horiz</span>
                    </summary>
                    <div class="absolute right-0 mt-2 w-32 rounded-xl border border-border-light dark:border-border-dark bg-surface-light dark:bg-surface-dark shadow-soft2 overflow-hidden z-10">
                      <a href="{{ route('admin.leads.show', $lead) }}" class="block px-3 py-2 text-sm hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300">View</a>
                    </div>
                  </details>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>

        <div class="flex items-center justify-between px-6 py-4 border-t border-border-light dark:border-border-dark bg-slate-50/70 dark:bg-slate-800/30">
          <div class="text-xs text-slate-500 dark:text-slate-400">
            Showing <span class="font-medium">{{ $leads->firstItem() ?? 0 }}</span> to <span class="font-medium">{{ $leads->lastItem() ?? 0 }}</span> of <span class="font-medium">{{ $leads->total() }}</span> results
          </div>
          <div class="text-sm">{{ $leads->links() }}</div>
        </div>
      </div>
    @endif
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
      showToast({ tone: 'danger', title: 'No selection', message: 'Select at least one lead.' });
    }
  });
</script>
HTML;
  @endphp
  <x-slot:scripts>{!! $scripts !!}</x-slot:scripts>
</x-admin.layout>

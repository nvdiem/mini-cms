<x-admin.layout :title="'Leads · Mini CMS'" :crumb="'Leads'">
  <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
      <h1 class="text-2xl font-semibold text-text-strong dark:text-white tracking-tight">Leads</h1>
      <p class="text-sm text-text-muted dark:text-slate-400 mt-1">Manage contact form submissions.</p>
    </div>
  </div>

  <div class="card overflow-hidden">
    <div class="px-4 sm:px-6 py-3 bg-slate-50/70 dark:bg-slate-800/30 border-b border-border-light dark:border-border-dark space-y-3">
      <form class="flex flex-col sm:flex-row gap-3 sm:items-center" method="GET" action="{{ route('admin.leads.index') }}">
        <div class="relative w-full sm:w-80">
          <input class="input pr-10" name="q" value="{{ $q }}" placeholder="Search leads..." />
          <span class="material-icons-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400" aria-hidden="true">search</span>
        </div>

        <div class="relative w-full sm:w-44">
          <select class="select" name="status">
            <option value="">All status</option>
            <option value="new" {{ $status==='new' ? 'selected' : '' }}>New</option>
            <option value="handled" {{ $status==='handled' ? 'selected' : '' }}>Handled</option>
            <option value="spam" {{ $status==='spam' ? 'selected' : '' }}>Spam</option>
          </select>
          <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-500">
            <span class="material-icons-outlined text-sm" aria-hidden="true">expand_more</span>
          </div>
        </div>

        <button class="btn-ghost" type="submit">Filter</button>
        <a class="btn-soft px-3 py-2" href="{{ route('admin.leads.index') }}">Clear</a>

        <div class="sm:ml-auto text-sm text-slate-500 dark:text-slate-400">
          {{ $leads->total() }} leads
        </div>
      </form>

      <form id="bulkForm" class="flex flex-col sm:flex-row gap-3 sm:items-center" method="POST" action="{{ route('admin.leads.bulk') }}">
        @csrf
        <div class="relative w-full sm:w-60">
          <select class="select" name="action" required>
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

        <button class="btn-ghost" type="submit">Apply</button>
        <div class="text-xs text-slate-500 dark:text-slate-400">Select rows then apply.</div>
      </form>
    </div>

    @if($leads->count() === 0)
      <div class="p-10 text-center">
        <div class="mx-auto h-12 w-12 rounded-2xl bg-primary/10 flex items-center justify-center">
          <span class="material-icons-outlined text-primary" aria-hidden="true">inbox</span>
        </div>
        <h2 class="mt-4 text-lg font-semibold text-text-strong dark:text-white">No leads found</h2>
        <p class="mt-1 text-sm text-text-muted dark:text-slate-400">Adjust filters or wait for new submissions.</p>
      </div>
    @else
      <div class="sm:hidden p-4 space-y-3">
        @foreach($leads as $lead)
          <article class="card p-4">
            <div class="flex items-start gap-3">
              <input class="chk mt-1 row-chk" type="checkbox" value="{{ $lead->id }}" aria-label="Select lead" />
              <div class="min-w-0 flex-1">
                <div class="flex items-center gap-2">
                  <div class="font-medium text-text-strong dark:text-white truncate">{{ $lead->name }}</div>
                  <span class="badge {{ $lead->status==='handled' ? 'badge-pub' : ($lead->status==='spam' ? 'badge-danger' : 'badge-draft') }}">{{ ucfirst($lead->status) }}</span>
                </div>

                <div class="mt-1 text-sm text-text-muted dark:text-slate-400">
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
              <tr class="row group">
                <td class="p-4"><input class="chk row-chk" type="checkbox" value="{{ $lead->id }}" aria-label="Select lead"/></td>
                <td class="td">
                  <div class="font-medium text-text-strong dark:text-white">{{ $lead->name }}</div>
                  @if($lead->phone)<div class="text-xs text-text-muted dark:text-slate-400">{{ $lead->phone }}</div>@endif
                </td>
                <td class="td text-slate-600 dark:text-slate-300">{{ $lead->email }}</td>
                <td class="td"><span class="badge {{ $lead->status==='handled' ? 'badge-pub' : ($lead->status==='spam' ? 'badge-danger' : 'badge-draft') }}">{{ ucfirst($lead->status) }}</span></td>
                <td class="td text-slate-500 dark:text-slate-400">{{ $lead->created_at->format('M j, Y') }}</td>
                <td class="td text-right">
                  <a href="{{ route('admin.leads.show', $lead) }}" class="btn-soft px-3 py-1.5">View</a>
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

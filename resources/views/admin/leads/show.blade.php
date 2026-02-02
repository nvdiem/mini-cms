<x-admin.layout :title="'Lead Detail · Mini CMS'" :crumb="'Lead Detail'">
  <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
      <h1 class="text-2xl font-semibold text-slate-900 dark:text-white tracking-tight">Lead #{{ $lead->id }}</h1>
      <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Submitted {{ $lead->created_at->format('M j, Y \a\t g:i A') }}</p>
    </div>
    <a class="btn-ghost" href="{{ route('admin.leads.index') }}">← Back to list</a>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    <div class="card lg:col-span-2 p-6">
      <div class="space-y-4">
        <div>
          <div class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Name</div>
          <div class="mt-1 text-base text-slate-900 dark:text-white">{{ $lead->name }}</div>
        </div>

        <div>
          <div class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Email</div>
          <div class="mt-1 text-base text-slate-900 dark:text-white">
            <a href="mailto:{{ $lead->email }}" class="text-primary hover:underline">{{ $lead->email }}</a>
          </div>
        </div>

        @if($lead->phone)
          <div>
            <div class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Phone</div>
            <div class="mt-1 text-base text-slate-900 dark:text-white">
              <a href="tel:{{ $lead->phone }}" class="text-primary hover:underline">{{ $lead->phone }}</a>
            </div>
          </div>
        @endif

        <div>
          <div class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Message</div>
          <div class="mt-2 p-4 bg-slate-50 dark:bg-slate-800 rounded-lg border border-border-light dark:border-border-dark">
            <p class="text-sm text-slate-700 dark:text-slate-200 whitespace-pre-wrap">{{ $lead->message }}</p>
          </div>
        </div>

        <div>
          <div class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Source</div>
          <div class="mt-1 text-sm text-slate-700 dark:text-slate-300">{{ ucfirst(str_replace('_', ' ', $lead->source)) }}</div>
        </div>
      </div>
    </div>

    <div class="space-y-4">
      <div class="card p-6">
        <div class="text-sm font-semibold text-slate-900 dark:text-white">Status</div>
        <div class="mt-4">
          <span class="badge {{ $lead->status==='handled' ? 'badge-pub' : ($lead->status==='spam' ? 'badge-danger' : 'badge-draft') }}">{{ ucfirst($lead->status) }}</span>
        </div>

        <div class="mt-6 space-y-2">
          @if($lead->status !== 'handled')
            <form method="POST" action="{{ route('admin.leads.status', $lead) }}">
              @csrf
              <input type="hidden" name="status" value="handled" />
              <button class="btn-primary w-full" type="submit">
                <span class="material-icons-outlined text-[18px]" aria-hidden="true">done</span>
                Mark as Handled
              </button>
            </form>
          @endif

          @if($lead->status !== 'spam')
            <form method="POST" action="{{ route('admin.leads.status', $lead) }}">
              @csrf
              <input type="hidden" name="status" value="spam" />
              <button class="btn-danger w-full" type="submit">
                <span class="material-icons-outlined text-[18px]" aria-hidden="true">block</span>
                Mark as Spam
              </button>
            </form>
          @endif

          @if($lead->status !== 'new')
            <form method="POST" action="{{ route('admin.leads.status', $lead) }}">
              @csrf
              <input type="hidden" name="status" value="new" />
              <button class="btn-ghost w-full" type="submit">Mark as New</button>
            </form>
          @endif
        </div>
      </div>

      <div class="card p-6">
        <div class="text-sm font-semibold text-slate-900 dark:text-white">Details</div>
        <div class="mt-4 space-y-2 text-sm">
          <div class="flex justify-between">
            <span class="text-slate-500 dark:text-slate-400">ID</span>
            <span class="text-slate-900 dark:text-white font-medium">#{{ $lead->id }}</span>
          </div>
          <div class="flex justify-between">
            <span class="text-slate-500 dark:text-slate-400">Submitted</span>
            <span class="text-slate-900 dark:text-white">{{ $lead->created_at->format('M j, Y') }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</x-admin.layout>

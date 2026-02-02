<x-admin.layout :title="'Support Inbox · Mini CMS'" :crumb="'Support Inbox'">
  <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
      <h1 class="text-2xl font-semibold text-slate-900 dark:text-white tracking-tight">Support Inbox</h1>
      <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">Manage visitor support conversations via chat.</p>
    </div>
  </div>

  <div class="card overflow-hidden">
    <div class="px-4 sm:px-6 py-3 bg-slate-50/70 dark:bg-slate-800/30 border-b border-border-light dark:border-border-dark">
      <div class="flex flex-col lg:flex-row gap-3 lg:items-center">
        {{-- Search & Filters --}}
        <form class="flex flex-col sm:flex-row gap-2 sm:items-center flex-1" method="GET" action="{{ route('admin.support.index') }}">
          <div class="relative w-full sm:w-64">
            <input class="input pr-10 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-200 focus:border-slate-300" name="q" value="{{ $q }}" placeholder="Search name, email, message..." />
            <span class="material-icons-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400" aria-hidden="true">search</span>
          </div>

          <div class="relative w-full sm:w-40">
            <select class="select focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-200 focus:border-slate-300" name="status">
              <option value="">All status</option>
              <option value="open" {{ $status==='open' ? 'selected' : '' }}>Open</option>
              <option value="pending" {{ $status==='pending' ? 'selected' : '' }}>Pending</option>
              <option value="closed" {{ $status==='closed' ? 'selected' : '' }}>Closed</option>
            </select>
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-500">
              <span class="material-icons-outlined text-sm" aria-hidden="true">expand_more</span>
            </div>
          </div>

          <button class="btn bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm sm:w-auto focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-200 focus:border-slate-300" type="submit">Filter</button>
          <a class="px-2 py-1 rounded-md text-sm text-slate-600 hover:text-slate-900 hover:underline dark:text-slate-400 dark:hover:text-slate-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-200 transition-colors" href="{{ route('admin.support.index') }}">Clear</a>
        </form>

        {{-- Item Count --}}
        <div class="text-sm text-slate-500 dark:text-slate-400">
          {{ $conversations->total() }} conversations
        </div>
      </div>
    </div>

    @if($conversations->count() === 0)
      <div class="p-10 text-center">
        <div class="mx-auto h-12 w-12 rounded-2xl bg-primary/10 flex items-center justify-center">
          <span class="material-icons-outlined text-primary" aria-hidden="true">support_agent</span>
        </div>
        <h2 class="mt-4 text-lg font-semibold text-slate-900 dark:text-white">No conversations found</h2>
        <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">Adjust filters or wait for new visitor messages.</p>
      </div>
    @else
      {{-- Mobile Cards --}}
      <div class="sm:hidden p-4 space-y-3">
        @foreach($conversations as $conv)
          <a href="{{ route('admin.support.show', $conv->id) }}" class="block">
            <article class="card p-4 hover:shadow-md transition-shadow">
              <div class="flex items-start gap-3">
                <div class="min-w-0 flex-1">
                  <div class="flex items-center gap-2">
                    <div class="font-medium text-slate-900 dark:text-white truncate">
                        @if($conv->unread_count > 0)
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-red-500 text-white mr-1 shadow-sm min-w-[20px] justify-center align-middle">
                                {{ $conv->unread_count > 99 ? '99+' : $conv->unread_count }}
                            </span>
                        @endif
                        {{ $conv->name }}
                    </div>
                    @php
                      $badgeClass = match($conv->status) {
                        'open' => 'badge-draft',
                        'pending' => 'bg-amber-100 text-amber-700 border-amber-200 dark:bg-amber-900/20 dark:text-amber-300 dark:border-amber-900/40',
                        'closed' => 'badge-pub',
                      };
                    @endphp
                    <span class="badge {{ $badgeClass }}">{{ ucfirst($conv->status) }}</span>
                  </div>

                  <div class="mt-1 text-sm text-slate-600 dark:text-slate-400">
                    {{ $conv->email ?? 'No email' }}
                    · {{ $conv->last_message_at?->diffForHumans() ?? 'No messages' }}
                  </div>

                  @if($conv->latestMessage->first())
                    <div class="mt-2 text-xs text-slate-500 dark:text-slate-400 truncate">
                      {{ Str::limit($conv->latestMessage->first()->message, 60) }}
                    </div>
                  @endif
                </div>
                <span class="material-icons-outlined text-slate-400" aria-hidden="true">chevron_right</span>
              </div>
            </article>
          </a>
        @endforeach
      </div>

      {{-- Desktop Table --}}
      <div class="hidden sm:block overflow-x-auto">
        <table class="table">
          <thead>
            <tr class="bg-slate-50/70 dark:bg-slate-800/40 border-b border-border-light dark:border-border-dark">
              <th class="th">Visitor</th>
              <th class="th">Email</th>
              <th class="th">Status</th>
              <th class="th">Last Message</th>
              <th class="th">Updated</th>
              <th class="th text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-border-light dark:divide-border-dark">
            @foreach($conversations as $conv)
              <tr class="row group cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors" onclick="window.location='{{ route('admin.support.show', $conv->id) }}'">
                <td class="td">
                  <div class="font-medium text-slate-900 dark:text-white flex items-center">
                    @if($conv->unread_count > 0)
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-500 text-white mr-2 shadow-sm min-w-[20px] justify-center h-5">
                            {{ $conv->unread_count > 99 ? '99+' : $conv->unread_count }}
                        </span>
                    @endif
                    {{ $conv->name }}
                  </div>
                  @if($conv->assignedAgent)
                    <div class="text-xs text-text-muted dark:text-slate-400">→ {{ $conv->assignedAgent->email }}</div>
                  @endif
                </td>
                <td class="td text-slate-600 dark:text-slate-300">{{ $conv->email ?? '—' }}</td>
                <td class="td">
                  @php
                    $badgeClass = match($conv->status) {
                      'open' => 'badge-draft',
                      'pending' => 'bg-amber-100 text-amber-700 border-amber-200 dark:bg-amber-900/20 dark:text-amber-300 dark:border-amber-900/40',
                      'closed' => 'badge-pub',
                    };
                  @endphp
                  <span class="badge {{ $badgeClass }}">{{ ucfirst($conv->status) }}</span>
                </td>
                <td class="td text-slate-500 dark:text-slate-400 max-w-xs truncate">
                  @if($conv->latestMessage->first())
                    {{ Str::limit($conv->latestMessage->first()->message, 50) }}
                  @else
                    —
                  @endif
                </td>
                <td class="td text-slate-500 dark:text-slate-400 whitespace-nowrap">
                  {{ $conv->last_message_at?->diffForHumans() ?? '—' }}
                </td>
                <td class="td text-right" onclick="event.stopPropagation()">
                  <details class="relative inline-block">
                    <summary class="inline-flex items-center justify-center h-9 w-9 rounded-lg border border-border-light dark:border-border-dark bg-surface-light dark:bg-surface-dark hover:bg-slate-50 dark:hover:bg-slate-800 cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/25" aria-label="Open actions menu">
                      <span class="material-icons-outlined text-[20px] text-slate-500 dark:text-slate-300" aria-hidden="true">more_horiz</span>
                    </summary>
                    <div class="absolute right-0 mt-2 w-48 rounded-xl border border-border-light dark:border-border-dark bg-surface-light dark:bg-surface-dark shadow-soft2 overflow-hidden z-10">
                      <a href="{{ route('admin.support.show', $conv->id) }}" class="block px-3 py-2 text-sm hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300">View conversation</a>
                    </div>
                  </details>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>

        <div class="flex items-center justify-between px-6 py-4 border-t border-border-light dark:border-border-dark bg-slate-50/70 dark:bg-slate-800/30">
          <div class="text-xs text-slate-500 dark:text-slate-400">
            Showing <span class="font-medium">{{ $conversations->firstItem() ?? 0 }}</span> to <span class="font-medium">{{ $conversations->lastItem() ?? 0 }}</span> of <span class="font-medium">{{ $conversations->total() }}</span> results
          </div>
          <div class="text-sm">{{ $conversations->links() }}</div>
        </div>
      </div>
    @endif
  </div>
</x-admin.layout>

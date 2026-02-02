<x-admin.layout :title="'Page Builder · Mini CMS'" :crumb="'Page Builder'">
  <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
      <h1 class="text-2xl font-semibold text-slate-900 dark:text-white tracking-tight">Page Builder</h1>
      <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">Upload and manage static HTML sites with contact form integration.</p>
    </div>
    <a href="{{ route('admin.page-builder.create') }}" class="btn-primary">
      <span class="material-icons-outlined text-sm mr-1">upload_file</span>
      Upload Package
    </a>
  </div>

  @if($packages->count() === 0)
    <div class="card p-10 text-center">
      <div class="mx-auto h-12 w-12 rounded-2xl bg-primary/10 flex items-center justify-center">
        <span class="material-icons-outlined text-primary" aria-hidden="true">web</span>
      </div>
      <h2 class="mt-4 text-lg font-semibold text-slate-900 dark:text-white">No packages yet</h2>
      <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
        Upload your first static HTML package to get started.
      </p>
      <a href="{{ route('admin.page-builder.create') }}" class="btn-primary mt-4 inline-flex items-center">
        <span class="material-icons-outlined text-sm mr-1">upload_file</span>
        Upload Package
      </a>
    </div>
  @else
    <div class="card overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-slate-50/70 dark:bg-slate-800/40 border-b border-border-light dark:border-border-dark">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Name</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Slug</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Version</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Status</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Created</th>
              <th class="px-4 py-3 text-right text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-border-light dark:divide-border-dark">
            @foreach($packages as $package)
              <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition">
                <td class="px-4 py-3">
                  <a href="{{ route('admin.page-builder.show', $package) }}" class="font-medium text-slate-900 dark:text-white hover:text-primary hover:underline">
                    {{ $package->name }}
                  </a>
                </td>
                <td class="px-4 py-3">
                  <code class="text-xs bg-slate-100 dark:bg-slate-800 px-2 py-1 rounded">{{ $package->slug }}</code>
                </td>
                <td class="px-4 py-3">
                  <span class="text-xs text-slate-600 dark:text-slate-400">{{ substr($package->version, 0, 8) }}</span>
                </td>
                <td class="px-4 py-3">
                  <div class="flex items-center gap-2">
                    @if($package->is_active)
                      <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">
                        <span class="material-icons-outlined text-xs mr-1">check_circle</span>
                        Active
                      </span>
                    @else
                      <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400">
                        Inactive
                      </span>
                    @endif
                    @if($package->wire_contact)
                      <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                        <span class="material-icons-outlined text-xs mr-1">link</span>
                        Wired
                      </span>
                    @endif
                  </div>
                </td>
                <td class="px-4 py-3 text-sm text-slate-600 dark:text-slate-400">
                  {{ $package->created_at->format('M j, Y') }}
                </td>
                <td class="px-4 py-3 text-right">
                  <div class="flex items-center justify-end gap-2">
                    <a href="{{ $package->public_url }}" target="_blank" class="btn-xs btn-ghost text-primary" title="Preview">
                      <span class="material-icons-outlined text-sm">open_in_new</span>
                    </a>
                    <a href="{{ route('admin.page-builder.show', $package) }}" class="btn-xs btn-ghost text-slate-600" title="View Details">
                      <span class="material-icons-outlined text-sm">visibility</span>
                    </a>
                    @if(!$package->is_active)
                      <form action="{{ route('admin.page-builder.activate', $package) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="btn-xs btn-ghost text-green-600" title="Activate">
                          <span class="material-icons-outlined text-sm">play_circle</span>
                        </button>
                      </form>
                    @endif
                    <form action="{{ route('admin.page-builder.destroy', $package) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this package? This action cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-xs btn-ghost text-red-600" title="Delete">
                          <span class="material-icons-outlined text-sm">delete</span>
                        </button>
                    </form>
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>

    <div class="mt-4">
      {{ $packages->links() }}
    </div>
  @endif
</x-admin.layout>

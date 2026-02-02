<x-admin.layout :title="$package->name . ' · Page Builder'" :crumb="'Page Builder'">
  <div class="mb-6">
    <div class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400 mb-2">
      <a href="{{ route('admin.page-builder.index') }}" class="hover:text-primary">Page Builder</a>
      <span class="material-icons-outlined text-xs">chevron_right</span>
      <span>{{ $package->name }}</span>
    </div>
    <div class="flex items-start justify-between gap-4">
      <div>
        <h1 class="text-2xl font-semibold text-slate-900 dark:text-white tracking-tight">{{ $package->name }}</h1>
        <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">
          Created by {{ $package->creator->name }} on {{ $package->created_at->format('M j, Y') }}
        </p>
      </div>
      <div class="flex items-center gap-2">
        @if(!$package->is_active)
          <form action="{{ route('admin.page-builder.activate', $package) }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="btn-primary">
              <span class="material-icons-outlined text-sm mr-1">play_circle</span>
              Activate
            </button>
          </form>
        @endif
        <a href="{{ $package->public_url }}" target="_blank" class="btn-soft">
          <span class="material-icons-outlined text-sm mr-1">open_in_new</span>
          Preview
        </a>
      </div>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Info -->
    <div class="lg:col-span-2 space-y-6">
      <!-- Status Card -->
      <div class="card p-6">
        <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Package Status</h2>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <div class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Status</div>
            @if($package->is_active)
              <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">
                <span class="material-icons-outlined text-sm mr-1">check_circle</span>
                Active
              </span>
            @else
              <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400">
                <span class="material-icons-outlined text-sm mr-1">pause_circle</span>
                Inactive
              </span>
            @endif
          </div>
          <div>
            <div class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Contact Wiring</div>
            @if($package->wire_contact)
              <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                <span class="material-icons-outlined text-sm mr-1">link</span>
                Enabled
              </span>
            @else
              <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400">
                <span class="material-icons-outlined text-sm mr-1">link_off</span>
                Disabled
              </span>
            @endif
          </div>
        </div>
      </div>

      <!-- Details Card -->
      <div class="card p-6">
        <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Package Details</h2>
        <dl class="space-y-4">
          <div class="flex items-start justify-between border-b border-border-light dark:border-border-dark pb-3">
            <dt class="text-sm font-medium text-slate-600 dark:text-slate-400">Slug</dt>
            <dd class="text-sm text-slate-900 dark:text-white font-mono">{{ $package->slug }}</dd>
          </div>
          <div class="flex items-start justify-between border-b border-border-light dark:border-border-dark pb-3">
            <dt class="text-sm font-medium text-slate-600 dark:text-slate-400">Version</dt>
            <dd class="text-sm text-slate-900 dark:text-white font-mono">{{ $package->version }}</dd>
          </div>
          <div class="flex items-start justify-between border-b border-border-light dark:border-border-dark pb-3">
            <dt class="text-sm font-medium text-slate-600 dark:text-slate-400">Entry File</dt>
            <dd class="text-sm text-slate-900 dark:text-white font-mono">{{ $package->entry_file }}</dd>
          </div>
          <div class="flex items-start justify-between border-b border-border-light dark:border-border-dark pb-3">
            <dt class="text-sm font-medium text-slate-600 dark:text-slate-400">Public URL</dt>
            <dd class="text-sm">
              <a href="{{ $package->public_url }}" target="_blank" class="text-primary hover:underline font-mono">
                {{ $package->public_url }}
              </a>
            </dd>
          </div>
          @if($package->wire_contact)
            <div class="flex items-start justify-between">
              <dt class="text-sm font-medium text-slate-600 dark:text-slate-400">Form Selector</dt>
              <dd class="text-sm text-slate-900 dark:text-white font-mono">{{ $package->wire_selector }}</dd>
            </div>
          @endif
        </dl>
      </div>

      <!-- Technical Info -->
      <div class="card p-6">
        <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Technical Information</h2>
        <dl class="space-y-4">
          <div class="flex items-start justify-between border-b border-border-light dark:border-border-dark pb-3">
            <dt class="text-sm font-medium text-slate-600 dark:text-slate-400">ZIP Path</dt>
            <dd class="text-xs text-slate-900 dark:text-white font-mono break-all">{{ $package->zip_path }}</dd>
          </div>
          <div class="flex items-start justify-between">
            <dt class="text-sm font-medium text-slate-600 dark:text-slate-400">Public Directory</dt>
            <dd class="text-xs text-slate-900 dark:text-white font-mono break-all">{{ $package->public_dir }}</dd>
          </div>
        </dl>
      </div>
    </div>

    <!-- Sidebar -->
    <div class="lg:col-span-1 space-y-6">
      <!-- Quick Actions -->
      <div class="card p-6">
        <h3 class="text-sm font-semibold text-slate-900 dark:text-white mb-4">Quick Actions</h3>
        <div class="space-y-2">
          <a href="{{ $package->public_url }}" target="_blank" class="btn-soft w-full justify-center">
            <span class="material-icons-outlined text-sm mr-2">open_in_new</span>
            Preview Site
          </a>
          @if(!$package->is_active)
            <form action="{{ route('admin.page-builder.activate', $package) }}" method="POST">
              @csrf
              <button type="submit" class="btn-primary w-full justify-center">
                <span class="material-icons-outlined text-sm mr-2">play_circle</span>
                Activate Package
              </button>
            </form>
          @endif
            <a href="{{ route('admin.leads.index') }}?source=pagebuilder:{{ $package->slug }}" class="btn-soft w-full justify-center">
            <span class="material-icons-outlined text-sm mr-2">mail</span>
            View Leads
          </a>
          
          <div class="border-t border-border-light dark:border-border-dark my-4 pt-4">
               <h4 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Update Package</h4>
               <form action="{{ route('admin.page-builder.update', $package->id) }}" method="POST" enctype="multipart/form-data">
                   @csrf
                   @method('PUT')
                   <div class="mb-3">
                       <input type="file" name="zip_file" accept=".zip" required class="block w-full text-xs text-slate-500 file:mr-2 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
                   </div>
                   <button type="submit" class="btn w-full btn-soft justify-center text-xs">
                       <span class="material-icons-outlined text-sm mr-1">upload</span> Overwrite Version
                   </button>
               </form>
          </div>

          <div class="border-t border-border-light dark:border-border-dark my-4 pt-4">
              <form action="{{ route('admin.page-builder.destroy', $package->id) }}" method="POST" onsubmit="return confirm('WARNING: This will permanently delete this package and all associated files. This action cannot be undone.');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn w-full btn-danger justify-center">
                      <span class="material-icons-outlined text-sm mr-2">delete</span>
                      Delete Package
                  </button>
              </form>
          </div>
        </div>
      </div>

      <!-- Info Box -->
      @if($package->wire_contact)
        <div class="card p-6 bg-blue-50/50 dark:bg-blue-900/10 border border-blue-200 dark:border-blue-800">
          <div class="flex items-start gap-3">
            <span class="material-icons-outlined text-blue-600 dark:text-blue-400">info</span>
            <div class="flex-1">
              <h3 class="text-sm font-semibold text-blue-900 dark:text-blue-300 mb-2">Contact Form Integration</h3>
              <p class="text-xs text-blue-800 dark:text-blue-400">
                Forms matching the selector will automatically submit to <code class="bg-blue-100 dark:bg-blue-900/30 px-1 rounded">/lead</code> endpoint and create leads in your CMS.
              </p>
            </div>
          </div>
        </div>
      @endif

      <!-- Timestamps -->
      <div class="card p-6">
        <h3 class="text-sm font-semibold text-slate-900 dark:text-white mb-4">Timestamps</h3>
        <dl class="space-y-3 text-sm">
          <div>
            <dt class="text-slate-600 dark:text-slate-400">Created</dt>
            <dd class="text-slate-900 dark:text-white mt-1">{{ $package->created_at->format('M j, Y g:i A') }}</dd>
          </div>
          <div>
            <dt class="text-slate-600 dark:text-slate-400">Updated</dt>
            <dd class="text-slate-900 dark:text-white mt-1">{{ $package->updated_at->format('M j, Y g:i A') }}</dd>
          </div>
        </dl>
      </div>
    </div>
  </div>
</x-admin.layout>

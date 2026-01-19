<x-admin.layout :title="'Media Details · Mini CMS'" :crumb="'Media / Details'">
  <div class="flex items-center gap-2 mb-6">
    <a href="{{ route('admin.media.index') }}" class="btn-ghost px-2">
      <span class="material-icons-outlined text-lg">arrow_back</span> Back
    </a>
    <h1 class="text-2xl font-semibold text-text-strong dark:text-white tracking-tight">Media Details</h1>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <!-- Left Column: Preview & Danger Zone -->
    <div class="space-y-6">
      <div class="card p-2 overflow-hidden bg-slate-100 dark:bg-slate-800 border bg-[url('/img/checker-pattern.png')]">
        <img src="{{ $media->url() }}" alt="{{ $media->alt_text }}" class="w-full h-auto rounded-lg shadow-sm border border-border-light dark:border-border-dark" />
      </div>

      <!-- Delete Card -->
      <div class="card p-6 border-red-100 dark:border-red-900/30">
        <h3 class="text-sm font-semibold text-red-600 mb-2">Danger Zone</h3>
        @php $isUsed = $media->posts_count > 0 || $media->pages_count > 0; @endphp
        
        @if($isUsed)
          <div class="rounded-lg bg-red-50 text-red-700 p-3 text-sm mb-4 border border-red-200">
             <div class="flex gap-2">
               <span class="material-icons-outlined text-lg">warning</span>
               <div>
                 <strong>Cannot delete:</strong> This file is used in 
                 {{ $media->posts_count }} post(s) and {{ $media->pages_count }} page(s).
               </div>
             </div>
          </div>
          <button disabled class="btn-danger w-full opacity-50 cursor-not-allowed">Delete Media</button>
        @else
           <p class="text-xs text-text-muted mb-4">Permanently delete this file and remove it from the database.</p>
           <form action="{{ route('admin.media.destroy', $media) }}" method="POST" onsubmit="return confirm('Are you sure? This cannot be undone.');">
             @csrf
             @method('DELETE')
             <button type="submit" class="btn-danger w-full">Delete Media</button>
           </form>
        @endif
      </div>
    </div>

    <!-- Right Column: Info & Edit -->
    <div class="lg:col-span-2 space-y-6">
      
      <!-- File Info -->
      <div class="card p-6">
        <h3 class="text-lg font-semibold text-text-strong dark:text-white mb-4">File Information</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
           <div>
             <span class="block text-text-muted text-xs uppercase tracking-wider font-semibold">File name</span>
             <div class="mt-1 font-mono text-slate-600 dark:text-slate-300 break-all">{{ $media->original_name }}</div>
           </div>
           <div>
             <span class="block text-text-muted text-xs uppercase tracking-wider font-semibold">Dimensions</span>
             <div class="mt-1 font-medium">{{ $media->width }} x {{ $media->height }} px</div>
           </div>
           <div>
             <span class="block text-text-muted text-xs uppercase tracking-wider font-semibold">Size</span>
             <div class="mt-1 font-medium">{{ number_format($media->size / 1024, 2) }} KB</div>
           </div>
           <div>
             <span class="block text-text-muted text-xs uppercase tracking-wider font-semibold">Type</span>
             <div class="mt-1 font-medium">{{ $media->mime }}</div>
           </div>
           <div class="sm:col-span-2">
             <span class="block text-text-muted text-xs uppercase tracking-wider font-semibold mb-1">Public URL</span>
             <div class="flex gap-2">
               <input type="text" readonly value="{{ $media->url() }}" id="mediaUrl" 
                      class="input bg-slate-50 dark:bg-slate-800 text-slate-500 text-xs font-mono" />
               <button type="button" onclick="copyUrl()" class="btn-soft px-3" title="Copy URL">
                 <span class="material-icons-outlined text-sm">content_copy</span>
               </button>
             </div>
             <div id="copyMsg" class="text-xs text-green-600 font-medium mt-1 opacity-0 transition">Copied!</div>
           </div>
        </div>
      </div>

      <!-- Edit Metadata -->
      <div class="card p-6">
        <h3 class="text-lg font-semibold text-text-strong dark:text-white mb-4">Metadata</h3>
        <form action="{{ route('admin.media.update', $media) }}" method="POST" class="space-y-4">
           @csrf
           @method('PUT')
           
           <div>
             <label class="block text-sm font-medium mb-1">Alt Text</label>
             <input type="text" name="alt_text" value="{{ old('alt_text', $media->alt_text) }}" class="input" placeholder="Describe the image for accessibility/SEO">
             <p class="text-xs text-text-muted mt-1">Used by screen readers and SEO.</p>
           </div>
           
           <div>
             <label class="block text-sm font-medium mb-1">Caption</label>
             <textarea name="caption" rows="3" class="input" placeholder="Optional caption to display under the image">{{ old('caption', $media->caption) }}</textarea>
           </div>

           <div class="pt-2 flex justify-end">
             <button type="submit" class="btn-primary">Save Changes</button>
           </div>
        </form>
      </div>

    </div>
  </div>

  <script>
    function copyUrl() {
      const input = document.getElementById('mediaUrl');
      input.select();
      navigator.clipboard.writeText(input.value); 
      
      const msg = document.getElementById('copyMsg');
      msg.classList.remove('opacity-0');
      setTimeout(() => msg.classList.add('opacity-0'), 2000);
    }
  </script>
</x-admin.layout>

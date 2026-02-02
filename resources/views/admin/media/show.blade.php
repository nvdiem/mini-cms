<x-admin.layout :title="'Media Details · Mini CMS'" :crumb="'Media / Details'">
  <div class="flex items-center gap-2 mb-6">
    <a href="{{ route('admin.media.index') }}" class="btn bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 transition-colors shadow-sm rounded-lg px-3 py-1.5 text-sm font-medium flex items-center gap-1 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-200">
      <span class="material-icons-outlined text-[18px]">arrow_back</span> Back
    </a>
    <h1 class="text-2xl font-semibold text-slate-900 dark:text-white tracking-tight ml-2">Media Details</h1>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <!-- Left Column: Preview & Danger Zone -->
    <div class="space-y-6">
      <div class="card p-2 overflow-hidden bg-slate-100 dark:bg-slate-800 border bg-[url('/img/checker-pattern.png')]">
        <img src="{{ $media->url() }}" alt="{{ $media->alt_text }}" class="w-full h-auto rounded-lg shadow-sm border border-border-light dark:border-border-dark" />
      </div>

      <!-- Delete Card -->
      <div class="card p-6 border border-slate-200 dark:border-slate-700 shadow-sm rounded-xl">
        <h3 class="text-sm font-semibold text-rose-700 dark:text-rose-400 mb-3">Danger Zone</h3>
        @php $isUsed = $media->posts_count > 0 || $media->pages_count > 0; @endphp
        
        @if($isUsed)
          <div class="rounded-xl bg-orange-50 border border-orange-200 text-orange-800 p-4 text-sm mb-4">
             <div class="flex gap-3">
               <span class="material-icons-outlined text-xl text-orange-600">warning_amber</span>
               <div>
                 <strong class="font-semibold block mb-1">Cannot delete file</strong>
                 <div class="opacity-90">
                    This media is currently used in <strong>{{ $media->posts_count }} post(s)</strong> and <strong>{{ $media->pages_count }} page(s)</strong>.
                 </div>
               </div>
             </div>
          </div>
          <button disabled class="w-full py-2.5 rounded-lg bg-slate-100 text-slate-400 font-medium text-sm cursor-not-allowed border border-slate-200 dark:bg-slate-800 dark:border-slate-700 dark:text-slate-500 shadow-sm">Delete Media</button>
        @else
           <p class="text-xs text-rose-700/80 mb-4 bg-rose-50 border border-rose-100 p-3 rounded-lg">Permanently delete this file and remove it from the database.</p>
           <form action="{{ route('admin.media.destroy', $media) }}" method="POST" onsubmit="return confirm('Are you sure? This cannot be undone.');">
             @csrf
             @method('DELETE')
             <button type="submit" class="w-full py-2.5 rounded-lg bg-white text-rose-600 font-medium text-sm border border-rose-200 hover:bg-rose-50 hover:border-rose-300 hover:text-rose-700 transition focus:outline-none focus:ring-2 focus:ring-rose-200 shadow-sm">Delete Media</button>
           </form>
        @endif
      </div>
    </div>

    <!-- Right Column: Info & Edit -->
    <div class="lg:col-span-2 space-y-6">
      
      <!-- File Info -->
      <div class="card p-6 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm">
        <h3 class="text-base font-semibold text-slate-900 dark:text-white mb-6">File Information</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-sm">
           <div>
             <span class="block text-slate-500 text-xs uppercase tracking-wider font-semibold mb-1">File name</span>
             <div class="font-mono text-slate-700 dark:text-slate-300 break-all bg-slate-50 border border-slate-100 rounded px-2 py-1 inline-block">{{ $media->original_name }}</div>
           </div>
           <div>
             <span class="block text-slate-500 text-xs uppercase tracking-wider font-semibold mb-1">Dimensions</span>
             <div class="font-medium text-slate-900 dark:text-white">{{ $media->width }} x {{ $media->height }} px</div>
           </div>
           <div>
             <span class="block text-slate-500 text-xs uppercase tracking-wider font-semibold mb-1">Size</span>
             <div class="font-medium text-slate-900 dark:text-white">{{ number_format($media->size / 1024, 2) }} KB</div>
           </div>
           <div>
             <span class="block text-slate-500 text-xs uppercase tracking-wider font-semibold mb-1">Type</span>
             <div class="font-medium text-slate-900 dark:text-white">{{ $media->mime }}</div>
           </div>
           <div class="sm:col-span-2 pt-2 border-t border-slate-100">
             <span class="block text-slate-500 text-xs uppercase tracking-wider font-semibold mb-2">Public URL</span>
             <div class="flex gap-2">
               <input type="text" readonly value="{{ $media->url() }}" id="mediaUrl" 
                      class="input bg-slate-50 border-slate-200 text-slate-600 text-xs font-mono focus:ring-slate-200 pointer-events-none" />
               <button type="button" onclick="copyUrl()" class="btn bg-white border border-slate-200 text-slate-500 hover:text-slate-700 hover:bg-slate-50 px-3 shadow-sm rounded-lg flex items-center justify-center focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-200" title="Copy URL">
                 <span class="material-icons-outlined text-sm">content_copy</span>
               </button>
             </div>
             <div id="copyMsg" class="text-xs text-emerald-600 font-medium mt-2 opacity-0 transition flex items-center gap-1">
                <span class="material-icons-outlined text-sm">check_circle</span> Copied to clipboard!
             </div>
           </div>
        </div>
      </div>

      <!-- Edit Metadata -->
      <div class="card p-6 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm">
        <h3 class="text-base font-semibold text-slate-900 dark:text-white mb-6">Metadata</h3>
        <form action="{{ route('admin.media.update', $media) }}" method="POST" class="space-y-5">
           @csrf
           @method('PUT')
           
           <div>
             <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Alt Text</label>
             <input type="text" name="alt_text" value="{{ old('alt_text', $media->alt_text) }}" class="input focus:ring-slate-200 focus:border-slate-300 focus:outline-none focus:placeholder-slate-300" placeholder="Describe the image for accessibility/SEO">
             <p class="text-xs text-slate-500 mt-1.5">Used by screen readers and SEO.</p>
           </div>

           <div>
              <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Folder</label>
              <div class="relative">
                <select name="folder_id" class="select focus:ring-slate-200 focus:border-slate-300 focus:outline-none bg-white">
                  <option value="">(Unsorted)</option>
                  @foreach(\App\Models\MediaFolder::orderBy('name')->get() as $f)
                    <option value="{{ $f->id }}" {{ $media->folder_id == $f->id ? 'selected' : '' }}>{{ $f->name }}</option>
                  @endforeach
                </select>
              </div>
            </div>
           
           <div>
             <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Caption</label>
             <textarea name="caption" rows="3" class="input focus:ring-slate-200 focus:border-slate-300 focus:outline-none focus:placeholder-slate-300" placeholder="Optional caption to display under the image">{{ old('caption', $media->caption) }}</textarea>
           </div>

           <div class="pt-2 flex justify-end">
             <button type="submit" class="btn-primary px-6 py-2 shadow-sm focus:ring-2 focus:ring-slate-200 focus:ring-offset-2">Save Changes</button>
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

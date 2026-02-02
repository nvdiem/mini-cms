<x-admin.layout :title="'Media · Mini CMS'" :crumb="'Media'">
  <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
      <h1 class="text-2xl font-semibold text-slate-900 dark:text-white tracking-tight">Media Library</h1>
      <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">Manage, organize, and upload media files.</p>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
    <!-- Sidebar -->
    <div class="lg:col-span-1 space-y-6">
      <!-- Upload Card -->
      <div class="card p-6">
        <div class="text-sm font-semibold text-slate-900 dark:text-white">Upload</div>

        @if ($errors->any())
          <div class="mt-4 p-3 rounded-lg border border-red-200 bg-red-50 text-sm text-red-700">
            {{ $errors->first() }}
          </div>
        @endif

        <form class="mt-4 space-y-3" method="POST" action="{{ route('admin.media.store') }}" enctype="multipart/form-data">
          @csrf
          <div>
            <label class="text-sm font-medium">File</label>
            <input class="mt-2 block w-full text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-primary/10 file:text-primary hover:file:bg-primary/15 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-200 border border-slate-200 rounded-lg bg-white text-slate-700"
                   type="file" name="file" accept="image/*" required/>
            <div class="text-xs text-slate-500 mt-2">Max 4MB. jpg/png/webp/gif.</div>
          </div>
          <button class="btn-primary w-full py-2 shadow-sm" type="submit">Upload</button>
        </form>
      </div>

      <!-- Folders List -->
      <div class="card overflow-hidden">
        <div class="flex items-center justify-between p-3 border-b border-border-light dark:border-border-dark bg-slate-50/50 dark:bg-slate-800/50">
           <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Library</span>
           <button onclick="document.getElementById('newFolderModal').showModal()" class="btn-xs btn-ghost text-primary" title="New Folder">
             <span class="material-icons-outlined text-sm">create_new_folder</span>
           </button>
        </div>
        <div class="p-2 space-y-1">
           <!-- All Media -->
           <a href="{{ route('admin.media.index', ['q'=>$q]) }}" 
              class="flex items-center justify-between px-3 py-2 rounded-lg text-sm transition {{ ($folderParam === 'all' || !$folderParam) ? 'bg-slate-100 text-slate-900 font-medium' : 'text-slate-600 hover:bg-slate-50 dark:text-slate-300 dark:hover:bg-slate-800' }}">
              <div class="flex items-center gap-2">
                 <span class="material-icons-outlined text-lg opacity-70">photo_library</span>
                 <span>All Media</span>
              </div>
              <span class="text-xs font-medium px-2 py-0.5 rounded-full {{ ($folderParam === 'all') ? 'bg-white text-slate-700 border border-slate-200' : 'bg-slate-100 text-slate-700 dark:bg-slate-700' }}">
                {{ $totalCount }}
              </span>
           </a>

           <!-- Unsorted -->
           <a href="{{ route('admin.media.index', ['folder'=>'none', 'q'=>$q]) }}" 
              class="flex items-center justify-between px-3 py-2 rounded-lg text-sm transition {{ $folderParam === 'none' ? 'bg-slate-100 text-slate-900 font-medium' : 'text-slate-600 hover:bg-slate-50 dark:text-slate-300 dark:hover:bg-slate-800' }}">
              <div class="flex items-center gap-2">
                 <span class="material-icons-outlined text-lg opacity-70">folder_off</span>
                 <span>Unsorted</span>
              </div>
              <span class="text-xs font-medium px-2 py-0.5 rounded-full {{ $folderParam === 'none' ? 'bg-white text-slate-700 border border-slate-200' : 'bg-slate-100 text-slate-700 dark:bg-slate-700' }}">
                {{ $unsortedCount }}
              </span>
           </a>
        </div>
        
        <div class="flex items-center justify-between p-3 border-t border-b border-border-light dark:border-border-dark bg-slate-50/50 dark:bg-slate-800/50">
           <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Folders</span>
        </div>
        <div class="p-2 space-y-1 max-h-[300px] overflow-y-auto custom-scrollbar">
           @foreach($folders as $folder)
             <div class="group flex items-center justify-between px-3 py-2 rounded-lg text-sm transition {{ $folderParam == $folder->id ? 'bg-slate-100 text-slate-900 font-medium' : 'text-slate-600 hover:bg-slate-50 dark:text-slate-300 dark:hover:bg-slate-800' }}">
                <a href="{{ route('admin.media.index', ['folder'=>$folder->id, 'q'=>$q]) }}" class="flex items-center gap-2 flex-grow truncate">
                   <span class="material-icons-outlined text-lg opacity-70 {{ $folderParam == $folder->id ? 'text-slate-700' : 'text-amber-400' }}">folder</span>
                   <span class="truncate">{{ $folder->name }}</span>
                </a>
                <div class="flex items-center gap-1">
                  <span class="text-xs font-medium px-2 py-0.5 rounded-full {{ $folderParam == $folder->id ? 'bg-white text-slate-700 border border-slate-200' : 'bg-slate-100 text-slate-700 dark:bg-slate-700' }}">
                    {{ $folder->current_count }}
                  </span>
                  <!-- Edit/Delete Actions -->
                  <div class="hidden group-hover:flex">
                    <button type="button" onclick="editFolder('{{ route('admin.media.folders.update', $folder) }}', '{{ addslashes($folder->name) }}')" class="p-1 hover:text-primary"><span class="material-icons-outlined text-sm">edit</span></button>
                    <form action="{{ route('admin.media.folders.destroy', $folder) }}" method="POST" class="inline" onsubmit="return confirm('Delete folder? (Media will become unsorted)');">
                      @csrf @method('DELETE')
                      <button type="submit" class="p-1 hover:text-red-600"><span class="material-icons-outlined text-sm">delete</span></button>
                    </form>
                  </div>
                </div>
             </div>
           @endforeach
           @if($folders->isEmpty())
             <div class="px-3 py-4 text-center text-xs text-slate-600 italic">No folders yet.</div>
           @endif
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="lg:col-span-3 space-y-4">
      <!-- Search & Toolbar -->
      <div class="card p-4">
        <form class="flex flex-col sm:flex-row gap-3 items-center justify-between" method="GET" action="{{ route('admin.media.index') }}">
          @if($folderParam && $folderParam !== 'all')
             <input type="hidden" name="folder" value="{{ $folderParam }}">
          @endif
          <div class="relative w-full sm:w-80">
            <input class="input pr-10 focus:outline-none focus:ring-0 focus-visible:ring-2 focus-visible:ring-slate-200 focus:border-slate-300" name="q" value="{{ $q }}" placeholder="Search current view..." />
            <span class="material-icons-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400" aria-hidden="true">search</span>
          </div>
          <div class="flex items-center gap-2 w-full sm:w-auto">
            <button class="btn bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm w-full sm:w-auto focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-200 focus:border-slate-300" type="submit">Search</button>
            @if($q || ($folderParam && $folderParam !== 'all'))
               <a class="px-2 py-1 rounded-md text-sm text-slate-600 hover:text-slate-900 hover:underline dark:text-slate-400 dark:hover:text-slate-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-200 transition-colors" href="{{ route('admin.media.index') }}">Clear</a>
            @endif
          </div>
        </form>
      </div>

      <!-- Grid -->
      @if($items->count() === 0)
        <div class="card p-10 text-center">
          <div class="mx-auto h-12 w-12 rounded-2xl bg-primary/10 flex items-center justify-center">
            <span class="material-icons-outlined text-primary" aria-hidden="true">photo_library</span>
          </div>
          <h2 class="mt-4 text-lg font-semibold text-slate-900 dark:text-white">No media found</h2>
          <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
             @if($q) No matches for "{{ $q }}". @else No media in this folder. @endif
          </p>
        </div>
      @else
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
          @foreach($items as $m)
            <div class="group relative card p-0 overflow-hidden hover:shadow-md hover:bg-slate-50 transition">
              <div class="aspect-[4/3] bg-slate-100 dark:bg-slate-800 hover:opacity-90 transition relative">
                <a href="{{ route('admin.media.show', $m) }}" class="block w-full h-full">
                   <img src="{{ $m->url() }}" alt="{{ $m->alt_text }}" class="h-full w-full object-cover"/>
                </a>
              </div>
              <div class="p-3">
                <div class="text-xs font-medium text-slate-700 dark:text-slate-200 truncate">
                  <a href="{{ route('admin.media.show', $m) }}" class="hover:text-primary hover:underline" title="{{ $m->original_name }}">{{ $m->original_name }}</a>
                </div>
                <div class="flex items-center justify-between mt-1">
                   <div class="text-[10px] text-slate-500 truncate">{{ number_format($m->size/1024, 0) }} KB</div>
                   @if($m->folder)
                     <span class="text-[10px] px-1.5 py-0.5 rounded bg-amber-50 text-amber-700 border border-amber-200 truncate max-w-[60px]">{{ $m->folder->name }}</span>
                   @endif
                </div>
              </div>
            </div>
          @endforeach
        </div>

        <div class="mt-4">
           {{ $items->links() }}
        </div>
      @endif
    </div>
  </div>

  <!-- New Folder Modal -->
  <dialog id="newFolderModal" class="rounded-xl shadow-xl p-0 w-full max-w-sm backdrop:bg-slate-900/50">
    <div class="p-6">
       <h3 class="text-lg font-semibold mb-4">New Folder</h3>
       <form method="POST" action="{{ route('admin.media.folders.store') }}">
         @csrf
         <div class="mb-4">
           <label class="block text-sm font-medium mb-1">Name</label>
           <input name="name" class="input w-full focus:ring-slate-200 focus:border-slate-300 focus:outline-none focus-visible:ring-slate-200" placeholder="e.g. Blog Images" required maxlength="50">
         </div>
         <div class="flex justify-end gap-2">
           <button type="button" onclick="this.closest('dialog').close()" class="btn-ghost">Cancel</button>
           <button type="button" onclick="this.closest('form').submit()" class="btn-primary">Create</button>
         </div>
       </form>
    </div>
  </dialog>

  <!-- Edit Folder Modal (Reused Logic via JS) -->
  <dialog id="editFolderModal" class="rounded-xl shadow-xl p-0 w-full max-w-sm backdrop:bg-slate-900/50">
     <div class="p-6">
       <h3 class="text-lg font-semibold mb-4">Rename Folder</h3>
       <form method="POST" id="editFolderForm" action="#" data-route="{{ route('admin.media.folders.update', ':id') }}">
         @csrf @method('PUT')
         <div class="mb-4">
           <label class="block text-sm font-medium mb-1">Name</label>
           <input name="name" id="editFolderName" class="input w-full focus:ring-slate-200 focus:border-slate-300 focus:outline-none focus-visible:ring-slate-200" required maxlength="50">
         </div>
         <div class="flex justify-end gap-2">
           <button type="button" onclick="this.closest('dialog').close()" class="btn-ghost">Cancel</button>
           <button type="button" onclick="this.closest('form').submit()" class="btn-primary">Save</button>
         </div>
       </form>
     </div>
  </dialog>

  <script>
    function editFolder(url, name) {
      const modal = document.getElementById('editFolderModal');
      const form = document.getElementById('editFolderForm');
      const input = document.getElementById('editFolderName');
      
      form.action = url;
      input.value = name;
      modal.showModal();
    }
  </script>
</x-admin.layout>

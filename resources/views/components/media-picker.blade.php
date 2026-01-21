@props(['mediaFolders' => collect(), 'allMedia' => collect()])

{{-- Media Picker Modal Component --}}
<dialog id="mediaPickerModal" class="rounded-xl shadow-2xl p-0 w-full max-w-5xl backdrop:bg-slate-900/50">
  <div class="bg-surface-light dark:bg-surface-dark">
    {{-- Header --}}
    <div class="flex items-center justify-between p-6 border-b border-border-light dark:border-border-dark">
      <div class="flex items-center gap-3">
        <span class="material-icons-outlined text-2xl text-primary">photo_library</span>
        <h2 class="text-xl font-semibold text-text-strong dark:text-white">Media Library</h2>
      </div>
      <button type="button" onclick="closeMediaPicker()" class="btn-ghost px-2 py-2" aria-label="Close">
        <span class="material-icons-outlined text-[20px]">close</span>
      </button>
    </div>

    {{-- Toolbar --}}
    <div class="px-6 py-4 bg-slate-50/70 dark:bg-slate-800/30 border-b border-border-light dark:border-border-dark">
      <div class="flex flex-col sm:flex-row gap-3 sm:items-center">
        {{-- Search --}}
        <div class="relative flex-1">
          <input 
            type="text" 
            id="mediaPickerSearch" 
            class="input pr-10" 
            placeholder="Search media..." 
            onkeyup="filterMediaPicker()"
          />
          <span class="material-icons-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
        </div>

        {{-- Folder Filter --}}
        <div class="relative w-full sm:w-48">
          <select id="mediaPickerFolder" class="select" onchange="filterMediaPicker()">
            <option value="all">All Media</option>
            <option value="none">Unsorted</option>
            @foreach($mediaFolders ?? [] as $folder)
              <option value="{{ $folder->id }}">{{ $folder->name }}</option>
            @endforeach
          </select>
          <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-500">
            <span class="material-icons-outlined text-sm">expand_more</span>
          </div>
        </div>
      </div>
    </div>

    {{-- Media Grid --}}
    <div class="p-6 max-h-[500px] overflow-y-auto" id="mediaPickerGrid">
      <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
        @foreach($allMedia ?? [] as $media)
          <div 
            class="media-picker-item group relative cursor-pointer rounded-lg border-2 border-border-light dark:border-border-dark hover:border-primary transition-all"
            data-id="{{ $media->id }}"
            data-url="{{ $media->url() }}"
            data-alt="{{ $media->alt_text ?? '' }}"
            data-name="{{ $media->original_name }}"
            data-folder="{{ $media->folder_id ?? 'none' }}"
            data-width="{{ $media->width }}"
            data-height="{{ $media->height }}"
            onclick="selectMediaItem(this)"
          >
            {{-- Image Preview --}}
            <div class="aspect-square bg-slate-100 dark:bg-slate-800 rounded-t-lg overflow-hidden">
              @if(str_starts_with($media->mime, 'image/'))
                <img 
                  src="{{ $media->url() }}" 
                  alt="{{ $media->alt_text }}" 
                  class="w-full h-full object-cover group-hover:scale-105 transition-transform"
                />
              @else
                <div class="w-full h-full flex items-center justify-center">
                  <span class="material-icons-outlined text-4xl text-slate-400">insert_drive_file</span>
                </div>
              @endif
            </div>

            {{-- Info --}}
            <div class="p-2 bg-white dark:bg-slate-900 rounded-b-lg">
              <div class="text-xs font-medium text-text-strong dark:text-white truncate">
                {{ $media->original_name }}
              </div>
              <div class="text-xs text-text-muted dark:text-slate-400">
                {{ number_format($media->size / 1024, 1) }} KB
              </div>
            </div>

            {{-- Selected Indicator --}}
            <div class="media-selected-indicator hidden absolute top-2 right-2 bg-primary text-white rounded-full p-1">
              <span class="material-icons-outlined text-sm">check</span>
            </div>
          </div>
        @endforeach
      </div>

      {{-- Empty State --}}
      @if(($allMedia ?? collect())->isEmpty())
        <div class="text-center py-12">
          <div class="mx-auto h-16 w-16 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-4">
            <span class="material-icons-outlined text-3xl text-slate-400">photo_library</span>
          </div>
          <p class="text-text-muted dark:text-slate-400">No media found</p>
        </div>
      @endif
    </div>

    {{-- Footer --}}
    <div class="flex flex-col sm:flex-row items-center justify-between p-6 border-t border-border-light dark:border-border-dark bg-slate-50/50 dark:bg-slate-800/30 gap-4">
      <div class="flex items-center gap-4 w-full sm:w-auto">
        <div class="text-sm text-text-muted dark:text-slate-400 whitespace-nowrap">
          <span id="mediaPickerCount">{{ ($allMedia ?? collect())->count() }}</span> items
        </div>

        {{-- Resize Options (Hidden by default) --}}
        <div id="mediaPickerResize" class="hidden flex items-center gap-2 border-l border-border-light dark:border-border-dark pl-4">
          <div class="flex items-center gap-2">
            <span class="text-xs text-text-muted dark:text-slate-400">W:</span>
            <input type="number" id="imgWidth" class="input h-8 w-20 text-xs px-2" placeholder="px" oninput="calculateHeight()">
          </div>
          <div class="flex items-center gap-2">
            <span class="text-xs text-text-muted dark:text-slate-400">H:</span>
            <input type="number" id="imgHeight" class="input h-8 w-20 text-xs px-2" placeholder="px" oninput="calculateWidth()">
          </div>
          <button type="button" class="btn-ghost h-8 w-8 p-0 flex items-center justify-center" onclick="resetDimensions()" title="Reset to original">
            <span class="material-icons-outlined text-sm">restart_alt</span>
          </button>
        </div>
      </div>

      <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
        <button type="button" onclick="closeMediaPicker()" class="btn-ghost">Cancel</button>
        <button type="button" id="mediaPickerSelectBtn" onclick="confirmMediaSelection()" class="btn-primary" disabled>
          <span class="material-icons-outlined text-[18px]">check</span>
          Select
        </button>
      </div>
    </div>
  </div>
</dialog>

{{-- JavaScript --}}
<script>
// Global variables
let mediaPickerCallback = null;
let selectedMediaItem = null;
let originalRatio = 0;

// Open media picker with callback
function openMediaPicker(callback) {
  mediaPickerCallback = callback;
  selectedMediaItem = null;
  
  // Reset filters
  document.getElementById('mediaPickerSearch').value = '';
  document.getElementById('mediaPickerFolder').value = 'all';
  
  // Reset resize inputs
  document.getElementById('mediaPickerResize').classList.add('hidden');
  document.getElementById('imgWidth').value = '';
  document.getElementById('imgHeight').value = '';
  
  // Clear selection
  document.querySelectorAll('.media-picker-item').forEach(item => {
    item.classList.remove('border-primary', 'bg-primary/5');
    item.querySelector('.media-selected-indicator').classList.add('hidden');
  });
  
  // Disable select button
  document.getElementById('mediaPickerSelectBtn').disabled = true;
  
  // Show all items
  filterMediaPicker();
  
  // Open modal
  document.getElementById('mediaPickerModal').showModal();
}

// Close media picker
function closeMediaPicker() {
  document.getElementById('mediaPickerModal').close();
  mediaPickerCallback = null;
  selectedMediaItem = null;
}

// Select media item
function selectMediaItem(element) {
  // Clear previous selection
  document.querySelectorAll('.media-picker-item').forEach(item => {
    item.classList.remove('border-primary', 'bg-primary/5');
    item.querySelector('.media-selected-indicator').classList.add('hidden');
  });
  
  // Mark as selected
  element.classList.add('border-primary', 'bg-primary/5');
  element.querySelector('.media-selected-indicator').classList.remove('hidden');
  
  // Store selected item data
  const originalWidth = parseInt(element.dataset.width) || 0;
  const originalHeight = parseInt(element.dataset.height) || 0;
  
  selectedMediaItem = {
    id: element.dataset.id,
    url: element.dataset.url,
    alt: element.dataset.alt,
    name: element.dataset.name,
    originalWidth: originalWidth,
    originalHeight: originalHeight
  };

  // Show resize options if it's an image
  if (originalWidth > 0 && originalHeight > 0) {
    document.getElementById('mediaPickerResize').classList.remove('hidden');
    document.getElementById('imgWidth').value = originalWidth;
    document.getElementById('imgHeight').value = originalHeight;
    originalRatio = originalWidth / originalHeight;
  } else {
    document.getElementById('mediaPickerResize').classList.add('hidden');
  }
  
  // Enable select button
  document.getElementById('mediaPickerSelectBtn').disabled = false;
}

// Calculate Height based on Width
function calculateHeight() {
  const w = parseInt(document.getElementById('imgWidth').value);
  if (w && originalRatio > 0) {
    document.getElementById('imgHeight').value = Math.round(w / originalRatio);
  }
}

// Calculate Width based on Height
function calculateWidth() {
  const h = parseInt(document.getElementById('imgHeight').value);
  if (h && originalRatio > 0) {
    document.getElementById('imgWidth').value = Math.round(h * originalRatio);
  }
}

// Reset Dimensions
function resetDimensions() {
  if (selectedMediaItem && selectedMediaItem.originalWidth) {
    document.getElementById('imgWidth').value = selectedMediaItem.originalWidth;
    document.getElementById('imgHeight').value = selectedMediaItem.originalHeight;
  }
}

// Confirm selection
function confirmMediaSelection() {
  if (selectedMediaItem && mediaPickerCallback) {
    const width = document.getElementById('imgWidth').value;
    const height = document.getElementById('imgHeight').value;
    
    mediaPickerCallback(
      selectedMediaItem.id, 
      selectedMediaItem.url, 
      selectedMediaItem.alt,
      width,
      height
    );
    closeMediaPicker();
  }
}

// Filter media items
function filterMediaPicker() {
  const searchTerm = document.getElementById('mediaPickerSearch').value.toLowerCase();
  const folderFilter = document.getElementById('mediaPickerFolder').value;
  
  let visibleCount = 0;
  
  document.querySelectorAll('.media-picker-item').forEach(item => {
    const name = item.dataset.name.toLowerCase();
    const folder = item.dataset.folder;
    
    // Check search match
    const matchesSearch = name.includes(searchTerm);
    
    // Check folder match
    let matchesFolder = true;
    if (folderFilter === 'all') {
      matchesFolder = true;
    } else if (folderFilter === 'none') {
      matchesFolder = folder === 'none';
    } else {
      matchesFolder = folder === folderFilter;
    }
    
    // Show/hide item
    if (matchesSearch && matchesFolder) {
      item.style.display = 'block';
      visibleCount++;
    } else {
      item.style.display = 'none';
    }
  });
  
  // Update count
  document.getElementById('mediaPickerCount').textContent = visibleCount;
}

// Close on Escape key
document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape') {
    const modal = document.getElementById('mediaPickerModal');
    if (modal && modal.open) {
      closeMediaPicker();
    }
  }
});
</script>

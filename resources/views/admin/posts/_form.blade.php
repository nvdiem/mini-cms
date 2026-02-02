@php
  $isEdit = isset($post) && $post->exists;
  $selectedCats = collect(old('category_ids', $post->categories?->pluck('id')->all() ?? []))->map(fn($v) => (int)$v)->all();
  $selectedTags = collect(old('tag_ids', $post->tags?->pluck('id')->all() ?? []))->map(fn($v) => (int)$v)->all();
@endphp

<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
  {{-- LEFT COLUMN: Main Content (8/12 width on desktop) --}}
  <div class="lg:col-span-8 space-y-6">
    {{-- Title --}}
    <div class="card p-6">
      <div>
        <label class="text-sm font-medium text-slate-700 dark:text-slate-200">Title</label>
        <input class="input mt-1 text-lg" name="title" value="{{ old('title', $post->title) }}" placeholder="Enter post title..." required autofocus />
        @error('title') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
      </div>
    </div>

    {{-- Slug --}}
    <div class="card p-6">
      <div>
        <label class="text-sm font-medium text-slate-700 dark:text-slate-200">Permalink</label>
        <div class="flex items-center gap-2 mt-1">
          <span class="text-sm text-slate-500 dark:text-slate-400">{{ url('/posts/') }}/</span>
          <input class="input flex-1" name="slug" value="{{ old('slug', $post->slug) }}" placeholder="my-post-title" />
        </div>
        @error('slug') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
        <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">Leave empty to auto-generate from title.</div>
      </div>
    </div>

    {{-- Content Editor --}}
    <div class="card p-6">
      <div>
        <label class="text-sm font-medium text-slate-700 dark:text-slate-200">Content</label>
        <textarea id="content" class="input mt-2 min-h-[400px] font-mono text-sm" name="content" placeholder="Write your content here...">{{ old('content', $post->content) }}</textarea>
        @error('content') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
      </div>
    </div>

    {{-- Excerpt --}}
    <div class="card p-6">
      <div>
        <label class="text-sm font-medium text-slate-700 dark:text-slate-200">Excerpt</label>
        <textarea class="input mt-2 min-h-[100px]" name="excerpt" placeholder="Optional. Write a short summary...">{{ old('excerpt', $post->excerpt) }}</textarea>
        @error('excerpt') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror>
        <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">Excerpts are optional hand-crafted summaries of your content.</div>
      </div>
    </div>

    {{-- SEO Settings (Collapsible) --}}
    <details class="card overflow-hidden group">
      <summary class="flex cursor-pointer items-center justify-between p-6 font-medium text-slate-900 dark:text-white hover:bg-slate-50 dark:hover:bg-slate-800/50 transition">
        <div class="flex items-center gap-2">
          <span class="material-icons-outlined text-lg text-primary">search</span>
          <span>SEO Settings</span>
        </div>
        <span class="material-icons-outlined text-slate-500 transition-transform group-open:rotate-180" aria-hidden="true">expand_more</span>
      </summary>
      <div class="border-t border-border-light dark:border-border-dark p-6 space-y-4 bg-slate-50/50 dark:bg-slate-800/30">
        <div>
          <label class="text-sm font-medium text-slate-700 dark:text-slate-200">Meta Title</label>
          <input class="input mt-1" name="meta_title" value="{{ old('meta_title', $post->meta_title ?? '') }}" placeholder="Custom title for search engines" />
          <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">Leave empty to use post title.</div>
        </div>
        <div>
          <label class="text-sm font-medium text-slate-700 dark:text-slate-200">Meta Description</label>
          <textarea class="input mt-1 min-h-[80px]" name="meta_description" placeholder="Custom description for search engines">{{ old('meta_description', $post->meta_description ?? '') }}</textarea>
          <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">Leave empty to use excerpt or content summary.</div>
        </div>
        <div>
          <label class="text-sm font-medium text-slate-700 dark:text-slate-200">Meta Keywords</label>
          <input class="input mt-1" name="meta_keywords" value="{{ old('meta_keywords', $post->meta_keywords ?? '') }}" placeholder="keyword1, keyword2, keyword3" />
        </div>
      </div>
    </details>
  </div>

  {{-- RIGHT SIDEBAR: Metadata (4/12 width on desktop) --}}
  <div class="lg:col-span-4 space-y-6">
    {{-- Publish Box --}}
    <div class="card p-6">
      <div class="flex items-center gap-2 mb-4">
        <span class="material-icons-outlined text-lg text-primary">publish</span>
        <div class="text-sm font-semibold text-slate-900 dark:text-white">Publish</div>
      </div>
      
      <div class="space-y-4">
        <div>
          <label class="text-sm font-medium text-slate-700 dark:text-slate-200">Status</label>
          <div class="relative mt-1">
            <select class="select" name="status">
              @foreach(['draft'=>'Draft','review'=>'Review','published'=>'Published'] as $k=>$v)
                <option value="{{ $k }}" {{ old('status', $post->status) === $k ? 'selected' : '' }}>{{ $v }}</option>
              @endforeach
            </select>
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-500">
              <span class="material-icons-outlined text-sm" aria-hidden="true">expand_more</span>
            </div>
          </div>
          @error('status') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
        </div>

        <div>
          <label class="text-sm font-medium text-slate-700 dark:text-slate-200">Publish Date</label>
          <input type="datetime-local" class="input mt-1" name="published_at"
                 value="{{ old('published_at', optional($post->published_at)->format('Y-m-d\TH:i')) }}" />
          @error('published_at') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
        </div>

        <div class="pt-3 border-t border-border-light dark:border-border-dark flex items-center gap-2">
          <a class="btn-ghost flex-1 justify-center" href="{{ route('admin.posts.index') }}">Cancel</a>
          <button class="btn-primary flex-1 justify-center" type="submit">
            <span class="material-icons-outlined text-[18px]" aria-hidden="true">{{ $isEdit ? 'save' : 'add' }}</span>
            {{ $isEdit ? 'Update' : 'Publish' }}
          </button>
        </div>
      </div>
    </div>

    {{-- Categories --}}
    <div class="card p-6">
      <div class="flex items-center justify-between gap-3 mb-3">
        <div class="flex items-center gap-2">
          <span class="material-icons-outlined text-lg text-amber-500">folder</span>
          <div class="text-sm font-semibold text-slate-900 dark:text-white">Categories</div>
        </div>
        <a class="text-xs text-primary hover:underline" href="{{ route('admin.categories.index') }}" target="_blank">+ Add new</a>
      </div>

      @if(($categories ?? collect())->count() === 0)
        <p class="text-sm text-slate-500 dark:text-slate-400">No categories yet.</p>
      @else
        <div class="space-y-2 max-h-48 overflow-auto pr-1 custom-scrollbar">
          @foreach($categories as $cat)
            <label class="flex items-center gap-2 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 p-1.5 rounded cursor-pointer transition">
              <input class="chk" type="checkbox" name="category_ids[]" value="{{ $cat->id }}" {{ in_array($cat->id, $selectedCats, true) ? 'checked' : '' }}/>
              <span>{{ $cat->name }}</span>
            </label>
          @endforeach
        </div>
      @endif

      @error('category_ids') <div class="text-xs text-red-600 mt-2">{{ $message }}</div> @enderror
    </div>

    {{-- Tags --}}
    <div class="card p-6">
      <div class="flex items-center justify-between gap-3 mb-3">
        <div class="flex items-center gap-2">
          <span class="material-icons-outlined text-lg text-blue-500">label</span>
          <div class="text-sm font-semibold text-slate-900 dark:text-white">Tags</div>
        </div>
        <a class="text-xs text-primary hover:underline" href="{{ route('admin.tags.index') }}" target="_blank">+ Add new</a>
      </div>

      @if(($tags ?? collect())->count() === 0)
        <p class="text-sm text-slate-500 dark:text-slate-400">No tags yet.</p>
      @else
        <div class="space-y-2 max-h-48 overflow-auto pr-1 custom-scrollbar">
          @foreach($tags as $tag)
            <label class="flex items-center gap-2 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 p-1.5 rounded cursor-pointer transition">
              <input class="chk" type="checkbox" name="tag_ids[]" value="{{ $tag->id }}" {{ in_array($tag->id, $selectedTags, true) ? 'checked' : '' }}/>
              <span>{{ $tag->name }}</span>
            </label>
          @endforeach
        </div>
      @endif

      @error('tag_ids') <div class="text-xs text-red-600 mt-2">{{ $message }}</div> @enderror
    </div>

    {{-- Featured Image --}}
    <div class="card p-6">
      <div class="flex items-center justify-between gap-3 mb-3">
        <div class="flex items-center gap-2">
          <span class="material-icons-outlined text-lg text-green-500">image</span>
          <div class="text-sm font-semibold text-slate-900 dark:text-white">Featured Image</div>
        </div>
      </div>

      <input type="hidden" name="featured_image_id" id="featured_image_id" value="{{ old('featured_image_id', $post->featured_image_id) }}">

      <div id="featured_image_preview" class="mb-3">
        @if($post->featuredImage)
          <div class="relative group">
            <img src="{{ $post->featuredImage->url() }}" alt="{{ $post->featuredImage->alt_text }}" class="w-full h-auto rounded-lg border border-border-light dark:border-border-dark"/>
            <button type="button" onclick="removeFeaturedImage()" class="absolute top-2 right-2 bg-red-500 text-white p-1 rounded-full opacity-0 group-hover:opacity-100 transition-opacity" title="Remove image">
              <span class="material-icons-outlined text-sm">close</span>
            </button>
          </div>
        @else
          <div class="aspect-video rounded-lg border-2 border-dashed border-border-light dark:border-border-dark bg-slate-50 dark:bg-slate-800 flex items-center justify-center">
            <span class="material-icons-outlined text-4xl text-slate-300 dark:text-slate-600">image</span>
          </div>
        @endif
      </div>

      <button type="button" onclick="chooseFeaturedImage()" class="btn-secondary w-full justify-center">
        <span class="material-icons-outlined text-[18px]">add_photo_alternate</span>
        <span class="ml-2">Choose Image</span>
      </button>

      @error('featured_image_id') <div class="text-xs text-red-600 mt-2">{{ $message }}</div> @enderror
    </div>
  </div>
</div>

{{-- ================= TINYMCE & MEDIA PICKER ================= --}}
<x-media-picker :mediaFolders="$mediaFolders ?? collect()" :allMedia="$allMedia ?? collect()" />

<script src="{{ asset('js/tinymce/tinymce.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  tinymce.init({
    selector: '#content',
    license_key: 'gpl',
    height: 600,
    menubar: true,
    plugins: 'lists link code table fullscreen image',
    toolbar: 'undo redo | blocks | bold italic | bullist numlist | link table | media | code fullscreen',
    relative_urls: false,
    remove_script_host: false,

    // Custom media button
    setup: function(editor) {
      editor.ui.registry.addButton('media', {
        text: 'Media Library',
        icon: 'image',
        tooltip: 'Insert from Media Library',
        onAction: function() {
          openMediaPicker(function(id, url, alt, width, height) {
            let style = 'max-width: 100%; height: auto;';
            let attrs = `src="${url}" alt="${alt}"`;
            
            if (width) attrs += ` width="${width}"`;
            if (height) attrs += ` height="${height}"`;
            
            editor.insertContent(`<img ${attrs} style="${style}" />`);
          });
        }
      });
    }
  });
});

// Featured Image Functions
function chooseFeaturedImage() {
  openMediaPicker(function(id, url, alt) {
    // Update hidden input
    document.getElementById('featured_image_id').value = id;
    
    // Update preview
    const previewContainer = document.getElementById('featured_image_preview');
    previewContainer.innerHTML = `
      <div class="relative group">
        <img src="${url}" alt="${alt}" class="w-full h-auto rounded-lg border border-border-light dark:border-border-dark"/>
        <button type="button" onclick="removeFeaturedImage()" class="absolute top-2 right-2 bg-red-500 text-white p-1 rounded-full opacity-0 group-hover:opacity-100 transition-opacity" title="Remove image">
          <span class="material-icons-outlined text-sm">close</span>
        </button>
      </div>
    `;
  });
}

function removeFeaturedImage() {
  document.getElementById('featured_image_id').value = '';
  document.getElementById('featured_image_preview').innerHTML = `
    <div class="aspect-video rounded-lg border-2 border-dashed border-border-light dark:border-border-dark bg-slate-50 dark:bg-slate-800 flex items-center justify-center">
      <span class="material-icons-outlined text-4xl text-slate-300 dark:text-slate-600">image</span>
    </div>
  `;
}
</script>

@php
  $isEdit = isset($page) && $page->exists;
@endphp

<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
  {{-- LEFT COLUMN: Main Content (8/12 width on desktop) --}}
  <div class="lg:col-span-8 space-y-6">
    {{-- Title --}}
    <div class="card p-6">
      <div>
        <label class="text-sm font-medium text-text dark:text-slate-200">Title</label>
        <input class="input mt-1 text-lg" name="title" value="{{ old('title', $page->title) }}" placeholder="Enter page title..." required autofocus />
        @error('title') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
      </div>
    </div>

    {{-- Slug --}}
    <div class="card p-6">
      <div>
        <label class="text-sm font-medium text-text dark:text-slate-200">Permalink</label>
        <div class="flex items-center gap-2 mt-1">
          <span class="text-sm text-text-muted dark:text-slate-400">{{ url('/p/') }}/</span>
          <input class="input flex-1" name="slug" value="{{ old('slug', $page->slug) }}" placeholder="my-page-title" />
        </div>
        @error('slug') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
        <div class="text-xs text-text-muted dark:text-slate-400 mt-1">Leave empty to auto-generate from title.</div>
      </div>
    </div>

    {{-- Content Editor --}}
    <div class="card p-6">
      <div>
        <label class="text-sm font-medium text-text dark:text-slate-200">Content</label>
        <textarea class="input mt-2 min-h-[400px] font-mono text-sm" name="content" placeholder="Write your content here...">{{ old('content', $page->content) }}</textarea>
        @error('content') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
      </div>
    </div>

    {{-- Excerpt --}}
    <div class="card p-6">
      <div>
        <label class="text-sm font-medium text-text dark:text-slate-200">Excerpt</label>
        <textarea class="input mt-2 min-h-[100px]" name="excerpt" placeholder="Optional. Write a short summary...">{{ old('excerpt', $page->excerpt) }}</textarea>
        @error('excerpt') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
        <div class="text-xs text-text-muted dark:text-slate-400 mt-1">Excerpts are optional hand-crafted summaries of your content.</div>
      </div>
    </div>

    {{-- SEO Settings (Collapsible) --}}
    <details class="card overflow-hidden group">
      <summary class="flex cursor-pointer items-center justify-between p-6 font-medium text-text-strong dark:text-white hover:bg-slate-50 dark:hover:bg-slate-800/50 transition">
        <div class="flex items-center gap-2">
          <span class="material-icons-outlined text-lg text-primary">search</span>
          <span>SEO Settings</span>
        </div>
        <span class="material-icons-outlined text-slate-500 transition-transform group-open:rotate-180" aria-hidden="true">expand_more</span>
      </summary>
      <div class="border-t border-border-light dark:border-border-dark p-6 space-y-4 bg-slate-50/50 dark:bg-slate-800/30">
        <div>
          <label class="text-sm font-medium text-text dark:text-slate-200">Meta Title</label>
          <input class="input mt-1" name="meta_title" value="{{ old('meta_title', $page->meta_title ?? '') }}" placeholder="Custom title for search engines" />
          <div class="text-xs text-text-muted dark:text-slate-400 mt-1">Leave empty to use page title.</div>
        </div>
        <div>
          <label class="text-sm font-medium text-text dark:text-slate-200">Meta Description</label>
          <textarea class="input mt-1 min-h-[80px]" name="meta_description" placeholder="Custom description for search engines">{{ old('meta_description', $page->meta_description ?? '') }}</textarea>
          <div class="text-xs text-text-muted dark:text-slate-400 mt-1">Leave empty to use excerpt or content summary.</div>
        </div>
        <div>
          <label class="text-sm font-medium text-text dark:text-slate-200">Meta Keywords</label>
          <input class="input mt-1" name="meta_keywords" value="{{ old('meta_keywords', $page->meta_keywords ?? '') }}" placeholder="keyword1, keyword2, keyword3" />
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
        <div class="text-sm font-semibold text-text-strong dark:text-white">Publish</div>
      </div>
      
      <div class="space-y-4">
        <div>
          <label class="text-sm font-medium text-text dark:text-slate-200">Status</label>
          <div class="relative mt-1">
            <select class="select" name="status">
              @foreach(['draft'=>'Draft','review'=>'Review','published'=>'Published'] as $k=>$v)
                <option value="{{ $k }}" {{ old('status', $page->status) === $k ? 'selected' : '' }}>{{ $v }}</option>
              @endforeach
            </select>
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-500">
              <span class="material-icons-outlined text-sm" aria-hidden="true">expand_more</span>
            </div>
          </div>
          @error('status') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
        </div>

        <div>
          <label class="text-sm font-medium text-text dark:text-slate-200">Publish Date</label>
          <input type="datetime-local" class="input mt-1" name="published_at"
                 value="{{ old('published_at', optional($page->published_at)->format('Y-m-d\TH:i')) }}" />
          @error('published_at') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
        </div>

        <div class="pt-3 border-t border-border-light dark:border-border-dark flex items-center gap-2">
          <a class="btn-ghost flex-1 justify-center" href="{{ route('admin.pages.index') }}">Cancel</a>
          <button class="btn-primary flex-1 justify-center" type="submit">
            <span class="material-icons-outlined text-[18px]" aria-hidden="true">{{ $isEdit ? 'save' : 'add' }}</span>
            {{ $isEdit ? 'Update' : 'Publish' }}
          </button>
        </div>
      </div>
    </div>

    {{-- Featured Image --}}
    <div class="card p-6">
      <div class="flex items-center justify-between gap-3 mb-3">
        <div class="flex items-center gap-2">
          <span class="material-icons-outlined text-lg text-green-500">image</span>
          <div class="text-sm font-semibold text-text-strong dark:text-white">Featured Image</div>
        </div>
        <a class="text-xs text-primary hover:underline" href="{{ route('admin.media.index') }}" target="_blank">Library</a>
      </div>

      @php $img = $page->featuredImage ?? null; @endphp
      
      @if($img)
        <div class="mb-3">
          <img src="{{ $img->url() }}" alt="{{ $img->alt_text }}" class="w-full h-auto rounded-lg border border-border-light dark:border-border-dark"/>
        </div>
      @else
        <div class="mb-3 aspect-video rounded-lg border-2 border-dashed border-border-light dark:border-border-dark bg-slate-50 dark:bg-slate-800 flex items-center justify-center">
          <span class="material-icons-outlined text-4xl text-slate-300 dark:text-slate-600">image</span>
        </div>
      @endif

      <div class="relative">
        <select class="select text-sm" name="featured_image_id">
          <option value="">No image</option>
          @foreach(($media ?? collect()) as $m)
            <option value="{{ $m->id }}" {{ (string)old('featured_image_id', $page->featured_image_id) === (string)$m->id ? 'selected' : '' }}>
              #{{ $m->id }} — {{ Str::limit($m->original_name, 30) }}
            </option>
          @endforeach
        </select>
        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-500">
          <span class="material-icons-outlined text-sm" aria-hidden="true">expand_more</span>
        </div>
      </div>
      <div class="text-xs text-text-muted dark:text-slate-400 mt-2">Showing latest 50 media items.</div>

      @error('featured_image_id') <div class="text-xs text-red-600 mt-2">{{ $message }}</div> @enderror
    </div>

    {{-- Page Info Box --}}
    <div class="card p-6 bg-blue-50/50 dark:bg-blue-900/10 border-blue-200 dark:border-blue-800">
      <div class="flex items-start gap-3">
        <span class="material-icons-outlined text-blue-500">info</span>
        <div class="text-xs text-slate-600 dark:text-slate-300">
          <p class="font-medium mb-1">About Pages</p>
          <p>Pages are for static content like "About Us", "Contact", "Privacy Policy", etc. Unlike posts, pages don't have categories or tags.</p>
        </div>
      </div>
    </div>
  </div>
</div>

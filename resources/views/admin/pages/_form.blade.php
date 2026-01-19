@php
  $isEdit = isset($page) && $page->exists;
@endphp

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
  <div class="card lg:col-span-2 p-6">
    <div class="space-y-4">
      <div>
        <label class="text-sm font-medium text-text dark:text-slate-200">Title</label>
        <input class="input mt-1" name="title" value="{{ old('title', $page->title) }}" placeholder="Enter page title..." required />
        @error('title') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
      </div>

      <div>
        <label class="text-sm font-medium text-text dark:text-slate-200">Slug</label>
        <input class="input mt-1" name="slug" value="{{ old('slug', $page->slug) }}" placeholder="my-page-title" />
        @error('slug') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
        <div class="text-xs text-text-muted dark:text-slate-400 mt-1">Leave empty to auto-generate from title.</div>
      </div>

     <div>
        <label class="text-sm font-medium text-text dark:text-slate-200">Content</label>
        <textarea class="input mt-1 min-h-[240px]" name="content" placeholder="Write your content...">{{ old('content', $page->content) }}</textarea>
        @error('content') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
      </div>

      </div>

      <div>
        <label class="text-sm font-medium text-text dark:text-slate-200">Excerpt</label>
        <textarea class="input mt-1 min-h-[96px]" name="excerpt" placeholder="Short summary...">{{ old('excerpt', $page->excerpt) }}</textarea>
        @error('excerpt') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
      </div>

      <details class="group rounded-lg border border-border-light dark:border-border-dark bg-slate-50 dark:bg-slate-800/50">
        <summary class="flex cursor-pointer items-center justify-between p-4 font-medium text-text-strong dark:text-white">
          <span>SEO Settings</span>
          <span class="material-icons-outlined text-slate-500 transition-transform group-open:rotate-180" aria-hidden="true">expand_more</span>
        </summary>
        <div class="border-t border-border-light dark:border-border-dark p-4 space-y-4">
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
  </div>

  <div class="space-y-4">
    <div class="card p-6">
      <div class="text-sm font-semibold text-text-strong dark:text-white">Publish</div>
      <div class="mt-4 space-y-3">
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
          <label class="text-sm font-medium text-text dark:text-slate-200">Publish date</label>
          <input type="datetime-local" class="input mt-1" name="published_at"
                 value="{{ old('published_at', optional($page->published_at)->format('Y-m-d\TH:i')) }}" />
          @error('published_at') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
        </div>

        <div class="pt-2 flex items-center justify-between">
          <a class="btn-ghost" href="{{ route('admin.pages.index') }}">Back</a>
          <button class="btn-primary" type="submit">
            <span class="material-icons-outlined text-[18px]" aria-hidden="true">save</span>
            {{ $isEdit ? 'Save' : 'Create' }}
          </button>
        </div>
      </div>
    </div>

    <div class="card p-6">
      <div class="flex items-center justify-between gap-3">
        <div class="text-sm font-semibold text-text-strong dark:text-white">Featured image</div>
        <a class="text-sm text-primary hover:underline" href="{{ route('admin.media.index') }}">Open library</a>
      </div>

      <div class="mt-3 flex items-start gap-3">
        @php $img = $page->featuredImage ?? null; @endphp
        @if($img)
          <img src="{{ $img->url() }}" alt="" class="h-14 w-20 object-cover rounded-lg border border-border-light dark:border-border-dark"/>
        @else
          <div class="h-14 w-20 rounded-lg border border-border-light dark:border-border-dark bg-slate-100 dark:bg-slate-800"></div>
        @endif

        <div class="flex-1">
          <div class="relative">
            <select class="select" name="featured_image_id">
              <option value="">No image</option>
              @foreach(($media ?? collect()) as $m)
                <option value="{{ $m->id }}" {{ (string)old('featured_image_id', $page->featured_image_id) === (string)$m->id ? 'selected' : '' }}>
                  #{{ $m->id }} — {{ $m->original_name }}
                </option>
              @endforeach
            </select>
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-500">
              <span class="material-icons-outlined text-sm" aria-hidden="true">expand_more</span>
            </div>
          </div>
          <div class="text-xs text-text-muted dark:text-slate-400 mt-1">Showing latest 50 media items.</div>
        </div>
      </div>

      @error('featured_image_id') <div class="text-xs text-red-600 mt-2">{{ $message }}</div> @enderror
    </div>

  </div>
</div>

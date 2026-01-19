@php
  $isEdit = isset($post) && $post->exists;
  $selectedCats = collect(old('category_ids', $post->categories?->pluck('id')->all() ?? []))->map(fn($v) => (int)$v)->all();
@endphp

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
  <div class="card lg:col-span-2 p-6">
    <div class="space-y-4">
      <div>
        <label class="text-sm font-medium text-text dark:text-slate-200">Title</label>
        <input class="input mt-1" name="title" value="{{ old('title', $post->title) }}" placeholder="Enter post title..." required />
        @error('title') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
      </div>

      <div>
        <label class="text-sm font-medium text-text dark:text-slate-200">Slug</label>
        <input class="input mt-1" name="slug" value="{{ old('slug', $post->slug) }}" placeholder="my-post-title" />
        @error('slug') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
        <div class="text-xs text-text-muted dark:text-slate-400 mt-1">Leave empty to auto-generate from title.</div>
      </div>

      <div>
        <label class="text-sm font-medium text-text dark:text-slate-200">Content</label>
        <textarea class="input mt-1 min-h-[240px]" name="content" placeholder="Write your content...">{{ old('content', $post->content) }}</textarea>
        @error('content') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
      </div>

      <div>
        <label class="text-sm font-medium text-text dark:text-slate-200">Excerpt</label>
        <textarea class="input mt-1 min-h-[96px]" name="excerpt" placeholder="Short summary...">{{ old('excerpt', $post->excerpt) }}</textarea>
        @error('excerpt') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
      </div>
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
          <label class="text-sm font-medium text-text dark:text-slate-200">Publish date</label>
          <input type="datetime-local" class="input mt-1" name="published_at"
                 value="{{ old('published_at', optional($post->published_at)->format('Y-m-d\TH:i')) }}" />
          @error('published_at') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
        </div>

        <div class="pt-2 flex items-center justify-between">
          <a class="btn-ghost" href="{{ route('admin.posts.index') }}">Back</a>
          <button class="btn-primary" type="submit">
            <span class="material-icons-outlined text-[18px]" aria-hidden="true">save</span>
            {{ $isEdit ? 'Save' : 'Create' }}
          </button>
        </div>
      </div>
    </div>

    <div class="card p-6">
      <div class="flex items-center justify-between gap-3">
        <div class="text-sm font-semibold text-text-strong dark:text-white">Categories</div>
        <a class="text-sm text-primary hover:underline" href="{{ route('admin.categories.index') }}">Manage</a>
      </div>

      @if(($categories ?? collect())->count() === 0)
        <p class="mt-3 text-sm text-text-muted dark:text-slate-400">No categories yet. Create one first.</p>
      @else
        <div class="mt-3 space-y-2 max-h-56 overflow-auto pr-1">
          @foreach($categories as $cat)
            <label class="flex items-center gap-2 text-sm text-slate-700 dark:text-slate-200">
              <input class="chk" type="checkbox" name="category_ids[]" value="{{ $cat->id }}" {{ in_array($cat->id, $selectedCats, true) ? 'checked' : '' }}/>
              <span>{{ $cat->name }}</span>
            </label>
          @endforeach
        </div>
      @endif

      @error('category_ids') <div class="text-xs text-red-600 mt-2">{{ $message }}</div> @enderror
    </div>

    <div class="card p-6">
      <div class="flex items-center justify-between gap-3">
        <div class="text-sm font-semibold text-text-strong dark:text-white">Featured image</div>
        <a class="text-sm text-primary hover:underline" href="{{ route('admin.media.index') }}">Open library</a>
      </div>

      <div class="mt-3 flex items-start gap-3">
        @php $img = $post->featuredImage ?? null; @endphp
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
                <option value="{{ $m->id }}" {{ (string)old('featured_image_id', $post->featured_image_id) === (string)$m->id ? 'selected' : '' }}>
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

    @if($isEdit)
      <div class="card p-6">
        <div class="text-sm font-semibold text-text-strong dark:text-white">Danger zone</div>
        <div class="mt-4">
          <form method="POST" action="{{ route('admin.posts.destroy', $post) }}">
            @csrf @method('DELETE')
            <button class="btn-danger" type="submit">Move to trash</button>
          </form>
        </div>
      </div>
    @endif
  </div>
</div>

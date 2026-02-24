<x-admin.layout :title="'Add Product · Mini CMS'" :crumb="'Add Product'">
  <div class="mb-6">
    <h1 class="text-2xl font-bold text-text-strong dark:text-white">Add Product</h1>
  </div>

  <form method="POST" action="{{ route('admin.shop.products.store') }}" id="productForm">
    @csrf
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      {{-- Left Column --}}
      <div class="lg:col-span-2 space-y-6">
        {{-- Title --}}
        <div class="card p-6">
          <label class="block text-sm font-medium mb-2">Title <span class="text-red-500">*</span></label>
          <input type="text" name="title" value="{{ old('title') }}" class="input" required>
          @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror

          <label class="block text-sm font-medium mb-2 mt-4">Slug</label>
          <input type="text" name="slug" value="{{ old('slug') }}" class="input" placeholder="auto-generated from title">

          <label class="block text-sm font-medium mb-2 mt-4">Excerpt</label>
          <textarea name="excerpt" rows="2" class="input">{{ old('excerpt') }}</textarea>
        </div>

        {{-- Description (TinyMCE) --}}
        <div class="card p-6">
          <label class="block text-sm font-medium mb-2">Description</label>
          <textarea name="description_html" id="description_html" rows="12" class="input">{{ old('description_html') }}</textarea>
        </div>

        {{-- Options --}}
        <div class="card p-6" id="optionsSection">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-text-strong dark:text-white">Options</h3>
            <button type="button" class="btn-soft text-sm" onclick="addOption()">
              <span class="material-icons-outlined text-[16px]">add</span> Add Option
            </button>
          </div>
          <div id="optionsList" class="space-y-4">
            {{-- JS will populate --}}
          </div>
        </div>

        {{-- Variant Matrix (shown after save with options) --}}
        <div class="card p-6">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-text-strong dark:text-white">Variants</h3>
          </div>
          <p class="text-sm text-text-muted">Save the product first, then generate variants from the Edit page.</p>
        </div>
      </div>

      {{-- Right Column --}}
      <div class="space-y-6">
        {{-- Publish Box --}}
        <div class="card p-6">
          <h3 class="text-sm font-semibold text-text-strong dark:text-white mb-4">Publish</h3>
          <label class="flex items-center gap-2 mb-3">
            <input type="checkbox" name="is_active" value="1" class="chk" {{ old('is_active') ? 'checked' : '' }}>
            <span class="text-sm">Active (visible in shop)</span>
          </label>
          <label class="block text-sm font-medium mb-2">Publish Date</label>
          <input type="datetime-local" name="published_at" value="{{ old('published_at') }}" class="input">
          <button type="submit" class="btn-primary w-full mt-4">
            <span class="material-icons-outlined text-[18px]">save</span> Save Product
          </button>
        </div>

        {{-- Featured Image --}}
        <div class="card p-6">
          <h3 class="text-sm font-semibold text-text-strong dark:text-white mb-4">Featured Image</h3>
          <x-media-picker name="featured_image_id" :value="old('featured_image_id')" :media="$media" />
        </div>

        {{-- SEO --}}
        <div class="card p-6">
          <h3 class="text-sm font-semibold text-text-strong dark:text-white mb-4">SEO</h3>
          <label class="block text-sm font-medium mb-2">SEO Title</label>
          <input type="text" name="seo_title" value="{{ old('seo_title') }}" class="input" maxlength="60">

          <label class="block text-sm font-medium mb-2 mt-3">SEO Description</label>
          <textarea name="seo_description" rows="3" class="input" maxlength="160">{{ old('seo_description') }}</textarea>

          <label class="block text-sm font-medium mb-2 mt-3">Canonical URL</label>
          <input type="url" name="canonical_url" value="{{ old('canonical_url') }}" class="input">

          <label class="flex items-center gap-2 mt-3">
            <input type="checkbox" name="is_noindex" value="1" class="chk" {{ old('is_noindex') ? 'checked' : '' }}>
            <span class="text-sm">No-index</span>
          </label>
        </div>
      </div>
    </div>
  </form>

  <x-slot:scripts>
  <script src="{{ asset('js/tinymce/tinymce.min.js') }}"></script>
  <script>
    // TinyMCE
    tinymce.init({
      selector: '#description_html',
      height: 350,
      menubar: false,
      plugins: 'lists link code table fullscreen image',
      toolbar: 'undo redo | blocks | bold italic | bullist numlist | link table image | code fullscreen',
      content_style: 'body { font-family: Inter, sans-serif; font-size: 14px; }'
    });

    // Options builder
    let optionIndex = 0;
    function addOption(data) {
      data = data || { id: '', label: '', values: [{ id: '', value: '' }] };
      const idx = optionIndex++;
      const valuesHtml = (data.values || []).map((v, vi) => valueRow(idx, vi, v)).join('');

      const html = `
        <div class="border border-border-light dark:border-border-dark rounded-lg p-4" id="option_${idx}">
          <div class="flex items-center gap-3 mb-3">
            <input type="hidden" name="options[${idx}][id]" value="${data.id || ''}">
            <input type="text" name="options[${idx}][label]" value="${data.label || ''}" placeholder="Option name (e.g. Size, Color)" class="input flex-1">
            <button type="button" class="btn-danger px-2 py-1.5" onclick="document.getElementById('option_${idx}').remove()">
              <span class="material-icons-outlined text-[16px]">close</span>
            </button>
          </div>
          <div class="space-y-2" id="option_${idx}_values">${valuesHtml}</div>
          <button type="button" class="btn-ghost text-xs mt-2" onclick="addValue(${idx})">+ Add value</button>
        </div>`;

      document.getElementById('optionsList').insertAdjacentHTML('beforeend', html);
    }

    let valueIndex = 100;
    function valueRow(optIdx, vi, data) {
      const vid = valueIndex++;
      return `
        <div class="flex items-center gap-2" id="val_${vid}">
          <input type="hidden" name="options[${optIdx}][values][${vi}][id]" value="${data.id || ''}">
          <input type="text" name="options[${optIdx}][values][${vi}][value]" value="${data.value || ''}" placeholder="e.g. S, M, L" class="input flex-1">
          <button type="button" class="btn-danger px-2 py-1" onclick="document.getElementById('val_${vid}').remove()">
            <span class="material-icons-outlined text-[14px]">close</span>
          </button>
        </div>`;
    }

    function addValue(optIdx) {
      const container = document.getElementById(`option_${optIdx}_values`);
      const vi = container.children.length;
      container.insertAdjacentHTML('beforeend', valueRow(optIdx, vi, {}));
    }
  </script>
  </x-slot:scripts>
</x-admin.layout>

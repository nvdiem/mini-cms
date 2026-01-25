<x-admin.layout :title="'Upload Package · Page Builder'" :crumb="'Page Builder'">
  <div class="mb-6">
    <div class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400 mb-2">
      <a href="{{ route('admin.page-builder.index') }}" class="hover:text-primary">Page Builder</a>
      <span class="material-icons-outlined text-xs">chevron_right</span>
      <span>Upload Package</span>
    </div>
    <h1 class="text-2xl font-semibold text-text-strong dark:text-white tracking-tight">Upload Package</h1>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Form -->
    <div class="lg:col-span-2">
      <div class="card p-6">
        @if($errors->any())
          <div class="mb-6 p-4 rounded-lg border border-red-200 bg-red-50 dark:bg-red-900/20 dark:border-red-800">
            <div class="flex items-start gap-2">
              <span class="material-icons-outlined text-red-600 dark:text-red-400">error</span>
              <div class="flex-1">
                <h3 class="text-sm font-semibold text-red-800 dark:text-red-300">Upload Failed</h3>
                <ul class="mt-1 text-sm text-red-700 dark:text-red-400 list-disc list-inside">
                  @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
            </div>
          </div>
        @endif

        <form method="POST" action="{{ route('admin.page-builder.store') }}" enctype="multipart/form-data" class="space-y-6">
          @csrf

          <!-- Name -->
          <div>
            <label class="block text-sm font-medium text-text-strong dark:text-white mb-2">
              Package Name <span class="text-red-500">*</span>
            </label>
            <input 
              type="text" 
              name="name" 
              class="input w-full" 
              placeholder="e.g. Landing Page 2024"
              value="{{ old('name') }}"
              required
              maxlength="255"
              oninput="document.getElementById('slug').value = this.value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '')"
            >
            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">A friendly name for this package</p>
          </div>

          <!-- Slug -->
          <div>
            <label class="block text-sm font-medium text-text-strong dark:text-white mb-2">
              Slug <span class="text-red-500">*</span>
            </label>
            <input 
              type="text" 
              id="slug"
              name="slug" 
              class="input w-full font-mono text-sm" 
              placeholder="landing-page-2024"
              value="{{ old('slug') }}"
              required
              pattern="[a-z0-9-]+"
              maxlength="255"
            >
            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
              URL-friendly identifier (lowercase, numbers, hyphens only). Public URL: <code class="text-primary">/b/{slug}</code>
            </p>
          </div>

          <!-- ZIP File -->
          <div>
            <label class="block text-sm font-medium text-text-strong dark:text-white mb-2">
              ZIP File <span class="text-red-500">*</span>
            </label>
            <input 
              type="file" 
              name="zip_file" 
              class="block w-full text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-primary/10 file:text-primary hover:file:bg-primary/15 file:font-medium cursor-pointer"
              accept=".zip"
              required
            >
            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
              Max 20MB. Must contain <code>index.html</code> as entry point.
            </p>
          </div>

          <!-- Wire Contact -->
          <div class="border-t border-border-light dark:border-border-dark pt-6">
            <div class="flex items-start gap-3">
              <input 
                type="checkbox" 
                id="wire_contact" 
                name="wire_contact" 
                value="1"
                class="mt-1 h-4 w-4 rounded border-slate-300 text-primary focus:ring-primary"
                {{ old('wire_contact', true) ? 'checked' : '' }}
                onchange="document.getElementById('wire_selector_group').style.display = this.checked ? 'block' : 'none'"
              >
              <div class="flex-1">
                <label for="wire_contact" class="block text-sm font-medium text-text-strong dark:text-white cursor-pointer">
                  Enable Contact Form Integration
                </label>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                  Automatically inject JavaScript to connect contact forms to your CMS leads system
                </p>
              </div>
            </div>

            <div id="wire_selector_group" class="mt-4 ml-7" style="display: {{ old('wire_contact', true) ? 'block' : 'none' }}">
              <label class="block text-sm font-medium text-text-strong dark:text-white mb-2">
                Form Selector (Optional)
              </label>
              <input 
                type="text" 
                name="wire_selector" 
                class="input w-full font-mono text-sm" 
                placeholder="[data-contact-form],#contactForm,.js-contact"
                value="{{ old('wire_selector', '[data-contact-form],#contactForm,.js-contact') }}"
              >
              <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                CSS selector to find contact forms. Supports multiple selectors separated by commas.
              </p>
            </div>
          </div>

          <!-- Submit -->
          <div class="flex items-center gap-3 pt-4 border-t border-border-light dark:border-border-dark">
            <button type="submit" class="btn-primary">
              <span class="material-icons-outlined text-sm mr-1">upload</span>
              Upload & Publish
            </button>
            <a href="{{ route('admin.page-builder.index') }}" class="btn-ghost">Cancel</a>
          </div>
        </form>
      </div>
    </div>

    <!-- Sidebar Info -->
    <div class="lg:col-span-1">
      <div class="card p-6 space-y-6">
        <div>
          <h3 class="text-sm font-semibold text-text-strong dark:text-white mb-3 flex items-center gap-2">
            <span class="material-icons-outlined text-lg text-primary">info</span>
            Requirements
          </h3>
          <ul class="space-y-2 text-sm text-slate-600 dark:text-slate-400">
            <li class="flex items-start gap-2">
              <span class="material-icons-outlined text-xs mt-0.5 text-green-600">check_circle</span>
              <span>Must contain <code class="text-xs bg-slate-100 dark:bg-slate-800 px-1 rounded">index.html</code></span>
            </li>
            <li class="flex items-start gap-2">
              <span class="material-icons-outlined text-xs mt-0.5 text-green-600">check_circle</span>
              <span>Max file size: 20MB</span>
            </li>
            <li class="flex items-start gap-2">
              <span class="material-icons-outlined text-xs mt-0.5 text-green-600">check_circle</span>
              <span>Max 500 files per package</span>
            </li>
          </ul>
        </div>

        <div class="border-t border-border-light dark:border-border-dark pt-6">
          <h3 class="text-sm font-semibold text-text-strong dark:text-white mb-3 flex items-center gap-2">
            <span class="material-icons-outlined text-lg text-blue-600">verified_user</span>
            Allowed Files
          </h3>
          <div class="text-xs text-slate-600 dark:text-slate-400 space-y-1">
            <p><strong>Web:</strong> html, css, js, json</p>
            <p><strong>Images:</strong> png, jpg, gif, svg, webp, ico</p>
            <p><strong>Fonts:</strong> woff, woff2, ttf, otf</p>
          </div>
        </div>

        <div class="border-t border-border-light dark:border-border-dark pt-6">
          <h3 class="text-sm font-semibold text-text-strong dark:text-white mb-3 flex items-center gap-2">
            <span class="material-icons-outlined text-lg text-red-600">block</span>
            Blocked Files
          </h3>
          <div class="text-xs text-slate-600 dark:text-slate-400">
            <p>PHP, executables, and server config files are automatically blocked for security.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</x-admin.layout>

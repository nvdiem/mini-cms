<x-admin.layout :title="'Settings · Mini CMS'" :crumb="'Settings'">
  <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
      <h1 class="text-2xl font-semibold text-slate-900 dark:text-white tracking-tight">Settings</h1>
      <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">Configure general site options, SEO defaults, and writing settings.</p>
    </div>
  </div>

  <form method="POST" action="{{ route('admin.settings.update') }}">
    @csrf
    
    <div class="card overflow-hidden">
      <!-- Tabs Header -->
      <div class="px-6 py-4 bg-slate-50/70 dark:bg-slate-800/30 border-b border-border-light dark:border-border-dark">
        <div class="flex gap-2 overflow-x-auto">
          <button type="button" class="tab-btn active" data-tab="general">
            <span class="material-icons-outlined text-[18px]" aria-hidden="true">settings</span>
            General
          </button>
          <button type="button" class="tab-btn" data-tab="writing">
            <span class="material-icons-outlined text-[18px]" aria-hidden="true">edit</span>
            Writing
          </button>
          <button type="button" class="tab-btn" data-tab="reading">
            <span class="material-icons-outlined text-[18px]" aria-hidden="true">menu_book</span>
            Reading
          </button>
          <button type="button" class="tab-btn" data-tab="seo">
            <span class="material-icons-outlined text-[18px]" aria-hidden="true">search</span>
            SEO Defaults
          </button>
        </div>
      </div>

      <!-- Tab Content -->
      <div class="p-6">
        <!-- General Tab -->
        <div class="tab-content active" data-tab="general">
          <div class="max-w-2xl space-y-4">
            <div>
              <label class="text-sm font-medium text-slate-900 dark:text-slate-200">Site Name</label>
              <input class="input mt-1 focus:ring-slate-200 focus:border-slate-300 focus:outline-none focus-visible:ring-slate-200" name="site_name" value="{{ old('site_name', $settings['site_name'] ?? '') }}" placeholder="PointOne CMS" />
              @error('site_name') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
              <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">The name of your site.</div>
            </div>

            <div>
              <label class="text-sm font-medium text-slate-900 dark:text-slate-200">Tagline</label>
              <input class="input mt-1 focus:ring-slate-200 focus:border-slate-300 focus:outline-none focus-visible:ring-slate-200" name="tagline" value="{{ old('tagline', $settings['tagline'] ?? '') }}" placeholder="Built with Laravel" />
              @error('tagline') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
              <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">A short description of your site.</div>
            </div>

            <div>
              <label class="text-sm font-medium text-slate-900 dark:text-slate-200">Timezone</label>
              <div class="relative mt-1">
                <select class="select focus:ring-slate-200 focus:border-slate-300 focus:outline-none focus-visible:ring-slate-200" name="timezone">
                  @foreach(['UTC', 'America/New_York', 'America/Chicago', 'America/Los_Angeles', 'Europe/London', 'Europe/Paris', 'Asia/Tokyo', 'Asia/Shanghai', 'Asia/Ho_Chi_Minh', 'Australia/Sydney'] as $tz)
                    <option value="{{ $tz }}" {{ old('timezone', $settings['timezone'] ?? 'UTC') === $tz ? 'selected' : '' }}>{{ $tz }}</option>
                  @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-500">
                  <span class="material-icons-outlined text-sm" aria-hidden="true">expand_more</span>
                </div>
              </div>
              @error('timezone') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
            </div>

            <div>
              <label class="text-sm font-medium text-slate-900 dark:text-slate-200">Logo</label>
              <div class="mt-3 flex items-start gap-3">
                @php $logoId = $settings['logo_media_id'] ?? null; $logo = $logoId ? $media->firstWhere('id', $logoId) : null; @endphp
                @if($logo)
                  <img src="{{ $logo->url() }}" alt="Logo" class="h-14 w-20 object-cover rounded-lg border border-border-light dark:border-border-dark"/>
                @else
                  <div class="h-14 w-20 rounded-lg border border-border-light dark:border-border-dark bg-slate-100 dark:bg-slate-800"></div>
                @endif

                <div class="flex-1">
                  <div class="relative">
                    <select class="select focus:ring-slate-200 focus:border-slate-300 focus:outline-none focus-visible:ring-slate-200" name="logo_media_id">
                      <option value="">No logo</option>
                      @foreach($media as $m)
                        <option value="{{ $m->id }}" {{ (string)old('logo_media_id', $logoId) === (string)$m->id ? 'selected' : '' }}>
                          #{{ $m->id }} — {{ $m->original_name }}
                        </option>
                      @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-500">
                      <span class="material-icons-outlined text-sm" aria-hidden="true">expand_more</span>
                    </div>
                  </div>
                  <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">Select from media library. <a class="text-primary hover:underline" href="{{ route('admin.media.index') }}" target="_blank">Upload new</a></div>
                </div>
              </div>
              @error('logo_media_id') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
            </div>

            <div>
              <label class="text-sm font-medium text-slate-900 dark:text-slate-200">Contact Recipient Email</label>
              <input type="email" class="input mt-1 focus:ring-slate-200 focus:border-slate-300 focus:outline-none focus-visible:ring-slate-200" name="contact_recipient_email" value="{{ old('contact_recipient_email', $settings['contact_recipient_email'] ?? '') }}" placeholder="admin@example.com" />
              @error('contact_recipient_email') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
              <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">Email address to receive contact form notifications.</div>
            </div>
          </div>
        </div>

        <!-- Writing Tab -->
        <div class="tab-content hidden" data-tab="writing">
          <div class="max-w-2xl space-y-4">
            <div>
              <label class="text-sm font-medium text-slate-900 dark:text-slate-200">Default Post Status</label>
              <div class="relative mt-1">
                <select class="select focus:ring-slate-200 focus:border-slate-300 focus:outline-none focus-visible:ring-slate-200" name="default_post_status">
                  @foreach(['draft' => 'Draft', 'review' => 'Review', 'published' => 'Published'] as $k => $v)
                    <option value="{{ $k }}" {{ old('default_post_status', $settings['default_post_status'] ?? 'draft') === $k ? 'selected' : '' }}>{{ $v }}</option>
                  @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-500">
                  <span class="material-icons-outlined text-sm" aria-hidden="true">expand_more</span>
                </div>
              </div>
              @error('default_post_status') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
              <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">Default status for new posts.</div>
            </div>
          </div>
        </div>

        <!-- Reading Tab -->
        <div class="tab-content hidden" data-tab="reading">
          <div class="max-w-2xl space-y-4">
            <div>
              <label class="text-sm font-medium text-slate-900 dark:text-slate-200">Posts Per Page</label>
              <input type="number" class="input mt-1 focus:ring-slate-200 focus:border-slate-300 focus:outline-none focus-visible:ring-slate-200" name="posts_per_page" value="{{ old('posts_per_page', $settings['posts_per_page'] ?? '10') }}" min="1" max="100" />
              @error('posts_per_page') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
              <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">Number of posts to display per page on the frontend.</div>
            </div>
          </div>
        </div>

        <!-- SEO Tab -->
        <div class="tab-content hidden" data-tab="seo">
          <div class="max-w-2xl space-y-4">
            <div>
              <label class="text-sm font-medium text-slate-900 dark:text-slate-200">Default Title</label>
              <input class="input mt-1 focus:ring-slate-200 focus:border-slate-300 focus:outline-none focus-visible:ring-slate-200" name="seo_default_title" value="{{ old('seo_default_title', $settings['seo_default_title'] ?? '') }}" placeholder="PointOne CMS - Your Modern Content Platform" />
              @error('seo_default_title') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
              <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">Fallback title for pages without custom meta title.</div>
            </div>

            <div>
              <label class="text-sm font-medium text-slate-900 dark:text-slate-200">Default Description</label>
              <textarea class="input mt-1 min-h-[96px] focus:ring-slate-200 focus:border-slate-300 focus:outline-none focus-visible:ring-slate-200" name="seo_default_description" placeholder="A modern content management system...">{{ old('seo_default_description', $settings['seo_default_description'] ?? '') }}</textarea>
              @error('seo_default_description') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
              <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">Fallback meta description.</div>
            </div>

            <div>
              <label class="text-sm font-medium text-slate-900 dark:text-slate-200">Default Keywords</label>
              <input class="input mt-1 focus:ring-slate-200 focus:border-slate-300 focus:outline-none focus-visible:ring-slate-200" name="seo_default_keywords" value="{{ old('seo_default_keywords', $settings['seo_default_keywords'] ?? '') }}" placeholder="cms, laravel, content" />
              @error('seo_default_keywords') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
              <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">Comma-separated keywords.</div>
            </div>
          </div>
        </div>

        <!-- Actions -->
        <div class="mt-6 pt-6 border-t border-border-light dark:border-border-dark flex items-center justify-between">
          <a class="btn-ghost" href="{{ route('admin.posts.index') }}">Cancel</a>
          <button class="btn-primary" type="submit">
            <span class="material-icons-outlined text-[18px]" aria-hidden="true">save</span>
            Save Settings
          </button>
        </div>
      </div>
    </div>
  </form>

  @php
    $scripts = <<<'HTML'
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');

    tabButtons.forEach(btn => {
      btn.addEventListener('click', () => {
        const targetTab = btn.getAttribute('data-tab');
        
        // Update buttons
        tabButtons.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        // Update content
        tabContents.forEach(content => {
          if (content.getAttribute('data-tab') === targetTab) {
            content.classList.remove('hidden');
            content.classList.add('active');
          } else {
            content.classList.add('hidden');
            content.classList.remove('active');
          }
        });
      });
    });
  });
</script>
<style>
  .tab-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
    font-weight: 500;
    border-radius: 0.5rem;
    transition: all 0.2s;
    white-space: nowrap;
  }
  .tab-btn:hover {
    background-color: rgba(0, 0, 0, 0.05);
  }
  .dark .tab-btn:hover {
    background-color: rgba(255, 255, 255, 0.05);
  }
  .tab-btn.active {
    background-color: rgb(var(--c-primary) / 0.1);
    color: rgb(var(--c-primary));
    font-weight: 600;
  }
</style>
HTML;
  @endphp
  <x-slot:scripts>{!! $scripts !!}</x-slot:scripts>
</x-admin.layout>

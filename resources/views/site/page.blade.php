<x-site.layout :title="($page->title ?? 'Page') . ' · PointOne'" :isPreview="($isPreview ?? false)" :backUrl="($backUrl ?? null)">
  <article class="rounded-2xl border border-slate-200 bg-white overflow-hidden">
    @if($page->featuredImage)
      <img src="{{ $page->featuredImage->url() }}" alt="" class="h-56 w-full object-cover"/>
    @endif

    <div class="p-6 sm:p-8">
      <div class="flex flex-wrap items-center gap-2 text-sm text-slate-500">
        <span>{{ optional($page->published_at)->format('M j, Y') ?? $page->updated_at->format('M j, Y') }}</span>
        <span class="text-slate-300">•</span>
        <span>{{ $page->author?->name ?? '—' }}</span>

        @if(!empty($isPreview))
          <span class="text-slate-300">•</span>
          <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">Preview</span>
          <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">Status: {{ ucfirst($page->status) }}</span>
        @endif
      </div>

      <h1 class="mt-3 text-2xl sm:text-3xl font-semibold tracking-tight text-slate-900">{{ $page->title }}</h1>

      @if($page->excerpt)
        <p class="mt-5 text-slate-700">{{ $page->excerpt }}</p>
      @endif

      <div class="prose prose-slate max-w-none mt-6">
        {!! nl2br(e($page->content ?? '')) !!}
      </div>

      <div class="mt-8 pt-6 border-t border-slate-200 flex items-center justify-between">
        <a href="{{ route('site.home') }}" class="text-primary font-medium hover:underline">← Back to home</a>
        @auth
          <a href="{{ route('admin.pages.edit', $page) }}" class="text-slate-600 hover:underline">Edit in admin</a>
        @endauth
      </div>
    </div>
  </article>
</x-site.layout>

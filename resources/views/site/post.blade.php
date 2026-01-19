<x-site.layout :title="($post->title ?? 'Post') . ' · PointOne Blog'" :isPreview="($isPreview ?? false)" :backUrl="($backUrl ?? null)">
  <article class="rounded-2xl border border-slate-200 bg-white overflow-hidden">
    @if($post->featuredImage)
      <img src="{{ $post->featuredImage->url() }}" alt="" class="h-56 w-full object-cover"/>
    @endif

    <div class="p-6 sm:p-8">
      <div class="flex flex-wrap items-center gap-2 text-sm text-slate-500">
        <span>{{ optional($post->published_at)->format('M j, Y') ?? $post->updated_at->format('M j, Y') }}</span>
        <span class="text-slate-300">•</span>
        <span>{{ $post->author?->name ?? '—' }}</span>

        @if(!empty($isPreview))
          <span class="text-slate-300">•</span>
          <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">Preview</span>
          <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">Status: {{ ucfirst($post->status) }}</span>
        @endif
      </div>

      <h1 class="mt-3 text-2xl sm:text-3xl font-semibold tracking-tight text-slate-900">{{ $post->title }}</h1>

      @if($post->categories && $post->categories->count())
        <div class="mt-4 flex flex-wrap gap-2">
          @foreach($post->categories as $c)
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-primary/10 text-primary">{{ $c->name }}</span>
          @endforeach
        </div>
      @endif

      @if($post->excerpt)
        <p class="mt-5 text-slate-700">{{ $post->excerpt }}</p>
      @endif

      <div class="prose prose-slate max-w-none mt-6">
        {!! nl2br(e($post->content ?? '')) !!}
      </div>

      <div class="mt-8 pt-6 border-t border-slate-200 flex items-center justify-between">
        <a href="{{ route('site.home') }}" class="text-primary font-medium hover:underline">← Back to home</a>
        @auth
          <a href="{{ route('admin.posts.edit', $post) }}" class="text-slate-600 hover:underline">Edit in admin</a>
        @endauth
      </div>
    </div>
  </article>
</x-site.layout>

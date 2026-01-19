<x-site.layout :title="'Home · PointOne Blog'">
  <div class="flex items-end justify-between gap-4">
    <div>
      <h1 class="text-2xl sm:text-3xl font-semibold tracking-tight text-slate-900">Latest Posts</h1>
      <p class="mt-1 text-slate-600">Read published articles.</p>
    </div>
  </div>

  @if($posts->count() === 0)
    <div class="mt-8 rounded-2xl border border-slate-200 bg-white p-10 text-center">
      <div class="mx-auto h-12 w-12 rounded-2xl bg-primary/10 flex items-center justify-center text-primary font-bold">i</div>
      <h2 class="mt-4 text-lg font-semibold text-slate-900">No published posts yet</h2>
      <p class="mt-1 text-slate-600">Go to admin and publish your first post.</p>
      @auth
        <div class="mt-5">
          <a href="{{ route('admin.posts.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-primary text-white font-medium hover:bg-blue-700">Create post</a>
        </div>
      @endauth
    </div>
  @else
    <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 gap-4">
      @foreach($posts as $post)
        <article class="rounded-2xl border border-slate-200 bg-white overflow-hidden hover:shadow-sm transition">
          @if($post->featuredImage)
            <img src="{{ $post->featuredImage->url() }}" alt="" class="h-44 w-full object-cover"/>
          @endif
          <div class="p-5">
            <div class="text-xs text-slate-500">
              {{ optional($post->published_at)->format('M j, Y') ?? $post->updated_at->format('M j, Y') }}
              · {{ $post->author?->name ?? '—' }}
            </div>
            <h2 class="mt-2 text-lg font-semibold text-slate-900 leading-snug">
              <a class="hover:underline" href="{{ route('site.posts.show', $post->slug) }}">{{ $post->title }}</a>
            </h2>
            <p class="mt-2 text-slate-600 text-sm line-clamp-3">
              {{ $post->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($post->content ?? ''), 140) }}
            </p>
            <div class="mt-4">
              <a class="text-primary font-medium text-sm hover:underline" href="{{ route('site.posts.show', $post->slug) }}">Read more →</a>
            </div>
          </div>
        </article>
      @endforeach
    </div>

    <div class="mt-8">
      {{ $posts->links() }}
    </div>
  @endif
</x-site.layout>

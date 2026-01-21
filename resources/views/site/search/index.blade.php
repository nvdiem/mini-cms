<x-site.layout :title="'Search Results: ' . e($q)">
  <div class="max-w-3xl mx-auto px-4 sm:px-6 py-12">
    {{-- Search Header --}}
    <div class="text-center mb-10">
      <h1 class="text-3xl font-bold text-slate-900 tracking-tight mb-6">Search Results</h1>
      <form action="{{ route('site.search') }}" method="GET" class="relative max-w-lg mx-auto">
        <input 
          type="text" 
          name="q" 
          value="{{ $q }}" 
          class="w-full h-12 pl-4 pr-12 rounded-xl border border-slate-300 focus:border-primary focus:ring-2 focus:ring-primary/20 transition shadow-sm text-lg"
          placeholder="Search for posts..."
          autofocus
        >
        <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-primary transition p-1">
          <span class="material-icons-outlined text-2xl">search</span>
        </button>
      </form>
    </div>

    {{-- Results Info --}}
    @if($q)
      <div class="mb-6 flex items-center justify-between border-b border-slate-200 pb-4">
        <div class="text-slate-600">
          Found <span class="font-semibold text-slate-900">{{ $total }}</span> results for "<span class="font-semibold text-slate-900">{{ $q }}</span>"
        </div>
      </div>
    @endif

    {{-- Results List --}}
    @if($posts->count() > 0)
      <div class="space-y-8">
        @foreach($posts as $post)
          <article class="group relative flex flex-col sm:flex-row gap-6">
            {{-- Optional Thumbnail (Hidden on mobile for compactness, or keep) --}}
            @if($post->featuredImage)
              <div class="shrink-0 w-full sm:w-48 h-32 bg-slate-100 rounded-lg overflow-hidden border border-slate-200 order-last sm:order-first">
                <img src="{{ $post->featuredImage->url() }}" alt="" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
              </div>
            @endif

            <div class="flex-1 min-w-0">
              <div class="flex items-center gap-2 text-xs text-slate-500 mb-2">
                <span>{{ $post->published_at ? $post->published_at->format('M d, Y') : $post->created_at->format('M d, Y') }}</span>
                @if($post->categories->count() > 0)
                  <span>&bull;</span>
                  <span class="text-primary font-medium">{{ $post->categories->first()->name }}</span>
                @endif
              </div>

              <h2 class="text-xl font-bold text-slate-900 group-hover:text-primary transition mb-2 tracking-tight">
                <a href="{{ route('site.posts.show', $post->slug) }}">
                  {!! $post->highlighted_title !!}
                </a>
              </h2>

              <div class="text-slate-600 leading-relaxed text-[15px]">
                {!! $post->search_snippet !!}
              </div>
            </div>
          </article>
        @endforeach
      </div>

      {{-- Pagination --}}
      <div class="mt-12">
        {{ $posts->links() }}
      </div>

    @elseif($q)
      {{-- Empty State --}}
      <div class="text-center py-12">
        <div class="mx-auto h-16 w-16 bg-slate-100 rounded-full flex items-center justify-center mb-4 text-slate-400">
          <span class="material-icons-outlined text-3xl">sentiment_dissatisfied</span>
        </div>
        <h3 class="text-lg font-medium text-slate-900">No matches found</h3>
        <p class="text-slate-500 mt-1">Try adjusting your search terms or check for typos.</p>
      </div>
    @endif
  </div>
</x-site.layout>

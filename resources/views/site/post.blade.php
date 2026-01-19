<x-site.layout :title="$post->meta_title ?? $post->title" :meta_description="$post->meta_description ?? $post->excerpt" :meta_keywords="$post->meta_keywords">
  
  <article class="bg-white">
    <!-- Post Header -->
    <header class="max-w-3xl mx-auto px-4 sm:px-6 pt-16 pb-12 text-center">
      <div class="flex items-center justify-center gap-3 mb-6">
        @foreach($post->categories as $cat)
          <a href="#" class="text-xs font-bold uppercase tracking-wider text-primary hover:text-blue-700 transition">
            {{ $cat->name }}
          </a>
        @endforeach
      </div>

      <h1 class="text-3xl sm:text-5xl font-bold text-slate-900 tracking-tight mb-6 leading-tight">
        {{ $post->title }}
      </h1>

      <div class="flex items-center justify-center gap-2 text-slate-500 font-medium text-sm">
        <span>{{ $post->author->name }}</span>
        <span>&middot;</span>
        <time datetime="{{ optional($post->published_at)->toIso8601String() }}">
          {{ optional($post->published_at)->format('F j, Y') ?? 'Just now' }}
        </time>
        @if($post->tags->isNotEmpty())
          <span>&middot;</span>
          <div class="flex items-center gap-1">
            @foreach($post->tags as $tag)
              <span class="text-slate-400">#{{ $tag->name }}</span>
            @endforeach
          </div>
        @endif
      </div>
    </header>

    <!-- Featured Image -->
    @if($post->featuredImage)
      <div class="max-w-5xl mx-auto px-4 sm:px-6 mb-12 sm:mb-20">
        <div class="aspect-[21/9] bg-slate-100 rounded-2xl overflow-hidden shadow-sm">
          <img src="{{ $post->featuredImage->url() }}" alt="{{ $post->featuredImage->alt_text ?? $post->title }}" class="w-full h-full object-cover">
        </div>
      </div>
    @else
       <div class="border-b border-slate-100 mb-12 sm:mb-20"></div>
    @endif

    <!-- Content -->
    <div class="max-w-3xl mx-auto px-4 sm:px-6">
      <div class="prose prose-slate prose-lg max-w-none 
           prose-headings:font-bold prose-headings:tracking-tight prose-headings:text-slate-900
           prose-p:text-slate-600 prose-p:leading-relaxed
           prose-a:text-primary prose-a:font-medium prose-a:no-underline hover:prose-a:underline
           prose-blockquote:border-primary prose-blockquote:bg-slate-50 prose-blockquote:py-2 prose-blockquote:px-4 prose-blockquote:not-italic prose-blockquote:font-medium prose-blockquote:text-slate-700
           first-letter:text-5xl first-letter:font-bold first-letter:text-slate-900 first-letter:float-left first-letter:mr-3 first-letter:mt-[-4px]">
        {!! $post->content !!}
      </div>

      <!-- Share / Back -->
      <div class="mt-16 pt-8 border-t border-slate-200 flex items-center justify-between">
        <a href="{{ route('site.home') }}" class="text-slate-500 hover:text-primary font-medium flex items-center gap-1 transition">
          <span class="material-icons-outlined text-[18px]">arrow_back</span>
          Back to articles
        </a>
        <div class="flex gap-2">
           <!-- Social share placeholders could go here -->
        </div>
      </div>
    </div>
  </article>

  <!-- Author Bio / NextPrev -->
  <div class="max-w-3xl mx-auto px-4 sm:px-6 py-12">
     <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        @if($prevPost)
          <a href="{{ route('site.posts.show', $prevPost->slug) }}" class="group p-6 rounded-xl border border-slate-200 hover:border-primary/30 hover:bg-slate-50 transition text-right sm:text-left">
            <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Previous</div>
            <div class="font-bold text-slate-900 group-hover:text-primary transition line-clamp-1">{{ $prevPost->title }}</div>
          </a>
        @else
          <div></div>
        @endif

        @if($nextPost)
          <a href="{{ route('site.posts.show', $nextPost->slug) }}" class="group p-6 rounded-xl border border-slate-200 hover:border-primary/30 hover:bg-slate-50 transition text-right">
            <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Next</div>
            <div class="font-bold text-slate-900 group-hover:text-primary transition line-clamp-1">{{ $nextPost->title }}</div>
          </a>
        @endif
     </div>
  </div>

  <!-- Related Posts -->
  @if(isset($relatedPosts) && $relatedPosts->isNotEmpty())
    <div class="bg-slate-50 border-t border-slate-200 py-20">
      <div class="max-w-5xl mx-auto px-4 sm:px-6">
        <h3 class="text-2xl font-bold text-slate-900 mb-8">Read next</h3>
        <div class="grid sm:grid-cols-3 gap-8">
          @foreach($relatedPosts as $rPost)
            <a href="{{ route('site.posts.show', $rPost->slug) }}" class="group">
              <div class="aspect-[3/2] bg-white rounded-xl overflow-hidden mb-4 border border-slate-200 group-hover:shadow-md transition">
                @if($rPost->featuredImage)
                  <img src="{{ $rPost->featuredImage->url() }}" class="w-full h-full object-cover transition duration-500 group-hover:scale-105">
                @else
                   <div class="w-full h-full bg-slate-100 flex items-center justify-center text-slate-300">
                     <span class="material-icons-outlined text-2xl">image</span>
                   </div>
                @endif
              </div>
              <h4 class="font-bold text-slate-900 group-hover:text-primary transition mb-2">{{ $rPost->title }}</h4>
              <p class="text-sm text-slate-500 line-clamp-2">{{ $rPost->excerpt }}</p>
            </a>
          @endforeach
        </div>
      </div>
    </div>
  @endif
</x-site.layout>

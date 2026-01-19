<x-site.layout>
  <!-- Hero Section -->
  <div class="bg-white border-b border-slate-100">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 py-20 sm:py-28 text-center">
      <h1 class="text-4xl sm:text-5xl font-bold text-slate-900 tracking-tight mb-4">
        {{ setting('site_name') ?: 'PointOne' }}
      </h1>
      <p class="text-xl sm:text-2xl text-slate-500 max-w-2xl mx-auto font-light leading-relaxed mb-10">
        {{ setting('tagline') ?: 'Minimalist software for the modern web.' }}
      </p>
      
      <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
        <a href="{{ route('contact.index') }}" class="w-full sm:w-auto px-8 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-blue-700 transition shadow-sm">
          Get in touch
        </a>
        <a href="#latest" class="w-full sm:w-auto px-8 py-3 bg-white text-slate-700 font-medium border border-slate-200 rounded-lg hover:border-slate-300 hover:bg-slate-50 transition">
          Read the blog
        </a>
      </div>
    </div>
  </div>

  <div class="max-w-5xl mx-auto px-4 sm:px-6 py-12 sm:py-20" id="latest">
    @if($posts->count() === 0)
      <div class="text-center py-20 bg-white rounded-2xl border border-dashed border-slate-200">
        <div class="text-slate-400 mb-2">
          <span class="material-icons-outlined text-4xl">article</span>
        </div>
        <h3 class="text-lg font-medium text-slate-900">No posts yet</h3>
        <p class="text-slate-500 mt-1">Check back later for updates.</p>
        @auth
          <a href="{{ route('admin.posts.create') }}" class="inline-block mt-4 text-primary hover:underline font-medium">Create your first post</a>
        @endauth
      </div>
    @else
      <div class="space-y-16">
        <!-- Featured Post (First item provided by controller) -->
        @if(isset($featuredPost) && $posts->currentPage() === 1)
          <section>
            <div class="flex items-center justify-between mb-8">
              <h2 class="text-xs font-bold uppercase tracking-widest text-slate-400">Featured</h2>
            </div>
            
            <article class="group relative grid md:grid-cols-2 gap-8 md:gap-12 items-center">
              <div class="aspect-[16/10] bg-slate-100 rounded-2xl overflow-hidden">
                @if($featuredPost->featuredImage)
                  <img src="{{ $featuredPost->featuredImage->url() }}" alt="{{ $featuredPost->title }}" class="w-full h-full object-cover transition duration-500 group-hover:scale-105">
                @else
                  <div class="w-full h-full flex items-center justify-center bg-slate-100 text-slate-300">
                    <span class="material-icons-outlined text-4xl">image</span>
                  </div>
                @endif
              </div>
              
              <div>
                <div class="flex flex-wrap gap-2 mb-4">
                  @foreach($featuredPost->categories as $cat)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
                      {{ $cat->name }}
                    </span>
                  @endforeach
                </div>
                
                <h3 class="text-3xl font-bold text-slate-900 tracking-tight mb-3 group-hover:text-primary transition">
                  <a href="{{ route('site.posts.show', $featuredPost->slug) }}">
                    <span class="absolute inset-0"></span>
                    {{ $featuredPost->title }}
                  </a>
                </h3>
                
                <p class="text-slate-600 text-lg leading-relaxed mb-4 line-clamp-3">
                  {{ $featuredPost->excerpt }}
                </p>
                
                <div class="flex items-center text-sm text-slate-400 font-medium">
                  {{ $featuredPost->author->name }} &middot; {{ optional($featuredPost->published_at)->format('M d, Y') ?? 'Just now' }}
                </div>
              </div>
            </article>
          </section>
        @endif

        <!-- Latest Posts Grid -->
        <section>
          <div class="flex items-center justify-between mb-8">
            <h2 class="text-xs font-bold uppercase tracking-widest text-slate-400">Latest Articles</h2>
          </div>

          <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-x-8 gap-y-12">
            @foreach($posts as $post)
              {{-- Skip featured post on first page --}}
              @if(isset($featuredPost) && $posts->currentPage() === 1 && $loop->first) @continue @endif

              <article class="group relative flex flex-col h-full">
                <div class="aspect-[3/2] bg-slate-100 rounded-xl overflow-hidden mb-4 border border-slate-100">
                  @if($post->featuredImage)
                    <img src="{{ $post->featuredImage->url() }}" alt="{{ $post->title }}" class="w-full h-full object-cover transition duration-500 group-hover:scale-105">
                  @else
                    <div class="w-full h-full flex items-center justify-center bg-slate-50 text-slate-300">
                      <span class="material-icons-outlined text-3xl">image</span>
                    </div>
                  @endif
                </div>

                <div class="flex-1">
                  @if($post->categories->isNotEmpty())
                    <div class="text-xs font-semibold text-primary mb-2 uppercase tracking-wide">
                      {{ $post->categories->first()->name }}
                    </div>
                  @endif

                  <h3 class="text-xl font-bold text-slate-900 mb-2 group-hover:text-primary transition line-clamp-2">
                    <a href="{{ route('site.posts.show', $post->slug) }}">
                      <span class="absolute inset-0"></span>
                      {{ $post->title }}
                    </a>
                  </h3>

                  <p class="text-slate-600 text-sm leading-relaxed line-clamp-3 mb-4">
                    {{ $post->excerpt }}
                  </p>
                </div>

                <div class="mt-auto text-xs font-medium text-slate-400 flex items-center gap-2">
                  <span>{{ optional($post->published_at)->format('M d, Y') ?? 'Just now' }}</span>
                  @if($post->tags->isNotEmpty())
                    <span class="w-1 h-1 bg-slate-300 rounded-full"></span>
                    <span>#{{ $post->tags->first()->name }}</span>
                  @endif
                </div>
              </article>
            @endforeach
          </div>

          <div class="mt-16">
            {{ $posts->onEachSide(1)->links() }}
          </div>
        </section>
      </div>
    @endif
  </div>

  <!-- Bottom CTA -->
  <div class="bg-slate-900 py-20 sm:py-24">
    <div class="max-w-4xl mx-auto px-4 text-center">
      <h2 class="text-3xl sm:text-4xl font-bold text-white mb-6 tracking-tight">Ready to start your project?</h2>
      <p class="text-slate-400 text-lg mb-10 max-w-2xl mx-auto">
        Join hundreds of companies that trust us to deliver exceptional software experiences. We are available for new collaborations.
      </p>
      <a href="{{ route('contact.index') }}" class="inline-flex items-center justify-center px-8 py-3 bg-white text-slate-900 font-bold rounded-lg hover:bg-slate-100 transition">
        Let's work together
      </a>
    </div>
  </div>
</x-site.layout>

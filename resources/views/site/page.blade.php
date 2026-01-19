<x-site.layout :title="$page->meta_title ?? $page->title" :meta_description="$page->meta_description ?? $page->excerpt" :meta_keywords="$page->meta_keywords">
  
  <article class="bg-white min-h-[60vh]">
    <header class="max-w-3xl mx-auto px-4 sm:px-6 pt-16 pb-12 text-center">
      <h1 class="text-3xl sm:text-5xl font-bold text-slate-900 tracking-tight leading-tight">
        {{ $page->title }}
      </h1>
      @if($page->excerpt)
        <p class="mt-4 text-xl text-slate-500 leading-relaxed font-light">{{ $page->excerpt }}</p>
      @endif
    </header>

    @if($page->featuredImage)
      <div class="max-w-5xl mx-auto px-4 sm:px-6 mb-12 sm:mb-20">
        <div class="aspect-[3/1] bg-slate-100 rounded-2xl overflow-hidden shadow-sm">
          <img src="{{ $page->featuredImage->url() }}" alt="{{ $page->featuredImage->alt_text ?? $page->title }}" class="w-full h-full object-cover">
        </div>
      </div>
    @else
      <div class="border-b border-slate-100 mb-12"></div>
    @endif

    <div class="max-w-3xl mx-auto px-4 sm:px-6 pb-20">
      <div class="prose prose-slate prose-lg max-w-none 
           prose-headings:font-bold prose-headings:tracking-tight prose-headings:text-slate-900
           prose-p:text-slate-600 prose-p:leading-relaxed
           prose-a:text-primary prose-a:font-medium hover:prose-a:underline">
        {!! $page->content !!}
      </div>
    </div>
  </article>

  <!-- Optional Bottom CTA for Pages -->
  <div class="bg-slate-50 border-t border-slate-200 py-16">
    <div class="max-w-3xl mx-auto px-4 text-center">
      <h2 class="text-2xl font-bold text-slate-900 mb-4">Have questions?</h2>
      <p class="text-slate-600 mb-8">Whether you want to learn more about our process or just say hello, we're here.</p>
      <a href="{{ route('contact.index') }}" class="btn-primary px-8 py-3 rounded-lg shadow-sm">Contact Us</a>
    </div>
  </div>
</x-site.layout>

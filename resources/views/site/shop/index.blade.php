<x-site.layout :title="setting('site_name', 'Shop') . ' - Shop'">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

  {{-- Header --}}
  <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8">
    <div>
      <h1 class="text-3xl font-bold text-slate-900 dark:text-white">Shop</h1>
      <p class="text-slate-500 dark:text-slate-400 mt-1">Browse our products</p>
    </div>
    <form method="GET" class="flex gap-2 w-full sm:w-auto">
      <input type="text" name="q" value="{{ request('q') }}" placeholder="Search products..." class="flex-1 sm:w-64 rounded-lg border border-slate-200 dark:border-slate-700 px-4 py-2.5 text-sm bg-white dark:bg-slate-900 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
      <button type="submit" class="inline-flex items-center justify-center px-4 py-2.5 rounded-lg bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700 transition">
        <span class="material-icons-outlined text-[18px]">search</span>
      </button>
    </form>
  </div>

  {{-- Product Grid --}}
  @if($products->isNotEmpty())
  <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
    @foreach($products as $product)
    <a href="{{ route('shop.show', $product->slug) }}" class="group block bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
      {{-- Image --}}
      <div class="aspect-square bg-slate-100 dark:bg-slate-800 overflow-hidden">
        @if($product->featuredImage)
          <img src="{{ asset('storage/' . $product->featuredImage->path) }}" alt="{{ $product->title }}"
            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
        @else
          <div class="w-full h-full flex items-center justify-center">
            <span class="material-icons-outlined text-5xl text-slate-300 dark:text-slate-600">image</span>
          </div>
        @endif
      </div>
      {{-- Info --}}
      <div class="p-4">
        <h3 class="text-sm font-semibold text-slate-900 dark:text-white group-hover:text-indigo-600 transition-colors line-clamp-2">{{ $product->title }}</h3>
        <div class="mt-2 text-sm font-bold text-indigo-600 dark:text-indigo-400">
          @if($product->min_price == $product->max_price)
            {{ number_format($product->min_price, 0, ',', '.') }}₫
          @elseif($product->min_price > 0)
            {{ number_format($product->min_price, 0, ',', '.') }}₫ – {{ number_format($product->max_price, 0, ',', '.') }}₫
          @else
            <span class="text-slate-400">Contact</span>
          @endif
        </div>
        @if($product->active_variants_count > 0)
          <div class="text-xs text-slate-400 mt-1">{{ $product->active_variants_count }} variant(s)</div>
        @endif
      </div>
    </a>
    @endforeach
  </div>

  <div class="mt-10">{{ $products->links() }}</div>
  @else
  <div class="text-center py-20">
    <span class="material-icons-outlined text-6xl text-slate-300 dark:text-slate-600 mb-4 block">storefront</span>
    <h2 class="text-xl font-semibold text-slate-700 dark:text-slate-200 mb-2">No products found</h2>
    <p class="text-slate-400">
      @if(request('q'))
        Try a different search term.
      @else
        Check back later for new arrivals!
      @endif
    </p>
  </div>
  @endif
</div>
</x-site.layout>

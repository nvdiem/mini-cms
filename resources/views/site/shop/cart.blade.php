<x-site.layout :title="'Your Cart'">
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

  <h1 class="text-3xl font-bold text-slate-900 dark:text-white mb-8">Your Cart</h1>

  @if(!empty($items))
  <div class="space-y-4">
    @foreach($items as $item)
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-4 flex items-start gap-4">
      {{-- Image --}}
      <div class="w-20 h-20 rounded-lg bg-slate-100 dark:bg-slate-800 overflow-hidden flex-shrink-0">
        @if($item['image'])
          <img src="{{ asset('storage/' . $item['image']->path) }}" alt="" class="w-full h-full object-cover">
        @else
          <div class="w-full h-full flex items-center justify-center">
            <span class="material-icons-outlined text-2xl text-slate-300">image</span>
          </div>
        @endif
      </div>

      {{-- Info --}}
      <div class="flex-1 min-w-0">
        <h3 class="font-semibold text-slate-900 dark:text-white">{{ $item['title'] }}</h3>
        @if($item['signature'])
          <p class="text-sm text-slate-500 mt-0.5">{{ $item['signature'] }}</p>
        @endif
        <p class="text-sm font-bold text-indigo-600 dark:text-indigo-400 mt-1">{{ number_format($item['price'], 0, ',', '.') }}₫</p>
      </div>

      {{-- Quantity --}}
      <div class="flex items-center gap-2">
        <form method="POST" action="{{ route('cart.update') }}" class="flex items-center gap-1">
          @csrf
          <input type="hidden" name="variant_id" value="{{ $item['variant_id'] }}">
          <button type="submit" name="qty" value="{{ max(0, $item['qty'] - 1) }}" class="w-8 h-8 rounded-lg border border-slate-200 dark:border-slate-700 flex items-center justify-center text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
            <span class="material-icons-outlined text-[16px]">remove</span>
          </button>
          <span class="w-8 text-center text-sm font-medium">{{ $item['qty'] }}</span>
          <button type="submit" name="qty" value="{{ min($item['stock_qty'], $item['qty'] + 1) }}" class="w-8 h-8 rounded-lg border border-slate-200 dark:border-slate-700 flex items-center justify-center text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
            <span class="material-icons-outlined text-[16px]">add</span>
          </button>
        </form>
      </div>

      {{-- Line Total --}}
      <div class="text-right flex-shrink-0 w-28">
        <div class="font-bold text-slate-900 dark:text-white">{{ number_format($item['line_total'], 0, ',', '.') }}₫</div>
        <form method="POST" action="{{ route('cart.remove') }}" class="mt-1">
          @csrf
          <input type="hidden" name="variant_id" value="{{ $item['variant_id'] }}">
          <button type="submit" class="text-xs text-red-500 hover:text-red-700 transition">Remove</button>
        </form>
      </div>
    </div>
    @endforeach
  </div>

  {{-- Summary --}}
  <div class="mt-8 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6">
    <div class="flex items-center justify-between text-lg font-bold text-slate-900 dark:text-white">
      <span>Subtotal</span>
      <span>{{ number_format($subtotal, 0, ',', '.') }}₫</span>
    </div>
    <p class="text-sm text-slate-400 mt-1">Shipping calculated at checkout</p>

    <div class="flex flex-col sm:flex-row gap-3 mt-6">
      <a href="{{ route('shop.index') }}" class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-lg border border-slate-200 dark:border-slate-700 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
        <span class="material-icons-outlined text-[18px]">arrow_back</span> Continue Shopping
      </a>
      <a href="{{ route('checkout.index') }}" class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-3 rounded-lg bg-indigo-600 text-white font-semibold text-sm hover:bg-indigo-700 transition">
        <span class="material-icons-outlined text-[18px]">shopping_cart_checkout</span> Proceed to Checkout
      </a>
    </div>
  </div>

  @else
  <div class="text-center py-20">
    <span class="material-icons-outlined text-6xl text-slate-300 dark:text-slate-600 mb-4 block">shopping_cart</span>
    <h2 class="text-xl font-semibold text-slate-700 dark:text-slate-200 mb-2">Your cart is empty</h2>
    <p class="text-slate-400 mb-6">Start adding products to your cart.</p>
    <a href="{{ route('shop.index') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-lg bg-indigo-600 text-white font-semibold text-sm hover:bg-indigo-700 transition">
      Browse Products
    </a>
  </div>
  @endif
</div>
</x-site.layout>

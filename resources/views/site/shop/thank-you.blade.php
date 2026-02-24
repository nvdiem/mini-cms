<x-site.layout :title="'Order Confirmed!'">
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center">

  {{-- Success Icon --}}
  <div class="w-20 h-20 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center mx-auto mb-6">
    <span class="material-icons-outlined text-4xl text-emerald-600">check_circle</span>
  </div>

  <h1 class="text-3xl font-bold text-slate-900 dark:text-white mb-2">Thank you!</h1>
  <p class="text-lg text-slate-500 dark:text-slate-400 mb-6">Your order has been placed successfully.</p>

  {{-- Order Number --}}
  <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6 mb-8 inline-block">
    <p class="text-sm text-slate-500 mb-1">Order Number</p>
    <p class="text-2xl font-bold font-mono text-indigo-600 dark:text-indigo-400">{{ $order->order_no }}</p>
    <p class="text-sm text-slate-400 mt-2">{{ $order->created_at->format('d/m/Y H:i') }}</p>
  </div>

  {{-- Order Details --}}
  <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 text-left overflow-hidden mb-8">
    <div class="p-6">
      <h3 class="font-semibold text-slate-900 dark:text-white mb-4">Order Details</h3>
      <div class="space-y-3">
        @foreach($order->items as $item)
        <div class="flex items-center justify-between">
          <div>
            <span class="text-sm font-medium text-slate-900 dark:text-white">{{ $item->product_title_snapshot }}</span>
            @if($item->variant_signature_snapshot)
              <span class="text-xs text-slate-400 ml-1">{{ $item->variant_signature_snapshot }}</span>
            @endif
            <span class="text-xs text-slate-400">× {{ $item->qty }}</span>
          </div>
          <span class="text-sm font-medium">{{ number_format($item->line_total, 0, ',', '.') }}₫</span>
        </div>
        @endforeach
      </div>

      <div class="border-t border-slate-200 dark:border-slate-700 mt-4 pt-4 space-y-2 text-sm">
        <div class="flex justify-between">
          <span class="text-slate-500">Subtotal</span>
          <span>{{ number_format($order->subtotal, 0, ',', '.') }}₫</span>
        </div>
        <div class="flex justify-between">
          <span class="text-slate-500">Shipping</span>
          <span>{{ number_format($order->shipping_fee, 0, ',', '.') }}₫</span>
        </div>
        <div class="flex justify-between font-bold text-slate-900 dark:text-white text-base pt-2 border-t border-slate-200 dark:border-slate-700">
          <span>Total</span>
          <span class="text-indigo-600 dark:text-indigo-400">{{ number_format($order->grand_total, 0, ',', '.') }}₫</span>
        </div>
      </div>
    </div>
  </div>

  {{-- COD Info --}}
  <div class="bg-amber-50 dark:bg-amber-900/20 rounded-xl border border-amber-200 dark:border-amber-800 p-6 text-left mb-8">
    <div class="flex items-start gap-3">
      <span class="material-icons-outlined text-amber-600 text-xl">payments</span>
      <div>
        <p class="font-semibold text-amber-800 dark:text-amber-200">Cash on Delivery</p>
        <p class="text-sm text-amber-700 dark:text-amber-300 mt-1">Please prepare <strong>{{ number_format($order->grand_total, 0, ',', '.') }}₫</strong> for payment upon delivery.</p>
        @if($codInstructions)
          <p class="text-sm text-amber-600 dark:text-amber-400 mt-2">{{ $codInstructions }}</p>
        @endif
      </div>
    </div>
  </div>

  {{-- Delivery Info --}}
  <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6 text-left mb-8">
    <h3 class="font-semibold text-slate-900 dark:text-white mb-3">Delivery To</h3>
    <p class="text-sm"><strong>{{ $order->customer_name }}</strong></p>
    <p class="text-sm text-slate-500">{{ $order->phone }}</p>
    <p class="text-sm text-slate-500 mt-1">{{ $order->address_line }}</p>
    @if($order->ward || $order->district || $order->province)
      <p class="text-sm text-slate-500">{{ collect([$order->ward, $order->district, $order->province])->filter()->implode(', ') }}</p>
    @endif
  </div>

  <a href="{{ route('shop.index') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-lg bg-indigo-600 text-white font-semibold text-sm hover:bg-indigo-700 transition">
    <span class="material-icons-outlined text-[18px]">storefront</span>
    Continue Shopping
  </a>
</div>
</x-site.layout>

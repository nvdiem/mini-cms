<x-site.layout :title="'Checkout'">
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

  <h1 class="text-3xl font-bold text-slate-900 dark:text-white mb-8">Checkout</h1>

  <form method="POST" action="{{ route('checkout.store') }}">
    @csrf
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
      {{-- Left: Customer Info --}}
      <div class="lg:col-span-3 space-y-6">
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6">
          <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Delivery Information</h2>

          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Full Name <span class="text-red-500">*</span></label>
              <input type="text" name="customer_name" value="{{ old('customer_name') }}" required
                class="w-full rounded-lg border border-slate-200 dark:border-slate-700 px-4 py-2.5 text-sm bg-white dark:bg-slate-900 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
              @error('customer_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Phone <span class="text-red-500">*</span></label>
                <input type="tel" name="phone" value="{{ old('phone') }}" required
                  class="w-full rounded-lg border border-slate-200 dark:border-slate-700 px-4 py-2.5 text-sm bg-white dark:bg-slate-900 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
                @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
              </div>
              <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}"
                  class="w-full rounded-lg border border-slate-200 dark:border-slate-700 px-4 py-2.5 text-sm bg-white dark:bg-slate-900 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
                @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Address <span class="text-red-500">*</span></label>
              <input type="text" name="address_line" value="{{ old('address_line') }}" required
                class="w-full rounded-lg border border-slate-200 dark:border-slate-700 px-4 py-2.5 text-sm bg-white dark:bg-slate-900 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition"
                placeholder="Street address, building, etc.">
              @error('address_line') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
              <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Ward</label>
                <input type="text" name="ward" value="{{ old('ward') }}"
                  class="w-full rounded-lg border border-slate-200 dark:border-slate-700 px-4 py-2.5 text-sm bg-white dark:bg-slate-900 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
              </div>
              <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">District</label>
                <input type="text" name="district" value="{{ old('district') }}"
                  class="w-full rounded-lg border border-slate-200 dark:border-slate-700 px-4 py-2.5 text-sm bg-white dark:bg-slate-900 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
              </div>
              <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Province</label>
                <input type="text" name="province" value="{{ old('province') }}"
                  class="w-full rounded-lg border border-slate-200 dark:border-slate-700 px-4 py-2.5 text-sm bg-white dark:bg-slate-900 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Note</label>
              <textarea name="note" rows="2"
                class="w-full rounded-lg border border-slate-200 dark:border-slate-700 px-4 py-2.5 text-sm bg-white dark:bg-slate-900 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition"
                placeholder="Delivery instructions...">{{ old('note') }}</textarea>
            </div>
          </div>
        </div>

        {{-- Payment Method --}}
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6">
          <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Payment</h2>
          <div class="flex items-center gap-3 p-4 bg-slate-50 dark:bg-slate-800 rounded-lg border-2 border-indigo-500">
            <span class="material-icons-outlined text-indigo-600 text-2xl">payments</span>
            <div>
              <p class="font-semibold text-slate-900 dark:text-white">Cash on Delivery (COD)</p>
              <p class="text-sm text-slate-500">Pay when you receive your order</p>
            </div>
          </div>
          @if($codInstructions)
            <p class="text-sm text-slate-500 mt-3">{{ $codInstructions }}</p>
          @endif
        </div>
      </div>

      {{-- Right: Order Summary --}}
      <div class="lg:col-span-2">
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6 sticky top-8">
          <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Order Summary</h2>

          <div class="space-y-3 max-h-60 overflow-y-auto">
            @foreach($items as $item)
            <div class="flex items-center gap-3">
              <div class="w-12 h-12 rounded-lg bg-slate-100 dark:bg-slate-800 overflow-hidden flex-shrink-0">
                @if($item['image'])
                  <img src="{{ asset('storage/' . $item['image']->path) }}" alt="" class="w-full h-full object-cover">
                @endif
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-slate-900 dark:text-white truncate">{{ $item['title'] }}</p>
                <p class="text-xs text-slate-400">{{ $item['signature'] }} × {{ $item['qty'] }}</p>
              </div>
              <div class="text-sm font-medium text-slate-900 dark:text-white">{{ number_format($item['line_total'], 0, ',', '.') }}₫</div>
            </div>
            @endforeach
          </div>

          <div class="border-t border-slate-200 dark:border-slate-700 mt-4 pt-4 space-y-2 text-sm">
            <div class="flex justify-between">
              <span class="text-slate-500">Subtotal</span>
              <span class="font-medium">{{ number_format($subtotal, 0, ',', '.') }}₫</span>
            </div>
            <div class="flex justify-between">
              <span class="text-slate-500">Shipping</span>
              <span class="font-medium">{{ number_format($shippingFee, 0, ',', '.') }}₫</span>
            </div>
            <div class="flex justify-between text-lg font-bold text-slate-900 dark:text-white pt-2 border-t border-slate-200 dark:border-slate-700">
              <span>Total</span>
              <span class="text-indigo-600 dark:text-indigo-400">{{ number_format($grandTotal, 0, ',', '.') }}₫</span>
            </div>
          </div>

          <button type="submit" class="w-full mt-6 inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-lg bg-indigo-600 text-white font-bold text-base hover:bg-indigo-700 transition-all duration-200 shadow-lg shadow-indigo-200 dark:shadow-none">
            <span class="material-icons-outlined text-[20px]">lock</span>
            Place Order
          </button>

          <p class="text-xs text-center text-slate-400 mt-3">
            By placing this order, you agree to our terms and conditions.
          </p>
        </div>
      </div>
    </div>
  </form>
</div>
</x-site.layout>

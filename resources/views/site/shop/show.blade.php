<x-site.layout :title="$product->seo_title ?: $product->title">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

  {{-- Breadcrumb --}}
  <nav class="text-sm text-slate-500 mb-6">
    <a href="{{ route('shop.index') }}" class="hover:text-indigo-600 transition-colors">Shop</a>
    <span class="mx-2">/</span>
    <span class="text-slate-900 dark:text-white">{{ $product->title }}</span>
  </nav>

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
    {{-- Image --}}
    <div>
      <div class="aspect-square bg-slate-100 dark:bg-slate-800 rounded-2xl overflow-hidden" id="mainImageContainer">
        @if($product->featuredImage)
          <img src="{{ asset('storage/' . $product->featuredImage->path) }}" alt="{{ $product->title }}"
            class="w-full h-full object-cover" id="mainImage">
        @else
          <div class="w-full h-full flex items-center justify-center">
            <span class="material-icons-outlined text-7xl text-slate-300 dark:text-slate-600">image</span>
          </div>
        @endif
      </div>
    </div>

    {{-- Info --}}
    <div>
      <h1 class="text-3xl font-bold text-slate-900 dark:text-white mb-3">{{ $product->title }}</h1>

      @if($product->excerpt)
        <p class="text-slate-500 dark:text-slate-400 mb-6">{{ $product->excerpt }}</p>
      @endif

      {{-- Price --}}
      <div class="mb-6" id="priceDisplay">
        <div class="text-2xl font-bold text-indigo-600 dark:text-indigo-400" id="currentPrice">
          @php $range = $product->priceRange(); @endphp
          @if($range['min'] == $range['max'])
            {{ number_format($range['min'], 0, ',', '.') }}₫
          @else
            {{ number_format($range['min'], 0, ',', '.') }}₫ – {{ number_format($range['max'], 0, ',', '.') }}₫
          @endif
        </div>
        <div class="text-sm text-slate-400 line-through mt-1 hidden" id="comparePrice"></div>
      </div>

      {{-- Option Selectors --}}
      @foreach($product->options as $option)
      <div class="mb-4">
        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">{{ $option->label }}</label>
        <div class="flex flex-wrap gap-2 option-group" data-option-name="{{ $option->name }}">
          @foreach($option->values as $val)
          <button type="button"
            class="px-4 py-2 border-2 rounded-lg text-sm font-medium transition-all duration-200 option-btn hover:border-indigo-400 border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300"
            data-option-name="{{ $option->name }}"
            data-option-value="{{ $val->value }}"
            onclick="selectOption('{{ $option->name }}', '{{ $val->value }}', this)">
            {{ $val->value }}
          </button>
          @endforeach
        </div>
      </div>
      @endforeach

      {{-- Stock Status --}}
      <div class="mb-4 text-sm" id="stockStatus">
        <span class="text-slate-400">Select options to check availability</span>
      </div>

      {{-- Add to Cart --}}
      <form method="POST" action="{{ route('cart.add') }}" id="addToCartForm">
        @csrf
        <input type="hidden" name="variant_id" id="selectedVariantId" value="">
        <div class="flex items-center gap-4 mb-6">
          <div class="flex items-center border border-slate-200 dark:border-slate-700 rounded-lg overflow-hidden">
            <button type="button" class="px-3 py-2.5 text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-800 transition" onclick="changeQty(-1)">
              <span class="material-icons-outlined text-[18px]">remove</span>
            </button>
            <input type="number" name="qty" id="qtyInput" value="1" min="1" max="999" class="w-14 text-center border-x border-slate-200 dark:border-slate-700 py-2.5 text-sm font-medium bg-transparent outline-none">
            <button type="button" class="px-3 py-2.5 text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-800 transition" onclick="changeQty(1)">
              <span class="material-icons-outlined text-[18px]">add</span>
            </button>
          </div>

          <button type="submit" id="addToCartBtn" disabled
            class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-3 rounded-lg bg-indigo-600 text-white font-semibold text-sm hover:bg-indigo-700 transition-all duration-200 disabled:opacity-40 disabled:cursor-not-allowed disabled:hover:bg-indigo-600">
            <span class="material-icons-outlined text-[20px]">shopping_cart</span>
            Add to Cart
          </button>
        </div>
      </form>

      {{-- Description --}}
      @if($product->description_html)
      <div class="border-t border-slate-200 dark:border-slate-700 pt-6 mt-6">
        <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Description</h3>
        <div class="prose prose-slate dark:prose-invert max-w-none text-sm">
          {!! $product->description_html !!}
        </div>
      </div>
      @endif
    </div>
  </div>
</div>

<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined">

<script>
  const variants = @json($variantsJson);
  const selectedOptions = {};
  let selectedVariant = null;

  function selectOption(optionName, value, btn) {
    // Toggle selection
    const group = btn.closest('.option-group');
    group.querySelectorAll('.option-btn').forEach(b => {
      b.classList.remove('border-indigo-600', 'bg-indigo-50', 'dark:bg-indigo-900/30', 'text-indigo-700', 'dark:text-indigo-300');
      b.classList.add('border-slate-200', 'dark:border-slate-700');
    });
    btn.classList.add('border-indigo-600', 'bg-indigo-50', 'dark:bg-indigo-900/30', 'text-indigo-700', 'dark:text-indigo-300');
    btn.classList.remove('border-slate-200', 'dark:border-slate-700');

    selectedOptions[optionName] = value;
    matchVariant();
  }

  function matchVariant() {
    const optionGroups = document.querySelectorAll('.option-group');
    if (Object.keys(selectedOptions).length < optionGroups.length) {
      selectedVariant = null;
      updateUI();
      return;
    }

    // Build signature pattern
    selectedVariant = variants.find(v => {
      const parts = v.signature.split('|');
      return parts.every(part => {
        const [name, value] = part.split(':');
        // Match case-insensitively because seeder signature is "size:S|color:Red"
        // And option labels from DB might have different capitalization
        return selectedOptions[name.charAt(0).toUpperCase() + name.slice(1)] === value || 
               selectedOptions[name.toLowerCase()] === value || 
               selectedOptions[name] === value;
      });
    });

    updateUI();
  }

  function updateUI() {
    const priceEl = document.getElementById('currentPrice');
    const compareEl = document.getElementById('comparePrice');
    const stockEl = document.getElementById('stockStatus');
    const variantInput = document.getElementById('selectedVariantId');
    const addBtn = document.getElementById('addToCartBtn');
    const qtyInput = document.getElementById('qtyInput');
    const mainImage = document.getElementById('mainImage');

    if (selectedVariant) {
      priceEl.textContent = formatVND(selectedVariant.price) + '₫';

      if (selectedVariant.compare_at_price && selectedVariant.compare_at_price > selectedVariant.price) {
        compareEl.textContent = formatVND(selectedVariant.compare_at_price) + '₫';
        compareEl.classList.remove('hidden');
      } else {
        compareEl.classList.add('hidden');
      }

      if (selectedVariant.in_stock) {
        stockEl.innerHTML = '<span class="text-emerald-600 font-medium">✓ In stock</span> <span class="text-slate-400">(' + selectedVariant.stock_qty + ' available)</span>';
        addBtn.disabled = false;
        qtyInput.max = selectedVariant.stock_qty;
        if (parseInt(qtyInput.value) > selectedVariant.stock_qty) {
          qtyInput.value = selectedVariant.stock_qty;
        }
      } else {
        stockEl.innerHTML = '<span class="text-red-500 font-medium">✗ Out of stock</span>';
        addBtn.disabled = true;
      }

      variantInput.value = selectedVariant.id;

      if (selectedVariant.image && mainImage) {
        mainImage.src = selectedVariant.image;
      }
    } else {
      compareEl.classList.add('hidden');
      stockEl.innerHTML = '<span class="text-slate-400">Select all options to check availability</span>';
      addBtn.disabled = true;
      variantInput.value = '';
    }
  }

  function changeQty(delta) {
    const input = document.getElementById('qtyInput');
    let val = parseInt(input.value) + delta;
    val = Math.max(1, Math.min(val, parseInt(input.max) || 999));
    input.value = val;
  }

  function formatVND(n) {
    return new Intl.NumberFormat('vi-VN').format(n);
  }
</script>
</x-site.layout>

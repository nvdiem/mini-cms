<x-admin.layout :title="'Products · Mini CMS'" :crumb="'Products'">
  <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
    <div>
      <h1 class="text-2xl font-bold text-text-strong dark:text-white">Products</h1>
      <p class="text-sm text-text-muted mt-1">Manage your shop products</p>
    </div>
    <a href="{{ route('admin.shop.products.create') }}" class="btn-primary">
      <span class="material-icons-outlined text-[18px]">add</span>
      Add Product
    </a>
  </div>

  {{-- Filters --}}
  <form method="GET" class="card mb-6">
    <div class="p-4 flex flex-wrap gap-3 items-end">
      <div class="flex-1 min-w-[200px]">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products..." class="input">
      </div>
      <div class="w-40">
        <select name="status" class="select">
          <option value="">All Status</option>
          <option value="active" @selected(request('status')==='active')>Active</option>
          <option value="inactive" @selected(request('status')==='inactive')>Inactive</option>
          <option value="trashed" @selected(request('status')==='trashed')>Trashed</option>
        </select>
      </div>
      <label class="flex items-center gap-2 text-sm">
        <input type="checkbox" name="out_of_stock" value="1" class="chk" @checked(request()->boolean('out_of_stock'))>
        Out of stock
      </label>
      <button type="submit" class="btn-soft">Filter</button>
      <a href="{{ route('admin.shop.products.index') }}" class="btn-ghost">Clear</a>
    </div>
  </form>

  {{-- Products Table (desktop) --}}
  <div class="card hidden md:block">
    <table class="table">
      <thead>
        <tr class="border-b border-border-light dark:border-border-dark">
          <th class="th">Image</th>
          <th class="th">Title</th>
          <th class="th">Variants</th>
          <th class="th">Status</th>
          <th class="th text-right">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($products as $product)
        <tr class="row border-b border-border-light dark:border-border-dark">
          <td class="td w-16">
            @if($product->featuredImage)
              <img src="{{ asset('storage/' . $product->featuredImage->path) }}" alt="" class="w-12 h-12 rounded-lg object-cover border border-border-light dark:border-border-dark">
            @else
              <div class="w-12 h-12 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
                <span class="material-icons-outlined text-slate-400 text-[20px]">image</span>
              </div>
            @endif
          </td>
          <td class="td">
            <div class="font-medium text-text-strong dark:text-white">{{ $product->title }}</div>
            <div class="text-xs text-text-muted">/shop/{{ $product->slug }}</div>
          </td>
          <td class="td">
            <span class="text-sm">{{ $product->active_variants_count ?? 0 }} active / {{ $product->variants_count ?? 0 }} total</span>
          </td>
          <td class="td">
            @if($product->trashed())
              <span class="badge bg-red-100 text-red-700 border-red-200">Trashed</span>
            @elseif($product->is_active)
              <span class="badge badge-pub">Active</span>
            @else
              <span class="badge badge-draft">Inactive</span>
            @endif
          </td>
          <td class="td text-right">
            <div class="flex items-center justify-end gap-1">
              @if($product->trashed())
                <form method="POST" action="{{ route('admin.shop.products.restore', $product->id) }}">
                  @csrf
                  <button class="btn-ghost px-2 py-1.5 text-sm" title="Restore">
                    <span class="material-icons-outlined text-[18px]">restore</span>
                  </button>
                </form>
              @else
                <a href="{{ route('admin.shop.products.edit', $product) }}" class="btn-ghost px-2 py-1.5 text-sm" title="Edit">
                  <span class="material-icons-outlined text-[18px]">edit</span>
                </a>
                <form method="POST" action="{{ route('admin.shop.products.publish', $product) }}">
                  @csrf
                  <button class="btn-ghost px-2 py-1.5 text-sm" title="{{ $product->is_active ? 'Deactivate' : 'Activate' }}">
                    <span class="material-icons-outlined text-[18px]">{{ $product->is_active ? 'visibility_off' : 'visibility' }}</span>
                  </button>
                </form>
                <button type="button" class="btn-danger px-2 py-1.5 text-sm" title="Delete"
                  onclick="openDeleteModal('{{ route('admin.shop.products.destroy', $product) }}', '{{ addslashes($product->title) }}')">
                  <span class="material-icons-outlined text-[18px]">delete</span>
                </button>
              @endif
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td class="td text-center text-text-muted py-12" colspan="5">
            <span class="material-icons-outlined text-4xl mb-2 block">storefront</span>
            No products found.
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Mobile Cards --}}
  <div class="md:hidden space-y-3">
    @foreach($products as $product)
    <div class="card p-4">
      <div class="flex items-center gap-3">
        @if($product->featuredImage)
          <img src="{{ asset('storage/' . $product->featuredImage->path) }}" alt="" class="w-14 h-14 rounded-lg object-cover border border-border-light dark:border-border-dark">
        @else
          <div class="w-14 h-14 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center flex-shrink-0">
            <span class="material-icons-outlined text-slate-400">image</span>
          </div>
        @endif
        <div class="min-w-0 flex-1">
          <div class="font-medium text-text-strong dark:text-white truncate">{{ $product->title }}</div>
          <div class="text-xs text-text-muted mt-0.5">{{ $product->active_variants_count ?? 0 }} variant(s)</div>
          <div class="mt-1">
            @if($product->trashed())
              <span class="badge bg-red-100 text-red-700 border-red-200 text-[10px]">Trashed</span>
            @elseif($product->is_active)
              <span class="badge badge-pub text-[10px]">Active</span>
            @else
              <span class="badge badge-draft text-[10px]">Inactive</span>
            @endif
          </div>
        </div>
        <div class="flex items-center gap-1 flex-shrink-0">
          @if($product->trashed())
            <form method="POST" action="{{ route('admin.shop.products.restore', $product->id) }}">
              @csrf
              <button class="btn-ghost px-2 py-1.5"><span class="material-icons-outlined text-[18px]">restore</span></button>
            </form>
          @else
            <a href="{{ route('admin.shop.products.edit', $product) }}" class="btn-ghost px-2 py-1.5"><span class="material-icons-outlined text-[18px]">edit</span></a>
          @endif
        </div>
      </div>
    </div>
    @endforeach
  </div>

  {{-- Pagination --}}
  <div class="mt-6">
    {{ $products->links() }}
  </div>

  {{-- Delete Modal --}}
  <div id="deleteModal" class="modal hidden">
    <div class="modal-backdrop" onclick="closeDeleteModal()"></div>
    <div class="modal-panel relative">
      <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">Delete Product</h3>
      <p class="text-sm text-slate-600 dark:text-slate-300 mb-6">
        Are you sure you want to move "<span id="deleteProductTitle"></span>" to trash?
      </p>
      <div class="flex justify-end gap-3">
        <button class="btn-ghost" onclick="closeDeleteModal()">Cancel</button>
        <form id="deleteForm" method="POST">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn bg-red-600 text-white hover:bg-red-700">Delete</button>
        </form>
      </div>
    </div>
  </div>

  <x-slot:scripts>
  <script>
    function openDeleteModal(url, title) {
      document.getElementById('deleteForm').action = url;
      document.getElementById('deleteProductTitle').textContent = title;
      document.getElementById('deleteModal').classList.remove('hidden');
    }
    function closeDeleteModal() {
      document.getElementById('deleteModal').classList.add('hidden');
    }
  </script>
  </x-slot:scripts>
</x-admin.layout>

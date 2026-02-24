<x-admin.layout :title="'Orders · Mini CMS'" :crumb="'Orders'">
  <div class="mb-6">
    <h1 class="text-2xl font-bold text-text-strong dark:text-white">Orders</h1>
    <p class="text-sm text-text-muted mt-1">Manage customer orders</p>
  </div>

  {{-- Filters --}}
  <form method="GET" class="card mb-6">
    <div class="p-4 flex flex-wrap gap-3 items-end">
      <div class="flex-1 min-w-[200px]">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search order #, name, phone..." class="input">
      </div>
      <div class="w-40">
        <select name="status" class="select">
          <option value="">All Status</option>
          @foreach(\App\Models\Order::STATUSES as $s)
            <option value="{{ $s }}" @selected(request('status')===$s)>{{ ucfirst($s) }}</option>
          @endforeach
        </select>
      </div>
      <button type="submit" class="btn-soft">Filter</button>
      <a href="{{ route('admin.shop.orders.index') }}" class="btn-ghost">Clear</a>
    </div>
  </form>

  {{-- Orders Table (desktop) --}}
  <div class="card hidden md:block">
    <table class="table">
      <thead>
        <tr class="border-b border-border-light dark:border-border-dark">
          <th class="th">Order #</th>
          <th class="th">Customer</th>
          <th class="th">Status</th>
          <th class="th text-right">Total</th>
          <th class="th">Date</th>
          <th class="th text-right">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($orders as $order)
        <tr class="row border-b border-border-light dark:border-border-dark">
          <td class="td">
            <span class="font-mono font-medium text-text-strong dark:text-white">{{ $order->order_no }}</span>
          </td>
          <td class="td">
            <div class="text-sm font-medium">{{ $order->customer_name }}</div>
            <div class="text-xs text-text-muted">{{ $order->phone }}</div>
          </td>
          <td class="td">
            <span class="badge {{ $order->statusColor() }}">{{ ucfirst($order->status) }}</span>
          </td>
          <td class="td text-right font-medium">{{ number_format($order->grand_total, 0, ',', '.') }}₫</td>
          <td class="td text-sm text-text-muted">{{ $order->created_at->format('d/m/Y H:i') }}</td>
          <td class="td text-right">
            <a href="{{ route('admin.shop.orders.show', $order) }}" class="btn-ghost px-2 py-1.5 text-sm">
              <span class="material-icons-outlined text-[18px]">visibility</span>
            </a>
          </td>
        </tr>
        @empty
        <tr>
          <td class="td text-center text-text-muted py-12" colspan="6">
            <span class="material-icons-outlined text-4xl mb-2 block">receipt_long</span>
            No orders found.
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Mobile Cards --}}
  <div class="md:hidden space-y-3">
    @foreach($orders as $order)
    <a href="{{ route('admin.shop.orders.show', $order) }}" class="card p-4 block hover:shadow-md transition-shadow">
      <div class="flex items-center justify-between mb-2">
        <span class="font-mono font-medium text-sm text-text-strong dark:text-white">{{ $order->order_no }}</span>
        <span class="badge {{ $order->statusColor() }} text-[10px]">{{ ucfirst($order->status) }}</span>
      </div>
      <div class="text-sm">{{ $order->customer_name }} · {{ $order->phone }}</div>
      <div class="flex items-center justify-between mt-2">
        <span class="text-xs text-text-muted">{{ $order->created_at->format('d/m/Y H:i') }}</span>
        <span class="text-sm font-semibold text-text-strong dark:text-white">{{ number_format($order->grand_total, 0, ',', '.') }}₫</span>
      </div>
    </a>
    @endforeach
  </div>

  <div class="mt-6">{{ $orders->links() }}</div>
</x-admin.layout>

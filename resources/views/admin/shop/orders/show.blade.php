<x-admin.layout :title="'Order ' . $order->order_no . ' · Mini CMS'" :crumb="'Order Detail'">
  <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
    <div>
      <h1 class="text-2xl font-bold text-text-strong dark:text-white">{{ $order->order_no }}</h1>
      <p class="text-sm text-text-muted mt-1">
        Placed {{ $order->created_at->format('d/m/Y H:i') }}
        · <span class="badge {{ $order->statusColor() }}">{{ ucfirst($order->status) }}</span>
      </p>
    </div>
    <a href="{{ route('admin.shop.orders.index') }}" class="btn-ghost">
      <span class="material-icons-outlined text-[18px]">arrow_back</span> Back
    </a>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Left: Items + Status --}}
    <div class="lg:col-span-2 space-y-6">
      {{-- Order Items --}}
      <div class="card">
        <div class="card-hd">
          <h3 class="text-sm font-semibold text-text-strong dark:text-white">Order Items</h3>
        </div>
        <div class="overflow-x-auto">
          <table class="table">
            <thead>
              <tr class="border-b border-border-light dark:border-border-dark">
                <th class="th">Product</th>
                <th class="th">SKU</th>
                <th class="th text-right">Price</th>
                <th class="th text-center">Qty</th>
                <th class="th text-right">Total</th>
              </tr>
            </thead>
            <tbody>
              @foreach($order->items as $item)
              <tr class="row border-b border-border-light dark:border-border-dark">
                <td class="td">
                  <div class="font-medium text-text-strong dark:text-white">{{ $item->product_title_snapshot }}</div>
                  @if($item->variant_signature_snapshot)
                    <div class="text-xs text-text-muted">{{ $item->variant_signature_snapshot }}</div>
                  @endif
                </td>
                <td class="td text-sm text-text-muted font-mono">{{ $item->sku_snapshot ?? '-' }}</td>
                <td class="td text-right">{{ number_format($item->unit_price_snapshot, 0, ',', '.') }}₫</td>
                <td class="td text-center">{{ $item->qty }}</td>
                <td class="td text-right font-medium">{{ number_format($item->line_total, 0, ',', '.') }}₫</td>
              </tr>
              @endforeach
            </tbody>
            <tfoot>
              <tr class="border-t border-border-light dark:border-border-dark">
                <td class="td font-medium text-right" colspan="4">Subtotal</td>
                <td class="td text-right font-medium">{{ number_format($order->subtotal, 0, ',', '.') }}₫</td>
              </tr>
              <tr>
                <td class="td text-right text-text-muted" colspan="4">Shipping</td>
                <td class="td text-right">{{ number_format($order->shipping_fee, 0, ',', '.') }}₫</td>
              </tr>
              @if($order->discount_total > 0)
              <tr>
                <td class="td text-right text-text-muted" colspan="4">Discount</td>
                <td class="td text-right text-red-600">-{{ number_format($order->discount_total, 0, ',', '.') }}₫</td>
              </tr>
              @endif
              <tr class="border-t-2 border-border-light dark:border-border-dark">
                <td class="td font-bold text-right text-text-strong dark:text-white" colspan="4">Grand Total</td>
                <td class="td text-right font-bold text-lg text-text-strong dark:text-white">{{ number_format($order->grand_total, 0, ',', '.') }}₫</td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>

      {{-- Status Workflow --}}
      @if(!empty($allowedTransitions))
      <div class="card p-6">
        <h3 class="text-sm font-semibold text-text-strong dark:text-white mb-4">Update Status</h3>
        <div class="flex flex-wrap gap-2">
          @foreach($allowedTransitions as $nextStatus)
          <form method="POST" action="{{ route('admin.shop.orders.status', $order) }}" class="inline">
            @csrf
            <input type="hidden" name="status" value="{{ $nextStatus }}">
            <button type="submit" class="btn {{ $nextStatus === 'cancelled' ? 'bg-red-600 text-white hover:bg-red-700' : 'btn-primary' }}">
              <span class="material-icons-outlined text-[18px]">
                @switch($nextStatus)
                  @case('confirmed') check_circle @break
                  @case('packed') inventory_2 @break
                  @case('shipped') local_shipping @break
                  @case('completed') done_all @break
                  @case('cancelled') cancel @break
                @endswitch
              </span>
              Mark {{ ucfirst($nextStatus) }}
            </button>
          </form>
          @endforeach
        </div>
      </div>
      @endif
    </div>

    {{-- Right: Customer Info --}}
    <div class="space-y-6">
      <div class="card p-6">
        <h3 class="text-sm font-semibold text-text-strong dark:text-white mb-4">Customer</h3>
        <div class="space-y-3 text-sm">
          <div>
            <span class="text-text-muted">Name:</span>
            <span class="font-medium text-text-strong dark:text-white ml-1">{{ $order->customer_name }}</span>
          </div>
          <div>
            <span class="text-text-muted">Phone:</span>
            <span class="font-medium ml-1">{{ $order->phone }}</span>
          </div>
          @if($order->email)
          <div>
            <span class="text-text-muted">Email:</span>
            <span class="font-medium ml-1">{{ $order->email }}</span>
          </div>
          @endif
          <div>
            <span class="text-text-muted">Address:</span>
            <span class="ml-1">{{ $order->address_line }}</span>
          </div>
          @if($order->ward || $order->district || $order->province)
          <div class="text-text-muted">
            {{ collect([$order->ward, $order->district, $order->province])->filter()->implode(', ') }}
          </div>
          @endif
          @if($order->note)
          <div class="mt-2 p-3 bg-slate-50 dark:bg-slate-800 rounded-lg">
            <span class="text-text-muted text-xs block mb-1">Note:</span>
            {{ $order->note }}
          </div>
          @endif
        </div>
      </div>

      <div class="card p-6">
        <h3 class="text-sm font-semibold text-text-strong dark:text-white mb-4">Payment</h3>
        <div class="space-y-2 text-sm">
          <div>
            <span class="text-text-muted">Method:</span>
            <span class="badge badge-draft ml-1">{{ strtoupper($order->payment_method) }}</span>
          </div>
          <div>
            <span class="text-text-muted">Status:</span>
            <span class="badge {{ $order->payment_status === 'paid' ? 'badge-pub' : 'bg-amber-100 text-amber-700 border-amber-200' }} ml-1">{{ ucfirst($order->payment_status) }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</x-admin.layout>

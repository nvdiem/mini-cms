<?php

namespace App\Http\Controllers\Admin\Shop;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('items')->latest();

        // Status filter
        if ($status = $request->input('status')) {
            if (in_array($status, Order::STATUSES)) {
                $query->where('status', $status);
            }
        }

        // Search by order_no or customer
        if ($s = $request->input('search')) {
            $query->where(function ($q) use ($s) {
                $q->where('order_no', 'like', "%{$s}%")
                  ->orWhere('customer_name', 'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%");
            });
        }

        $orders = $query->paginate(20)->appends($request->query());

        return view('admin.shop.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load('items');
        $allowedTransitions = Order::STATUS_TRANSITIONS[$order->status] ?? [];

        return view('admin.shop.orders.show', compact('order', 'allowedTransitions'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $data = $request->validate([
            'status' => ['required', 'string', 'in:' . implode(',', Order::STATUSES)],
        ]);

        $newStatus = $data['status'];

        if (!$order->canTransitionTo($newStatus)) {
            return back()->with('toast', [
                'tone'    => 'danger',
                'title'   => 'Invalid Transition',
                'message' => "Cannot change from '{$order->status}' to '{$newStatus}'.",
            ]);
        }

        $oldStatus     = $order->status;
        $order->status = $newStatus;
        $order->save();

        activity_log('order.status_changed', ['id' => $order->id, 'type' => \App\Models\Order::class],
            "Order {$order->order_no} status: {$oldStatus} → {$newStatus}", [
                'order_id' => $order->id,
                'before'   => $oldStatus,
                'after'    => $newStatus,
            ]);

        return back()->with('toast', [
            'tone'    => 'success',
            'title'   => 'Status Updated',
            'message' => "Order updated to " . ucfirst($newStatus) . ".",
        ]);
    }
}

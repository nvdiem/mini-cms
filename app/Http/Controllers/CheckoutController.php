<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart  = session('shop_cart', []);
        $items = CartController::resolveCartItems($cart);

        if (empty($items)) {
            return redirect()->route('shop.index')->with('toast', [
                'tone' => 'info', 'title' => 'Empty Cart',
                'message' => 'Your cart is empty.',
            ]);
        }

        $subtotal    = collect($items)->sum('line_total');
        $shippingFee = (float) setting('shop.shipping_fee_fixed', 30000);
        $grandTotal  = $subtotal + $shippingFee;
        $codInstructions = setting('shop.cod_instructions', '');

        return view('site.shop.checkout', compact(
            'items', 'subtotal', 'shippingFee', 'grandTotal', 'codInstructions'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'phone'         => ['required', 'string', 'max:20'],
            'email'         => ['nullable', 'email', 'max:255'],
            'address_line'  => ['required', 'string', 'max:500'],
            'ward'          => ['nullable', 'string', 'max:100'],
            'district'      => ['nullable', 'string', 'max:100'],
            'province'      => ['nullable', 'string', 'max:100'],
            'note'          => ['nullable', 'string', 'max:1000'],
        ]);

        $cart  = session('shop_cart', []);
        $items = CartController::resolveCartItems($cart);

        if (empty($items)) {
            return redirect()->route('shop.index')->with('toast', [
                'tone' => 'danger', 'title' => 'Error',
                'message' => 'Cart is empty. Please add items first.',
            ]);
        }

        $shippingFee = (float) setting('shop.shipping_fee_fixed', 30000);

        try {
            $order = DB::transaction(function () use ($data, $items, $shippingFee) {
                $subtotal = 0;

                // Validate stock and build order items
                $orderItemsData = [];
                foreach ($items as $item) {
                    $variant = ProductVariant::lockForUpdate()->find($item['variant_id']);

                    if (!$variant || !$variant->is_active) {
                        throw new \Exception("Variant '{$item['signature']}' is no longer available.");
                    }

                    $qty = min($item['qty'], $variant->stock_qty);
                    if ($qty < 1) {
                        throw new \Exception("'{$item['title']} - {$item['signature']}' is out of stock.");
                    }

                    $lineTotal = $variant->price * $qty;
                    $subtotal += $lineTotal;

                    // Deduct stock
                    $variant->stock_qty -= $qty;
                    $variant->save();

                    $orderItemsData[] = [
                        'product_id'               => $variant->product_id,
                        'variant_id'               => $variant->id,
                        'product_title_snapshot'    => $item['title'],
                        'variant_signature_snapshot' => $item['signature'],
                        'sku_snapshot'              => $variant->sku,
                        'unit_price_snapshot'       => $variant->price,
                        'qty'                       => $qty,
                        'line_total'                => $lineTotal,
                    ];
                }

                $grandTotal = $subtotal + $shippingFee;

                $order = Order::create([
                    'order_no'       => Order::generateOrderNo(),
                    'status'         => 'new',
                    'payment_method' => 'cod',
                    'payment_status' => 'unpaid',
                    'customer_name'  => $data['customer_name'],
                    'phone'          => $data['phone'],
                    'email'          => $data['email'] ?? null,
                    'address_line'   => $data['address_line'],
                    'ward'           => $data['ward'] ?? null,
                    'district'       => $data['district'] ?? null,
                    'province'       => $data['province'] ?? null,
                    'note'           => $data['note'] ?? null,
                    'subtotal'       => $subtotal,
                    'shipping_fee'   => $shippingFee,
                    'discount_total' => 0,
                    'grand_total'    => $grandTotal,
                ]);

                foreach ($orderItemsData as $itemData) {
                    $order->items()->create($itemData);
                }

                return $order;
            });

            // Clear cart
            session()->forget('shop_cart');

            activity_log('order.created', ['id' => $order->id, 'type' => 'Order'],
                "New order {$order->order_no} created", [
                    'order_id'   => $order->id,
                    'order_no'   => $order->order_no,
                    'grand_total' => $order->grand_total,
                ]);

            return redirect()->route('checkout.thank-you', $order->order_no);

        } catch (\Exception $e) {
            return back()->withInput()->with('toast', [
                'tone'    => 'danger',
                'title'   => 'Checkout Failed',
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function thankYou(string $orderNo)
    {
        $order = Order::where('order_no', $orderNo)->with('items')->firstOrFail();
        $codInstructions = setting('shop.cod_instructions', '');

        return view('site.shop.thank-you', compact('order', 'codInstructions'));
    }
}

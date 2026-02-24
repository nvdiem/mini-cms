<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    const STATUSES = ['new', 'confirmed', 'packed', 'shipped', 'completed', 'cancelled'];

    const STATUS_TRANSITIONS = [
        'new'       => ['confirmed', 'cancelled'],
        'confirmed' => ['packed', 'cancelled'],
        'packed'    => ['shipped'],
        'shipped'   => ['completed'],
        'completed' => [],
        'cancelled' => [],
    ];

    protected $fillable = [
        'order_no', 'status', 'payment_method', 'payment_status',
        'customer_name', 'phone', 'email', 'address_line',
        'ward', 'district', 'province', 'note',
        'subtotal', 'shipping_fee', 'discount_total', 'grand_total',
    ];

    protected $casts = [
        'subtotal'       => 'decimal:2',
        'shipping_fee'   => 'decimal:2',
        'discount_total' => 'decimal:2',
        'grand_total'    => 'decimal:2',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Generate a unique order number: ORD-YYYYMMDD-XXXXX
     */
    public static function generateOrderNo(): string
    {
        $prefix = setting('shop.order_prefix', 'ORD');
        $date   = now()->format('Ymd');

        // Count today's orders + 1
        $todayCount = self::whereDate('created_at', today())->count() + 1;
        $seq        = str_pad($todayCount, 5, '0', STR_PAD_LEFT);

        return "{$prefix}-{$date}-{$seq}";
    }

    /**
     * Check if a status transition is allowed.
     */
    public function canTransitionTo(string $newStatus): bool
    {
        $allowed = self::STATUS_TRANSITIONS[$this->status] ?? [];
        return in_array($newStatus, $allowed);
    }

    /**
     * Status badge color helper.
     */
    public function statusColor(): string
    {
        return match ($this->status) {
            'new'       => 'bg-blue-100 text-blue-700 border-blue-200',
            'confirmed' => 'bg-indigo-100 text-indigo-700 border-indigo-200',
            'packed'    => 'bg-amber-100 text-amber-700 border-amber-200',
            'shipped'   => 'bg-cyan-100 text-cyan-700 border-cyan-200',
            'completed' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
            'cancelled' => 'bg-red-100 text-red-700 border-red-200',
            default     => 'bg-slate-100 text-slate-600 border-slate-200',
        };
    }
}

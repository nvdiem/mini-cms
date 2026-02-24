<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Product;
use App\Models\ProductOption;
use App\Models\ProductOptionValue;
use App\Models\ProductVariant;
use App\Models\ProductVariantValue;
use App\Models\Order;
use App\Models\OrderItem;

class ShopSeeder extends Seeder
{
    public function run()
    {
        // 1. Settings (Tùy chọn, vì có thể đã seed)
        setting_set('shop.shipping_fee_fixed', '30000', 'number');
        setting_set('shop.cod_instructions', 'Thanh toán khi nhận hàng (COD). Nhân viên sẽ liên hệ xác nhận.', 'text');

        // Sizes & Colors
        $sizes = ['S', 'M', 'L'];
        $colors = ['Red', 'Black', 'White'];

        // 2. Products Data
        $productData = [
            ['title' => 'Áo thun Basic', 'slug' => 'ao-thun-basic', 'sizes' => $sizes, 'colors' => $colors, 'base_price' => 150000, 'active' => true],
            ['title' => 'Áo thun Graphic', 'slug' => 'ao-thun-graphic', 'sizes' => $sizes, 'colors' => $colors, 'base_price' => 180000, 'active' => true],
            ['title' => 'Áo thun Vintage', 'slug' => 'ao-thun-vintage', 'sizes' => $sizes, 'colors' => $colors, 'base_price' => 200000, 'active' => true],
            ['title' => 'Hoodie Classic', 'slug' => 'hoodie-classic', 'sizes' => $sizes, 'colors' => ['Black', 'White'], 'base_price' => 350000, 'active' => true],
            ['title' => 'Mũ lưỡi trai', 'slug' => 'mu-luoi-trai', 'sizes' => ['Free Size'], 'colors' => ['Black', 'White'], 'base_price' => 100000, 'active' => true],
            ['title' => 'Áo thun Lỗi Tạm Ẩn', 'slug' => 'ao-thun-loi', 'sizes' => ['M'], 'colors' => ['Black'], 'base_price' => 100000, 'active' => false],
        ];

        $allVariants = [];

        foreach ($productData as $idx => $data) {
            $p = Product::create([
                'title' => $data['title'],
                'slug' => $data['slug'] . '-' . rand(100, 999), // Để chạy seeder nhiều lần không lỗi unique
                'excerpt' => 'Mô tả ngắn cho ' . $data['title'],
                'description_html' => '<p>Mô tả chi tiết cho ' . $data['title'] . '</p>',
                'is_active' => $data['active'],
                'published_at' => $data['active'] ? now()->subDays(rand(1, 10)) : null,
                'seo_title' => $data['title'] . ' - Mua ngay',
            ]);

            $optSize = ProductOption::create(['product_id' => $p->id, 'name' => 'Size', 'label' => 'Size', 'sort_order' => 1]);
            $optColor = ProductOption::create(['product_id' => $p->id, 'name' => 'Color', 'label' => 'Color', 'sort_order' => 2]);

            $sizeVals = [];
            foreach ($data['sizes'] as $s) {
                $sizeVals[$s] = ProductOptionValue::create(['product_option_id' => $optSize->id, 'value' => $s])->id;
            }
            $colorVals = [];
            foreach ($data['colors'] as $c) {
                $colorVals[$c] = ProductOptionValue::create(['product_option_id' => $optColor->id, 'value' => $c])->id;
            }

            foreach ($data['sizes'] as $s) {
                foreach ($data['colors'] as $c) {
                    // Logic giá khác nhau
                    $sizeSurcharge = $s === 'M' ? 10000 : ($s === 'L' ? 20000 : 0);
                    $colorSurcharge = $c === 'Red' ? 10000 : 0;
                    $price = $data['base_price'] + $sizeSurcharge + $colorSurcharge;
                    
                    // Stock & Active rules
                    $stock = rand(5, 50);
                    if ($idx === 0 && $s === 'S') $stock = 0; // Áo thun Basic Size S -> Out of Stock
                    
                    $isActive = true;
                    if ($idx === 1 && $c === 'White') $isActive = false; // Áo thun Graphic màu White -> Inactive

                    $var = ProductVariant::create([
                        'product_id' => $p->id,
                        'sku' => strtoupper(Str::slug($p->slug . '-' . $s . '-' . $c)) . rand(10,99),
                        'price' => $price,
                        'compare_at_price' => $price + 50000,
                        'stock_qty' => $stock,
                        'is_active' => $isActive,
                        'option_signature' => "size:$s|color:$c",
                    ]);

                    ProductVariantValue::insert([
                        ['variant_id' => $var->id, 'option_value_id' => $sizeVals[$s]],
                        ['variant_id' => $var->id, 'option_value_id' => $colorVals[$c]],
                    ]);

                    $allVariants[] = $var;
                }
            }
        }

        // 3. Orders (12 orders: 3 new, 3 confirmed, 2 packed, 2 shipped, 1 completed, 1 cancelled)
        $statuses = ['new'=>3, 'confirmed'=>3, 'packed'=>2, 'shipped'=>2, 'completed'=>1, 'cancelled'=>1];
        
        foreach ($statuses as $status => $count) {
            for ($i = 0; $i < $count; $i++) {
                $itemCount = rand(1, 3);
                $subtotal = 0;
                
                $order = Order::create([
                    'order_no' => Order::generateOrderNo() . '-' . rand(100,9999), // Tránh đụng mã
                    'status' => $status,
                    'payment_method' => 'cod',
                    'payment_status' => $status === 'completed' ? 'paid' : 'pending',
                    'customer_name' => 'Khách Hàng ' . rand(1, 100),
                    'phone' => '090' . rand(1000000, 9999999),
                    'email' => rand(0, 1) ? "test$status$i@example.com" : null, // Test NULL email
                    'address_line' => '123 Đường Test',
                    'ward' => 'Phường 1',
                    'district' => 'Quận 1',
                    'province' => 'TP HCM',
                    'subtotal' => 0,
                    'shipping_fee' => 30000,
                    'grand_total' => 0,
                ]);

                for ($j = 0; $j < $itemCount; $j++) {
                    // Cố tình chọn 1 số variant out_of_stock cho order cancelled
                    if ($status === 'cancelled' && $j === 0) {
                        $v = collect($allVariants)->firstWhere('stock_qty', 0) ?? $allVariants[0];
                    } else {
                        $v = $allVariants[array_rand($allVariants)];
                    }

                    $qty = rand(1, 2);
                    $lineTotal = $v->price * $qty;
                    $subtotal += $lineTotal;

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $v->product_id,
                        'variant_id' => $v->id,
                        'qty' => $qty,
                        'unit_price_snapshot' => $v->price,
                        'product_title_snapshot' => $v->product->title,
                        'variant_signature_snapshot' => $v->option_signature,
                        'sku_snapshot' => $v->sku,
                        'line_total' => $lineTotal,
                    ]);
                }

                $order->update([
                    'subtotal' => $subtotal,
                    'grand_total' => $subtotal + 30000,
                ]);
            }
        }
    }
}

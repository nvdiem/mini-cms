<?php

namespace App\Http\Controllers\Admin\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ShopSettingsController extends Controller
{
    private const KEYS = [
        'shop.shipping_fee_fixed' => ['label' => 'Shipping Fee (VND)', 'type' => 'number', 'default' => '30000'],
        'shop.cod_instructions'   => ['label' => 'COD Instructions', 'type' => 'textarea', 'default' => ''],
        'shop.currency'           => ['label' => 'Currency', 'type' => 'text', 'default' => 'VND'],
        'shop.order_prefix'       => ['label' => 'Order Prefix', 'type' => 'text', 'default' => 'ORD'],
    ];

    public function index()
    {
        $settings = [];
        foreach (self::KEYS as $key => $meta) {
            $settings[$key] = [
                'value'   => setting($key, $meta['default']),
                'label'   => $meta['label'],
                'type'    => $meta['type'],
            ];
        }

        return view('admin.shop.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'shop_shipping_fee_fixed' => ['nullable', 'numeric', 'min:0'],
            'shop_cod_instructions'   => ['nullable', 'string'],
            'shop_currency'           => ['nullable', 'string', 'max:10'],
            'shop_order_prefix'       => ['nullable', 'string', 'max:20'],
        ]);

        // Map form fields back to setting keys
        $map = [
            'shop_shipping_fee_fixed' => 'shop.shipping_fee_fixed',
            'shop_cod_instructions'   => 'shop.cod_instructions',
            'shop_currency'           => 'shop.currency',
            'shop_order_prefix'       => 'shop.order_prefix',
        ];

        foreach ($map as $formKey => $settingKey) {
            if (array_key_exists($formKey, $validated)) {
                setting_set($settingKey, $validated[$formKey] ?? '');
            }
        }

        activity_log('shop.settings.updated', null, "Updated shop settings");

        return redirect()->route('admin.shop.settings.index')
            ->with('toast', [
                'tone'    => 'success',
                'title'   => 'Settings saved',
                'message' => 'Shop settings have been updated.',
            ]);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = Setting::pluck('value', 'key')->toArray();
        $media = Media::latest()->take(50)->get();
        
        return view('admin.settings.index', compact('settings', 'media'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'site_name' => ['nullable', 'string', 'max:255'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'timezone' => ['nullable', 'string', 'max:100'],
            'posts_per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'default_post_status' => ['nullable', 'in:draft,review,published'],
            'seo_default_title' => ['nullable', 'string', 'max:255'],
            'seo_default_description' => ['nullable', 'string'],
            'seo_default_keywords' => ['nullable', 'string', 'max:255'],
            'logo_media_id' => ['nullable', 'integer'],
            'contact_recipient_email' => ['nullable', 'email'],
        ]);

        foreach ($validated as $key => $value) {
            $type = in_array($key, ['posts_per_page', 'logo_media_id']) ? 'number' : 'text';
            setting_set($key, $value, $type);
        }

        // Clear all settings cache
        $allSettings = Setting::pluck('key');
        foreach ($allSettings as $key) {
            Cache::forget("setting_{$key}");
        }

        activity_log('settings.updated', null, "Updated system settings", ['count' => count($validated)]);

        return redirect()->route('admin.settings.index')
            ->with('toast', [
                'tone' => 'success',
                'title' => 'Settings saved',
                'message' => 'Your settings have been updated successfully.'
            ]);
    }
}

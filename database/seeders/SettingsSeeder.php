<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            ['key' => 'site_name', 'value' => 'PointOne CMS', 'type' => 'text'],
            ['key' => 'tagline', 'value' => 'Built with Laravel', 'type' => 'text'],
            ['key' => 'timezone', 'value' => 'UTC', 'type' => 'text'],
            ['key' => 'posts_per_page', 'value' => '10', 'type' => 'number'],
            ['key' => 'default_post_status', 'value' => 'draft', 'type' => 'text'],
            ['key' => 'seo_default_title', 'value' => 'PointOne CMS - Your Modern Content Platform', 'type' => 'text'],
            ['key' => 'seo_default_description', 'value' => 'A modern content management system built with Laravel and Blade templates.', 'type' => 'text'],
            ['key' => 'seo_default_keywords', 'value' => 'cms, laravel, content management', 'type' => 'text'],
            ['key' => 'logo_media_id', 'value' => null, 'type' => 'number'],
            ['key' => 'contact_recipient_email', 'value' => null, 'type' => 'text'],
        ];

        foreach ($defaults as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                ['value' => $setting['value'], 'type' => $setting['type']]
            );
        }
    }
}

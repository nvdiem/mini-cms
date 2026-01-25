<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            ['key' => 'site_name', 'value' => 'CareerPath', 'type' => 'text'],
            ['key' => 'tagline', 'value' => 'Your Partner in Professional Growth', 'type' => 'text'],
            ['key' => 'timezone', 'value' => 'UTC', 'type' => 'text'],
            ['key' => 'posts_per_page', 'value' => '10', 'type' => 'number'],
            ['key' => 'default_post_status', 'value' => 'draft', 'type' => 'text'],
            ['key' => 'seo_default_title', 'value' => 'CareerPath - Professional Career Consulting & Training', 'type' => 'text'],
            ['key' => 'seo_default_description', 'value' => 'CareerPath helps professionals achieve their career goals through expert consulting, resume review, interview coaching, and skills training.', 'type' => 'text'],
            ['key' => 'seo_default_keywords', 'value' => 'career consulting, resume review, interview coaching, career advice, job search, professional development', 'type' => 'text'],
            ['key' => 'logo_media_id', 'value' => null, 'type' => 'number'],
            ['key' => 'contact_recipient_email', 'value' => 'contact@careerpath.example', 'type' => 'text'],
        ];

        foreach ($defaults as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                ['value' => $setting['value'], 'type' => $setting['type']]
            );
        }
    }
}

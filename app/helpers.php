<?php

use Illuminate\Support\Facades\Cache;
use App\Models\Setting;

if (!function_exists('setting')) {
    /**
     * Get a setting value from the database with caching
     */
    function setting(string $key, $default = null)
    {
        return Cache::remember("setting_{$key}", 600, function() use ($key, $default) {
            $setting = Setting::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }
}

if (!function_exists('setting_set')) {
    /**
     * Set a setting value and clear its cache
     */
    function setting_set(string $key, $value, string $type = 'text')
    {
        Setting::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'type' => $type]
        );
        Cache::forget("setting_{$key}");
        return true;
    }
}

if (!function_exists('activity_log')) {
    /**
     * Log a system activity
     */
    function activity_log(string $type, $subject = null, ?string $message = null, array $meta = [])
    {
        try {
            $userId = auth()->id();
            
            // Auto-generate message if missing
            if (!$message) {
                $parts = explode('.', $type);
                $action = $parts[1] ?? $parts[0];
                $modelName = 'Item';
                
                if (is_object($subject)) {
                    $modelName = class_basename($subject);
                } elseif (is_array($subject) && isset($subject['type'])) {
                    $modelName = $subject['type'];
                }
                
                $message = ucfirst($action) . " " . $modelName;
            }

            // Extract subject ID and Type
            $subjectId = null;
            $subjectType = null;

            if (is_object($subject)) {
                $subjectId = $subject->id ?? null;
                $subjectType = get_class($subject);
                
                // Auto-add title to meta if available
                if (empty($meta['title'])) {
                    if (isset($subject->title)) $meta['title'] = $subject->title;
                    elseif (isset($subject->name)) $meta['title'] = $subject->name;
                }
                
                // Auto-add routing info if not present
                if (empty($meta['url'])) {
                    // Try to guess admin edit route
                    $resource = strtolower(class_basename($subject)) . 's'; // e.g. posts
                    // Simple heuristic for existing modules
                    if (in_array($resource, ['posts', 'pages', 'users', 'leads'])) {
                         $meta['url'] = route("admin.{$resource}.edit", $subjectId);
                    }
                }

            } elseif (is_array($subject)) {
                $subjectId = $subject['id'] ?? null;
                $subjectType = $subject['type'] ?? null;
            }

            \App\Models\ActivityLog::create([
                'user_id' => $userId,
                'type' => $type,
                'subject_id' => $subjectId,
                'subject_type' => $subjectType,
                'message' => $message,
                'meta' => $meta,
            ]);
        } catch (\Throwable $e) {
            // Silently fail to prevent breaking main flow
            try {
                logger()->error("Activity Log Failed: " . $e->getMessage());
            } catch (\Throwable $ex) {}
        }
    }
}

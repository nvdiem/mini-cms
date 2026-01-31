<?php

namespace App\Http\Controllers;

use App\Models\PagePackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class PublicPageBuilderController extends Controller
{
    /**
     * Serve published page package content directly
     */
    public function serve($slug, $path = null)
    {
        $package = PagePackage::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        // If path is null/empty, defaults to the entry file (e.g. index.html)
        $targetFile = $path ? $path : $package->entry_file;

        // Prevent path traversal
        if (Str::contains($targetFile, '..')) {
            abort(403, 'Forbidden');
        }

        // Construct full path
        $fullPath = public_path("{$package->public_dir}/{$targetFile}");

        if (!File::exists($fullPath)) {
            abort(404);
        }

        // Return the file with proper headers
        return response()->file($fullPath);
    }
}

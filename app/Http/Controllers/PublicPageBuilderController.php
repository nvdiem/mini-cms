<?php

namespace App\Http\Controllers;

use App\Models\PagePackage;

class PublicPageBuilderController extends Controller
{
    /**
     * Show published page package
     */
    public function show($slug)
    {
        $package = PagePackage::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        // Redirect to the public directory entry file
        $publicUrl = asset("{$package->public_dir}/{$package->entry_file}");
        
        return redirect($publicUrl);
    }
}

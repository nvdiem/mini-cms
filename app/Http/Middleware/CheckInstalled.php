<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class CheckInstalled
{
    /**
     * Handle an incoming request.
     * Redirect to installer if not installed.
     */
    public function handle(Request $request, Closure $next)
    {
        $isInstalled = File::exists(storage_path('installed'));

        // If accessing installer routes
        if ($request->is('install*')) {
            // Block installer if already installed
            if ($isInstalled) {
                abort(404);
            }
            return $next($request);
        }

        // For all other routes, redirect to installer if not installed
        if (!$isInstalled && !$request->is('install*')) {
            return redirect()->route('installer.requirements');
        }

        return $next($request);
    }
}

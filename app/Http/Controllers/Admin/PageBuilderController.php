<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PagePackage;
use App\Services\PackageUploadService;
use Illuminate\Http\Request;

class PageBuilderController extends Controller
{
    private PackageUploadService $uploadService;

    public function __construct(PackageUploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    /**
     * Display list of page packages
     */
    public function index()
    {
        $packages = PagePackage::with('creator')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.page-builder.index', compact('packages'));
    }

    /**
     * Show upload form
     */
    public function create()
    {
        return view('admin.page-builder.create');
    }

    /**
     * Store new package - PR-06: Refactored to use PackageUploadService
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:page_packages,slug', 'regex:/^[a-z0-9-]+$/'],
            'zip_file' => ['required', 'file', 'mimes:zip', 'max:20480'],
            'wire_contact' => ['boolean'],
            'wire_selector' => ['nullable', 'string', 'max:255'],
        ]);

        $result = $this->uploadService->upload(
            [
                'name' => $validated['name'],
                'slug' => $validated['slug'],
                'wire_contact' => $request->boolean('wire_contact', true),
                'wire_selector' => $validated['wire_selector'] ?? null,
            ],
            $request->file('zip_file'),
            auth()->id()
        );

        if ($result->failed()) {
            return back()->withErrors(['zip_file' => $result->error]);
        }

        return redirect()
            ->route('admin.page-builder.show', $result->package->id)
            ->with('toast', [
                'tone' => 'success',
                'title' => 'Package created!',
                'message' => "Page package '{$result->package->name}' has been published successfully."
            ]);
    }

    /**
     * Show package details
     */
    public function show($id)
    {
        $package = PagePackage::with('creator')->findOrFail($id);
        return view('admin.page-builder.show', compact('package'));
    }

    /**
     * Activate a package (deactivate others with same slug)
     */
    public function activate($id)
    {
        $package = PagePackage::findOrFail($id);

        // Deactivate other packages with same slug
        PagePackage::where('slug', $package->slug)
            ->where('id', '!=', $package->id)
            ->update(['is_active' => false]);

        // Activate this package
        $package->is_active = true;
        $package->save();

        activity_log(
            'pagebuilder.activated',
            $package,
            "Activated page package '{$package->name}' (version: {$package->version})"
        );

        return back()->with('toast', [
            'tone' => 'success',
            'title' => 'Package activated!',
            'message' => "Package '{$package->name}' is now active."
        ]);
    }
}


<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PagePackage;
use App\Services\ZipExtractService;
use App\Services\PublishService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PageBuilderController extends Controller
{
    private $zipExtractor;
    private $publisher;

    public function __construct(ZipExtractService $zipExtractor, PublishService $publisher)
    {
        $this->zipExtractor = $zipExtractor;
        $this->publisher = $publisher;
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
     * Store new package
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:page_packages,slug', 'regex:/^[a-z0-9-]+$/'],
            'zip_file' => ['required', 'file', 'mimes:zip', 'max:20480'], // 20MB
            'wire_contact' => ['boolean'],
            'wire_selector' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            // Create package record first to get ID
            $package = new PagePackage();
            $package->name = $validated['name'];
            $package->slug = $validated['slug'];
            $package->wire_contact = $request->boolean('wire_contact', true);
            $package->wire_selector = $validated['wire_selector'] ?? '[data-contact-form],#contactForm,.js-contact';
            $package->created_by = auth()->id();
            $package->version = 'pending';
            $package->zip_path = 'pending';
            $package->public_dir = 'pending';
            $package->save();

            // Store ZIP file
            $zipPath = storage_path("app/pagebuilder/zips/{$package->id}.zip");
            $zipDir = dirname($zipPath);
            if (!is_dir($zipDir)) {
                mkdir($zipDir, 0755, true);
            }
            $request->file('zip_file')->move($zipDir, "{$package->id}.zip");

            // Extract ZIP to temporary directory
            $tempExtractPath = storage_path("app/pagebuilder/temp/{$package->id}");
            $extractResult = $this->zipExtractor->extract($zipPath, $tempExtractPath);

            if (!$extractResult['success']) {
                // Delete package and ZIP on failure
                $package->delete();
                @unlink($zipPath);
                return back()->withErrors(['zip_file' => $extractResult['error']]);
            }

            // Verify entry file exists
            if (!$this->zipExtractor->verifyEntryFile($tempExtractPath, 'index.html')) {
                $package->delete();
                @unlink($zipPath);
                $this->deleteDirectory($tempExtractPath);
                return back()->withErrors(['zip_file' => 'Entry file (index.html) not found in ZIP']);
            }

            // Generate version and publish
            $version = $this->publisher->generateVersion($zipPath);
            $publishResult = $this->publisher->publish(
                $tempExtractPath,
                $package->slug,
                $version,
                $package->wire_contact,
                $package->wire_selector
            );

            if (!$publishResult['success']) {
                $package->delete();
                @unlink($zipPath);
                $this->deleteDirectory($tempExtractPath);
                return back()->withErrors(['zip_file' => $publishResult['error']]);
            }

            // Update package with final details
            $package->version = $version;
            $package->zip_path = "pagebuilder/zips/{$package->id}.zip";
            $package->public_dir = $publishResult['public_dir'];
            $package->is_active = true; // First package is active by default
            $package->save();

            // Clean up temp directory
            $this->deleteDirectory($tempExtractPath);

            // Log activity
            activity_log(
                'pagebuilder.created',
                $package,
                "Created page package '{$package->name}' (slug: {$package->slug})"
            );

            return redirect()
                ->route('admin.page-builder.show', $package->id)
                ->with('toast', [
                    'tone' => 'success',
                    'title' => 'Package created!',
                    'message' => "Page package '{$package->name}' has been published successfully."
                ]);

        } catch (\Exception $e) {
            if (isset($package) && $package->exists) {
                $package->delete();
            }
            if (isset($zipPath) && file_exists($zipPath)) {
                @unlink($zipPath);
            }
            if (isset($tempExtractPath) && is_dir($tempExtractPath)) {
                $this->deleteDirectory($tempExtractPath);
            }

            return back()->withErrors(['zip_file' => 'Upload failed: ' . $e->getMessage()]);
        }
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

    /**
     * Delete directory recursively
     */
    private function deleteDirectory($dir)
    {
        if (!is_dir($dir)) {
            return;
        }

        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            is_dir($path) ? $this->deleteDirectory($path) : unlink($path);
        }
        rmdir($dir);
    }
}

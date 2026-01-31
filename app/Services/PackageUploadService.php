<?php

namespace App\Services;

use App\Models\PagePackage;
use Illuminate\Http\UploadedFile;

/**
 * PR-06: Service to handle PageBuilder package uploads
 * Extracted from PageBuilderController for better testability and maintainability
 */
class PackageUploadService
{
    private ZipExtractService $zipExtractor;
    private PublishService $publisher;

    public function __construct(ZipExtractService $zipExtractor, PublishService $publisher)
    {
        $this->zipExtractor = $zipExtractor;
        $this->publisher = $publisher;
    }

    /**
     * Upload and publish a page package
     *
     * @return PackageUploadResult
     */
    public function upload(array $data, UploadedFile $zipFile, int $userId): PackageUploadResult
    {
        try {
            // Create package record first to get ID
            $package = new PagePackage();
            $package->name = $data['name'];
            $package->slug = $data['slug'];
            $package->wire_contact = $data['wire_contact'] ?? true;
            $package->wire_selector = $data['wire_selector'] ?? '[data-contact-form],#contactForm,.js-contact';
            $package->created_by = $userId;
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
            $zipFile->move($zipDir, "{$package->id}.zip");

            // Extract ZIP to temporary directory
            $tempExtractPath = storage_path("app/pagebuilder/temp/{$package->id}");
            $extractResult = $this->zipExtractor->extract($zipPath, $tempExtractPath);

            if (!$extractResult['success']) {
                $this->cleanup($package, $zipPath, $tempExtractPath);
                return PackageUploadResult::failure($extractResult['error']);
            }

            // Verify entry file exists
            if (!$this->zipExtractor->verifyEntryFile($tempExtractPath, 'index.html')) {
                $this->cleanup($package, $zipPath, $tempExtractPath);
                return PackageUploadResult::failure('Entry file (index.html) not found in ZIP');
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
                $this->cleanup($package, $zipPath, $tempExtractPath);
                return PackageUploadResult::failure($publishResult['error']);
            }

            // Update package with final details
            $package->version = $version;
            $package->zip_path = "pagebuilder/zips/{$package->id}.zip";
            $package->public_dir = $publishResult['public_dir'];
            $package->is_active = true;
            $package->save();

            // Clean up temp directory
            $this->deleteDirectory($tempExtractPath);

            // Log activity
            activity_log(
                'pagebuilder.created',
                $package,
                "Created page package '{$package->name}' (slug: {$package->slug})"
            );

            return PackageUploadResult::success($package);

        } catch (\Exception $e) {
            if (isset($package) && $package->exists) {
                $this->cleanup($package, $zipPath ?? null, $tempExtractPath ?? null);
            }
            return PackageUploadResult::failure('Upload failed: ' . $e->getMessage());
        }
    }

    /**
     * Cleanup on failure
     */
    private function cleanup(PagePackage $package, ?string $zipPath, ?string $tempPath): void
    {
        $package->delete();
        
        if ($zipPath && file_exists($zipPath)) {
            @unlink($zipPath);
        }
        
        if ($tempPath && is_dir($tempPath)) {
            $this->deleteDirectory($tempPath);
        }
    }

    /**
     * Delete directory recursively
     */
    private function deleteDirectory(string $dir): void
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
    /**
     * Update an existing package with a new ZIP file
     */
    public function update(PagePackage $package, UploadedFile $zipFile): PackageUploadResult
    {
        $oldZipPath = $package->zip_path ? storage_path("app/{$package->zip_path}") : null;
        $oldPublicDir = $package->public_dir ? public_path($package->public_dir) : null;
        $newZipPath = null;
        $tempExtractPath = null;

        try {
            // Store NEW ZIP file (overwrite existing or create new)
            $newZipPath = storage_path("app/pagebuilder/zips/{$package->id}.zip");
            $zipDir = dirname($newZipPath);
            if (!is_dir($zipDir)) {
                mkdir($zipDir, 0755, true);
            }
            // Move and overwrite
            $zipFile->move($zipDir, "{$package->id}.zip");

            // Extract ZIP to temporary directory
            $tempExtractPath = storage_path("app/pagebuilder/temp/{$package->id}_" . time());
            $extractResult = $this->zipExtractor->extract($newZipPath, $tempExtractPath);

            if (!$extractResult['success']) {
                $this->cleanupTemp($tempExtractPath);
                return PackageUploadResult::failure($extractResult['error']);
            }

            // Verify entry file exists
            if (!$this->zipExtractor->verifyEntryFile($tempExtractPath, 'index.html')) {
                $this->cleanupTemp($tempExtractPath);
                return PackageUploadResult::failure('Entry file (index.html) not found in ZIP');
            }

            // Generate version and publish
            $version = $this->publisher->generateVersion($newZipPath);
            
            // Start Clean Publishing (New Folder)
            $publishResult = $this->publisher->publish(
                $tempExtractPath,
                $package->slug,
                $version,
                $package->wire_contact,
                $package->wire_selector
            );

            if (!$publishResult['success']) {
                $this->cleanupTemp($tempExtractPath);
                return PackageUploadResult::failure($publishResult['error']);
            }

            // Success! Logic to removing OLD public directory if it's different
            if ($oldPublicDir && is_dir($oldPublicDir) && $oldPublicDir !== public_path($publishResult['public_dir'])) {
                $this->deleteDirectory($oldPublicDir);
            }

            // Update package
            $package->version = $version;
            $package->zip_path = "pagebuilder/zips/{$package->id}.zip";
            $package->public_dir = $publishResult['public_dir'];
            $package->is_active = true;
            $package->touch();
            $package->save();

            // Clean up temp
            $this->deleteDirectory($tempExtractPath);

            // Log activity
            activity_log(
                'pagebuilder.updated',
                $package,
                "Updated page package '{$package->name}' with new version ({$package->version})"
            );

            return PackageUploadResult::success($package);

        } catch (\Exception $e) {
            if ($tempExtractPath) {
                $this->cleanupTemp($tempExtractPath);
            }
            return PackageUploadResult::failure('Update failed: ' . $e->getMessage());
        }
    }

    private function cleanupTemp(?string $path): void {
        if ($path && is_dir($path)) {
            $this->deleteDirectory($path);
        }
    }

    public function delete(PagePackage $package): void {
        // Delete Zip
        $zipPath = storage_path("app/pagebuilder/zips/{$package->id}.zip");
        if (file_exists($zipPath)) {
            @unlink($zipPath);
        }

        // Delete Public Dir
        if ($package->public_dir) {
            $publicDir = public_path($package->public_dir);
            // Safety check: ensure we are deleting inside pagebuilder
            if (is_dir($publicDir) && str_contains($publicDir, 'pagebuilder')) {
                $this->deleteDirectory($publicDir);
            }
        }

        // Delete DB record
        $package->delete();

         activity_log(
            'pagebuilder.deleted',
            null, // Subject deleted
            "Deleted page package '{$package->name}'"
        );
    }
}

/**
 * Result object for package upload
 */
class PackageUploadResult
{
    public bool $success;
    public ?PagePackage $package;
    public ?string $error;

    private function __construct(bool $success, ?PagePackage $package, ?string $error)
    {
        $this->success = $success;
        $this->package = $package;
        $this->error = $error;
    }

    public static function success(PagePackage $package): self
    {
        return new self(true, $package, null);
    }

    public static function failure(string $error): self
    {
        return new self(false, null, $error);
    }

    public function failed(): bool
    {
        return !$this->success;
    }
}

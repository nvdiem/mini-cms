<?php

namespace App\Services;

use Exception;
use ZipArchive;

class ZipExtractService
{
    // Allowed file extensions
    private const ALLOWED_EXTENSIONS = [
        'html', 'htm', 'css', 'js', 'json', 'map',
        'png', 'jpg', 'jpeg', 'gif', 'svg', 'webp', 'ico',
        'woff', 'woff2', 'ttf', 'otf', 'eot'
    ];

    // Dangerous file extensions to block
    private const BLOCKED_EXTENSIONS = [
        'php', 'phtml', 'phar', 'env', 'htaccess', 'htpasswd',
        'exe', 'sh', 'bat', 'cmd', 'com', 'dll', 'so',
        'blade.php', 'config', 'ini'
    ];

    // Limits
    private const MAX_FILES = 500;
    private const MAX_TOTAL_SIZE = 52428800; // 50MB in bytes
    private const MAX_ZIP_SIZE = 20971520; // 20MB in bytes

    /**
     * Validate and extract ZIP file safely
     *
     * @param string $zipPath Full path to ZIP file
     * @param string $extractTo Directory to extract to
     * @return array ['success' => bool, 'path' => string|null, 'error' => string|null]
     */
    public function extract(string $zipPath, string $extractTo): array
    {
        try {
            // Validate file exists
            if (!file_exists($zipPath)) {
                throw new Exception('ZIP file not found');
            }

            // Validate file size
            if (filesize($zipPath) > self::MAX_ZIP_SIZE) {
                throw new Exception('ZIP file too large (max 20MB)');
            }

            // Validate MIME type
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $zipPath);
            finfo_close($finfo);

            if (!in_array($mimeType, ['application/zip', 'application/x-zip-compressed'])) {
                throw new Exception('Invalid file type. Must be a ZIP file');
            }

            // Open ZIP
            $zip = new ZipArchive();
            $result = $zip->open($zipPath);

            if ($result !== true) {
                throw new Exception('Failed to open ZIP file');
            }

            // Validate file count
            if ($zip->numFiles > self::MAX_FILES) {
                $zip->close();
                throw new Exception("Too many files in ZIP (max " . self::MAX_FILES . ")");
            }

            // Create extraction directory
            if (!is_dir($extractTo)) {
                mkdir($extractTo, 0755, true);
            }

            $totalSize = 0;
            $files = [];

            // Validate each file before extraction
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $stat = $zip->statIndex($i);
                $filename = $stat['name'];

                // Skip directories
                if (substr($filename, -1) === '/') {
                    continue;
                }

                // Check for path traversal
                if ($this->hasPathTraversal($filename)) {
                    $zip->close();
                    throw new Exception("Security violation: Path traversal detected in '{$filename}'");
                }

                // Check file extension
                $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

                if (in_array($extension, self::BLOCKED_EXTENSIONS)) {
                    $zip->close();
                    throw new Exception("Blocked file type detected: .{$extension}");
                }

                if (!empty($extension) && !in_array($extension, self::ALLOWED_EXTENSIONS)) {
                    $zip->close();
                    throw new Exception("File type not allowed: .{$extension}");
                }

                // Check total size
                $totalSize += $stat['size'];
                if ($totalSize > self::MAX_TOTAL_SIZE) {
                    $zip->close();
                    throw new Exception("Total extracted size exceeds limit (max 50MB)");
                }

                $files[] = $filename;
            }

            // Extract all files
            $zip->extractTo($extractTo);
            $zip->close();

            return [
                'success' => true,
                'path' => $extractTo,
                'files' => $files,
                'error' => null
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'path' => null,
                'files' => [],
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Check if filename contains path traversal attempts
     */
    private function hasPathTraversal(string $filename): bool
    {
        // Check for .. in path
        if (strpos($filename, '..') !== false) {
            return true;
        }

        // Check for absolute paths
        if (substr($filename, 0, 1) === '/' || substr($filename, 1, 1) === ':') {
            return true;
        }

        // Normalize and check
        $normalized = str_replace('\\', '/', $filename);
        if (strpos($normalized, '../') !== false || strpos($normalized, '/..') !== false) {
            return true;
        }

        return false;
    }

    /**
     * Verify entry file exists in extracted directory
     */
    public function verifyEntryFile(string $extractedPath, string $entryFile = 'index.html'): bool
    {
        $entryPath = $extractedPath . '/' . $entryFile;
        return file_exists($entryPath) && is_file($entryPath);
    }
}

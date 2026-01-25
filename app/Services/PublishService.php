<?php

namespace App\Services;

use Exception;

class PublishService
{
    /**
     * Publish extracted files to public directory with optional contact form wiring
     *
     * @param string $extractedPath Path to extracted files
     * @param string $slug Package slug
     * @param string $version Version hash
     * @param bool $wireContact Whether to inject contact form JS
     * @param string|null $wireSelector CSS selector for contact form
     * @param string $entryFile Entry file name (default: index.html)
     * @return array ['success' => bool, 'public_dir' => string|null, 'error' => string|null]
     */
    public function publish(
        string $extractedPath,
        string $slug,
        string $version,
        bool $wireContact = true,
        ?string $wireSelector = null,
        string $entryFile = 'index.html'
    ): array {
        try {
            // Default selector
            if (empty($wireSelector)) {
                $wireSelector = '[data-contact-form],#contactForm,.js-contact';
            }

            // Create public directory path
            $publicDir = "pagebuilder/{$slug}/{$version}";
            $fullPublicPath = public_path($publicDir);

            // Create directory if not exists
            if (!is_dir($fullPublicPath)) {
                mkdir($fullPublicPath, 0755, true);
            }

            // Copy all files recursively
            $this->copyDirectory($extractedPath, $fullPublicPath);

            // Inject contact form wiring if enabled
            if ($wireContact) {
                $entryPath = $fullPublicPath . '/' . $entryFile;
                if (file_exists($entryPath)) {
                    $this->injectContactWiring($entryPath, $slug, $wireSelector);
                }
            }

            return [
                'success' => true,
                'public_dir' => $publicDir,
                'error' => null
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'public_dir' => null,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Copy directory recursively
     */
    private function copyDirectory(string $source, string $destination): void
    {
        if (!is_dir($destination)) {
            mkdir($destination, 0755, true);
        }

        $dir = opendir($source);
        while (($file = readdir($dir)) !== false) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $srcPath = $source . '/' . $file;
            $destPath = $destination . '/' . $file;

            if (is_dir($srcPath)) {
                $this->copyDirectory($srcPath, $destPath);
            } else {
                copy($srcPath, $destPath);
            }
        }
        closedir($dir);
    }

    /**
     * Inject contact form wiring JavaScript into HTML file
     */
    private function injectContactWiring(string $htmlPath, string $slug, string $selector): void
    {
        $html = file_get_contents($htmlPath);

        // Meta tags to inject before </head>
        $metaTags = <<<HTML

    <!-- CMS Contact Integration -->
    <meta name="cms-contact-endpoint" content="/lead">
    <meta name="cms-package-slug" content="{$slug}">
HTML;

        // JavaScript to inject before </body>
        $script = <<<'JS'

    <!-- CMS Contact Form Wiring -->
    <script>
    (function() {
        'use strict';
        
        const SELECTOR = '{{SELECTOR}}';
        const SLUG = '{{SLUG}}';
        
        document.addEventListener('DOMContentLoaded', function() {
            const forms = document.querySelectorAll(SELECTOR);
            
            forms.forEach(function(form) {
                // Set form attributes
                form.method = 'POST';
                form.action = '/lead';
                
                // Add honeypot field (hidden)
                if (!form.querySelector('input[name="website"]')) {
                    const honeypot = document.createElement('input');
                    honeypot.type = 'text';
                    honeypot.name = 'website';
                    honeypot.style.display = 'none';
                    honeypot.tabIndex = -1;
                    honeypot.autocomplete = 'off';
                    form.appendChild(honeypot);
                }
                
                // Add source field
                if (!form.querySelector('input[name="source"]')) {
                    const source = document.createElement('input');
                    source.type = 'hidden';
                    source.name = 'source';
                    source.value = 'pagebuilder:' + SLUG;
                    form.appendChild(source);
                }
                
                // Normalize field names
                const fieldMap = {
                    'your-name': 'name',
                    'fullname': 'name',
                    'full_name': 'name',
                    'your-email': 'email',
                    'email-address': 'email',
                    'your-phone': 'phone',
                    'phone-number': 'phone',
                    'tel': 'phone',
                    'your-message': 'message',
                    'comments': 'message',
                    'comment': 'message'
                };
                
                Object.keys(fieldMap).forEach(function(oldName) {
                    const field = form.querySelector('[name="' + oldName + '"]');
                    if (field) {
                        field.name = fieldMap[oldName];
                    }
                });
                
                // Handle form submission
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const formData = new FormData(form);
                    const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
                    const originalBtnText = submitBtn ? submitBtn.textContent || submitBtn.value : '';
                    
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        if (submitBtn.tagName === 'BUTTON') {
                            submitBtn.textContent = 'Sending...';
                        } else {
                            submitBtn.value = 'Sending...';
                        }
                    }
                    
                    fetch('/lead', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(function(response) {
                        return response.json();
                    })
                    .then(function(data) {
                        if (data.ok) {
                            // Show success message
                            const successMsg = document.createElement('div');
                            successMsg.style.cssText = 'padding:1rem;margin:1rem 0;background:#10b981;color:white;border-radius:0.5rem;';
                            successMsg.textContent = data.message || 'Thank you! Your message has been sent.';
                            form.insertAdjacentElement('afterend', successMsg);
                            
                            // Reset form
                            form.reset();
                            
                            // Remove success message after 5 seconds
                            setTimeout(function() {
                                successMsg.remove();
                            }, 5000);
                        } else {
                            throw new Error(data.message || 'Submission failed');
                        }
                    })
                    .catch(function(error) {
                        // Show error message
                        const errorMsg = document.createElement('div');
                        errorMsg.style.cssText = 'padding:1rem;margin:1rem 0;background:#ef4444;color:white;border-radius:0.5rem;';
                        errorMsg.textContent = 'Error: ' + error.message;
                        form.insertAdjacentElement('afterend', errorMsg);
                        
                        setTimeout(function() {
                            errorMsg.remove();
                        }, 5000);
                    })
                    .finally(function() {
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            if (submitBtn.tagName === 'BUTTON') {
                                submitBtn.textContent = originalBtnText;
                            } else {
                                submitBtn.value = originalBtnText;
                            }
                        }
                    });
                });
            });
        });
    })();
    </script>
JS;

        // Replace placeholders
        $script = str_replace('{{SELECTOR}}', addslashes($selector), $script);
        $script = str_replace('{{SLUG}}', $slug, $script);

        // Inject meta tags before </head>
        if (stripos($html, '</head>') !== false) {
            $html = preg_replace('/<\/head>/i', $metaTags . "\n</head>", $html, 1);
        }

        // Inject script before </body>
        if (stripos($html, '</body>') !== false) {
            $html = preg_replace('/<\/body>/i', $script . "\n</body>", $html, 1);
        }

        // Write back to file
        file_put_contents($htmlPath, $html);
    }

    /**
     * Generate version hash from ZIP file
     */
    public function generateVersion(string $zipPath): string
    {
        return substr(sha1_file($zipPath), 0, 12);
    }
}

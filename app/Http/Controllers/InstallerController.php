<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use App\Models\User;

class InstallerController extends Controller
{
    /**
     * Check if app is already installed OR installation is in progress
     */
    private function isInstalled(): bool
    {
        return File::exists(storage_path('installed')) 
            || File::exists(storage_path('installing'));
    }

    /**
     * Step 1: Requirements Check
     */
    public function requirements()
    {
        if ($this->isInstalled()) {
            return redirect('/');
        }

        $requirements = $this->checkRequirements();
        $canProceed = collect($requirements)->every(fn($req) => $req['status']);

        return view('installer.requirements', compact('requirements', 'canProceed'));
    }

    /**
     * Step 2: Database Configuration
     */
    public function database()
    {
        if ($this->isInstalled()) {
            return redirect('/');
        }

        return view('installer.database');
    }

    /**
     * Step 2: Process Database Setup
     */
    public function databaseStore(Request $request)
    {
        if ($this->isInstalled()) {
            return redirect('/');
        }

        $validated = $request->validate([
            'db_host' => 'required|string',
            'db_port' => 'required|numeric',
            'db_database' => 'required|string',
            'db_username' => 'required|string',
            'db_password' => 'nullable|string',
        ]);

        // Test connection
        try {
            $pdo = new \PDO(
                "mysql:host={$validated['db_host']};port={$validated['db_port']};dbname={$validated['db_database']}",
                $validated['db_username'],
                $validated['db_password'] ?? ''
            );
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            return back()->withErrors(['db_connection' => 'Connection failed: ' . $e->getMessage()]);
        }

        // Store in session for later
        session([
            'install_db' => [
                'host' => $validated['db_host'],
                'port' => $validated['db_port'],
                'database' => $validated['db_database'],
                'username' => $validated['db_username'],
                'password' => $validated['db_password'] ?? '',
            ]
        ]);

        return redirect()->route('installer.admin');
    }

    /**
     * Step 3: Admin Account
     */
    public function admin()
    {
        if ($this->isInstalled()) {
            return redirect('/');
        }

        if (!session('install_db')) {
            return redirect()->route('installer.database');
        }

        return view('installer.admin');
    }

    /**
     * Step 3: Process Admin Account & Complete Installation
     */
    public function adminStore(Request $request)
    {
        if ($this->isInstalled()) {
            return redirect('/');
        }

        $dbConfig = session('install_db');
        if (!$dbConfig) {
            return redirect()->route('installer.database');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:6|confirmed',
            'site_name' => 'required|string|max:255',
            'site_url' => 'required|url',
        ]);

        try {
            // PR-02: Create "installing" lock file FIRST to prevent race conditions
            File::put(storage_path('installing'), date('Y-m-d H:i:s'));

            // Write .env file
            $this->writeEnvFile($dbConfig, $validated);

            // PR-02: Clear DB credentials from session immediately after use
            session()->forget('install_db');

            // Clear config cache
            Artisan::call('config:clear');
            
            // Reconnect with new config
            config([
                'database.connections.mysql.host' => $dbConfig['host'],
                'database.connections.mysql.port' => $dbConfig['port'],
                'database.connections.mysql.database' => $dbConfig['database'],
                'database.connections.mysql.username' => $dbConfig['username'],
                'database.connections.mysql.password' => $dbConfig['password'],
            ]);
            DB::purge('mysql');
            DB::reconnect('mysql');

            // Run migrations
            Artisan::call('migrate', ['--force' => true]);

            // Create admin user
            $admin = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'admin',
                'is_active' => true,
            ]);

            // Create default settings
            $this->createDefaultSettings($validated['site_name'], $validated['site_url']);

            // Mark as installed and remove installing lock
            File::put(storage_path('installed'), date('Y-m-d H:i:s'));
            File::delete(storage_path('installing'));

            // Store credentials for display
            session([
                'install_complete' => [
                    'email' => $validated['email'],
                    'site_url' => $validated['site_url'],
                ]
            ]);

            return redirect()->route('installer.complete');

        } catch (\Exception $e) {
            // PR-02: Cleanup installing lock on failure
            File::delete(storage_path('installing'));
            return back()->withErrors(['installation' => 'Installation failed: ' . $e->getMessage()]);
        }
    }

    /**
     * Step 4: Complete
     */
    public function complete()
    {
        $credentials = session('install_complete');
        
        if (!$credentials) {
            if ($this->isInstalled()) {
                return redirect('/admin');
            }
            return redirect()->route('installer.requirements');
        }

        session()->forget('install_complete');

        return view('installer.complete', compact('credentials'));
    }

    /**
     * Check server requirements
     */
    private function checkRequirements(): array
    {
        $requirements = [];

        // PHP Version
        $requirements['php_version'] = [
            'name' => 'PHP Version',
            'required' => '>= 8.1',
            'current' => PHP_VERSION,
            'status' => version_compare(PHP_VERSION, '8.1.0', '>='),
        ];

        // Extensions
        $extensions = ['pdo', 'pdo_mysql', 'mbstring', 'openssl', 'json', 'curl', 'zip', 'fileinfo', 'gd'];
        foreach ($extensions as $ext) {
            $requirements["ext_{$ext}"] = [
                'name' => "PHP Extension: {$ext}",
                'required' => 'Enabled',
                'current' => extension_loaded($ext) ? 'Enabled' : 'Missing',
                'status' => extension_loaded($ext),
            ];
        }

        // Writable directories
        $writables = ['storage', 'bootstrap/cache', 'public'];
        foreach ($writables as $dir) {
            $path = base_path($dir);
            $requirements["writable_{$dir}"] = [
                'name' => "Writable: {$dir}",
                'required' => 'Writable',
                'current' => is_writable($path) ? 'Writable' : 'Not Writable',
                'status' => is_writable($path),
            ];
        }

        return $requirements;
    }

    /**
     * Write .env file
     */
    private function writeEnvFile(array $db, array $site): void
    {
        $appKey = 'base64:' . base64_encode(random_bytes(32));
        
        $env = <<<ENV
APP_NAME="{$site['site_name']}"
APP_ENV=production
APP_KEY={$appKey}
APP_DEBUG=false
APP_URL={$site['site_url']}

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST={$db['host']}
DB_PORT={$db['port']}
DB_DATABASE={$db['database']}
DB_USERNAME={$db['username']}
DB_PASSWORD={$db['password']}

CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MAIL_MAILER=smtp
MAIL_HOST=localhost
MAIL_PORT=25
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@example.com"
MAIL_FROM_NAME="\${APP_NAME}"
ENV;

        File::put(base_path('.env'), $env);
    }

    /**
     * Create default settings
     */
    private function createDefaultSettings(string $siteName, string $siteUrl): void
    {
        $settings = [
            'site_name' => $siteName,
            'site_url' => $siteUrl,
            'site_description' => 'A modern mini CMS powered by Laravel',
            'posts_per_page' => '10',
            'contact_recipient_email' => '',
        ];

        foreach ($settings as $key => $value) {
            DB::table('settings')->updateOrInsert(
                ['key' => $key],
                ['value' => $value]
            );
        }
    }
}

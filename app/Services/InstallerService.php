<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class InstallerService
{
    public const INSTALLED_FILE = 'installed';

    public const INSTALL_DATA_FILE = 'install_data.json';

    /** Install data path - file-based to avoid session issues during install. */
    public static function installDataPath(): string
    {
        return storage_path('framework/' . self::INSTALL_DATA_FILE);
    }

    public static function putInstallData(string $key, array $data): void
    {
        $path = self::installDataPath();
        $all = file_exists($path) ? (array) json_decode(file_get_contents($path), true) : [];
        $all[$key] = $data;
        
        $result = @file_put_contents($path, json_encode($all));
        if ($result === false) {
            throw new \RuntimeException("Could not write to installation data file. Please ensure 'storage/framework' is writable.");
        }
    }

    public static function getInstallData(string $key): ?array
    {
        $path = self::installDataPath();
        if (! file_exists($path)) {
            return null;
        }
        $all = (array) json_decode(file_get_contents($path), true);

        return $all[$key] ?? null;
    }

    public static function forgetInstallData(): void
    {
        $path = self::installDataPath();
        if (file_exists($path)) {
            @unlink($path);
        }
    }

    public static function isInstalled(): bool
    {
        $path = storage_path('framework/' . self::INSTALLED_FILE);

        if (! file_exists($path)) {
            return false;
        }

        try {
            return \Illuminate\Support\Facades\Schema::hasTable('settings');
        } catch (\Throwable $e) {
            return false;
        }
    }

    public static function getInstalledPath(): string
    {
        return storage_path('framework/' . self::INSTALLED_FILE);
    }

    /**
     * @return array<string, array{label: string, required: bool, satisfied: bool, message?: string}>
     */
    public static function getRequirements(): array
    {
        $requirements = [];

        // PHP version
        $phpRequired = '8.2';
        $phpCurrent = PHP_VERSION;
        $requirements['php'] = [
            'label' => "PHP {$phpRequired} or higher",
            'required' => true,
            'satisfied' => version_compare($phpCurrent, $phpRequired, '>='),
            'message' => "Current: {$phpCurrent}",
        ];

        // Required extensions
        $extensions = [
            'pdo' => 'PDO',
            'mbstring' => 'Mbstring',
            'tokenizer' => 'Tokenizer',
            'xml' => 'XML',
            'ctype' => 'Ctype',
            'json' => 'JSON',
            'fileinfo' => 'Fileinfo',
            'openssl' => 'OpenSSL',
        ];

        foreach ($extensions as $ext => $label) {
            $loaded = extension_loaded($ext);
            $requirements["ext_{$ext}"] = [
                'label' => "{$label} extension",
                'required' => true,
                'satisfied' => $loaded,
            ];
        }

        // Optional PDO drivers (at least one)
        $pdoMysql = extension_loaded('pdo_mysql');
        $pdoPgsql = extension_loaded('pdo_pgsql');
        $pdoSqlite = extension_loaded('pdo_sqlite');
        $pdoDriver = $pdoMysql || $pdoPgsql || $pdoSqlite;

        $requirements['pdo_driver'] = [
            'label' => 'PDO driver (MySQL, PostgreSQL, or SQLite)',
            'required' => true,
            'satisfied' => $pdoDriver,
            'message' => $pdoDriver ? 'Available' : 'No PDO driver found',
        ];

        // Writable directories
        $dirs = [
            base_path('storage') => 'Storage directory',
            base_path('storage/framework') => 'Storage framework',
            base_path('storage/logs') => 'Storage logs',
            base_path('bootstrap/cache') => 'Bootstrap cache',
        ];

        foreach ($dirs as $path => $label) {
            $writable = is_dir($path) && is_writable($path);
            $requirements['dir_' . Str::slug($path)] = [
                'label' => $label . ' writable',
                'required' => true,
                'satisfied' => $writable,
            ];
        }

        return $requirements;
    }

    public static function requirementsSatisfied(): bool
    {
        foreach (self::getRequirements() as $req) {
            if ($req['required'] && ! $req['satisfied']) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return array{success: bool, message?: string}
     */
    public static function testDatabaseConnection(string $driver, array $config): array
    {
        try {
            if ($driver === 'sqlite') {
                $path = $config['database'] ?? database_path('database.sqlite');
                if (! file_exists($path)) {
                    touch($path);
                }
            }

            $connection = match ($driver) {
                'mysql', 'mariadb' => [
                    'driver' => $driver,
                    'host' => $config['host'] ?? '127.0.0.1',
                    'port' => $config['port'] ?? '3306',
                    'database' => $config['database'] ?? 'laravel',
                    'username' => $config['username'] ?? 'root',
                    'password' => $config['password'] ?? '',
                    'charset' => 'utf8mb4',
                    'collation' => 'utf8mb4_unicode_ci',
                    'prefix' => '',
                    'options' => [
                        \PDO::ATTR_TIMEOUT => 5, // 5 second timeout
                    ],
                ],
                'pgsql' => [
                    'driver' => 'pgsql',
                    'host' => $config['host'] ?? '127.0.0.1',
                    'port' => $config['port'] ?? '5432',
                    'database' => $config['database'] ?? 'laravel',
                    'username' => $config['username'] ?? 'root',
                    'password' => $config['password'] ?? '',
                    'charset' => 'utf8',
                    'prefix' => '',
                    'search_path' => 'public',
                    'options' => [
                        \PDO::ATTR_TIMEOUT => 5,
                    ],
                ],
                default => [
                    'driver' => 'sqlite',
                    'database' => $config['database'] ?? database_path('database.sqlite'),
                    'prefix' => '',
                ],
            };

            config(['database.connections.installer_test' => $connection]);
            DB::connection('installer_test')->getPdo();
            DB::purge('installer_test');

            return ['success' => true, 'message' => 'Database connection successful!'];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public static function install(array $data): array
    {
        $errors = [];

        // Ensure .env exists
        $envPath = base_path('.env');
        if (! file_exists($envPath)) {
            $example = base_path('.env.example');
            if (! file_exists($example)) {
                $errors[] = '.env.example not found';
            } else {
                copy($example, $envPath);
            }
        }

        if (! empty($errors)) {
            return $errors;
        }

        // Build .env content
        $envContent = file_get_contents($envPath);

        // Update database config
        $db = $data['database'];
        $driver = $db['driver'];

        $envContent = self::setEnvValue($envContent, 'DB_CONNECTION', $driver);
        if ($driver === 'sqlite') {
            $envContent = self::setEnvValue($envContent, 'DB_DATABASE', $db['database'] ?? database_path('database.sqlite'));
        } else {
            $envContent = self::setEnvValue($envContent, 'DB_HOST', $db['host'] ?? '127.0.0.1');
            $envContent = self::setEnvValue($envContent, 'DB_PORT', $db['port'] ?? ($driver === 'pgsql' ? '5432' : '3306'));
            $envContent = self::setEnvValue($envContent, 'DB_DATABASE', $db['database'] ?? 'laravel');
            $envContent = self::setEnvValue($envContent, 'DB_USERNAME', $db['username'] ?? 'root');
            $envContent = self::setEnvValue($envContent, 'DB_PASSWORD', $db['password'] ?? '');
        }

        // Update app config
        $app = $data['app'];
        $appUrl = rtrim($app['url'] ?? url('/'), '/');
        $envContent = self::setEnvValue($envContent, 'APP_NAME', $app['name'] ?? 'LearnFlow');
        $envContent = self::setEnvValue($envContent, 'APP_URL', $appUrl);
        $envContent = self::setEnvValue($envContent, 'ASSET_URL', $appUrl);
        $envContent = self::setEnvValue($envContent, 'APP_ENV', 'production');
        $envContent = self::setEnvValue($envContent, 'APP_DEBUG', 'false');

        // Use file-based session/cache/queue for fresh installs (works without Redis on shared hosting)
        $envContent = self::setEnvValue($envContent, 'SESSION_DRIVER', 'file');
        $envContent = self::setEnvValue($envContent, 'CACHE_STORE', 'file');
        $envContent = self::setEnvValue($envContent, 'QUEUE_CONNECTION', 'sync');
        // Generate key if empty
        if (! preg_match('/APP_KEY=base64:[a-zA-Z0-9+\/=]+/', $envContent)) {
            $envContent = self::setEnvValue($envContent, 'APP_KEY', 'base64:' . base64_encode(Str::random(32)));
        }

        // Write .env - if it fails, write to fallback file for manual merge
        if (! is_writable($envPath)) {
            $fallbackPath = storage_path('framework/install_env_merge.txt');
            file_put_contents($fallbackPath, $envContent);
            $errors[] = ".env is not writable. Database config written to: storage/framework/install_env_merge.txt – merge these lines into .env manually.";

            return $errors;
        }

        $written = file_put_contents($envPath, $envContent);
        if ($written === false || $written < 10) {
            $fallbackPath = storage_path('framework/install_env_merge.txt');
            file_put_contents($fallbackPath, $envContent);
            $errors[] = "Could not write .env. Config saved to: storage/framework/install_env_merge.txt – merge into .env manually.";

            return $errors;
        }

        // Verify .env contains our database config
        $verifyContent = file_get_contents($envPath);
        if (! str_contains($verifyContent, 'DB_CONNECTION=') && ! preg_match('/DB_CONNECTION=.+/', $verifyContent)) {
            $fallbackPath = storage_path('framework/install_env_merge.txt');
            file_put_contents($fallbackPath, $envContent);
            $errors[] = ".env write may have failed. Backup written to storage/framework/install_env_merge.txt";

            return $errors;
        }

        // Update runtime config so migrate uses new credentials
        $driver = $db['driver'];
        if ($driver === 'sqlite') {
            $path = $db['database'] ?? database_path('database.sqlite');
            if (! file_exists($path)) {
                touch($path);
            }
            config([
                'database.default' => 'sqlite',
                'database.connections.sqlite.database' => $path,
            ]);
        } else {
            config([
                'database.default' => $driver,
                'database.connections.' . $driver => array_merge(
                    config('database.connections.' . $driver),
                    [
                        'host' => $db['host'] ?? '127.0.0.1',
                        'port' => $db['port'] ?? ($driver === 'pgsql' ? '5432' : '3306'),
                        'database' => $db['database'] ?? 'laravel',
                        'username' => $db['username'] ?? 'root',
                        'password' => $db['password'] ?? '',
                    ]
                ),
            ]);
        }

        try {
            // Verify database connection works with new config before migrating
            $finalConfig = $driver === 'sqlite'
                ? ['driver' => 'sqlite', 'database' => $db['database'] ?? database_path('database.sqlite'), 'prefix' => '']
                : [
                    'driver' => $driver,
                    'host' => $db['host'] ?? '127.0.0.1',
                    'port' => $db['port'] ?? ($driver === 'pgsql' ? '5432' : '3306'),
                    'database' => $db['database'] ?? 'laravel',
                    'username' => $db['username'] ?? 'root',
                    'password' => $db['password'] ?? '',
                    'charset' => $driver === 'pgsql' ? 'utf8' : 'utf8mb4',
                    'prefix' => '',
                ];
            config(['database.connections.installer_final' => $finalConfig]);
            DB::connection('installer_final')->getPdo();
            DB::purge('installer_final');

            // Run migrations
            try {
                Artisan::call('migrate', ['--force' => true]);
            } catch (\Throwable $e) {
                if (str_contains($e->getMessage(), 'already exists')) {
                    return ["The database is not empty (Table 'users' already exists). Please clear your database tables and click 'Run Installation' again for a clean setup."];
                }
                throw $e;
            }

            // Create storage link for public files (e.g. logo)
            try {
                Artisan::call('storage:link');
            } catch (\Throwable) {
                // Ignore if link exists or fails (e.g. Windows)
            }

            // Seed roles (required for Spatie Permission)
            Artisan::call('db:seed', ['--class' => \Database\Seeders\RolesAndPermissionsSeeder::class, '--force' => true]);

            // Create admin user with username and email verified
            $admin = $data['admin'];
            $username = self::generateUniqueUsername($admin['name'], $admin['email']);
            $user = User::create([
                'name' => $admin['name'],
                'username' => $username,
                'email' => $admin['email'],
                'password' => Hash::make($admin['password']),
                'email_verified_at' => now(),
            ]);
            $user->assignRole('admin');

            // Seed default settings
            $app = $data['app'];
            \App\Models\Setting::set('site_name', $app['name'] ?? 'LearnFlow');
            \App\Models\Setting::set('site_color', '#1a42e0');
            \App\Models\Setting::set('currency', 'USD');
            \App\Models\Setting::set('payment_currency', 'USD');
            \App\Models\Setting::set('support_email', $admin['email']);

            // Mark as installed
            file_put_contents(self::getInstalledPath(), date('c'));

            return [];
        } catch (\Throwable $e) {
            return [$e->getMessage()];
        }
    }

    private static function generateUniqueUsername(string $name, string $email): string
    {
        $emailPart = explode('@', $email)[0] ?? '';
        $base = preg_replace('/[^a-z0-9_-]/', '', strtolower(Str::slug($emailPart ?: $name) ?: 'admin')) ?: 'admin';
        $username = $base;
        $i = 1;
        while (User::where('username', $username)->exists()) {
            $username = $base . $i;
            $i++;
        }

        return $username;
    }

    private static function setEnvValue(string $content, string $key, string $value): string
    {
        $escaped = str_replace(['\\', '"'], ['\\\\', '\\"'], $value);
        $pattern = '/^' . preg_quote($key, '/') . '=.*$/m';
        $replacement = "{$key}=\"{$escaped}\"";

        if (preg_match($pattern, $content)) {
            return preg_replace($pattern, $replacement, $content, 1);
        }

        return $content . "\n{$replacement}\n";
    }
}

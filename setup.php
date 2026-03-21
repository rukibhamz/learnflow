<?php
/**
 * LearnFlow Setup Assistant
 * Helps diagnose and fix common cPanel deployment issues.
 */

define('LARAVEL_START', microtime(true));

$baseDir = __DIR__;

echo "<html><head><title>LearnFlow Setup Assistant</title><style>
    body { font-family: sans-serif; line-height: 1.6; max-width: 800px; margin: 40px auto; padding: 20px; background: #f4f4f9; color: #333; }
    .card { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; }
    h1 { color: #2563eb; }
    .status { font-weight: bold; }
    .success { color: #16a34a; }
    .error { color: #dc2626; }
    .warning { color: #d97706; }
    pre { background: #1e293b; color: #f8fafc; padding: 15px; border-radius: 5px; overflow-x: auto; }
    button { background: #2563eb; color: #fff; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; }
    button:hover { background: #1d4ed8; }
</style></head><body>";

echo "<h1>🛠️ LearnFlow Setup Assistant</h1>";

$step = $_GET['step'] ?? 'status';

if ($step === 'status') {
    echo "<div class='card'>";
    echo "<h2>System Status</h2>";
    
    // Check PHP Version
    $phpVersion = PHP_VERSION;
    $phpOk = version_compare($phpVersion, '8.2.0', '>=');
    echo "<p>PHP Version: <span class='status " . ($phpOk ? "success" : "error") . "'>$phpVersion</span> " . ($phpOk ? "✅" : "❌ (Requires 8.2+)") . "</p>";

    // Check Vendor
    $vendorOk = file_exists($baseDir . '/vendor/autoload.php');
    echo "<p>Composer Dependencies (vendor): <span class='status " . ($vendorOk ? "success" : "error") . "'>" . ($vendorOk ? "Detected" : "Missing") . "</span> " . ($vendorOk ? "✅" : "❌") . "</p>";

    // Check .env
    $envOk = file_exists($baseDir . '/.env');
    echo "<p>Environment File (.env): <span class='status " . ($envOk ? "success" : "error") . "'>" . ($envOk ? "Detected" : "Missing") . "</span> " . ($envOk ? "✅" : "❌") . "</p>";

    // Check Storage Permissions
    $storagePath = $baseDir . '/storage';
    $storageWritable = is_writable($storagePath);
    echo "<p>Storage Directory Writable: <span class='status " . ($storageWritable ? "success" : "error") . "'>" . ($storageWritable ? "Yes" : "No") . "</span> " . ($storageWritable ? "✅" : "❌") . "</p>";

    $cachePath = $baseDir . '/bootstrap/cache';
    $cacheWritable = is_writable($cachePath);
    echo "<p>Bootstrap Cache Writable: <span class='status " . ($cacheWritable ? "success" : "error") . "'>" . ($cacheWritable ? "Yes" : "No") . "</span> " . ($cacheWritable ? "✅" : "❌") . "</p>";

    echo "</div>";

    echo "<div class='card'>";
    echo "<h2>Actions</h2>";
    echo "<form method='GET'><input type='hidden' name='step' value='fix'>";
    echo "<button type='submit'>Attempt Auto-Fix Commands</button>";
    echo "</form>";
    echo "</div>";
}

if ($step === 'fix') {
    echo "<div class='card'>";
    echo "<h2>Executing Fixes...</h2>";
    echo "<pre>";

    // 1. Try to create .env
    if (!file_exists($baseDir . '/.env')) {
        if (file_exists($baseDir . '/.env.example')) {
            copy($baseDir . '/.env.example', $baseDir . '/.env');
            echo "Created .env from .env.example\n";
        } else {
            echo "Error: .env.example not found. Cannot create .env automatically.\n";
        }
    }

    // 2. Try to fix permissions (only works if PHP has enough privs)
    @chmod($baseDir . '/storage', 0775);
    @chmod($baseDir . '/bootstrap/cache', 0775);
    echo "Attempted to set permissions (775) on storage and bootstrap/cache\n";

    // 3. Try to run artisan if vendor exists
    if (file_exists($baseDir . '/vendor/autoload.php')) {
        echo "\nRunning Laravel Maintenance Commands:\n";
        
        // Ensure SQLite file exists if configured
        if (!file_exists($baseDir . '/database/database.sqlite')) {
             @touch($baseDir . '/database/database.sqlite');
             @chmod($baseDir . '/database/database.sqlite', 0664);
             echo "Created database/database.sqlite\n";
        }

        passthru("php artisan key:generate --ansi");
        passthru("php artisan config:clear");
        passthru("php artisan storage:link");
        
        echo "\nNote: If commands failed, you may need to run them via SSH or cPanel Terminal.\n";
    } else {
        echo "\nWarning: Vendor directory missing. Skipping Artisan commands.\n";
        echo "Please upload the 'vendor' folder or run 'composer install' via Terminal.\n";
    }

    echo "</pre>";
    echo "<p><a href='setup.php'><button>Back to Status</button></a></p>";
    echo "</div>";
}

echo "</body></html>";

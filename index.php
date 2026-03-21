<?php

/**
 * Proxy for subdirectory installs (e.g. XAMPP).
 * Serves static assets from public/ and forwards all other
 * requests to public/index.php so Laravel handles them.
 */

$basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');

// Health Check: Ensure vendor exists
if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
    http_response_code(500);
    echo "<h1>Configuration Error</h1>";
    echo "<p>The <code>vendor</code> directory is missing. Please run <code>composer install</code> or upload the <code>vendor</code> folder.</p>";
    if (file_exists(__DIR__ . '/setup.php')) {
        echo "<p><a href='setup.php'>Click here to run the Setup Assistant</a></p>";
    }
    exit;
}

// Health Check: Automation for new installations
$hasKey = false;
if (file_exists(__DIR__ . '/.env')) {
    $envLines = file(__DIR__ . '/.env');
    foreach ($envLines as $line) {
        if (str_starts_with(trim($line), 'APP_KEY=base64:')) {
            $hasKey = true;
            break;
        }
    }
}

if (!$hasKey || !file_exists(__DIR__ . '/storage/framework/installed')) {
    // 1. Ensure .env exists
    if (!file_exists(__DIR__ . '/.env') && file_exists(__DIR__ . '/.env.example')) {
        copy(__DIR__ . '/.env.example', __DIR__ . '/.env');
    }

    // 2. Ensure APP_KEY exists
    if (file_exists(__DIR__ . '/.env')) {
        $envContent = file_get_contents(__DIR__ . '/.env');
        if (!str_contains($envContent, 'APP_KEY=base64:')) {
            $key = 'base64:'.base64_encode(random_bytes(32));
            if (str_contains($envContent, 'APP_KEY=')) {
                $envContent = preg_replace('/^APP_KEY=.*$/m', "APP_KEY=$key", $envContent);
            } else {
                $envContent .= "\nAPP_KEY=$key\n";
            }
            file_put_contents(__DIR__ . '/.env', $envContent);
            // If we generated a new key, we MUST ensure the app doesn't think it's installed
            if (file_exists(__DIR__ . '/storage/framework/installed')) {
                @unlink(__DIR__ . '/storage/framework/installed');
            }
        }
    }

    // 3. Ensure SQLite database exists
    if (!file_exists(__DIR__ . '/database/database.sqlite')) {
        @touch(__DIR__ . '/database/database.sqlite');
        @chmod(__DIR__ . '/database/database.sqlite', 0664);
    }

    // 4. Force clear ALL bootstrap caches
    foreach (glob(__DIR__ . '/bootstrap/cache/*.php') as $cacheFile) {
        @unlink($cacheFile);
    }
}

require __DIR__ . '/vendor/autoload.php';

$requestUri = urldecode(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? '');
$path = ($basePath !== '' && str_starts_with($requestUri, $basePath))
    ? (substr($requestUri, strlen($basePath)) ?: '/')
    : ($requestUri ?: '/');

$publicFile = __DIR__ . '/public' . $path;

if ($path !== '/' && is_file($publicFile)) {
    $mimeTypes = [
        'css'   => 'text/css',
        'js'    => 'application/javascript',
        'json'  => 'application/json',
        'png'   => 'image/png',
        'jpg'   => 'image/jpeg',
        'jpeg'  => 'image/jpeg',
        'gif'   => 'image/gif',
        'svg'   => 'image/svg+xml',
        'ico'   => 'image/x-icon',
        'woff'  => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf'   => 'font/ttf',
        'map'   => 'application/json',
        'webp'  => 'image/webp',
    ];

    $ext = strtolower(pathinfo($publicFile, PATHINFO_EXTENSION));
    if (isset($mimeTypes[$ext])) {
        header('Content-Type: ' . $mimeTypes[$ext]);
    }
    readfile($publicFile);
    exit;
}

// Strip subdirectory from REQUEST_URI so Laravel routing matches (e.g. /learnflow/login → /login)
$reqUri = $_SERVER['REQUEST_URI'] ?? '/';
$reqPath = parse_url($reqUri, PHP_URL_PATH) ?: '/';
if ($basePath !== '' && $basePath !== '/' && str_starts_with($reqPath, $basePath)) {
    $pathPart = substr($reqPath, strlen($basePath)) ?: '/';
    $query = parse_url($reqUri, PHP_URL_QUERY);
    $_SERVER['REQUEST_URI'] = $pathPart . ($query !== null && $query !== '' ? '?' . $query : '');
}

require __DIR__ . '/public/index.php';

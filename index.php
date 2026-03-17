<?php

/**
 * Proxy for subdirectory installs (e.g. XAMPP).
 * Serves static assets from public/ and forwards all other
 * requests to public/index.php so Laravel handles them.
 */

$basePath = dirname($_SERVER['SCRIPT_NAME']);
$requestUri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '');
$path = substr($requestUri, strlen($basePath)) ?: '/';

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

require __DIR__ . '/public/index.php';

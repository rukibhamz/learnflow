<?php

/**
 * Proxy for subdirectory installs (e.g. XAMPP).
 * Serves static assets from public/ and forwards all other
 * requests to public/index.php so Laravel handles them.
 */

$basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
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

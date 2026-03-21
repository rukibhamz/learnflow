<?php

/**
 * Install entry point - used when .htaccess rewrites don't work (e.g. AllowOverride None).
 * Strips the subdirectory from the path so Laravel can route correctly.
 */
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$basePath = dirname(dirname($scriptName));
$query = $_SERVER['QUERY_STRING'] ?? '';

// Inform Laravel of the base path and the route path
$_SERVER['SCRIPT_NAME'] = rtrim($basePath, '/') . '/index.php';
$_SERVER['REQUEST_URI'] = rtrim($basePath, '/') . '/install' . ($query ? '?' . $query : '');

require __DIR__.'/../public/index.php';

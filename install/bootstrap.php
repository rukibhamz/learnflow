<?php

/**
 * Shared bootstrap for install step entry points (e.g. install/database/, install/requirements/).
 * Strips the app base path (e.g. /learnflow) so Laravel receives /install/database, etc.
 */
return function (string $step): void {
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    $basePath = dirname(dirname(dirname($scriptName)));
    $query = $_SERVER['QUERY_STRING'] ?? '';

    // Inform Laravel of the base path and the route path
    $_SERVER['SCRIPT_NAME'] = rtrim($basePath, '/') . '/index.php';
    $_SERVER['REQUEST_URI'] = rtrim($basePath, '/') . '/install/' . $step . ($query ? '?' . $query : '');

    error_log("[INSTALL_BOOTSTRAP] step=$step, uri=".$_SERVER['REQUEST_URI'].", script=".$_SERVER['SCRIPT_NAME']);

    require dirname(__DIR__).'/public/index.php';
};

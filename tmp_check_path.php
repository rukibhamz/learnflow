<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Base Path: " . base_path() . "\n";
echo ".env Path: " . base_path('.env') . "\n";
echo "Realpath(.env): " . realpath(base_path('.env')) . "\n";
echo "Is Writable: " . (is_writable(base_path('.env')) ? 'YES' : 'NO') . "\n";
echo "__DIR__: " . __DIR__ . "\n";

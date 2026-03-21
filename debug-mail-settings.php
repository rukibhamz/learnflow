<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Setting;
$settings = Setting::where('key', 'like', 'mail_%')->get()->pluck('value', 'key')->toArray();
echo json_encode($settings, JSON_PRETTY_PRINT);

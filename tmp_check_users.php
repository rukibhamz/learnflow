<?php
use App\Models\User;
use Illuminate\Support\Facades\Hash;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$email = 'admin@example.com'; // Default or from session? I'll list all users.

$users = User::all();
echo "Total users: " . $users->count() . "\n";
foreach ($users as $user) {
    echo "ID: {$user->id}, Name: {$user->name}, Email: {$user->email}, Username: {$user->username}, Verified: {$user->email_verified_at}\n";
    $roles = $user->getRoleNames();
    echo "Roles: " . implode(', ', $roles->toArray()) . "\n";
}

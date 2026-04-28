<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$user = \App\Models\User::where('username', 'admin_untar')->first();
if ($user) {
    echo "Found: " . $user->name . "\n";
    echo "Role: " . $user->role . "\n";
    echo "Branch: " . $user->admin_branch . "\n";
    echo "Password check: " . (\Illuminate\Support\Facades\Hash::check('admin123', $user->password) ? 'OK' : 'FAIL') . "\n";
} else {
    echo "User 'admin_untar' NOT FOUND in database\n";
    echo "All admin users:\n";
    $admins = \App\Models\User::where('role', 'admin')->get(['username', 'name', 'admin_branch']);
    foreach ($admins as $a) {
        echo "  - " . $a->username . " (" . $a->name . ") branch: " . $a->admin_branch . "\n";
    }
    if ($admins->isEmpty()) {
        echo "  (No admin users found. Run: php artisan db:seed --class=AdminSeeder)\n";
    }
}

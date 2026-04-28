<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$u = \App\Models\User::where('nik', '0000000000000001')->first();
if ($u) {
    $u->username = 'admin_untar';
    $u->name = 'Admin Untar';
    $u->password = \Illuminate\Support\Facades\Hash::make('admin123');
    $u->save();
    echo "Updated: username is now 'admin_untar'\n";
} else {
    echo "Not found\n";
}

$check = \App\Models\User::where('username', 'admin_untar')->first();
echo "Verify: " . ($check ? "OK - " . $check->name . " (role: " . $check->role . ")" : "FAILED") . "\n";
echo "Password check: " . (\Illuminate\Support\Facades\Hash::check('admin123', $check->password) ? 'OK' : 'FAIL') . "\n";

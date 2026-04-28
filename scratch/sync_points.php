<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Transaction;

$transactions = Transaction::where('status', 'complete')->get();

$totalUpdated = 0;
foreach ($transactions as $tx) {
    if ($tx->points_earned == 0) {
        $points = 0;
        if ($tx->method === 'Drop-off') $points += 10;
        if ($tx->actual_weight > 5) $points += 10;
        // Asumsi sudah dikategorikan
        $points += 10;

        $tx->update(['points_earned' => $points]);
        
        // Catatan: Poin pengguna tidak diupdate massal untuk menghindari duplikasi jika sudah pernah diberikan sebagian
        // Tapi jika diinginkan bisa $tx->user->increment('points', $points);
        // Namun seeder awalnya sudah memberikan random points, jadi tidak perlu.
        $totalUpdated++;
    }
}

echo "Berhasil update $totalUpdated transaksi.\n";

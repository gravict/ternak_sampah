<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SystemTableSeeder extends Seeder
{
    public function run(): void
    {
        $jobPayload = fn($class) => json_encode([
            'uuid' => Str::uuid()->toString(),
            'displayName' => $class,
            'job' => 'Illuminate\\Queue\\CallQueuedHandler@call',
            'data' => ['commandName' => $class, 'command' => 'O:' . strlen($class) . ':"' . $class . '":0:{}'],
        ]);

        $jobClasses = [
            'App\\Jobs\\SendWelcomeEmail',
            'App\\Jobs\\ProcessTransaction',
            'App\\Jobs\\GenerateMonthlyReport',
            'App\\Jobs\\SendPickupReminder',
            'App\\Jobs\\CalculateUserPoints',
            'App\\Jobs\\SyncWastePrices',
            'App\\Jobs\\SendVoucherNotification',
            'App\\Jobs\\CleanupExpiredSessions',
            'App\\Jobs\\ProcessWithdrawal',
            'App\\Jobs\\BackupDatabase',
        ];

        foreach ($jobClasses as $i => $class) {
            DB::table('jobs')->insert([
                'queue' => 'default',
                'payload' => $jobPayload($class),
                'attempts' => rand(0, 2),
                'reserved_at' => $i < 3 ? now()->subMinutes(rand(1, 5))->timestamp : null,
                'available_at' => now()->subMinutes(rand(0, 30))->timestamp,
                'created_at' => now()->subMinutes(rand(10, 60))->timestamp,
            ]);
        }

        $batches = [
            ['name' => 'Kirim Email Selamat Datang Batch', 'total' => 50, 'pending' => 0, 'failed' => 0, 'finished' => true],
            ['name' => 'Proses Transaksi Harian', 'total' => 25, 'pending' => 0, 'failed' => 1, 'finished' => true],
            ['name' => 'Generate Laporan Bulanan April', 'total' => 12, 'pending' => 3, 'failed' => 0, 'finished' => false],
            ['name' => 'Notifikasi Voucher Baru', 'total' => 100, 'pending' => 0, 'failed' => 2, 'finished' => true],
            ['name' => 'Sinkronisasi Harga Sampah', 'total' => 10, 'pending' => 0, 'failed' => 0, 'finished' => true],
            ['name' => 'Reminder Pickup Jadwal Besok', 'total' => 15, 'pending' => 5, 'failed' => 0, 'finished' => false],
            ['name' => 'Kalkulasi Poin User Mingguan', 'total' => 80, 'pending' => 0, 'failed' => 3, 'finished' => true],
            ['name' => 'Cleanup Session Expired', 'total' => 200, 'pending' => 0, 'failed' => 0, 'finished' => true],
            ['name' => 'Proses Penarikan Saldo', 'total' => 8, 'pending' => 2, 'failed' => 0, 'finished' => false],
            ['name' => 'Backup Database Mingguan', 'total' => 5, 'pending' => 0, 'failed' => 0, 'finished' => true],
        ];

        foreach ($batches as $i => $batch) {
            $failedIds = $batch['failed'] > 0
                ? implode(',', range(1, $batch['failed']))
                : '';

            DB::table('job_batches')->insert([
                'id' => Str::uuid()->toString(),
                'name' => $batch['name'],
                'total_jobs' => $batch['total'],
                'pending_jobs' => $batch['pending'],
                'failed_jobs' => $batch['failed'],
                'failed_job_ids' => $failedIds,
                'options' => null,
                'cancelled_at' => null,
                'created_at' => now()->subDays($i)->timestamp,
                'finished_at' => $batch['finished'] ? now()->subDays($i)->addHours(1)->timestamp : null,
            ]);
        }

        $failedJobs = [
            ['class' => 'App\\Jobs\\SendWelcomeEmail', 'error' => 'Swift_TransportException: Connection could not be established with host smtp.mailtrap.io'],
            ['class' => 'App\\Jobs\\ProcessTransaction', 'error' => 'Illuminate\\Database\\QueryException: SQLSTATE[40001] Deadlock found when trying to get lock'],
            ['class' => 'App\\Jobs\\GenerateMonthlyReport', 'error' => 'ErrorException: Allowed memory size of 134217728 bytes exhausted'],
            ['class' => 'App\\Jobs\\SendPickupReminder', 'error' => 'GuzzleHttp\\Exception\\ConnectException: cURL error 28: Operation timed out after 30000 milliseconds'],
            ['class' => 'App\\Jobs\\CalculateUserPoints', 'error' => 'InvalidArgumentException: User ID 999 not found in database'],
            ['class' => 'App\\Jobs\\SyncWastePrices', 'error' => 'RuntimeException: External API returned status code 503 Service Unavailable'],
            ['class' => 'App\\Jobs\\SendVoucherNotification', 'error' => 'Twilio\\Exceptions\\RestException: The number +628xxx is not a valid phone number'],
            ['class' => 'App\\Jobs\\ProcessWithdrawal', 'error' => 'App\\Exceptions\\InsufficientBalanceException: User balance is insufficient for withdrawal amount'],
            ['class' => 'App\\Jobs\\BackupDatabase', 'error' => 'Symfony\\Component\\Process\\Exception\\ProcessTimedOutException: The process exceeded the timeout of 300 seconds'],
            ['class' => 'App\\Jobs\\CleanupExpiredSessions', 'error' => 'PDOException: SQLSTATE[HY000] [2002] Connection refused'],
        ];

        foreach ($failedJobs as $i => $fj) {
            DB::table('failed_jobs')->insert([
                'uuid' => Str::uuid()->toString(),
                'connection' => 'mysql',
                'queue' => 'default',
                'payload' => $jobPayload($fj['class']),
                'exception' => $fj['error'] . "\n\n#0 /vendor/laravel/framework/src/Queue/Worker.php(415): call()\n#1 /vendor/laravel/framework/src/Queue/Worker.php(364): process()\n#2 {main}",
                'failed_at' => now()->subDays($i)->subHours(rand(1, 12)),
            ]);
        }

        $emails = [
            'leon@ternaksampah.com', 'budi@email.com', 'siti@email.com',
            'ahmad@email.com', 'dewi@email.com', 'rizky@email.com',
            'putri@email.com', 'hendra@email.com', 'nurul@email.com',
            'admin@ternaksampah.com',
        ];

        foreach ($emails as $i => $email) {
            DB::table('password_reset_tokens')->insert([
                'email' => $email,
                'token' => hash('sha256', Str::random(60)),
                'created_at' => now()->subHours(rand(1, 48)),
            ]);
        }

        $userAgents = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/124.0.0.0 Safari/537.36',
            'Mozilla/5.0 (iPhone; CPU iPhone OS 17_4 like Mac OS X) AppleWebKit/605.1.15 Mobile/15E148 Safari/604.1',
            'Mozilla/5.0 (Linux; Android 14; SM-S918B) AppleWebKit/537.36 Chrome/124.0.0.0 Mobile Safari/537.36',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 Chrome/124.0.0.0 Safari/537.36',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:125.0) Gecko/20100101 Firefox/125.0',
            'Mozilla/5.0 (iPad; CPU OS 17_4 like Mac OS X) AppleWebKit/605.1.15 Mobile/15E148 Safari/604.1',
            'Mozilla/5.0 (Linux; Android 14; Pixel 8 Pro) AppleWebKit/537.36 Chrome/124.0.0.0 Mobile Safari/537.36',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Edg/124.0.0.0 Safari/537.36',
            'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 Chrome/124.0.0.0 Safari/537.36',
            'Mozilla/5.0 (iPhone; CPU iPhone OS 17_3 like Mac OS X) AppleWebKit/605.1.15 Mobile/15E148 Safari/604.1',
        ];

        $ips = [
            '192.168.1.10', '103.28.12.45', '180.252.100.33', '36.68.55.21',
            '114.124.200.15', '202.158.30.88', '125.163.72.140', '110.136.85.99',
            '182.1.33.210', '117.102.64.50',
        ];

        for ($i = 0; $i < 10; $i++) {
            DB::table('sessions')->insert([
                'id' => Str::random(40),
                'user_id' => $i + 1,
                'ip_address' => $ips[$i],
                'user_agent' => $userAgents[$i],
                'payload' => base64_encode(serialize([
                    '_token' => Str::random(40),
                    '_previous' => ['url' => 'http://localhost/dashboard'],
                    'login_web_' . sha1('Illuminate\\Auth\\SessionGuard') => $i + 1,
                ])),
                'last_activity' => now()->subMinutes(rand(1, 120))->timestamp,
            ]);
        }

        $userVouchers = [
            ['user_id' => 2, 'voucher_id' => 1, 'days_ago' => 10],
            ['user_id' => 2, 'voucher_id' => 2, 'days_ago' => 5],
            ['user_id' => 4, 'voucher_id' => 3, 'days_ago' => 8],
            ['user_id' => 4, 'voucher_id' => 5, 'days_ago' => 3],
            ['user_id' => 6, 'voucher_id' => 4, 'days_ago' => 7],
            ['user_id' => 6, 'voucher_id' => 6, 'days_ago' => 1],
            ['user_id' => 7, 'voucher_id' => 9, 'days_ago' => 4],
            ['user_id' => 9, 'voucher_id' => 7, 'days_ago' => 2],
            ['user_id' => 9, 'voucher_id' => 8, 'days_ago' => 6],
            ['user_id' => 10, 'voucher_id' => 10, 'days_ago' => 9],
        ];

        foreach ($userVouchers as $uv) {
            DB::table('user_vouchers')->insert([
                'user_id' => $uv['user_id'],
                'voucher_id' => $uv['voucher_id'],
                'claimed_at' => now()->subDays($uv['days_ago']),
                'created_at' => now()->subDays($uv['days_ago']),
                'updated_at' => now()->subDays($uv['days_ago']),
            ]);
        }
    }
}

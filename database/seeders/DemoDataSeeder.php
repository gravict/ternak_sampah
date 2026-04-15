<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\BankAccount;
use App\Models\Transaction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // 9 demo users (admin already created by AdminSeeder = total 10 users)
        $users = [
            [
                'nik' => '3578012345678901', 'name' => 'Leon Sugiharto',
                'email' => 'leon@ternaksampah.com', 'username' => 'leonsugih',
                'dob' => '2005-03-12', 'gender' => 'Laki-laki',
                'points' => 450, 'balance' => 25000, 'streak' => 3,
            ],
            [
                'nik' => '3578012345678902', 'name' => 'Budi Santoso',
                'email' => 'budi@email.com', 'username' => 'budisantoso',
                'dob' => '2000-05-20', 'gender' => 'Laki-laki',
                'points' => 120, 'balance' => 8000, 'streak' => 1,
            ],
            [
                'nik' => '3578012345678903', 'name' => 'Siti Rahmawati',
                'email' => 'siti@email.com', 'username' => 'sitirahmawati',
                'dob' => '1998-11-08', 'gender' => 'Perempuan',
                'points' => 780, 'balance' => 52000, 'streak' => 7,
            ],
            [
                'nik' => '3578012345678904', 'name' => 'Ahmad Fauzi',
                'email' => 'ahmad@email.com', 'username' => 'ahmadfauzi',
                'dob' => '2003-07-15', 'gender' => 'Laki-laki',
                'points' => 200, 'balance' => 15000, 'streak' => 0,
            ],
            [
                'nik' => '3578012345678905', 'name' => 'Dewi Lestari',
                'email' => 'dewi@email.com', 'username' => 'dewilestari',
                'dob' => '2001-02-28', 'gender' => 'Perempuan',
                'points' => 950, 'balance' => 68000, 'streak' => 12,
            ],
            [
                'nik' => '3578012345678906', 'name' => 'Rizky Pratama',
                'email' => 'rizky@email.com', 'username' => 'rizkypratama',
                'dob' => '1999-09-03', 'gender' => 'Laki-laki',
                'points' => 330, 'balance' => 22000, 'streak' => 5,
            ],
            [
                'nik' => '3578012345678907', 'name' => 'Putri Ayu Ningrum',
                'email' => 'putri@email.com', 'username' => 'putriayu',
                'dob' => '2004-12-19', 'gender' => 'Perempuan',
                'points' => 60, 'balance' => 3000, 'streak' => 0,
            ],
            [
                'nik' => '3578012345678908', 'name' => 'Hendra Wijaya',
                'email' => 'hendra@email.com', 'username' => 'hendrawijaya',
                'dob' => '1997-06-25', 'gender' => 'Laki-laki',
                'points' => 1200, 'balance' => 95000, 'streak' => 15,
            ],
            [
                'nik' => '3578012345678909', 'name' => 'Nurul Hidayah',
                'email' => 'nurul@email.com', 'username' => 'nurulhidayah',
                'dob' => '2002-04-10', 'gender' => 'Perempuan',
                'points' => 500, 'balance' => 35000, 'streak' => 4,
            ],
        ];

        $createdUsers = [];
        foreach ($users as $userData) {
            $createdUsers[] = User::create(array_merge($userData, [
                'password' => Hash::make('password'),
            ]));
        }

        // 10 bank accounts (one per user including admin)
        $bankAccounts = [
            ['user_id' => 1, 'bank' => 'BRI', 'account_number' => '0012345678901', 'account_name' => 'Admin Petugas'],
            ['user_id' => $createdUsers[0]->id, 'bank' => 'BCA', 'account_number' => '0811234567', 'account_name' => 'Leon Sugiharto'],
            ['user_id' => $createdUsers[1]->id, 'bank' => 'Mandiri', 'account_number' => '1234567890', 'account_name' => 'Budi Santoso'],
            ['user_id' => $createdUsers[2]->id, 'bank' => 'BNI', 'account_number' => '9876543210', 'account_name' => 'Siti Rahmawati'],
            ['user_id' => $createdUsers[3]->id, 'bank' => 'BCA', 'account_number' => '5551234567', 'account_name' => 'Ahmad Fauzi'],
            ['user_id' => $createdUsers[4]->id, 'bank' => 'BSI', 'account_number' => '7771234567', 'account_name' => 'Dewi Lestari'],
            ['user_id' => $createdUsers[5]->id, 'bank' => 'CIMB Niaga', 'account_number' => '8881234567', 'account_name' => 'Rizky Pratama'],
            ['user_id' => $createdUsers[6]->id, 'bank' => 'BCA', 'account_number' => '3331234567', 'account_name' => 'Putri Ayu Ningrum'],
            ['user_id' => $createdUsers[7]->id, 'bank' => 'Mandiri', 'account_number' => '4441234567', 'account_name' => 'Hendra Wijaya'],
            ['user_id' => $createdUsers[8]->id, 'bank' => 'BRI', 'account_number' => '2221234567', 'account_name' => 'Nurul Hidayah'],
        ];

        foreach ($bankAccounts as $ba) {
            BankAccount::create($ba);
        }

        // 10 transactions with varied statuses
        $transactions = [
            [
                'user_id' => $createdUsers[0]->id, 'category' => 'Plastik / PET',
                'est_weight' => 5.0, 'actual_weight' => 4.8, 'method' => 'Pick-up',
                'status' => 'complete', 'total_price' => 14400,
                'pickup_address' => 'Jl. Tanjung Duren Barat No. 12, Jakarta Barat',
                'pickup_datetime' => now()->subDays(6),
                'created_at' => now()->subDays(7),
            ],
            [
                'user_id' => $createdUsers[0]->id, 'category' => 'Campuran',
                'est_weight' => 3.0, 'method' => 'Drop-off', 'status' => 'rejected',
                'reject_reason' => 'Sampah tercampur dengan limbah medis B3 (jarum suntik).',
                'dropoff_location' => 'Bank Sampah Untar',
                'created_at' => now()->subDays(3),
            ],
            [
                'user_id' => $createdUsers[0]->id, 'category' => 'Plastik / PET',
                'est_weight' => 2.5, 'method' => 'Drop-off', 'status' => 'pending',
                'dropoff_location' => 'Bank Sampah Untar',
                'created_at' => now(),
            ],
            [
                'user_id' => $createdUsers[1]->id, 'category' => 'Kardus / Kertas',
                'est_weight' => 10.0, 'method' => 'Pick-up', 'status' => 'pending',
                'pickup_address' => 'Jl. Kebon Jeruk Raya No. 5, Jakarta Barat',
                'pickup_datetime' => now()->addDay(),
                'created_at' => now()->subDay(),
            ],
            [
                'user_id' => $createdUsers[2]->id, 'category' => 'Logam / Aluminium',
                'est_weight' => 8.0, 'actual_weight' => 7.5, 'method' => 'Drop-off',
                'status' => 'complete', 'total_price' => 60000,
                'dropoff_location' => 'Bank Sampah Untar',
                'created_at' => now()->subDays(2),
            ],
            [
                'user_id' => $createdUsers[3]->id, 'category' => 'Plastik HDPE',
                'est_weight' => 4.0, 'method' => 'Pick-up', 'status' => 'weighing',
                'pickup_address' => 'Jl. Grogol Petamburan No. 88, Jakarta Barat',
                'pickup_datetime' => now(),
                'created_at' => now()->subDays(1),
            ],
            [
                'user_id' => $createdUsers[4]->id, 'category' => 'Minyak Jelantah',
                'est_weight' => 6.0, 'actual_weight' => 5.8, 'method' => 'Drop-off',
                'status' => 'complete', 'total_price' => 29000,
                'dropoff_location' => 'Bank Sampah Untar',
                'created_at' => now()->subDays(5),
            ],
            [
                'user_id' => $createdUsers[5]->id, 'category' => 'Besi Padu',
                'est_weight' => 15.0, 'actual_weight' => 14.2, 'method' => 'Pick-up',
                'status' => 'complete', 'total_price' => 63900,
                'pickup_address' => 'Jl. Citra Garden No. 22, Jakarta Barat',
                'pickup_datetime' => now()->subDays(3),
                'created_at' => now()->subDays(4),
            ],
            [
                'user_id' => $createdUsers[7]->id, 'category' => 'Tembaga Super',
                'est_weight' => 2.0, 'actual_weight' => 1.8, 'method' => 'Drop-off',
                'status' => 'complete', 'total_price' => 108000,
                'dropoff_location' => 'Bank Sampah Untar',
                'created_at' => now()->subDays(1),
            ],
            [
                'user_id' => $createdUsers[8]->id, 'category' => 'Kertas HVS',
                'est_weight' => 12.0, 'method' => 'Pick-up', 'status' => 'pending',
                'pickup_address' => 'Jl. Daan Mogot Km. 3 No. 15, Jakarta Barat',
                'pickup_datetime' => now()->addDays(2),
                'created_at' => now(),
            ],
        ];

        foreach ($transactions as $tx) {
            Transaction::create($tx);
        }
    }
}

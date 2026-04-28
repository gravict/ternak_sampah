<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\BankAccount;
use App\Models\Transaction;
use App\Models\WalletHistory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'nik' => '3578012345678901', 'name' => 'Leon Sugiharto',
                'email' => 'gravict@gmail.com', 'username' => 'leonsugih',
                'dob' => '2005-03-12', 'gender' => 'Laki-laki',
                'streak' => 3, 'target_balance' => 25000,
            ],
            [
                'nik' => '3578012345678902', 'name' => 'Budi Santoso',
                'email' => 's4nt0s0@email.com', 'username' => 'budisantoso',
                'dob' => '2000-05-20', 'gender' => 'Laki-laki',
                'streak' => 1, 'target_balance' => 8000,
            ],
            [
                'nik' => '3578012345678903', 'name' => 'Siti Rahmawati',
                'email' => 's1t1r4hm4w4t1@email.com', 'username' => 'sitirahmawati',
                'dob' => '1998-11-08', 'gender' => 'Perempuan',
                'streak' => 7, 'target_balance' => 52000,
            ],
            [
                'nik' => '3578012345678904', 'name' => 'Ahmad Fauzi',
                'email' => 'mad17@email.com', 'username' => 'ahmadfauzi',
                'dob' => '2003-07-15', 'gender' => 'Laki-laki',
                'streak' => 0, 'target_balance' => 15000,
            ],
            [
                'nik' => '3578012345678905', 'name' => 'Dewi Lestari',
                'email' => 'brunette@email.com', 'username' => 'dewilestari',
                'dob' => '2001-02-28', 'gender' => 'Perempuan',
                'streak' => 12, 'target_balance' => 68000,
            ],
            [
                'nik' => '3578012345678906', 'name' => 'Rizky Pratama',
                'email' => 'arstep19@email.com', 'username' => 'rizkypratama',
                'dob' => '1999-09-03', 'gender' => 'Laki-laki',
                'streak' => 5, 'target_balance' => 22000,
            ],
            [
                'nik' => '3578012345678907', 'name' => 'Putri Ayu Ningrum',
                'email' => 'pan@email.com', 'username' => 'putriayu',
                'dob' => '2004-12-19', 'gender' => 'Perempuan',
                'streak' => 0, 'target_balance' => 3000,
            ],
            [
                'nik' => '3578012345678908', 'name' => 'Hendra Wijaya',
                'email' => 'hend25@email.com', 'username' => 'hendrawijaya',
                'dob' => '1997-06-25', 'gender' => 'Laki-laki',
                'streak' => 15, 'target_balance' => 95000,
            ],
            [
                'nik' => '3578012345678909', 'name' => 'Nurul Hidayah',
                'email' => 'nurul2002@email.com', 'username' => 'nurulhidayah',
                'dob' => '2002-04-10', 'gender' => 'Perempuan',
                'streak' => 4, 'target_balance' => 35000,
            ],
        ];

        $createdUsers = [];
        $targetBalances = [];
        foreach ($users as $userData) {
            $user = User::where('nik', $userData['nik'])->first();
            if (!$user) {
                $targetBalance = $userData['target_balance'];
                unset($userData['target_balance']);
                
                $user = User::create(array_merge($userData, [
                    'password' => Hash::make('password'),
                    'points' => 0,
                    'balance' => 0,
                    'kontribusi' => 0,
                    'level' => 1,
                ]));
                $targetBalances[$user->id] = $targetBalance;
            } else {
                $targetBalances[$user->id] = $userData['target_balance'];
            }
            $createdUsers[] = $user;
        }

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
            BankAccount::firstOrCreate(['user_id' => $ba['user_id'], 'bank' => $ba['bank']], $ba);
        }

        $categories = ['Plastik / PET', 'Kardus / Kertas', 'Besi / Logam', 'Minyak Jelantah', 'Campuran'];
        $methods = ['Drop-off', 'Pick-up'];
        $pickupAddresses = [
            'Jl. Tanjung Duren Barat No. 12, Jakarta Barat',
            'Jl. Kebon Jeruk Raya No. 5, Jakarta Barat',
            'Jl. Grogol Petamburan No. 88, Jakarta Barat',
            'Jl. Citra Garden No. 22, Jakarta Barat',
            'Jl. Daan Mogot Km. 3 No. 15, Jakarta Barat',
            'Jl. Tomang Raya No. 45, Jakarta Barat',
        ];
        $priceMap = [
            'Plastik / PET' => 3000,
            'Kardus / Kertas' => 2500,
            'Besi / Logam' => 4500,
            'Minyak Jelantah' => 5000,
            'Campuran' => 1000,
        ];

        $branchConfigs = [
            'Bank Sampah Untar' => 100,
            'Bank Sampah Tomang' => 50,
        ];

        foreach ($branchConfigs as $branch => $txCount) {
            for ($i = 0; $i < $txCount; $i++) {
                $userKey = array_rand($createdUsers);
                $user = $createdUsers[$userKey];
                
                $category = $categories[array_rand($categories)];
                $method = $methods[array_rand($methods)];
                $estWeight = round(rand(10, 200) / 10, 1); 
                $actualWeight = round($estWeight * (rand(80, 105) / 100), 1); 
                $pricePerKg = $priceMap[$category] ?? 1000;
                $totalPrice = (int) ($actualWeight * $pricePerKg);

                $daysAgo = rand(1, 365);
                $createdAt = now()->subDays($daysAgo)->addHours(rand(8, 17));
                $updatedAt = (clone $createdAt)->addDays(rand(1, 3));

                $points = 0;
                if ($method === 'Drop-off') $points += 10;
                if ($actualWeight > 5) $points += 10;
                if (rand(0, 1) === 1) {
                    $points += 10;
                }

                $txData = [
                    'user_id' => $user->id,
                    'category' => $category,
                    'est_weight' => $estWeight,
                    'actual_weight' => $actualWeight,
                    'method' => $method,
                    'status' => 'complete',
                    'total_price' => $totalPrice,
                    'points_earned' => $points,
                    'dropoff_location' => $branch,
                    'created_at' => $createdAt,
                    'updated_at' => $updatedAt,
                ];

                if ($method === 'Pick-up') {
                    $txData['pickup_address'] = $pickupAddresses[array_rand($pickupAddresses)];
                    $txData['pickup_datetime'] = $createdAt->addDay();
                }

                Transaction::create($txData);

                $user->balance += $totalPrice;
                $user->points += $points;
                $user->kontribusi += round($totalPrice / 100);
            }
        }

        $recentTx = [
            [
                'user_id' => $createdUsers[0]->id, 'category' => 'Plastik / PET',
                'est_weight' => 2.5, 'method' => 'Drop-off', 'status' => 'pending',
                'dropoff_location' => 'Bank Sampah Untar',
                'created_at' => now(),
            ],
            [
                'user_id' => $createdUsers[1]->id, 'category' => 'Kardus / Kertas',
                'est_weight' => 10.0, 'method' => 'Pick-up', 'status' => 'pending',
                'dropoff_location' => 'Bank Sampah Untar',
                'pickup_address' => 'Jl. Kebon Jeruk Raya No. 5, Jakarta Barat',
                'pickup_datetime' => now()->addDay(),
                'created_at' => now()->subDay(),
            ],
            [
                'user_id' => $createdUsers[3]->id, 'category' => 'Plastik / PET',
                'est_weight' => 4.0, 'method' => 'Pick-up', 'status' => 'weighing',
                'dropoff_location' => 'Bank Sampah Untar',
                'pickup_address' => 'Jl. Grogol Petamburan No. 88, Jakarta Barat',
                'pickup_datetime' => now(),
                'created_at' => now()->subDays(1),
            ],
            [
                'user_id' => $createdUsers[0]->id, 'category' => 'Campuran',
                'est_weight' => 3.0, 'method' => 'Drop-off', 'status' => 'rejected',
                'reject_reason' => 'Sampah tercampur dengan limbah medis B3 (jarum suntik).',
                'dropoff_location' => 'Bank Sampah Untar',
                'created_at' => now()->subDays(3),
            ],
            [
                'user_id' => $createdUsers[8]->id, 'category' => 'Kardus / Kertas',
                'est_weight' => 12.0, 'method' => 'Pick-up', 'status' => 'pending',
                'dropoff_location' => 'Bank Sampah Tomang',
                'pickup_address' => 'Jl. Daan Mogot Km. 3 No. 15, Jakarta Barat',
                'pickup_datetime' => now()->addDays(2),
                'created_at' => now(),
            ],
            [
                'user_id' => $createdUsers[0]->id, 'category' => 'Besi / Logam',
                'est_weight' => 69.70, 'method' => 'Pick-up', 'status' => 'pending',
                'dropoff_location' => 'Bank Sampah Tomang',
                'pickup_address' => 'Jl. Citra Garden No. 22, Jakarta Barat',
                'pickup_datetime' => now()->addDay(),
                'created_at' => now(),
            ],
            [
                'user_id' => $createdUsers[0]->id, 'category' => 'Plastik / PET',
                'est_weight' => 69.70, 'method' => 'Pick-up', 'status' => 'pending',
                'dropoff_location' => 'Bank Sampah Tomang',
                'pickup_address' => 'Jl. Tomang Raya No. 45, Jakarta Barat',
                'pickup_datetime' => now()->addDay(),
                'created_at' => now(),
            ],
            [
                'user_id' => $createdUsers[4]->id, 'category' => 'Besi / Logam',
                'est_weight' => 8.5, 'method' => 'Drop-off', 'status' => 'rejected',
                'reject_reason' => 'Kategori sampah tidak sesuai',
                'dropoff_location' => 'Bank Sampah Tomang',
                'created_at' => now()->subDays(5),
            ],
        ];

        foreach ($recentTx as $tx) {
            Transaction::create($tx);
        }

        foreach ($createdUsers as $user) {
            $targetBalance = $targetBalances[$user->id] ?? 0;
            
            if ($user->balance > $targetBalance) {
                $amountToWithdraw = $user->balance - $targetBalance;
                
                $bank = BankAccount::where('user_id', $user->id)->first();
                
                WalletHistory::create([
                    'user_id' => $user->id,
                    'amount' => $amountToWithdraw,
                    'type' => 'withdraw',
                    'status' => 'success',
                    'bank_name' => $bank ? $bank->bank : 'BCA',
                    'account_number' => $bank ? $bank->account_number : '1234567890',
                    'account_name' => $bank ? $bank->account_name : $user->name,
                    'created_at' => now()->subDays(rand(1, 15)),
                    'updated_at' => now()->subDays(rand(1, 15)),
                ]);
                
                $user->balance = $targetBalance;
            }

            $level = 1;
            while ($level < 20 && $user->kontribusi >= ($level * 100)) {
                $level++;
            }
            $user->level = $level;
            $user->save();
        }
    }
}

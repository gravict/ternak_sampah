<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $admins = [
            [
                'nik' => '0000000000000001',
                'name' => 'Admin Untar',
                'email' => 'admin@ternaksampah.com',
                'username' => 'admin_untar',
                'password' => Hash::make('admin123'),
                'dob' => '1990-01-01',
                'gender' => 'Laki-laki',
                'role' => 'admin',
                'admin_branch' => 'Bank Sampah Untar',
            ],
            [
                'nik' => '0000000000000002',
                'name' => 'Admin Tomang',
                'email' => 'admin.tomang@ternaksampah.com',
                'username' => 'admin_tomang',
                'password' => Hash::make('admin123'),
                'dob' => '1990-02-01',
                'gender' => 'Perempuan',
                'role' => 'admin',
                'admin_branch' => 'Bank Sampah Tomang',
            ],
            [
                'nik' => '0000000000000003',
                'name' => 'Admin Grogol',
                'email' => 'admin.grogol@ternaksampah.com',
                'username' => 'admin_grogol',
                'password' => Hash::make('admin123'),
                'dob' => '1990-03-01',
                'gender' => 'Laki-laki',
                'role' => 'admin',
                'admin_branch' => 'Bank Sampah Grogol',
            ],
            [
                'nik' => '0000000000000004',
                'name' => 'Admin Kebon Jeruk',
                'email' => 'admin.kebonjeruk@ternaksampah.com',
                'username' => 'admin_kebonjeruk',
                'password' => Hash::make('admin123'),
                'dob' => '1990-04-01',
                'gender' => 'Perempuan',
                'role' => 'admin',
                'admin_branch' => 'Bank Sampah Kebon Jeruk',
            ],
            [
                'nik' => '0000000000000005',
                'name' => 'Admin Tanjung Duren',
                'email' => 'admin.tanjungduren@ternaksampah.com',
                'username' => 'admin_tanjungduren',
                'password' => Hash::make('admin123'),
                'dob' => '1990-05-01',
                'gender' => 'Laki-laki',
                'role' => 'admin',
                'admin_branch' => 'Bank Sampah Tanjung Duren',
            ],
        ];

        foreach ($admins as $admin) {
            User::updateOrCreate(
                ['nik' => $admin['nik']],
                $admin
            );
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'nik' => '0000000000000001',
            'name' => 'Admin Petugas',
            'email' => 'admin@ternaksampah.com',
            'username' => 'admin',
            'password' => Hash::make('admin123'),
            'dob' => '1990-01-01',
            'gender' => 'Laki-laki',
            'role' => 'admin',
            'admin_branch' => 'Bank Sampah Untar',
        ]);
    }
}

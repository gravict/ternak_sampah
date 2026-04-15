<?php

namespace Database\Seeders;

use App\Models\Voucher;
use Illuminate\Database\Seeder;

class VoucherSeeder extends Seeder
{
    public function run(): void
    {
        $vouchers = [
            ['name' => 'Token Listrik PLN 20rb', 'cost_points' => 200, 'icon' => '⚡'],
            ['name' => 'Saldo GoPay Rp 10rb', 'cost_points' => 120, 'icon' => '💳'],
            ['name' => 'Voucher Indomaret Rp 25rb', 'cost_points' => 250, 'icon' => '🛒'],
            ['name' => 'Saldo OVO Rp 15rb', 'cost_points' => 150, 'icon' => '💰'],
            ['name' => 'Pulsa Telkomsel Rp 10rb', 'cost_points' => 100, 'icon' => '📱'],
            ['name' => 'Voucher Alfamart Rp 20rb', 'cost_points' => 200, 'icon' => '🏪'],
            ['name' => 'Saldo DANA Rp 25rb', 'cost_points' => 250, 'icon' => '💵'],
            ['name' => 'Token Listrik PLN 50rb', 'cost_points' => 480, 'icon' => '🔌'],
            ['name' => 'Voucher Grab Rp 15rb', 'cost_points' => 160, 'icon' => '🚗'],
            ['name' => 'Saldo ShopeePay Rp 10rb', 'cost_points' => 110, 'icon' => '🧡'],
        ];

        foreach ($vouchers as $voucher) {
            Voucher::create($voucher);
        }
    }
}

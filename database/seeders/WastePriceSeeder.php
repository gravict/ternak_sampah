<?php

namespace Database\Seeders;

use App\Models\WastePrice;
use Illuminate\Database\Seeder;

class WastePriceSeeder extends Seeder
{
    public function run(): void
    {
        $prices = [
            ['category' => 'Plastik', 'sub_category' => 'Plastik PET (Botol Bening)', 'price_per_unit' => 3000, 'unit' => 'Kg'],
            ['category' => 'Plastik', 'sub_category' => 'Plastik HDPE (Botol Sampo)', 'price_per_unit' => 2000, 'unit' => 'Kg'],
            ['category' => 'Kertas', 'sub_category' => 'Kertas HVS / Buku', 'price_per_unit' => 2500, 'unit' => 'Kg'],
            ['category' => 'Kertas', 'sub_category' => 'Kardus (Corrugated)', 'price_per_unit' => 2000, 'unit' => 'Kg'],
            ['category' => 'Logam & Besi', 'sub_category' => 'Besi Padu', 'price_per_unit' => 4500, 'unit' => 'Kg'],
            ['category' => 'Logam & Besi', 'sub_category' => 'Aluminium / Kaleng', 'price_per_unit' => 8000, 'unit' => 'Kg'],
            ['category' => 'Logam & Besi', 'sub_category' => 'Tembaga Super', 'price_per_unit' => 60000, 'unit' => 'Kg'],
            ['category' => 'Lainnya', 'sub_category' => 'Minyak Jelantah (Mijel)', 'price_per_unit' => 5000, 'unit' => 'Liter'],
            ['category' => 'Lainnya', 'sub_category' => 'Kaca Bening / Botol Sirup', 'price_per_unit' => 500, 'unit' => 'Kg'],
            ['category' => 'Lainnya', 'sub_category' => 'Campuran / Residu', 'price_per_unit' => 1000, 'unit' => 'Kg'],
        ];

        foreach ($prices as $price) {
            WastePrice::create($price);
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\WastePrice;

class DaftarHargaController extends Controller
{
    public function index()
    {
        $prices = WastePrice::all()->groupBy('category');
        return view('user.daftar_harga', compact('prices'));
    }
}

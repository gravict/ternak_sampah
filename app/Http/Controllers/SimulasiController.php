<?php

namespace App\Http\Controllers;

use App\Models\WastePrice;

class SimulasiController extends Controller
{
    public function index()
    {
        $prices = WastePrice::all()->groupBy('category');
        return view('user.simulasi', compact('prices'));
    }
}

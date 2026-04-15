<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $co2Saved = $user->transactions()
            ->where('status', 'complete')
            ->sum('actual_weight') * 2.5; // rough CO2 factor

        return view('user.dashboard', [
            'user' => $user,
            'co2Saved' => number_format($co2Saved, 1),
        ]);
    }
}

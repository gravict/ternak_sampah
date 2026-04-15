<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class RiwayatController extends Controller
{
    public function index()
    {
        $transactions = Auth::user()
            ->transactions()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('user.riwayat', compact('transactions'));
    }
}

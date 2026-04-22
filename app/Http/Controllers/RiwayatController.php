<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class RiwayatController extends Controller
{
    public function index()
    {
        // Tampung ke variabel $user dan berikan type hinting
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $transactions = $user
            ->transactions()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('user.riwayat', compact('transactions'));
    }
}

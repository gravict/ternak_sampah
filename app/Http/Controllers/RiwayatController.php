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
        $query = $user->transactions()->orderBy('created_at', 'desc');

        $status = request('status');
        if ($status === 'dikirim') {
            $query->where('status', 'pending');
        } elseif ($status === 'diterima') {
            $query->where('status', 'weighing');
        } elseif ($status === 'ditolak') {
            $query->where('status', 'rejected');
        } elseif ($status === 'selesai') {
            $query->where('status', 'complete');
        }

        $transactions = $query->paginate(20)->withQueryString();

        return view('user.riwayat', compact('transactions'));
    }
}

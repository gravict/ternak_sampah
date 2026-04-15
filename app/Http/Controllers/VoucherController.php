<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use App\Models\UserVoucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VoucherController extends Controller
{
    public function index()
    {
        $vouchers = Voucher::all();
        $myVouchers = Auth::user()->userVouchers()->with('voucher')->orderBy('claimed_at', 'desc')->get();

        return view('user.voucher', compact('vouchers', 'myVouchers'));
    }

    public function redeem(Request $request)
    {
        $request->validate(['voucher_id' => 'required|exists:vouchers,id']);

        $voucher = Voucher::findOrFail($request->voucher_id);
        $user = Auth::user();

        if ($user->points < $voucher->cost_points) {
            return back()->with('error', 'Poin tidak cukup! Terus setor sampah untuk kumpulkan poin.');
        }

        $user->decrement('points', $voucher->cost_points);

        UserVoucher::create([
            'user_id' => $user->id,
            'voucher_id' => $voucher->id,
            'claimed_at' => now(),
        ]);

        return back()->with('success', "Berhasil! Voucher {$voucher->name} masuk ke bagian Voucher Saya.");
    }
}

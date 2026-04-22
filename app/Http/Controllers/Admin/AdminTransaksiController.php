<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\WastePrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminTransaksiController extends Controller
{
    // Tahap 1: Permintaan Baru (pending)
    public function proses()
    {
        $branch = Auth::user()->admin_branch;
        $transactions = Transaction::with('user')
            ->where('status', 'pending')
            ->where('dropoff_location', $branch)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.proses', compact('transactions'));
    }

    public function terima($id)
    {
        $trx = Transaction::findOrFail($id);
        $trx->update(['status' => 'weighing']);

        return back()->with('success', "Transaksi #{$id} dipindahkan ke tahap Validasi & Timbang.");
    }

    public function tolak(Request $request, $id)
    {
        $request->validate(['reject_reason' => 'required|string|max:500']);

        $trx = Transaction::findOrFail($id);
        $trx->update([
            'status' => 'rejected',
            'reject_reason' => $request->reject_reason,
        ]);

        return back()->with('success', "Transaksi #{$id} ditolak.");
    }

    // Tahap 2: Validasi & Timbang (weighing)
    public function diterima()
    {
        $branch = Auth::user()->admin_branch;
        $transactions = Transaction::with('user')
            ->where('status', 'weighing')
            ->where('dropoff_location', $branch)
            ->orderBy('created_at', 'desc')
            ->get();

        $prices = WastePrice::all()->pluck('price_per_unit', 'sub_category');

        return view('admin.diterima', compact('transactions', 'prices'));
    }

    public function selesaikan(Request $request, $id)
    {
        $request->validate([
            'actual_weight' => 'required|numeric|min:0.1',
        ]);

        $trx = Transaction::findOrFail($id);

        // Lookup price
        $priceMap = [
            'Plastik / PET' => 3000,
            'Kardus / Kertas' => 2500,
            'Besi / Logam' => 4500,
            'Minyak Jelantah' => 5000,
            'Campuran' => 1000,
        ];

        $pricePerKg = $priceMap[$trx->category] ?? 1000;
        $totalPrice = (int) ($request->actual_weight * $pricePerKg);

        $trx->update([
            'actual_weight' => $request->actual_weight,
            'total_price' => $totalPrice,
            'status' => 'complete',
        ]);

        // Credit user balance
        $trx->user->increment('balance', $totalPrice);

        return redirect('/admin/selesai')->with('success',
            "Sukses! Dana Rp " . number_format($totalPrice, 0, ',', '.') . " dikirim ke saldo {$trx->user->name}."
        );
    }

    // Tahap 3: Riwayat (Selesai + Ditolak)
    public function selesai(Request $request)
    {
        $tab = $request->get('tab', 'complete');
        $branch = Auth::user()->admin_branch;

        $completeTransactions = Transaction::with('user')
            ->where('status', 'complete')
            ->where('dropoff_location', $branch)
            ->orderBy('updated_at', 'desc')
            ->get();

        $rejectedTransactions = Transaction::with('user')
            ->where('status', 'rejected')
            ->where('dropoff_location', $branch)
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('admin.selesai', compact('completeTransactions', 'rejectedTransactions', 'tab'));
    }
}

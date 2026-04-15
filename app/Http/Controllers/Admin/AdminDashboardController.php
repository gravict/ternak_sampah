<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $completed = Transaction::where('status', 'complete');

        $totalKg = (clone $completed)->sum('actual_weight');
        $totalRp = (clone $completed)->sum('total_price');

        $catPlastik = (clone $completed)->where('category', 'like', '%Plastik%')->sum('actual_weight');
        $catKertas = (clone $completed)->where('category', 'like', '%Kertas%')->sum('actual_weight');
        $catLogam = (clone $completed)->where('category', 'like', '%Besi%')->sum('actual_weight');
        $catCampur = (clone $completed)->where('category', 'like', '%Campuran%')
            ->orWhere(function ($q) { $q->where('status', 'complete')->where('category', 'like', '%Minyak%'); })
            ->sum('actual_weight');

        $pendingCount = Transaction::where('status', 'pending')->count();
        $weighingCount = Transaction::where('status', 'weighing')->count();

        return view('admin.dashboard', compact(
            'totalKg', 'totalRp', 'catPlastik', 'catKertas', 'catLogam', 'catCampur',
            'pendingCount', 'weighingCount'
        ));
    }
}

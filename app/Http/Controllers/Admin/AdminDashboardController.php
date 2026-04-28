<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        $branch = \Illuminate\Support\Facades\Auth::user()->admin_branch;
        // Date filter
        $filter = $request->get('filter', '1y');
        $from = match($filter) {
            '7d' => now()->subDays(7)->startOfDay(),
            '1m' => now()->subMonth()->startOfDay(),
            '3m' => now()->subMonths(3)->startOfMonth(),
            '6m' => now()->subMonths(6)->startOfMonth(),
            '1y' => now()->subYear()->startOfMonth(),
            '3y' => now()->subYears(3)->startOfMonth(),
            'all' => now()->subYears(10)->startOfMonth(),
            default => now()->subYear()->startOfMonth(),
        };
        $to = now()->endOfDay();

        // Determine group format based on filter range
        $daysDiff = $from->diffInDays($to);
        if ($daysDiff <= 31) {
            $groupFormat = '%Y-%m-%d';
            $labelFormat = 'd M';
        } elseif ($daysDiff <= 90) {
            $groupFormat = '%Y-W%W';
            $labelFormat = 'W';
        } else {
            $groupFormat = '%Y-%m';
            $labelFormat = 'M Y';
        }

        $completed = Transaction::where('status', 'complete')
            ->where('dropoff_location', $branch)
            ->whereBetween('updated_at', [$from, $to]);

        // Summary stats
        $totalKg = (clone $completed)->sum('actual_weight');
        $totalRp = (clone $completed)->sum('total_price');
        $totalUsers = User::where('role', 'user')
            ->whereHas('transactions', fn($q) => $q->where('dropoff_location', $branch))
            ->count();
        $totalTransactions = (clone $completed)->count();

        // Category breakdown
        $catPlastik = (clone $completed)->where('category', 'like', '%Plastik%')->sum('actual_weight');
        $catKertas = (clone $completed)->where('category', 'like', '%Kertas%')->sum('actual_weight');
        $catLogam = (clone $completed)->where('category', 'like', '%Besi%')
            ->orWhere(fn($q) => $q->where('status', 'complete')->where('dropoff_location', $branch)->whereBetween('updated_at', [$from, $to])->where('category', 'like', '%Logam%'))
            ->orWhere(fn($q) => $q->where('status', 'complete')->where('dropoff_location', $branch)->whereBetween('updated_at', [$from, $to])->where('category', 'like', '%Tembaga%'))
            ->sum('actual_weight');
        $catMinyak = (clone $completed)->where('category', 'like', '%Minyak%')->sum('actual_weight');
        $catCampur = (clone $completed)->where('category', 'like', '%Campuran%')->sum('actual_weight');

        // Monthly/daily chart data
        $monthlyData = Transaction::where('status', 'complete')
            ->where('dropoff_location', $branch)
            ->whereBetween('updated_at', [$from, $to])
            ->select(
                DB::raw("DATE_FORMAT(updated_at, '{$groupFormat}') as period"),
                DB::raw("SUM(actual_weight) as total_kg"),
                DB::raw("SUM(total_price) as total_rp"),
                DB::raw("COUNT(*) as count")
            )
            ->groupBy('period')
            ->orderBy('period')
            ->get();

        // Per-category for stacked chart
        $categories = ['Plastik', 'Kertas', 'Besi', 'Minyak', 'Campuran'];
        $monthlyCategoryData = [];
        foreach ($categories as $cat) {
            $monthlyCategoryData[$cat] = Transaction::where('status', 'complete')
                ->where('dropoff_location', $branch)
                ->whereBetween('updated_at', [$from, $to])
                ->where('category', 'like', "%{$cat}%")
                ->select(
                    DB::raw("DATE_FORMAT(updated_at, '{$groupFormat}') as period"),
                    DB::raw("SUM(actual_weight) as total_kg")
                )
                ->groupBy('period')
                ->orderBy('period')
                ->pluck('total_kg', 'period');
        }

        // Build JSON for charts
        $periods = $monthlyData->pluck('period');
        $chartLabels = $periods->map(function($p) use ($groupFormat) {
            if (str_contains($groupFormat, '-W')) {
                return 'Minggu ' . substr($p, -2);
            }
            try {
                if (strlen($p) === 10) {
                    return \Carbon\Carbon::parse($p)->translatedFormat('d M');
                }
                return \Carbon\Carbon::createFromFormat('Y-m', $p)->translatedFormat('M Y');
            } catch (\Exception $e) {
                return $p;
            }
        })->values();

        $chartData = [
            'labels' => $chartLabels,
            'totalKg' => $monthlyData->pluck('total_kg')->values(),
            'totalRp' => $monthlyData->pluck('total_rp')->values(),
            'count' => $monthlyData->pluck('count')->values(),
            'categories' => [],
            'pie' => [
                'labels' => ['Plastik/PET', 'Kertas/Kardus', 'Besi/Logam', 'Minyak Jelantah', 'Campuran'],
                'data' => [$catPlastik, $catKertas, $catLogam, $catMinyak, $catCampur],
            ],
        ];

        foreach ($categories as $cat) {
            $chartData['categories'][$cat] = $periods->map(fn($p) => $monthlyCategoryData[$cat][$p] ?? 0)->values();
        }

        return view('admin.dashboard', compact(
            'totalKg', 'totalRp', 'totalUsers', 'totalTransactions',
            'catPlastik', 'catKertas', 'catLogam', 'catMinyak', 'catCampur',
            'chartData', 'filter'
        ));
    }
}

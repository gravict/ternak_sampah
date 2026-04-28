<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class AdminForecastController extends Controller
{
    public function generate(Request $request)
    {
        $type = $request->input('type', 'forecast');
        $filter = $request->input('filter', '1y');
        $branch = Auth::user()->admin_branch;

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

        $daysDiff = $from->diffInDays($to);
        if ($daysDiff <= 31) {
            $groupFormat = '%Y-%m-%d';
            $periodName = 'hari';
        } elseif ($daysDiff <= 90) {
            $groupFormat = '%Y-W%W';
            $periodName = 'minggu';
        } else {
            $groupFormat = '%Y-%m';
            $periodName = 'bulan';
        }

        $categories = [
            'Plastik'  => 'Plastik',
            'Kertas'   => 'Kertas',
            'Besi'     => 'Besi',
            'Minyak'   => 'Minyak',
            'Campuran' => 'Campuran',
        ];

        $historicalData = [];
        $periods = Transaction::where('status', 'complete')
            ->where('dropoff_location', $branch)
            ->whereBetween('updated_at', [$from, $to])
            ->select(DB::raw("DATE_FORMAT(updated_at, '{$groupFormat}') as period"))
            ->groupBy('period')
            ->orderBy('period')
            ->pluck('period');

        foreach ($periods as $period) {
            $row = ['periode' => $period];
            foreach ($categories as $label => $keyword) {
                if ($keyword === 'Besi') {
                    $row[$label] = (float) Transaction::where('status', 'complete')
                        ->where('dropoff_location', $branch)
                        ->whereBetween('updated_at', [$from, $to])
                        ->where(function($q) {
                            $q->where('category', 'like', "%Besi%")
                              ->orWhere('category', 'like', "%Logam%")
                              ->orWhere('category', 'like', "%Tembaga%");
                        })
                        ->whereRaw("DATE_FORMAT(updated_at, '{$groupFormat}') = ?", [$period])
                        ->sum('actual_weight');
                } else {
                    $row[$label] = (float) Transaction::where('status', 'complete')
                        ->where('dropoff_location', $branch)
                        ->where('category', 'like', "%{$keyword}%")
                        ->whereBetween('updated_at', [$from, $to])
                        ->whereRaw("DATE_FORMAT(updated_at, '{$groupFormat}') = ?", [$period])
                        ->sum('actual_weight');
                }
            }
            $historicalData[] = $row;
        }

        $summary = [];
        foreach ($categories as $label => $keyword) {
            if ($keyword === 'Besi') {
                $summary[$label] = (float) Transaction::where('status', 'complete')
                    ->where('dropoff_location', $branch)
                    ->whereBetween('updated_at', [$from, $to])
                    ->where(function($q) {
                        $q->where('category', 'like', "%Besi%")
                          ->orWhere('category', 'like', "%Logam%")
                          ->orWhere('category', 'like', "%Tembaga%");
                    })
                    ->sum('actual_weight');
            } else {
                $summary[$label] = (float) Transaction::where('status', 'complete')
                    ->where('dropoff_location', $branch)
                    ->where('category', 'like', "%{$keyword}%")
                    ->whereBetween('updated_at', [$from, $to])
                    ->sum('actual_weight');
            }
        }

        $totalAll = array_sum($summary);

        $percentage = [];
        foreach ($summary as $label => $val) {
            $percentage[$label] = $totalAll > 0 ? round(($val / $totalAll) * 100, 1) : 0;
        }

        $halfPeriod = max(1, (int) ceil($daysDiff / 2));
        $trend = [];
        foreach ($categories as $label => $keyword) {
            $qRecent = Transaction::where('status', 'complete')
                ->where('dropoff_location', $branch)
                ->whereBetween('updated_at', [now()->subDays($halfPeriod)->startOfDay(), $to]);
            
            $qPrev = Transaction::where('status', 'complete')
                ->where('dropoff_location', $branch)
                ->whereBetween('updated_at', [
                    now()->subDays($daysDiff)->startOfDay(),
                    now()->subDays($halfPeriod)->endOfDay(),
                ]);

            if ($keyword === 'Besi') {
                $qRecent->where(fn($q) => $q->where('category', 'like', '%Besi%')->orWhere('category', 'like', '%Logam%')->orWhere('category', 'like', '%Tembaga%'));
                $qPrev->where(fn($q) => $q->where('category', 'like', '%Besi%')->orWhere('category', 'like', '%Logam%')->orWhere('category', 'like', '%Tembaga%'));
            } else {
                $qRecent->where('category', 'like', "%{$keyword}%");
                $qPrev->where('category', 'like', "%{$keyword}%");
            }

            $recent = (float) $qRecent->sum('actual_weight');
            $previous = (float) $qPrev->sum('actual_weight');

            $trend[$label] = [
                'recent'   => $recent,
                'previous' => $previous,
                'delta'    => $previous > 0 ? round((($recent - $previous) / $previous) * 100, 1) : 0,
            ];
        }

        $dataJson    = json_encode($historicalData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $summaryJson = json_encode($summary, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $trendJson   = json_encode($trend, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $pctJson     = json_encode($percentage, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $nextPeriod  = $periodName === 'bulan' ? now()->addMonth()->translatedFormat('F Y') : 'Periode berikutnya';

        $prompts = [
            'forecast' => "
Kamu adalah analis data bank sampah yang sangat berpengalaman.
Kamu saat ini menganalisis data untuk cabang: {$branch}.

Berikut data historis sampah yang berhasil dikumpulkan (status: complete) per {$periodName} dalam periode {$filter} terakhir (satuan: Kg):

DATA HISTORIS PER {$periodName}:
{$dataJson}

TOTAL PERIODE INI PER KATEGORI (Kg):
{$summaryJson}

PERSENTASE KONTRIBUSI BERDASARKAN DATA NYATA (%):
{$pctJson}

TREN (SETENGAH PERIODE TERAKHIR VS SEBELUMNYA):
{$trendJson}
(delta positif = naik, negatif = turun)

Tugasmu: Buat prediksi untuk {$nextPeriod}.

Jawab dengan format PERSIS seperti ini:

**🏆 Prediksi Kategori Terbanyak {$nextPeriod}:** [nama kategori]

**📊 Probabilitas Dominasi per Kategori:**
• Plastik/PET: XX%
• Kertas/Kardus: XX%
• Besi/Logam: XX%
• Minyak Jelantah: XX%
• Campuran/Residu: XX%
(total harus 100%)

**📦 Estimasi Volume {$nextPeriod}:**
• Plastik/PET: ~XXX Kg
• Kertas/Kardus: ~XXX Kg
• Besi/Logam: ~XXX Kg
• Minyak Jelantah: ~XXX Kg
• Campuran/Residu: ~XXX Kg

**💡 Alasan & Pola yang Ditemukan:**
[Jelaskan pola tren, musiman, atau anomali dari data historis yang mendukung prediksi ini. Minimal 3 poin konkret.]
",
            'trend' => "
Kamu adalah analis tren bank sampah yang berpengalaman. Analisis untuk cabang: {$branch}.

Data historis per {$periodName} (Kg):
{$dataJson}

Tren (delta pergerakan volume):
{$trendJson}

Lakukan analisis tren dengan format berikut:

**📈 Status Tren per Kategori:**
• Plastik/PET: [Naik/Turun/Stabil] — [penjelasan singkat]
• Kertas/Kardus: [Naik/Turun/Stabil] — [penjelasan singkat]
• Besi/Logam: [Naik/Turun/Stabil] — [penjelasan singkat]
• Minyak Jelantah: [Naik/Turun/Stabil] — [penjelasan singkat]
• Campuran/Residu: [Naik/Turun/Stabil] — [penjelasan singkat]

**📅 Pola Fluktuasi yang Terdeteksi:**
[Jelaskan jika ada pola berulang]

**🏅 Kategori Paling Konsisten:**
[Kategori mana yang paling stabil volume-nya dan mengapa]

**⚠️ Kategori yang Perlu Perhatian:**
[Kategori mana yang trennya mengkhawatirkan dan kenapa]
",
            'recommendation' => "
Kamu adalah konsultan bisnis bank sampah yang ahli. Rekomendasi untuk cabang: {$branch}.

Data historis (Kg):
{$dataJson}

Total & persentase kontribusi per kategori:
{$summaryJson}
{$pctJson}

Tren terkini:
{$trendJson}

Berikan rekomendasi bisnis actionable dengan format:

**🎯 Prioritas Utama (Quick Win):**
[Kategori dan aksi yang paling cepat memberikan dampak volume]

**📣 Strategi Pemasaran ke Nasabah di Cabang {$branch}:**
• [Rekomendasi 1]
• [Rekomendasi 2]
• [Rekomendasi 3]

**💰 Rekomendasi Insentif/Harga:**
[Saran penyesuaian harga atau bonus per kategori]

**🚀 Kategori yang Harus Di-boost:**
[Kategori underperform yang memiliki potensi naik dan strategi konkretnya]

**📌 Kesimpulan Eksekutif:**
[Rangkuman 2-3 kalimat untuk pengambil keputusan]
",
        ];

        $prompt = $prompts[$type] ?? $prompts['forecast'];

        try {
            $apiKey = env('GROQ_API_KEY');

            $response = Http::timeout(30)
                ->withoutVerifying()
                ->withHeaders([
                    'Content-Type'  => 'application/json',
                    'Authorization' => 'Bearer ' . $apiKey,
                ])
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model'       => 'llama-3.3-70b-versatile',
                    'messages'    => [
                        ['role' => 'user', 'content' => $prompt]
                    ],
                    'temperature' => 0.6,
                    'max_tokens'  => 1500,
                ]);

            $result = $response->json();
            $text   = $result['choices'][0]['message']['content']
                ?? 'Groq tidak mengembalikan hasil.';

            return response()->json([
                'result' => $text,
                'source' => 'groq',
            ]);
        } catch (\Exception $e) {
            Log::error('[AdminForecastController] ' . $e->getMessage());

            return response()->json([
                'result' => '⚠️ Error: ' . $e->getMessage(),
                'source' => 'error',
            ], 500);
        }
    }
}

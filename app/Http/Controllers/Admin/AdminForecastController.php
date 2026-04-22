<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminForecastController extends Controller
{
    public function generate(Request $request)
    {
        $type = $request->input('type', 'forecast');

        // ── 1. Ambil data 12 bulan terakhir per kategori ──────────────────────
        $categories = [
            'Plastik'  => 'Plastik',
            'Kertas'   => 'Kertas',
            'Besi'     => 'Besi',
            'Minyak'   => 'Minyak',
            'Campuran' => 'Campuran',
        ];

        $from = now()->subMonths(12)->startOfMonth();
        $to   = now()->endOfDay();

        // Buat daftar bulan 12 bulan terakhir
        $monthList = collect(range(11, 0))->map(
            fn($i) =>
            now()->subMonths($i)->format('Y-m')
        );

        // Ambil data per kategori per bulan
        $historicalData = [];
        foreach ($monthList as $month) {
            $row = ['bulan' => $month];
            foreach ($categories as $label => $keyword) {
                $row[$label] = (float) Transaction::where('status', 'complete')
                    ->where('category', 'like', "%{$keyword}%")
                    ->whereRaw("DATE_FORMAT(updated_at, '%Y-%m') = ?", [$month])
                    ->sum('actual_weight');
            }
            $historicalData[] = $row;
        }

        // ── 2. Ringkasan total per kategori (12 bulan) ────────────────────────
        $summary = [];
        foreach ($categories as $label => $keyword) {
            $summary[$label] = (float) Transaction::where('status', 'complete')
                ->where('category', 'like', "%{$keyword}%")
                ->whereBetween('updated_at', [$from, $to])
                ->sum('actual_weight');
        }

        $totalAll = array_sum($summary);

        // Hitung persentase kontribusi tiap kategori dari data nyata
        $percentage = [];
        foreach ($summary as $label => $val) {
            $percentage[$label] = $totalAll > 0 ? round(($val / $totalAll) * 100, 1) : 0;
        }

        // ── 3. Rata-rata per bulan & tren (3 bulan terakhir vs 3 bulan sebelumnya)
        $trend = [];
        foreach ($categories as $label => $keyword) {
            $recent = (float) Transaction::where('status', 'complete')
                ->where('category', 'like', "%{$keyword}%")
                ->whereBetween('updated_at', [now()->subMonths(3)->startOfMonth(), $to])
                ->sum('actual_weight');

            $previous = (float) Transaction::where('status', 'complete')
                ->where('category', 'like', "%{$keyword}%")
                ->whereBetween('updated_at', [
                    now()->subMonths(6)->startOfMonth(),
                    now()->subMonths(3)->endOfMonth(),
                ])
                ->sum('actual_weight');

            $trend[$label] = [
                'recent'   => $recent,
                'previous' => $previous,
                'delta'    => $previous > 0 ? round((($recent - $previous) / $previous) * 100, 1) : 0,
            ];
        }

        // ── 4. Susun prompt sesuai type ───────────────────────────────────────
        $dataJson    = json_encode($historicalData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $summaryJson = json_encode($summary, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $trendJson   = json_encode($trend, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $pctJson     = json_encode($percentage, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $nextMonth   = now()->addMonth()->translatedFormat('F Y');

        $prompts = [

            // ── FORECASTING ────────────────────────────────────────────────────
            'forecast' => "
Kamu adalah analis data bank sampah yang sangat berpengalaman.

Berikut data historis sampah yang berhasil dikumpulkan (status: complete) per bulan dalam 12 bulan terakhir (satuan: Kg):

DATA HISTORIS PER BULAN:
{$dataJson}

TOTAL 12 BULAN TERAKHIR PER KATEGORI (Kg):
{$summaryJson}

PERSENTASE KONTRIBUSI BERDASARKAN DATA NYATA (%):
{$pctJson}

TREN (3 BULAN TERAKHIR VS 3 BULAN SEBELUMNYA):
{$trendJson}
(delta positif = naik, negatif = turun)

Tugasmu: Buat prediksi untuk bulan {$nextMonth}.

Jawab dengan format PERSIS seperti ini:

**🏆 Prediksi Kategori Terbanyak Bulan {$nextMonth}:** [nama kategori]

**📊 Probabilitas Dominasi per Kategori:**
• Plastik/PET: XX%
• Kertas/Kardus: XX%
• Besi/Logam: XX%
• Minyak Jelantah: XX%
• Campuran/Residu: XX%
(total harus 100%)

**📦 Estimasi Volume Bulan {$nextMonth}:**
• Plastik/PET: ~XXX Kg
• Kertas/Kardus: ~XXX Kg
• Besi/Logam: ~XXX Kg
• Minyak Jelantah: ~XXX Kg
• Campuran/Residu: ~XXX Kg

**💡 Alasan & Pola yang Ditemukan:**
[Jelaskan pola tren, musiman, atau anomali dari data historis yang mendukung prediksi ini. Minimal 3 poin konkret.]
",

            // ── ANALISIS TREN ──────────────────────────────────────────────────
            'trend' => "
Kamu adalah analis tren bank sampah yang berpengalaman.

Data historis 12 bulan terakhir (Kg per bulan):
{$dataJson}

Tren 3 bulan terakhir vs 3 bulan sebelumnya:
{$trendJson}

Lakukan analisis tren dengan format berikut:

**📈 Status Tren per Kategori:**
• Plastik/PET: [Naik/Turun/Stabil] — [penjelasan singkat]
• Kertas/Kardus: [Naik/Turun/Stabil] — [penjelasan singkat]
• Besi/Logam: [Naik/Turun/Stabil] — [penjelasan singkat]
• Minyak Jelantah: [Naik/Turun/Stabil] — [penjelasan singkat]
• Campuran/Residu: [Naik/Turun/Stabil] — [penjelasan singkat]

**📅 Pola Musiman yang Terdeteksi:**
[Jelaskan jika ada pola berulang di bulan-bulan tertentu]

**🏅 Kategori Paling Konsisten:**
[Kategori mana yang paling stabil volume-nya dan mengapa]

**⚠️ Kategori yang Perlu Perhatian:**
[Kategori mana yang trennya mengkhawatirkan dan kenapa]
",

            // ── REKOMENDASI BISNIS ─────────────────────────────────────────────
            'recommendation' => "
Kamu adalah konsultan bisnis bank sampah yang ahli.

Data historis 12 bulan terakhir (Kg per bulan):
{$dataJson}

Total & persentase kontribusi per kategori:
{$summaryJson}
{$pctJson}

Tren terkini (delta = perubahan % vs periode sebelumnya):
{$trendJson}

Berikan rekomendasi bisnis actionable dengan format:

**🎯 Prioritas Utama (Quick Win):**
[Kategori dan aksi yang paling cepat memberikan dampak volume]

**📣 Strategi Pemasaran ke Nasabah:**
• [Rekomendasi 1]
• [Rekomendasi 2]
• [Rekomendasi 3]

**💰 Rekomendasi Insentif/Harga:**
[Saran penyesuaian harga atau bonus per kategori untuk mendorong setoran lebih banyak]

**🚀 Kategori yang Harus Di-boost:**
[Kategori underperform yang memiliki potensi naik dan strategi konkretnya]

**📌 Kesimpulan Eksekutif:**
[Rangkuman 2-3 kalimat untuk pengambil keputusan]
",
        ];

        $prompt = $prompts[$type] ?? $prompts['forecast'];

        // ── 5. Panggil Gemini API ──────────────────────────────────────────────
        try {
            $apiKey = env('GROQ_API_KEY');

            $response = Http::timeout(30)
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

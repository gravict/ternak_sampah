<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AdminForecastController extends Controller
{
    private array $prompts = [
        'forecast' => "Kamu adalah analis data Bank Sampah profesional. Berdasarkan data historis transaksi sampah berikut, buatlah PREDIKSI/FORECASTING untuk 1 bulan ke depan. Berikan prediksi volume sampah per kategori, estimasi pendapatan, dan tren yang diprediksi. Gunakan bahasa Indonesia yang jelas dan terstruktur dengan poin-poin. Data:\n\n",
        'trend' => "Kamu adalah analis data Bank Sampah profesional. Berdasarkan data historis transaksi sampah berikut, buatlah ANALISIS TREN yang komprehensif. Identifikasi pola musiman, kategori yang tumbuh/menurun, perbandingan bulan ke bulan, dan insight menarik. Gunakan bahasa Indonesia yang jelas dan terstruktur. Data:\n\n",
        'recommendation' => "Kamu adalah konsultan bisnis Bank Sampah profesional. Berdasarkan data historis transaksi sampah berikut, berikan REKOMENDASI BISNIS yang actionable. Saran bisa mencakup: kategori mana yang perlu difokuskan, waktu operasional optimal, strategi akuisisi nasabah, peluang kemitraan, dan rencana ekspansi. Gunakan bahasa Indonesia yang jelas. Data:\n\n",
    ];

    public function generate(Request $request)
    {
        $request->validate([
            'type' => 'required|in:forecast,trend,recommendation',
        ]);

        $type = $request->type;
        $cacheKey = "forecast_{$type}_" . now()->format('Y-m-d');

        // Return cached if available
        $cached = Cache::get($cacheKey);
        if ($cached) {
            return response()->json(['result' => $cached, 'source' => 'cache']);
        }

        // Gather historical data (1 year)
        $from = now()->subYear()->startOfMonth();
        $dataSummary = $this->buildDataSummary($from);

        $apiKey = config('services.gemini.api_key');
        if (!$apiKey || $apiKey === 'your_key_here') {
            return response()->json([
                'result' => $this->fallbackResult($type),
                'source' => 'fallback',
            ]);
        }

        try {
            $result = $this->callGemini($apiKey, $type, $dataSummary);
            Cache::put($cacheKey, $result, now()->addHours(12));
            return response()->json(['result' => $result, 'source' => 'gemini']);
        } catch (\Exception $e) {
            Log::warning("Gemini forecast failed: " . $e->getMessage());
            return response()->json([
                'result' => $this->fallbackResult($type),
                'source' => 'fallback',
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function buildDataSummary(\Carbon\Carbon $from): string
    {
        // Monthly summary
        $monthly = Transaction::where('status', 'complete')
            ->where('updated_at', '>=', $from)
            ->select(
                DB::raw("strftime('%Y-%m', updated_at) as month"),
                'category',
                DB::raw("SUM(actual_weight) as total_kg"),
                DB::raw("SUM(total_price) as total_rp"),
                DB::raw("COUNT(*) as jumlah_transaksi")
            )
            ->groupBy('month', 'category')
            ->orderBy('month')
            ->get();

        if ($monthly->isEmpty()) {
            return "Belum ada data transaksi yang tercatat.";
        }

        $lines = ["Data Transaksi Bank Sampah (periode {$from->format('M Y')} - " . now()->format('M Y') . "):\n"];

        $grouped = $monthly->groupBy('month');
        foreach ($grouped as $month => $rows) {
            $totalKg = $rows->sum('total_kg');
            $totalRp = $rows->sum('total_rp');
            $totalTrx = $rows->sum('jumlah_transaksi');
            $lines[] = "Bulan {$month}: {$totalTrx} transaksi, {$totalKg} kg, Rp " . number_format($totalRp, 0, ',', '.');

            foreach ($rows as $r) {
                $lines[] = "  - {$r->category}: {$r->total_kg} kg ({$r->jumlah_transaksi} transaksi)";
            }
        }

        return implode("\n", $lines);
    }

    private function callGemini(string $apiKey, string $type, string $data): string
    {
        $prompt = ($this->prompts[$type] ?? $this->prompts['forecast']) . $data;

        $response = Http::timeout(30)->post(
            "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key={$apiKey}",
            [
                'contents' => [
                    ['parts' => [['text' => $prompt]]]
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'maxOutputTokens' => 2048,
                ],
            ]
        );

        if ($response->status() === 429) {
            throw new \Exception('API rate limit (429). Coba lagi nanti.');
        }

        if (!$response->successful()) {
            throw new \Exception('Gemini API error: ' . $response->status());
        }

        $text = $response->json('candidates.0.content.parts.0.text');
        if (!$text) {
            throw new \Exception('Empty response from Gemini API');
        }

        return $text;
    }

    private function fallbackResult(string $type): string
    {
        return match($type) {
            'forecast' => "📊 **Forecasting (Mode Offline)**\n\nSaat ini fitur AI sedang tidak tersedia. Berdasarkan data umum Bank Sampah:\n\n• Volume sampah plastik PET cenderung meningkat 5-10% per bulan di area urban\n• Musim hujan biasanya menurunkan volume setoran 15-20%\n• Kategori kardus/kertas stabil dengan fluktuasi rendah\n• Minyak jelantah meningkat pesat seiring kesadaran masyarakat\n\n*Aktifkan Gemini API untuk analisis yang dipersonalisasi dari data Anda.*",
            'trend' => "📋 **Analisis Tren (Mode Offline)**\n\nSaat ini fitur AI sedang tidak tersedia. Berikut pola umum:\n\n• Tren nasional: Partisipasi Bank Sampah naik 12% YoY\n• Plastik PET mendominasi 45-55% volume di mayoritas Bank Sampah\n• Harga besi/logam berfluktuasi mengikuti harga komoditas global\n• Peak setoran biasanya di awal bulan (setelah payday)\n\n*Aktifkan Gemini API untuk analisis dari data aktual Anda.*",
            'recommendation' => "💡 **Rekomendasi Bisnis (Mode Offline)**\n\nSaat ini fitur AI sedang tidak tersedia. Saran umum:\n\n• Fokuskan program edukasi pada kategori bernilai tinggi (besi, minyak jelantah)\n• Terapkan insentif streak untuk meningkatkan retensi nasabah\n• Jalin kemitraan dengan UMKM sekitar untuk pengolahan kardus\n• Pertimbangkan layanan Pick-up terjadwal mingguan\n\n*Aktifkan Gemini API untuk rekomendasi berdasarkan data Anda.*",
            default => "Fitur AI sedang tidak tersedia.",
        };
    }
}

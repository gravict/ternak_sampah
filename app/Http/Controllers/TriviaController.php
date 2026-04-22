<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TriviaController extends Controller
{
    /**
     * Generate AI trivia questions from news headlines using Gemini API.
     * Results are cached for 6 hours per day to save API quota.
     */
    public function generate(Request $request)
    {
        $request->validate([
            'headlines' => 'required|array|min:1|max:10',
            'headlines.*' => 'required|string|max:300',
        ]);

        $headlines = $request->input('headlines');
        $cacheKey = 'trivia_' . now()->format('Y-m-d');

        // Return cached trivia if available (6 hours)
        $cached = Cache::get($cacheKey);
        if ($cached) {
            return response()->json(['questions' => $cached, 'source' => 'cache']);
        }

        $apiKey = config('services.gemini.api_key');
        if (empty($apiKey)) {
            return response()->json([
                'questions' => $this->fallbackTrivia($headlines),
                'source' => 'fallback',
                'reason' => 'GEMINI_API_KEY not configured',
            ]);
        }

        try {
            $questions = $this->callGeminiWithRetry($apiKey, $headlines);
            Cache::put($cacheKey, $questions, now()->addHours(6));
            return response()->json(['questions' => $questions, 'source' => 'gemini']);
        } catch (\Exception $e) {
            Log::warning('Gemini API trivia generation failed: ' . $e->getMessage());
            return response()->json([
                'questions' => $this->fallbackTrivia($headlines),
                'source' => 'fallback',
                'reason' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Retry wrapper for Gemini API with exponential backoff.
     */
    private function callGeminiWithRetry(string $apiKey, array $headlines, int $maxAttempts = 3): array
    {
        $lastException = null;
        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            try {
                return $this->callGemini($apiKey, $headlines);
            } catch (\Exception $e) {
                $lastException = $e;
                if (str_contains($e->getMessage(), '429') && $attempt < $maxAttempts) {
                    $delay = pow(2, $attempt); // 2s, 4s
                    Log::info("Gemini 429 rate limit, retrying in {$delay}s (attempt {$attempt}/{$maxAttempts})");
                    sleep($delay);
                    continue;
                }
                throw $e;
            }
        }
        throw $lastException;
    }

    /**
     * Call Gemini API to generate trivia questions.
     */
    private function callGemini(string $apiKey, array $headlines): array
    {
        $headlinesList = collect($headlines)->map(fn($h, $i) => ($i + 1) . ". " . $h)->implode("\n");

        $prompt = <<<PROMPT
Kamu adalah AI pembuat kuis trivia lingkungan untuk aplikasi Bank Sampah.

Berikut adalah judul-judul berita terbaru dari Google News tentang lingkungan dan sampah di Indonesia:
{$headlinesList}

Tugasmu:
1. Buat TEPAT 2 pertanyaan trivia pilihan ganda (A, B, C, D) dalam Bahasa Indonesia.
2. Pertanyaan HARUS berkaitan dengan isi/topik berita di atas. Bukan pertanyaan generik.
3. Setiap pertanyaan memiliki 4 opsi jawaban, dan tepat 1 jawaban benar.
4. Pertanyaan harus edukatif dan terkait lingkungan, daur ulang, atau pengelolaan sampah.
5. Jawaban salah harus masuk akal (bukan jawaban ngawur).

WAJIB output HANYA dalam format JSON array berikut TANPA teks lain, tanpa markdown code block:
[
  {
    "question": "pertanyaan di sini?",
    "options": ["opsi A", "opsi B", "opsi C", "opsi D"],
    "correctIndex": 0
  }
]

correctIndex adalah indeks (0-3) dari jawaban yang benar di array options.
PROMPT;

        $response = Http::timeout(30)->post(
            "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key={$apiKey}",
            [
                'contents' => [
                    ['parts' => [['text' => $prompt]]]
                ],
                'generationConfig' => [
                    'temperature' => 0.8,
                    'maxOutputTokens' => 1024,
                    'responseMimeType' => 'application/json',
                ],
            ]
        );

        if (!$response->successful()) {
            throw new \Exception('Gemini API returned status ' . $response->status());
        }

        $body = $response->json();
        $text = $body['candidates'][0]['content']['parts'][0]['text'] ?? null;

        if (!$text) {
            throw new \Exception('No text content in Gemini response');
        }

        // Clean response — remove markdown code block if present
        $text = trim($text);
        $text = preg_replace('/^```json\s*/i', '', $text);
        $text = preg_replace('/```\s*$/', '', $text);
        $text = trim($text);

        $questions = json_decode($text, true);

        if (!is_array($questions) || count($questions) === 0) {
            throw new \Exception('Invalid JSON from Gemini: ' . substr($text, 0, 200));
        }

        // Validate structure
        $validated = [];
        foreach ($questions as $q) {
            if (
                isset($q['question'], $q['options'], $q['correctIndex']) &&
                is_array($q['options']) &&
                count($q['options']) === 4 &&
                is_int($q['correctIndex']) &&
                $q['correctIndex'] >= 0 &&
                $q['correctIndex'] <= 3
            ) {
                $validated[] = [
                    'question' => $q['question'],
                    'options' => $q['options'],
                    'correctIndex' => $q['correctIndex'],
                ];
            }
        }

        if (count($validated) === 0) {
            throw new \Exception('No valid questions parsed from Gemini response');
        }

        return array_slice($validated, 0, 2);
    }

    /**
     * Fallback trivia when Gemini API is unavailable.
     * Provides a rotating pool of educational questions about waste & environment.
     */
    private function fallbackTrivia(array $headlines): array
    {
        $pool = [
            [
                'question' => 'Berapa lama botol plastik PET terurai secara alami di alam?',
                'options' => ['450 tahun', '50 tahun', '10 tahun', '1000 tahun'],
                'correctIndex' => 0,
            ],
            [
                'question' => 'Apa warna tempat sampah yang digunakan untuk sampah anorganik (plastik, kaca, logam)?',
                'options' => ['Kuning', 'Hijau', 'Merah', 'Biru'],
                'correctIndex' => 0,
            ],
            [
                'question' => 'Manakah prinsip pengelolaan sampah yang benar?',
                'options' => ['Reduce, Reuse, Recycle', 'Buang, Bakar, Kubur', 'Kumpul, Buang, Lupakan', 'Pilah, Buang, Selesai'],
                'correctIndex' => 0,
            ],
            [
                'question' => 'Minyak jelantah (minyak goreng bekas) dapat didaur ulang menjadi apa?',
                'options' => ['Biodiesel', 'Plastik daur ulang', 'Pupuk kompos', 'Kertas daur ulang'],
                'correctIndex' => 0,
            ],
            [
                'question' => 'Sampah jenis apa yang paling banyak mencemari lautan di Indonesia?',
                'options' => ['Plastik', 'Kertas', 'Logam', 'Kaca'],
                'correctIndex' => 0,
            ],
            [
                'question' => 'Apa manfaat utama dari Bank Sampah bagi masyarakat?',
                'options' => ['Mengurangi volume sampah & memberi nilai ekonomi', 'Membakar sampah secara terorganisir', 'Menimbun sampah di lahan kosong', 'Membuang sampah ke sungai secara legal'],
                'correctIndex' => 0,
            ],
            [
                'question' => 'Berapa persen sampah plastik di Indonesia yang berhasil didaur ulang saat ini?',
                'options' => ['Kurang dari 10%', '50%', '75%', '90%'],
                'correctIndex' => 0,
            ],
            [
                'question' => 'Indonesia berada di peringkat berapa sebagai penghasil sampah plastik laut terbesar di dunia?',
                'options' => ['Peringkat 2', 'Peringkat 1', 'Peringkat 5', 'Peringkat 10'],
                'correctIndex' => 0,
            ],
        ];

        // Rotate based on day of the year so questions change daily
        $dayOfYear = (int) now()->format('z');
        $startIdx = ($dayOfYear * 2) % count($pool);
        $q1 = $pool[$startIdx % count($pool)];
        $q2 = $pool[($startIdx + 1) % count($pool)];

        return [$q1, $q2];
    }
}

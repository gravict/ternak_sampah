<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TriviaController extends Controller
{
    public function generate(Request $request)
    {
        $request->validate([
            'headlines'               => 'required|array|min:1|max:10',
            'headlines.*.title'       => 'required|string|max:300',
            'headlines.*.description' => 'nullable|string|max:1000',
        ]);

        $headlines = $request->input('headlines');
        $cacheKey  = 'trivia_' . now()->format('Y-m-d');

        $cached = Cache::get($cacheKey);
        if ($cached) {
            return response()->json(['questions' => $cached, 'source' => 'cache']);
        }

        $apiKey = env('GROQ_API_KEY');
        if (empty($apiKey)) {
            return response()->json([
                'questions' => $this->fallbackTrivia($headlines),
                'source'    => 'fallback',
                'reason'    => 'GROQ_API_KEY not configured',
            ]);
        }

        try {
            $questions = $this->callGroqWithRetry($apiKey, $headlines);
            Cache::put($cacheKey, $questions, now()->addHours(6));
            return response()->json(['questions' => $questions, 'source' => 'groq']);
        } catch (\Exception $e) {
            Log::warning('Groq API trivia generation failed: ' . $e->getMessage());
            return response()->json([
                'questions' => $this->fallbackTrivia($headlines),
                'source'    => 'fallback',
                'reason'    => $e->getMessage(),
            ]);
        }
    }

    private function callGroqWithRetry(string $apiKey, array $headlines, int $maxAttempts = 3): array
    {
        $lastException = null;
        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            try {
                return $this->callGroq($apiKey, $headlines);
            } catch (\Exception $e) {
                $lastException = $e;
                if (str_contains($e->getMessage(), '429') && $attempt < $maxAttempts) {
                    $delay = pow(2, $attempt);
                    Log::info("Groq 429 rate limit, retrying in {$delay}s (attempt {$attempt}/{$maxAttempts})");
                    sleep($delay);
                    continue;
                }
                throw $e;
            }
        }
        throw $lastException;
    }

    private function callGroq(string $apiKey, array $headlines): array
    {
        $headlinesList = collect($headlines)->map(function ($h, $i) {
            $num   = $i + 1;
            $title = is_array($h) ? ($h['title'] ?? '') : $h;
            $desc  = is_array($h) ? ($h['description'] ?? '') : '';
            $text  = "{$num}. {$title}";
            if (!empty(trim($desc))) {
                $text .= "\n   Ringkasan: " . trim($desc);
            }
            return $text;
        })->implode("\n\n");

        $prompt = <<<PROMPT
Kamu adalah AI pembuat kuis trivia lingkungan untuk aplikasi Bank Sampah Indonesia.

Berikut adalah berita terbaru beserta ringkasannya:
{$headlinesList}

Tugasmu: Buat TEPAT 2 pertanyaan trivia pilihan ganda dalam Bahasa Indonesia.

ATURAN PENTING:
1. Pertanyaan harus berkaitan dengan TEMA atau TOPIK dari berita di atas.
2. JANGAN buat pertanyaan tentang angka/statistik/persentase spesifik kecuali angka tersebut DISEBUTKAN JELAS di ringkasan berita di atas.
3. Fokus pada pertanyaan konseptual yang bisa dijawab dari pengetahuan umum tentang lingkungan.
4. Setiap pertanyaan memiliki 4 opsi jawaban, tepat 1 benar, opsi salah harus masuk akal.
5. Pertanyaan harus edukatif dan menarik untuk pengguna bank sampah.

WAJIB output HANYA JSON array berikut TANPA teks lain, tanpa markdown code block:
[
  {
    "question": "pertanyaan di sini?",
    "options": ["opsi A", "opsi B", "opsi C", "opsi D"],
    "correctIndex": 0
  }
]

correctIndex adalah indeks (0-3) dari jawaban yang benar di array options.
PROMPT;

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
                'temperature' => 0.8,
                'max_tokens'  => 1024,
            ]);

        if (!$response->successful()) {
            throw new \Exception('Groq API returned status ' . $response->status() . ': ' . $response->body());
        }

        $body = $response->json();
        $text = $body['choices'][0]['message']['content'] ?? null;

        if (!$text) {
            throw new \Exception('No text content in Groq response');
        }

        $text = trim($text);
        $text = preg_replace('/^```json\s*/i', '', $text);
        $text = preg_replace('/^```\s*/i', '', $text);
        $text = preg_replace('/```\s*$/', '', $text);
        $text = trim($text);

        $questions = json_decode($text, true);

        if (!is_array($questions) || count($questions) === 0) {
            throw new \Exception('Invalid JSON from Groq: ' . substr($text, 0, 200));
        }

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
                    'question'     => $q['question'],
                    'options'      => $q['options'],
                    'correctIndex' => $q['correctIndex'],
                ];
            }
        }

        if (count($validated) === 0) {
            throw new \Exception('No valid questions parsed from Groq response');
        }

        return array_slice($validated, 0, 2);
    }

    private function fallbackTrivia(array $headlines): array
    {
        $pool = [
            [
                'question'     => 'Berapa lama botol plastik PET terurai secara alami di alam?',
                'options'      => ['450 tahun', '50 tahun', '10 tahun', '1000 tahun'],
                'correctIndex' => 0,
            ],
            [
                'question'     => 'Manakah prinsip pengelolaan sampah yang benar?',
                'options'      => ['Reduce, Reuse, Recycle', 'Buang, Bakar, Kubur', 'Kumpul, Buang, Lupakan', 'Pilah, Buang, Selesai'],
                'correctIndex' => 0,
            ],
            [
                'question'     => 'Minyak jelantah dapat didaur ulang menjadi apa?',
                'options'      => ['Biodiesel', 'Plastik daur ulang', 'Pupuk kompos', 'Kertas daur ulang'],
                'correctIndex' => 0,
            ],
            [
                'question'     => 'Sampah jenis apa yang paling banyak mencemari lautan di Indonesia?',
                'options'      => ['Plastik', 'Kertas', 'Logam', 'Kaca'],
                'correctIndex' => 0,
            ],
            [
                'question'     => 'Apa manfaat utama dari Bank Sampah bagi masyarakat?',
                'options'      => ['Mengurangi volume sampah & memberi nilai ekonomi', 'Membakar sampah secara terorganisir', 'Menimbun sampah di lahan kosong', 'Membuang sampah ke sungai secara legal'],
                'correctIndex' => 0,
            ],
            [
                'question'     => 'Indonesia berada di peringkat berapa sebagai penghasil sampah plastik laut terbesar di dunia?',
                'options'      => ['Peringkat 2', 'Peringkat 1', 'Peringkat 5', 'Peringkat 10'],
                'correctIndex' => 0,
            ],
        ];

        $dayOfYear = (int) now()->format('z');
        $startIdx  = ($dayOfYear * 2) % count($pool);

        return [
            $pool[$startIdx % count($pool)],
            $pool[($startIdx + 1) % count($pool)],
        ];
    }
}

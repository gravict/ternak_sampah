<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class TriviaController extends Controller
{
    public function answer(Request $request)
    {
        $request->validate([
            'is_correct' => 'required|boolean',
            'question_index' => 'required|integer|min:0'
        ]);
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $today = now()->toDateString();

        if ($user->last_trivia_date === $today) {
            return response()->json(['success' => false, 'message' => 'Kamu sudah menjawab semua trivia hari ini.']);
        }

        $cacheKey = 'trivia_ans_' . $user->id . '_' . $today;
        $answeredIndices = Cache::get($cacheKey, []);

        if (count($answeredIndices) >= 2) {
            return response()->json(['success' => false, 'message' => 'Kamu sudah menjawab maksimal 2 pertanyaan hari ini.']);
        }

        if (in_array($request->question_index, $answeredIndices)) {
            return response()->json(['success' => false, 'message' => 'Kamu sudah menjawab pertanyaan ini.']);
        }

        if ($request->is_correct) {
            $user->points += 2;
        }

        $answeredIndices[] = $request->question_index;
        Cache::put($cacheKey, $answeredIndices, now()->addDay());

        $answeredCount = count($answeredIndices);

        // Update streak and last_trivia_date only after completing both questions
        if ($answeredCount >= 2) {
            $yesterday = now()->subDay()->toDateString();
            if ($user->last_trivia_date === $yesterday) {
                $user->streak += 1;
            } elseif ($user->last_trivia_date !== $today) {
                $user->streak = 1;
            }
            $user->last_trivia_date = $today;
        }
        
        $user->save();

        return response()->json([
            'success'   => true,
            'points'    => $user->points,
            'streak'    => $user->streak,
            'completed' => $answeredCount >= 2
        ]);
    }
    public function generate(Request $request)
    {
        $request->validate([
            'headlines'               => 'required|array|min:1|max:10',
            'headlines.*.title'       => 'required|string|max:300',
            'headlines.*.description' => 'nullable|string|max:2000',
            'headlines.*.url'         => 'nullable|string|max:2000',
        ]);

        $headlines = $request->input('headlines');
        $cacheKey  = 'trivia_' . now()->format('Y-m-d');

        $cached = Cache::get($cacheKey);
        if ($cached) {
            // Handle both old cache format (array of questions) and new format (array with 'questions' and 'sources')
            $questions = isset($cached['questions']) ? $cached['questions'] : $cached;
            $sources = isset($cached['sources']) ? $cached['sources'] : [];
            return response()->json([
                'questions' => $questions,
                'sources'   => $sources,
                'source'    => 'cache'
            ]);
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
            $result = $this->callGroqWithRetry($apiKey, $headlines);
            Cache::put($cacheKey, $result, now()->addHours(6));
            return response()->json(['questions' => $result['questions'], 'sources' => $result['sources'], 'source' => 'groq']);
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
        // Build article list with full content for AI
        $headlinesList = collect($headlines)->map(function ($h, $i) {
            $num   = $i + 1;
            $title = is_array($h) ? ($h['title'] ?? '') : $h;
            $desc  = is_array($h) ? ($h['description'] ?? '') : '';
            $url   = is_array($h) ? ($h['url'] ?? '') : '';

            // Attempt to scrape full article content
            if (!empty($url)) {
                try {
                    $response = Http::timeout(8)
                        ->withOptions(['allow_redirects' => true])
                        ->withHeaders([
                            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
                        ])
                        ->get($url);

                    if ($response->successful()) {
                        $html = $response->body();
                        // Prevent warnings from malformed HTML
                        libxml_use_internal_errors(true);
                        $doc = new \DOMDocument();
                        // Convert encoding to prevent weird characters
                        @$doc->loadHTML('<?xml encoding="UTF-8">' . $html);
                        $paragraphs = $doc->getElementsByTagName('p');
                        
                        $scrapedText = '';
                        foreach ($paragraphs as $p) {
                            $clean = trim($p->textContent);
                            // Only include substantive paragraphs
                            if (strlen($clean) > 60) {
                                $scrapedText .= $clean . "\n\n";
                            }
                        }
                        
                        // If we successfully extracted text, use it instead of the short RSS description
                        if (strlen($scrapedText) > 200) {
                            // Limit to 4000 chars to avoid exceeding Groq prompt token limits
                            $desc = substr($scrapedText, 0, 4000) . "..."; 
                        }
                    }
                } catch (\Exception $e) {
                    // If scraping fails (timeout, blocked, etc.), it silently falls back to the RSS description
                }
            }

            $text  = "BERITA {$num}: {$title}";
            if (!empty(trim($desc))) {
                $text .= "\nISI BERITA: " . trim($desc);
            }
            return $text;
        })->implode("\n\n---\n\n");

        // Collect source URLs for each article
        $sourceUrls = collect($headlines)->map(function ($h) {
            return is_array($h) ? ($h['url'] ?? '') : '';
        })->toArray();

        $prompt = <<<PROMPT
Kamu adalah AI pembuat kuis trivia untuk aplikasi Bank Sampah Indonesia.

Berikut adalah BERITA UTAMA hari ini beserta ISI LENGKAPNYA:

{$headlinesList}

Tugasmu: Buat TEPAT 2 pertanyaan trivia pilihan ganda dalam Bahasa Indonesia berdasarkan berita di atas.

ATURAN PENTING:
1. Kedua pertanyaan HARUS berdasarkan DETAIL SPESIFIK yang ada di dalam ISI BERITA di atas (misalnya: fakta, lokasi, nama program, angka, penyebab, dampak, atau solusi yang disebutkan).
2. JANGAN buat pertanyaan pengetahuan umum yang bisa dijawab tanpa membaca beritanya.
3. Tujuan: User HARUS membaca isi berita terlebih dahulu agar bisa menjawab pertanyaan dengan benar.
4. Setiap pertanyaan memiliki 4 opsi jawaban, tepat 1 benar, opsi salah harus masuk akal dan mirip tapi salah.
5. Pertanyaan harus edukatif dan menarik.
6. Karena hanya ada 1 berita sumber, selalu isi "sourceIndex" dengan 0.

WAJIB output HANYA JSON array berikut TANPA teks lain, tanpa markdown code block:
[
  {
    "question": "Menurut berita, ...?",
    "options": ["opsi A", "opsi B", "opsi C", "opsi D"],
    "correctIndex": 0,
    "sourceIndex": 0
  }
]

correctIndex adalah indeks (0-3) dari jawaban yang benar di array options.
sourceIndex adalah indeks (0-based) dari berita yang menjadi sumber pertanyaan.
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
                $sourceIdx = isset($q['sourceIndex']) && is_int($q['sourceIndex']) ? $q['sourceIndex'] : 0;
                $validated[] = [
                    'question'     => $q['question'],
                    'options'      => $q['options'],
                    'correctIndex' => $q['correctIndex'],
                    'sourceUrl'    => $sourceUrls[$sourceIdx] ?? '',
                    'sourceTitle'  => is_array($headlines[$sourceIdx] ?? null) ? ($headlines[$sourceIdx]['title'] ?? '') : '',
                ];
            }
        }

        if (count($validated) === 0) {
            throw new \Exception('No valid questions parsed from Groq response');
        }

        return [
            'questions' => array_slice($validated, 0, 2),
            'sources'   => $sourceUrls,
        ];
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

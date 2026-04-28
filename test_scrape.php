<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

use Illuminate\Support\Facades\Http;

$url = 'https://news.google.com/rss/articles/CBMiJGh0dHBzOi8vd3d3LmNubi5pbmRvbmVzaWEuY29tL...'; // Placeholder, I will fetch real rss
$rssUrl = 'https://news.google.com/rss/search?q=sampah+lingkungan+indonesia&hl=id&gl=ID&ceid=ID:id';
$rssXml = file_get_contents($rssUrl);
$rss = simplexml_load_string($rssXml);
$firstLink = (string) $rss->channel->item[0]->link;

echo "Fetching: " . $firstLink . "\n";

try {
    $response = Http::timeout(10)
        ->withOptions(['allow_redirects' => true])
        ->withHeaders([
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
        ])
        ->get($firstLink);

    $html = $response->body();
    
    $doc = new DOMDocument();
    @$doc->loadHTML('<?xml encoding="UTF-8">' . $html);
    $paragraphs = $doc->getElementsByTagName('p');
    
    $text = '';
    foreach ($paragraphs as $p) {
        $clean = trim($p->textContent);
        if (strlen($clean) > 50) { 
            $text .= $clean . "\n\n";
        }
    }
    
    echo "Extracted Text Length: " . strlen($text) . "\n";
    echo "Preview:\n" . substr($text, 0, 1000) . "\n";

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

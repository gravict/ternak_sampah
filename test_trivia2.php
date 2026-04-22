<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

$request = Illuminate\Http\Request::create('/trivia/generate', 'POST', [
    'headlines' => [
        [
            'title' => 'Pemerintah Targetkan Pengurangan Sampah Plastik 70 Persen di 2025',
            'description' => 'Menteri Lingkungan Hidup menegaskan komitmen Indonesia mengurangi sampah plastik hingga 70 persen pada 2025 melalui program ekonomi sirkular.',
            'url' => 'http://example.com/news/1'
        ]
    ]
]);

$controller = app()->make(App\Http\Controllers\TriviaController::class);
try {
    $response = $controller->generate($request);
    echo "Status: " . $response->getStatusCode() . "\n";
    echo "Content: " . $response->getContent() . "\n";
} catch (\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}

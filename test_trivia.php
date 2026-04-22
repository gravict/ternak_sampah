<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::create('/trivia/generate', 'POST', [
    'headlines' => [
        [
            'title' => 'Pemerintah Targetkan Pengurangan Sampah Plastik 70 Persen di 2025',
            'description' => 'Menteri Lingkungan Hidup menegaskan komitmen Indonesia mengurangi sampah plastik hingga 70 persen pada 2025 melalui program ekonomi sirkular.',
            'url' => 'http://example.com/news/1'
        ],
        [
            'title' => 'Candi Borobudur Dibersihkan dari 1 Ton Sampah',
            'description' => 'Relawan membersihkan Candi Borobudur dan berhasil mengumpulkan sekitar 1 ton sampah plastik yang dibuang sembarangan oleh pengunjung.',
            'url' => 'http://example.com/news/2'
        ]
    ]
]);
$response = $kernel->handle($request);
echo "Status: " . $response->getStatusCode() . "\n";
echo "Content: " . $response->getContent() . "\n";

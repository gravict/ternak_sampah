@extends('layouts.admin')
@section('title', 'Admin Dashboard | TernakSampah')

@section('styles')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
@endsection

@section('content')
    {{-- Header + Filter --}}
    <div class="mb-6 sm:mb-8 flex flex-col sm:flex-row sm:justify-between sm:items-end gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-800">Ringkasan Bank Sampah</h1>
            <p class="text-slate-500 text-sm">Data akumulasi seluruh transaksi.</p>
        </div>
        <form method="GET" class="flex items-center gap-2">
            <label class="text-xs font-bold text-slate-500">Filter:</label>
            <select name="filter" onchange="this.form.submit()"
                class="text-sm font-bold border border-slate-200 rounded-lg px-3 py-2 bg-white outline-none focus:border-green-500">
                <option value="7d" {{ ($filter ?? '1y') == '7d' ? 'selected' : '' }}>7 Hari</option>
                <option value="1m" {{ ($filter ?? '1y') == '1m' ? 'selected' : '' }}>1 Bulan</option>
                <option value="3m" {{ ($filter ?? '1y') == '3m' ? 'selected' : '' }}>3 Bulan</option>
                <option value="6m" {{ ($filter ?? '1y') == '6m' ? 'selected' : '' }}>6 Bulan</option>
                <option value="1y" {{ ($filter ?? '1y') == '1y' ? 'selected' : '' }}>1 Tahun</option>
                <option value="3y" {{ ($filter ?? '1y') == '3y' ? 'selected' : '' }}>3 Tahun</option>
                <option value="all" {{ ($filter ?? '1y') == 'all' ? 'selected' : '' }}>Semua</option>
            </select>
        </form>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4 mb-6 sm:mb-8">
        <div class="bg-white p-4 sm:p-5 rounded-2xl shadow-sm border border-slate-200">
            <p class="text-[10px] sm:text-xs text-slate-500 font-bold uppercase tracking-wider mb-1">Total Sampah</p>
            <h3 class="text-xl sm:text-3xl font-extrabold text-slate-800">{{ number_format($totalKg, 1) }} <span
                    class="text-xs sm:text-sm text-slate-400">Kg</span></h3>
        </div>
        <div class="bg-white p-4 sm:p-5 rounded-2xl shadow-sm border border-slate-200">
            <p class="text-[10px] sm:text-xs text-slate-500 font-bold uppercase tracking-wider mb-1">Total Dana</p>
            <h3 class="text-lg sm:text-3xl font-extrabold text-green-600">Rp {{ number_format($totalRp, 0, ',', '.') }}</h3>
        </div>
        <div class="bg-white p-4 sm:p-5 rounded-2xl shadow-sm border border-slate-200">
            <p class="text-[10px] sm:text-xs text-slate-500 font-bold uppercase tracking-wider mb-1">Total Nasabah</p>
            <h3 class="text-xl sm:text-3xl font-extrabold text-slate-800">{{ $totalUsers }}</h3>
        </div>
        <div class="bg-white p-4 sm:p-5 rounded-2xl shadow-sm border border-slate-200">
            <p class="text-[10px] sm:text-xs text-slate-500 font-bold uppercase tracking-wider mb-1">Total Transaksi</p>
            <h3 class="text-xl sm:text-3xl font-extrabold text-slate-800">{{ $totalTransactions }}</h3>
        </div>
    </div>

    {{-- Charts Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6 mb-6 sm:mb-8">
        {{-- Line Chart --}}
        <div class="lg:col-span-2 bg-white p-4 sm:p-6 rounded-2xl shadow-sm border border-slate-200">
            <h3 class="text-base sm:text-lg font-extrabold text-slate-800 mb-4">📈 Sampah Masuk per Bulan (Kg)</h3>
            <canvas id="lineChart" class="w-full" style="max-height: 320px;"></canvas>
        </div>
        {{-- Pie Chart --}}
        <div class="bg-white p-4 sm:p-6 rounded-2xl shadow-sm border border-slate-200">
            <h3 class="text-base sm:text-lg font-extrabold text-slate-800 mb-4">🥧 Komposisi Kategori</h3>
            <canvas id="pieChart" class="w-full" style="max-height: 320px;"></canvas>
        </div>
    </div>

    {{-- Category Breakdown --}}
    <h3 class="text-base sm:text-lg font-extrabold text-slate-800 mb-4">Rincian per Kategori</h3>
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3 sm:gap-4 mb-6 sm:mb-8">
        <div class="bg-white p-3 sm:p-4 rounded-2xl shadow-sm border border-slate-200 border-b-4 border-b-blue-500">
            <p class="text-[10px] sm:text-xs text-slate-500 font-bold mb-1">Plastik / PET</p>
            <h4 class="text-lg sm:text-xl font-extrabold">{{ number_format($catPlastik, 1) }} <span
                    class="text-[10px] sm:text-xs text-slate-400">Kg</span></h4>
        </div>
        <div class="bg-white p-3 sm:p-4 rounded-2xl shadow-sm border border-slate-200 border-b-4 border-b-yellow-500">
            <p class="text-[10px] sm:text-xs text-slate-500 font-bold mb-1">Kertas / Kardus</p>
            <h4 class="text-lg sm:text-xl font-extrabold">{{ number_format($catKertas, 1) }} <span
                    class="text-[10px] sm:text-xs text-slate-400">Kg</span></h4>
        </div>
        <div class="bg-white p-3 sm:p-4 rounded-2xl shadow-sm border border-slate-200 border-b-4 border-b-slate-800">
            <p class="text-[10px] sm:text-xs text-slate-500 font-bold mb-1">Logam / Besi</p>
            <h4 class="text-lg sm:text-xl font-extrabold">{{ number_format($catLogam, 1) }} <span
                    class="text-[10px] sm:text-xs text-slate-400">Kg</span></h4>
        </div>
        <div class="bg-white p-3 sm:p-4 rounded-2xl shadow-sm border border-slate-200 border-b-4 border-b-amber-600">
            <p class="text-[10px] sm:text-xs text-slate-500 font-bold mb-1">Minyak Jelantah</p>
            <h4 class="text-lg sm:text-xl font-extrabold">{{ number_format($catMinyak, 1) }} <span
                    class="text-[10px] sm:text-xs text-slate-400">Kg</span></h4>
        </div>
        <div class="bg-white p-3 sm:p-4 rounded-2xl shadow-sm border border-slate-200 border-b-4 border-b-orange-500 col-span-2 sm:col-span-1">
            <p class="text-[10px] sm:text-xs text-slate-500 font-bold mb-1">Campuran / Residu</p>
            <h4 class="text-lg sm:text-xl font-extrabold">{{ number_format($catCampur, 1) }} <span
                    class="text-[10px] sm:text-xs text-slate-400">Kg</span></h4>
        </div>
    </div>

    {{-- AI Forecasting Panel --}}
    <div class="bg-white p-4 sm:p-6 rounded-2xl shadow-sm border border-slate-200 mb-6 sm:mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4 mb-4">
            <div>
                <h3 class="text-lg sm:text-xl font-extrabold text-slate-800 flex items-center gap-2">🤖 AI Insight & Forecasting</h3>
                <p class="text-slate-500 text-xs sm:text-sm mt-1">Analisis otomatis dari data historis bank sampah.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <button onclick="generateForecast('forecast')" id="btn-forecast"
                    class="bg-green-500 hover:bg-green-600 text-white text-xs font-bold px-3 sm:px-4 py-2 rounded-lg transition shadow-md flex items-center gap-1 flex-1 sm:flex-none justify-center">
                    📊 Forecasting
                </button>
                <button onclick="generateForecast('trend')" id="btn-trend"
                    class="bg-blue-500 hover:bg-blue-600 text-white text-xs font-bold px-3 sm:px-4 py-2 rounded-lg transition shadow-md flex items-center gap-1 flex-1 sm:flex-none justify-center">
                    📋 Analisis Tren
                </button>
                <button onclick="generateForecast('recommendation')" id="btn-recommendation"
                    class="bg-purple-500 hover:bg-purple-600 text-white text-xs font-bold px-3 sm:px-4 py-2 rounded-lg transition shadow-md flex items-center gap-1 w-full sm:w-auto justify-center">
                    💡 Rekomendasi
                </button>
            </div>
        </div>

        <div id="ai-result" class="bg-slate-50 rounded-xl p-4 sm:p-5 min-h-[120px] border border-slate-100">
            <p class="text-slate-500 text-sm italic">Klik salah satu tombol di atas untuk memulai analisis AI...</p>
        </div>
    </div>

    {{-- Rekening Sentral Info --}}
    <div class="bg-white p-4 sm:p-5 rounded-2xl shadow-sm border border-slate-200 flex items-start gap-4">
        <div
            class="w-10 h-10 sm:w-12 sm:h-12 bg-green-100 text-green-600 rounded-xl flex items-center justify-center text-lg sm:text-xl flex-shrink-0">
            🏦</div>
        <div>
            <h4 class="font-bold text-slate-800 text-sm">Rekening Sentral TernakSampah</h4>
            <p class="text-xs text-slate-500 mt-1">Seluruh pembayaran ke nasabah bersumber dari rekening sentral ini.</p>
            <div class="mt-2 bg-slate-50 px-3 sm:px-4 py-2 rounded-lg border border-slate-100 inline-block">
                <p class="font-bold text-slate-800 text-xs sm:text-sm">Bank BCA — 5270 3456 78 <span
                        class="text-slate-400 font-normal ml-1">a.n. PT TernakSampah Indonesia</span></p>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        const chartData = @json($chartData);

        // === Line Chart (By category) ===
        const catColors = {
            'Plastik': 'rgba(59, 130, 246, 1)',
            'Kertas': 'rgba(234, 179, 8, 1)',
            'Besi': 'rgba(30, 41, 59, 1)',
            'Minyak': 'rgba(180, 83, 9, 1)',
            'Campuran': 'rgba(249, 115, 22, 1)',
        };

        const lineDatasets = Object.keys(chartData.categories).map(cat => ({
            label: cat,
            data: chartData.categories[cat],
            borderColor: catColors[cat] || 'rgba(100,100,100,1)',
            backgroundColor: catColors[cat] || 'rgba(100,100,100,1)',
            borderWidth: 2,
            tension: 0.3,
            pointRadius: 3,
            pointBackgroundColor: '#fff',
            fill: false,
        }));

        new Chart(document.getElementById('lineChart'), {
            type: 'line',
            data: {
                labels: chartData.labels,
                datasets: lineDatasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: {
                                size: 10,
                                weight: 'bold'
                            },
                            padding: 8,
                            boxWidth: 12,
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 10
                            },
                            maxRotation: 45,
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            font: {
                                size: 10
                            },
                            callback: v => v + ' kg'
                        }
                    },
                },
            },
        });

        // === Pie Chart ===
        const pieColors = ['rgba(59,130,246,0.85)', 'rgba(234,179,8,0.85)', 'rgba(30,41,59,0.85)', 'rgba(180,83,9,0.85)',
            'rgba(249,115,22,0.85)'
        ];
        new Chart(document.getElementById('pieChart'), {
            type: 'doughnut',
            data: {
                labels: chartData.pie.labels,
                datasets: [{
                    data: chartData.pie.data,
                    backgroundColor: pieColors,
                    borderWidth: 2,
                    borderColor: '#fff',
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: {
                                size: 9,
                                weight: 'bold'
                            },
                            padding: 6,
                            boxWidth: 10,
                        }
                    },
                },
            },
        });

        // === AI Forecasting ===
        async function generateForecast(type) {
            const resultDiv = document.getElementById('ai-result');
            const btn = document.getElementById('btn-' + type);

            // Disable all buttons
            document.querySelectorAll('[id^="btn-"]').forEach(b => {
                b.disabled = true;
                b.classList.add('opacity-50');
            });

            resultDiv.innerHTML =
                '<div class="flex items-center gap-3"><div class="w-5 h-5 border-2 border-green-500 border-t-transparent rounded-full animate-spin"></div><span class="text-green-600 font-bold text-sm">Menganalisis data... (bisa memakan waktu 10-20 detik)</span></div>';

            const filter = new URLSearchParams(window.location.search).get('filter') || '1y';

            try {
                const res = await fetch('{{ route('admin.forecast') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        type: type,
                        filter: filter
                    }),
                });
                const data = await res.json();

                // ✅ Baru
                const source = data.source === 'gemini' ? '🟢 Powered by AI' :
                    data.source === 'groq' ? '🟣 Powered by AI' :
                    data.source === 'cache' ? '📦 Dari Cache' :
                    '🔶 Mode Offline';

                // Convert markdown-like formatting to HTML
                let html = data.result
                    .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                    .replace(/\n/g, '<br>')
                    .replace(/• /g, '&bull; ');

                resultDiv.innerHTML = `
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-4 border-b border-slate-200 pb-3 gap-2">
                <span class="text-xs font-bold text-slate-500 uppercase">${type === 'forecast' ? '📊 Forecasting' : type === 'trend' ? '📋 Analisis Tren' : '💡 Rekomendasi Bisnis'}</span>
                <span class="text-[10px] font-bold px-2 py-1 rounded-md ${data.source === 'gemini' ? 'bg-green-100 text-green-700 border border-green-200' : 'bg-orange-100 text-orange-700 border border-orange-200'}">${source}</span>
            </div>
            <div class="text-xs sm:text-sm text-slate-700 leading-relaxed space-y-2">${html}</div>
        `;
            } catch (e) {
                resultDiv.innerHTML =
                    '<p class="text-red-500 font-bold text-sm">⚠️ Gagal memuat analisis. Coba lagi nanti.</p>';
            }

            // Re-enable buttons
            document.querySelectorAll('[id^="btn-"]').forEach(b => {
                b.disabled = false;
                b.classList.remove('opacity-50');
            });
        }
    </script>
@endsection

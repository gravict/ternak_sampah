@extends('layouts.app')
@section('title', 'Dashboard | TernakSampah')

@section('content')
    <div class="mb-10">
        <div class="flex justify-between items-end mb-4">
            <h2 class="text-2xl font-extrabold flex items-center gap-2">Update Hari Ini 📰</h2>
            <span id="date-badge"
                class="text-xs font-bold text-green-600 bg-green-50 px-3 py-1 rounded-full border border-green-200 shadow-sm">{{ now()->translatedFormat('d F Y') }}</span>
        </div>
        <div id="dash-news-container"
            class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden relative flex flex-col justify-end min-h-[250px] md:min-h-[300px] p-4 md:p-6 group cursor-pointer">
            <div
                class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1611284446314-60a58ac0deb9?q=80&w=1200')] bg-cover bg-center group-hover:scale-105 transition duration-700">
            </div>
            <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/60 to-transparent"></div>
            <div class="relative z-10 text-white w-full lg:w-2/3">
                <span class="bg-red-500 text-white text-[10px] md:text-xs font-bold px-2 py-1 rounded mb-2 inline-block shadow-md">🌍 Live Google News</span>
                <h3 id="dash-news-title" class="text-xl md:text-3xl font-extrabold mb-2 leading-tight">Memuat berita terkini...</h3>
                <p id="dash-news-desc" class="text-xs md:text-sm text-slate-300 line-clamp-2">Sedang mengambil berita terbaru dari
                    Google News...</p>
            </div>
        </div>
    </div>

    <div
        class="bg-white p-4 md:p-6 rounded-3xl shadow-sm border border-slate-100 mb-6 flex flex-col md:flex-row justify-between md:items-center gap-4">
        <div class="w-full md:w-auto">
            <h1 class="text-xl font-extrabold mb-1">Dampak Positifmu, <span
                    class="text-green-600">{{ $user->name }}</span>!</h1>
            <div>
                <div class="flex items-center gap-4 w-full md:w-96">
                    @php
                        $progress = $user->kontribusiProgress();
                        $nextKontribusi = $user->kontribusiUntukNaikLevel();
                        $baseForCurrentLevel = \App\Models\User::totalKontribusiSampaiLevel($user->level);
                        $currentInLevel = $user->kontribusi - $baseForCurrentLevel;
                    @endphp
                    <span class="text-sm font-bold text-slate-500 w-16">Level {{ $user->level }}</span>
                    <div class="flex-1 bg-slate-100 h-4 rounded-full overflow-hidden progress-glow">
                        <div class="bg-gradient-to-r from-green-400 to-green-600 h-full transition-all duration-1000"
                            style="width: {{ $progress }}%"></div>
                    </div>
                    <span class="text-sm font-bold text-green-600 w-16 text-right">{{ $user->level >= 20 ? 'MAX' : 'Level ' . ($user->level + 1) }}</span>
                </div>
                @if($user->level < 20)
                <p class="text-xs text-slate-400 mt-2 text-center md:text-left">
                    {{ number_format($currentInLevel) }} / {{ number_format($nextKontribusi) }} Kontribusi untuk Level {{ $user->level + 1 }}
                </p>
                @else
                <p class="text-xs text-green-600 font-bold mt-2 text-center md:text-left">
                    Level Maksimal Tercapai! 🎉
                </p>
                @endif
            </div>
        </div>
        <a href="{{ route('profile') }}#withdraw"
            class="bg-slate-800 text-white px-6 py-3 rounded-xl font-bold hover:bg-slate-900 transition flex items-center justify-center gap-2 shadow-lg text-sm w-full md:w-auto text-center">
            💳 Tarik Saldo
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6 md:space-y-8">
            <div
                class="bg-white rounded-3xl p-4 md:p-6 shadow-sm border border-slate-100 relative overflow-hidden flex flex-col md:flex-row items-center gap-6">
                <div class="absolute top-0 right-0 w-64 h-64 bg-green-50 rounded-full blur-3xl -mr-10 -mt-10"></div>
                <div class="flex-1 text-center md:text-left relative z-10">
                    <h2 class="text-2xl font-extrabold mb-2">Pohon Virtualmu</h2>
                    <p class="text-slate-500 text-sm mb-4">Terus setor sampah untuk mendapatkan Kontribusi, menaikkan level, dan menumbuhkan pohonmu!</p>
                    <a href="{{ route('transaksi') }}"
                        class="inline-block bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-5 rounded-xl shadow-lg transition-all text-sm">♻️
                        Setor Sampah</a>
                </div>
                <div
                    class="w-40 h-40 bg-gradient-to-b from-green-50 to-emerald-100 rounded-full flex flex-col items-center justify-center border-4 border-white shadow-xl relative z-10">
                    @php
                        $pohon = $user->tahapPohon();
                    @endphp
                    <img src="{{ asset('images/tree/' . $pohon['image']) }}" 
                         alt="{{ $pohon['label'] }}" 
                         class="w-28 h-28 object-contain tree-grow eco-tree-img drop-shadow-xl"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                    <div class="eco-tree text-7xl" style="display: none;">{{ $pohon['emoji'] }}</div>
                </div>
                <div class="absolute bottom-2 right-4 bg-white/80 backdrop-blur-sm px-3 py-1 rounded-full border border-green-100 shadow-sm z-20">
                    <p class="text-[10px] font-bold text-green-700">{{ $pohon['label'] }} (Tahap {{ $pohon['stage'] }}/20)</p>
                </div>
            </div>

            <div>
                <h3 class="text-xl font-extrabold mb-4 flex items-center gap-2">🤖 AI Daily Trivia <span
                        class="text-sm font-normal text-slate-400">(Auto-generated dari berita hari ini)</span></h3>
                <div id="trivia-container" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="col-span-2 bg-white rounded-3xl border border-slate-100 shadow-sm p-8 text-center">
                        <div
                            class="w-8 h-8 border-4 border-indigo-500 border-t-transparent rounded-full animate-spin mx-auto mb-3">
                        </div>
                        <p class="text-sm text-slate-500 font-bold">AI sedang menyusun trivia berdasarkan berita hari ini...
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-4">
            <div class="bg-white p-4 md:p-6 rounded-2xl shadow-sm border border-slate-100">
                <p class="text-xs md:text-sm text-slate-500 font-bold uppercase tracking-wider mb-1">Saldo Tersedia</p>
                <h3 class="text-2xl md:text-3xl font-extrabold text-slate-800 mb-2">Rp {{ number_format($user->balance, 0, ',', '.') }}
                </h3>
            </div>
            <div class="bg-white p-4 md:p-6 rounded-2xl shadow-sm border border-slate-100">
                <p class="text-xs md:text-sm text-slate-500 font-bold uppercase tracking-wider mb-1">Total Poin</p>
                <h3 class="text-3xl md:text-4xl font-extrabold text-orange-500">{{ $user->points }} <span
                        class="text-base md:text-lg text-slate-400 font-semibold">Pts</span></h3>
            </div>
            <div class="bg-slate-800 p-4 md:p-6 rounded-2xl shadow-md text-white relative overflow-hidden">
                <div class="absolute right-0 top-0 opacity-10 text-8xl -mr-4 -mt-4">💨</div>
                <p class="text-xs md:text-sm text-slate-400 font-bold uppercase tracking-wider mb-1">Jejak Karbon Dihindari</p>
                <h3 class="text-3xl md:text-4xl font-extrabold text-emerald-400">{{ $co2Saved }} <span
                        class="text-base md:text-lg text-slate-400 font-semibold">Kg CO₂</span></h3>
            </div>
            <div
                class="bg-gradient-to-br from-orange-50 to-red-50 p-4 md:p-6 rounded-2xl shadow-sm border border-orange-100 relative overflow-hidden">
                <div class="absolute right-0 top-0 opacity-20 text-7xl -mr-4 -mt-2">🔥</div>
                <p class="text-xs md:text-sm text-orange-600 font-bold uppercase tracking-wider mb-1">Trivia Streak Aktif</p>
                <div class="flex items-end gap-2 mb-2">
                    <h3 class="text-3xl md:text-4xl font-extrabold text-orange-600">{{ $user->streak }}</h3>
                    <span class="text-base md:text-lg text-orange-500 font-semibold mb-1">Hari</span>
                </div>
                <p class="text-xs text-orange-600 font-medium">Buka app tiap hari & jawab trivia untuk jaga streak!</p>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            fetchAndIntegrate();
        });

        async function fetchAndIntegrate() {
            try {
                const rssUrl = encodeURIComponent(
                    'https://news.google.com/rss/search?q=sampah+lingkungan+indonesia&hl=id&gl=ID&ceid=ID:id');
                const res = await fetch('https://api.rss2json.com/v1/api.json?rss_url=' + rssUrl);
                const data = await res.json();
                if (data.status === 'ok' && data.items && data.items.length > 0) {
                    const articles = data.items;
                    const main = articles[0];
                    const mainTitle = main.title.split(' - ')[0];
                    document.getElementById('dash-news-title').innerText = mainTitle;
                    document.getElementById('dash-news-desc').innerText = new Date(main.pubDate).toLocaleDateString(
                        'id-ID', {
                            day: 'numeric',
                            month: 'long',
                            year: 'numeric'
                        }) + ' · Sumber: Google News';
                    document.getElementById('dash-news-container').onclick = () => window.open(main.link, '_blank');
                    
                    @php $hasPlayedToday = $user->last_trivia_date === now()->toDateString(); @endphp
                    @if($hasPlayedToday)
                        document.getElementById('trivia-container').innerHTML = `
                        <div class="col-span-2 bg-white rounded-3xl border border-slate-100 shadow-sm p-8 text-center">
                            <p class="text-3xl mb-3">🎉</p>
                            <p class="text-sm text-slate-500 font-bold">Kamu sudah menjawab trivia hari ini.</p>
                            <p class="text-xs text-slate-400 mt-1">Kembali lagi besok untuk menjaga streakmu!</p>
                        </div>`;
                    @else
                        generateAITrivia(articles);
                    @endif
                } else {
                    showTriviaError('Tidak ada berita ditemukan hari ini.');
                }
            } catch (e) {
                document.getElementById('dash-news-title').innerText = 'Tidak dapat terhubung ke Google News';
                document.getElementById('dash-news-desc').innerText = 'Cek koneksi internet dan refresh halaman.';
                showTriviaError('Gagal memuat berita untuk trivia.');
            }
        }

        async function generateAITrivia(articles) {
            const container = document.getElementById('trivia-container');

            container.innerHTML = `
        <div class="col-span-2 bg-white rounded-3xl border border-slate-100 shadow-sm p-8 text-center">
            <div class="w-8 h-8 border-4 border-indigo-500 border-t-transparent rounded-full animate-spin mx-auto mb-3"></div>
            <p class="text-sm text-slate-500 font-bold">🤖 Groq AI sedang membuat trivia...</p>
            <p class="text-xs text-slate-400 mt-1">Menganalisis isi Berita Utama hari ini untuk dijadikan pertanyaan...</p>
        </div>
    `;

            const mainArticle = articles[0];
            const headlines = [{
                title: mainArticle.title.split(' - ')[0],
                description: mainArticle.description ?
                    mainArticle.description.replace(/<[^>]*>/g, '').substring(0, 1500) :
                    '',
                url: mainArticle.link || ''
            }];

            try {
                const res = await fetch('{{ route('trivia.generate') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ headlines }),
                });

                const data = await res.json();

                if (data.questions && data.questions.length > 0) {
                    renderTrivia(data.questions, data.source);
                } else {
                    showTriviaError('AI tidak mengembalikan pertanyaan. Coba refresh.');
                }
            } catch (e) {
                console.error('Trivia fetch error:', e);
                showTriviaError('Gagal menghubungi server trivia. Coba refresh halaman.');
            }
        }

        function renderTrivia(triviaData, source) {
            const container = document.getElementById('trivia-container');
            container.innerHTML = '';

            const badgeHTML = source === 'groq' ?
                `<span class="absolute top-3 right-3 text-[10px] font-bold px-2 py-0.5 rounded-full border bg-purple-100 text-purple-700 border-purple-200">🟣 Powered by Groq AI</span>` :
                '';

            triviaData.forEach((q, idx) => {
                const labels = ['A', 'B', 'C', 'D'];
                let optionsHTML = '';
                q.options.forEach((opt, oi) => {
                    const isCorrect = (oi === q.correctIndex);
                    optionsHTML +=
                        `<button onclick="answerTrivia(${idx+1}, this, ${isCorrect}, 2, ${idx})" class="bg-white/20 hover:bg-white/40 text-white border border-white/50 py-2 rounded-xl text-xs font-bold transition shadow-sm">${labels[oi]}. ${opt}</button>`;
                });

                const badgeRowHTML = badgeHTML ?
                    `<div class="flex justify-end w-full px-3 pt-3">${badgeHTML.replace('absolute top-3 right-3', '')}</div>` : '';

                container.innerHTML += `
        <div class="flip-card" id="card-${idx+1}" onclick="flipCard('card-${idx+1}')">
            <div class="flip-card-inner">
                <div class="flip-card-front" style="justify-content: flex-start;">
                    ${badgeRowHTML}
                    <div class="flex-1 flex items-center justify-center px-4">
                        <p class="font-bold text-slate-700 text-center">${q.question}</p>
                    </div>
                    <span class="mb-4 text-xs text-indigo-600 font-bold bg-indigo-100 px-3 py-1 rounded-full">Tap untuk balik</span>
                </div>
                <div class="flip-card-back" onclick="event.stopPropagation()">
                    <p class="font-bold text-sm mb-3">Pilih jawabanmu:</p>
                    <div class="flex flex-col gap-2 w-full px-2" id="options-${idx+1}">${optionsHTML}</div>
                    <p id="feedback-${idx+1}" class="hidden mt-3 text-xs font-bold bg-white text-green-700 py-1 px-3 rounded-full"></p>
                </div>
            </div>
        </div>`;
            });
        }

        function showTriviaError(msg) {
            const container = document.getElementById('trivia-container');
            container.innerHTML = `
        <div class="col-span-2 bg-white rounded-3xl border border-red-100 shadow-sm p-8 text-center">
            <p class="text-3xl mb-3">😢</p>
            <p class="text-sm text-red-500 font-bold">${msg}</p>
            <button onclick="location.reload()" class="mt-3 bg-slate-800 text-white px-4 py-2 rounded-xl text-xs font-bold hover:bg-slate-700 transition">🔄 Refresh</button>
        </div>
    `;
        }

        function flipCard(id) {
            const el = document.getElementById(id);
            if (!el.classList.contains('flipped')) el.classList.add('flipped');
        }

        async function answerTrivia(num, btn, isCorrect, pts, questionIdx) {
            const fb = document.getElementById('feedback-' + num);
            if (fb.classList.contains('answered')) return;
            fb.classList.add('answered');
            document.querySelectorAll('#options-' + num + ' button').forEach(b => {
                b.disabled = true;
                b.classList.add('opacity-50', 'cursor-not-allowed');
            });
            fb.classList.remove('hidden');
            fb.innerText = 'Menyimpan jawaban...';

            try {
                const res = await fetch('{{ route('trivia.answer') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ is_correct: isCorrect, question_index: questionIdx }),
                });
                const data = await res.json();
                
                if (data.success) {
                    if (isCorrect) {
                        btn.classList.add('bg-white', 'text-green-700', 'opacity-100');
                        fb.innerText = 'Benar! 🎉 +' + pts + ' Poin';
                    } else {
                        btn.classList.add('bg-red-500', 'text-white', 'opacity-100');
                        fb.innerText = 'Sayang sekali, salah! 😢';
                    }
                    
                    if (data.completed) {
                        setTimeout(() => {
                            const container = document.getElementById('trivia-container');
                            container.innerHTML = `
                                <div class="col-span-2 bg-white rounded-3xl border border-slate-100 shadow-sm p-8 text-center">
                                    <p class="text-3xl mb-3">🎉</p>
                                    <p class="text-sm text-slate-500 font-bold">Kamu sudah menjawab semua trivia hari ini.</p>
                                    <p class="text-xs text-slate-400 mt-1">Poin berhasil ditambahkan. Kembali lagi besok untuk menjaga streakmu!</p>
                                    <button onclick="location.reload()" class="mt-4 bg-green-600 text-white px-4 py-2 rounded-xl text-xs font-bold hover:bg-green-500 transition shadow-sm">🔄 Refresh Poin di Header</button>
                                </div>`;
                        }, 1500);
                    }
                } else {
                    fb.innerText = data.message || 'Gagal menyimpan jawaban.';
                }
            } catch (e) {
                fb.innerText = 'Koneksi error, poin gagal disimpan.';
            }
        }
    </script>
@endsection

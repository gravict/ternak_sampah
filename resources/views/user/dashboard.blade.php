@extends('layouts.app')
@section('title', 'Dashboard | TernakSampah')

@section('content')
{{-- News Section --}}
<div class="mb-10">
    <div class="flex justify-between items-end mb-4">
        <h2 class="text-2xl font-extrabold flex items-center gap-2">Update Hari Ini 📰</h2>
        <span id="date-badge" class="text-xs font-bold text-green-600 bg-green-50 px-3 py-1 rounded-full border border-green-200 shadow-sm">{{ now()->translatedFormat('d F Y') }}</span>
    </div>
    <div id="dash-news-container" class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden relative flex flex-col justify-end min-h-[300px] p-6 group cursor-pointer">
        <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1611284446314-60a58ac0deb9?q=80&w=1200')] bg-cover bg-center group-hover:scale-105 transition duration-700"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/60 to-transparent"></div>
        <div class="relative z-10 text-white w-full lg:w-2/3">
            <span class="bg-red-500 text-white text-xs font-bold px-2 py-1 rounded mb-2 inline-block shadow-md">🌍 Live Google News</span>
            <h3 id="dash-news-title" class="text-3xl font-extrabold mb-2 leading-tight">Memuat berita terkini...</h3>
            <p id="dash-news-desc" class="text-sm text-slate-300 line-clamp-2">Sedang mengambil berita terbaru dari Google News...</p>
        </div>
    </div>
</div>

{{-- User Stats Bar --}}
<div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 mb-6 flex flex-col md:flex-row justify-between md:items-center gap-4">
    <div>
        <h1 class="text-xl font-extrabold mb-1">Dampak Positifmu, <span class="text-green-600">{{ $user->name }}</span>!</h1>
        <div class="flex items-center gap-4 w-full md:w-96">
            @php $level = intdiv($user->points, 150) + 1; $progress = ($user->points % 150) / 150 * 100; @endphp
            <span class="text-sm font-bold text-slate-500 w-16">Level {{ $level }}</span>
            <div class="flex-1 bg-slate-100 h-4 rounded-full overflow-hidden">
                <div class="bg-gradient-to-r from-green-400 to-green-600 h-full transition-all duration-1000" style="width: {{ $progress }}%"></div>
            </div>
            <span class="text-sm font-bold text-green-600 w-16 text-right">Level {{ $level + 1 }}</span>
        </div>
    </div>
    <a href="{{ route('profile') }}#withdraw" class="bg-slate-800 text-white px-6 py-3 rounded-xl font-bold hover:bg-slate-900 transition flex items-center gap-2 shadow-lg text-sm">
        💳 Tarik Saldo
    </a>
</div>

{{-- Main Grid --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-8">
        {{-- Virtual Tree --}}
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 relative overflow-hidden flex flex-col md:flex-row items-center gap-6">
            <div class="absolute top-0 right-0 w-64 h-64 bg-green-50 rounded-full blur-3xl -mr-10 -mt-10"></div>
            <div class="flex-1 text-center md:text-left relative z-10">
                <h2 class="text-2xl font-extrabold mb-2">Pohon Virtualmu</h2>
                <p class="text-slate-500 text-sm mb-4">Setiap kg sampah yang divalidasi admin akan menumbuhkan pohon ini. Terus setor untuk menyelamatkan bumi!</p>
                <a href="{{ route('transaksi') }}" class="inline-block bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-5 rounded-xl shadow-lg transition-all text-sm">♻️ Setor Sampah</a>
            </div>
            <div class="w-40 h-40 bg-gradient-to-b from-green-50 to-emerald-100 rounded-full flex items-center justify-center border-4 border-white shadow-xl relative z-10">
                @php
                    $totalKg = $user->transactions()->where('status','complete')->sum('actual_weight');
                    $tree = $totalKg >= 50 ? '🌳' : ($totalKg >= 20 ? '🌲' : ($totalKg >= 5 ? '🌿' : '🌱'));
                @endphp
                <div class="eco-tree text-7xl">{{ $tree }}</div>
            </div>
        </div>

        {{-- AI Trivia --}}
        <div>
            <h3 class="text-xl font-extrabold mb-4 flex items-center gap-2">🤖 AI Daily Trivia <span class="text-sm font-normal text-slate-400">(Auto-generated dari berita hari ini)</span></h3>
            <div id="trivia-container" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="col-span-2 bg-white rounded-3xl border border-slate-100 shadow-sm p-8 text-center">
                    <div class="w-8 h-8 border-4 border-indigo-500 border-t-transparent rounded-full animate-spin mx-auto mb-3"></div>
                    <p class="text-sm text-slate-500 font-bold">AI sedang menyusun trivia berdasarkan berita hari ini...</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Sidebar Stats --}}
    <div class="space-y-4">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
            <p class="text-sm text-slate-500 font-bold uppercase tracking-wider mb-1">Saldo Tersedia</p>
            <h3 class="text-3xl font-extrabold text-slate-800 mb-2">Rp {{ number_format($user->balance, 0, ',', '.') }}</h3>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
            <p class="text-sm text-slate-500 font-bold uppercase tracking-wider mb-1">Total Poin</p>
            <h3 class="text-4xl font-extrabold text-orange-500">{{ $user->points }} <span class="text-lg text-slate-400 font-semibold">Pts</span></h3>
        </div>
        <div class="bg-slate-800 p-6 rounded-2xl shadow-md text-white relative overflow-hidden">
            <div class="absolute right-0 top-0 opacity-10 text-8xl -mr-4 -mt-4">💨</div>
            <p class="text-sm text-slate-400 font-bold uppercase tracking-wider mb-1">Jejak Karbon Dihindari</p>
            <h3 class="text-4xl font-extrabold text-emerald-400">{{ $co2Saved }} <span class="text-lg text-slate-400 font-semibold">Kg CO₂</span></h3>
        </div>
        <div class="bg-gradient-to-br from-orange-50 to-red-50 p-6 rounded-2xl shadow-sm border border-orange-100 relative overflow-hidden">
            <div class="absolute right-0 top-0 opacity-20 text-7xl -mr-4 -mt-2">🔥</div>
            <p class="text-sm text-orange-600 font-bold uppercase tracking-wider mb-1">Trivia Streak Aktif</p>
            <div class="flex items-end gap-2 mb-2">
                <h3 class="text-4xl font-extrabold text-orange-600">{{ $user->streak }}</h3>
                <span class="text-lg text-orange-500 font-semibold mb-1">Hari</span>
            </div>
            <p class="text-xs text-orange-600 font-medium">Buka app tiap hari & jawab trivia untuk jaga streak!</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// === LIVE NEWS + AI TRIVIA ENGINE ===
document.addEventListener('DOMContentLoaded', () => { fetchAndIntegrate(); });

async function fetchAndIntegrate() {
    try {
        const rssUrl = encodeURIComponent('https://news.google.com/rss/search?q=sampah+lingkungan+indonesia&hl=id&gl=ID&ceid=ID:id');
        const res = await fetch('https://api.rss2json.com/v1/api.json?rss_url=' + rssUrl);
        const data = await res.json();
        if (data.status === 'ok' && data.items && data.items.length > 0) {
            const articles = data.items;
            const main = articles[0];
            const mainTitle = main.title.split(' - ')[0];
            document.getElementById('dash-news-title').innerText = mainTitle;
            document.getElementById('dash-news-desc').innerText = new Date(main.pubDate).toLocaleDateString('id-ID', {day:'numeric', month:'long', year:'numeric'}) + ' · Sumber: Google News';
            document.getElementById('dash-news-container').onclick = () => window.open(main.link, '_blank');
            generateAITrivia(articles);
        }
    } catch (e) {
        document.getElementById('dash-news-title').innerText = 'Tidak dapat terhubung ke Google News';
        document.getElementById('dash-news-desc').innerText = 'Cek koneksi internet dan refresh halaman.';
    }
}

function generateAITrivia(articles) {
    const titles = articles.slice(0, 5).map(a => a.title.split(' - ')[0]);
    const triviaData = aiCreateQuestions(titles);
    const container = document.getElementById('trivia-container');
    container.innerHTML = '';
    triviaData.forEach((q, idx) => {
        const labels = ['A', 'B', 'C', 'D'];
        let optionsHTML = '';
        q.options.forEach((opt, oi) => {
            const isCorrect = (oi === q.correctIndex);
            optionsHTML += `<button onclick="answerTrivia(${idx+1}, this, ${isCorrect}, 15)" class="bg-white/20 hover:bg-white/40 text-white border border-white/50 py-2 rounded-xl text-xs font-bold transition shadow-sm">${labels[oi]}. ${opt}</button>`;
        });
        container.innerHTML += `
        <div class="flip-card" id="card-${idx+1}" onclick="flipCard('card-${idx+1}')">
            <div class="flip-card-inner">
                <div class="flip-card-front"><p class="font-bold text-slate-700 px-4">${q.question}</p><span class="absolute bottom-4 text-xs text-indigo-600 font-bold bg-indigo-100 px-3 py-1 rounded-full">Tap untuk balik</span></div>
                <div class="flip-card-back" onclick="event.stopPropagation()"><p class="font-bold text-sm mb-3">Pilih jawabanmu:</p><div class="flex flex-col gap-2 w-full px-2" id="options-${idx+1}">${optionsHTML}</div><p id="feedback-${idx+1}" class="hidden mt-3 text-xs font-bold bg-white text-green-700 py-1 px-3 rounded-full"></p></div>
            </div>
        </div>`;
    });
}

function aiCreateQuestions(titles) {
    const questions = [];
    const keywords = ['sampah','limbah','TPA','daur ulang','plastik','organik','kompos','bank sampah','pengelolaan','pencemaran','polusi','lingkungan'];
    function extractTopics(t) { const found = []; const l = t.toLowerCase(); keywords.forEach(k => { if(l.includes(k)) found.push(k.charAt(0).toUpperCase()+k.slice(1)); }); return [...new Set(found)]; }
    function shuffle(a) { const b=[...a]; for(let i=b.length-1;i>0;i--){const j=Math.floor(Math.random()*(i+1));[b[i],b[j]]=[b[j],b[i]];} return b; }
    function swc(c,w) { const a=[c,...w.slice(0,3)]; const s=shuffle(a); return {options:s, correctIndex:s.indexOf(c)}; }
    if(titles[0]) { const t=extractTopics(titles[0]); const c=t[0]||titles[0].split(' ').slice(0,4).join(' '); const w=shuffle(['Polusi Udara','Emisi Karbon','Deforestasi','Limbah B3','Energi Terbarukan']).slice(0,3); questions.push({question:`Apa topik utama dari berita: "${titles[0].length>60?titles[0].slice(0,57)+'...':titles[0]}"?`, ...swc(c,w)}); }
    if(titles[1]) { const opts=swc(titles[1].length>50?titles[1].slice(0,47)+'...':titles[1], shuffle(['Indonesia Larang Total Kantong Plastik','Pabrik Daur Ulang Terbesar ASEAN','Robot AI Pembersih Sungai']).slice(0,3)); questions.push({question:'Manakah berita asli hari ini dari Google News?', ...opts}); }
    while(questions.length<2) { questions.push({question:'Ke mana sebaiknya menyerahkan sampah bernilai ekonomi?', ...swc('Bank Sampah',['TPA Ilegal','Sungai','Pembakaran Terbuka'])}); }
    return questions;
}

function flipCard(id) { const el=document.getElementById(id); if(!el.classList.contains('flipped')) el.classList.add('flipped'); }
function answerTrivia(num, btn, isCorrect, pts) {
    const fb=document.getElementById('feedback-'+num); if(fb.classList.contains('answered')) return; fb.classList.add('answered');
    document.querySelectorAll('#options-'+num+' button').forEach(b => { b.disabled=true; b.classList.add('opacity-50','cursor-not-allowed'); });
    fb.classList.remove('hidden');
    if(isCorrect) { btn.classList.add('bg-white','text-green-700','opacity-100'); fb.innerText='Benar! 🎉 +'+pts+' Poin'; }
    else { btn.classList.add('bg-red-500','text-white','opacity-100'); fb.innerText='Sayang sekali, salah! 😢'; }
}
</script>
@endsection

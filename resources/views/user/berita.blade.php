@extends('layouts.app')
@section('title', 'Berita & Edukasi | TernakSampah')

@section('content')
<div class="mb-12">
    <h2 class="text-3xl font-extrabold mb-6">Video Edukasi 📺</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden cursor-pointer group">
            <div class="h-48 bg-slate-800 relative flex items-center justify-center">
                <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1532996122724-e3c354a0b15b?q=80&w=600')] bg-cover bg-center opacity-60 group-hover:scale-105 transition duration-500"></div>
                <div class="w-16 h-16 bg-white/30 backdrop-blur-sm rounded-full flex items-center justify-center z-10">
                    <div class="w-0 h-0 border-t-[10px] border-t-transparent border-l-[16px] border-l-white border-b-[10px] border-b-transparent ml-1"></div>
                </div>
            </div>
            <div class="p-5">
                <span class="text-xs font-bold text-red-600 bg-red-50 px-2 py-1 rounded">Edukasi TPA</span>
                <h4 class="font-bold text-slate-800 mt-2 text-lg line-clamp-1">Kenyataan Pahit Dibalik Sampah Plastik</h4>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden cursor-pointer group">
            <div class="h-48 bg-slate-800 relative flex items-center justify-center">
                <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1611284446314-60a58ac0deb9?q=80&w=600')] bg-cover bg-center opacity-60 group-hover:scale-105 transition duration-500"></div>
                <div class="w-16 h-16 bg-white/30 backdrop-blur-sm rounded-full flex items-center justify-center z-10">
                    <div class="w-0 h-0 border-t-[10px] border-t-transparent border-l-[16px] border-l-white border-b-[10px] border-b-transparent ml-1"></div>
                </div>
            </div>
            <div class="p-5">
                <span class="text-xs font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded">Tutorial</span>
                <h4 class="font-bold text-slate-800 mt-2 text-lg line-clamp-1">Cara Daur Ulang E-Waste di Rumah</h4>
            </div>
        </div>
    </div>
</div>

<div>
    <h2 class="text-3xl font-extrabold mb-6">Berita Terkini (Live) 📰</h2>
    <div id="berita-live-container" class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden text-center p-8 col-span-3">
            <div class="w-6 h-6 border-4 border-green-500 border-t-transparent rounded-full animate-spin mx-auto mb-3"></div>
            <p class="text-sm text-slate-500 font-bold">Mengambil berita terbaru dari Google News...</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', async () => {
    try {
        const rssUrl = encodeURIComponent('https://news.google.com/rss/search?q=sampah+lingkungan+indonesia&hl=id&gl=ID&ceid=ID:id');
        const res = await fetch('https://api.rss2json.com/v1/api.json?rss_url=' + rssUrl);
        const data = await res.json();
        if (data.status === 'ok' && data.items) {
            const grid = document.getElementById('berita-live-container');
            const imgs = [
                'https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?q=80&w=600',
                'https://images.unsplash.com/photo-1550989460-0adf9ea622e2?q=80&w=600',
                'https://images.unsplash.com/photo-1532996122724-e3c354a0b15b?q=80&w=600'
            ];
            grid.innerHTML = '';
            for (let i = 0; i < 3 && i < data.items.length; i++) {
                const a = data.items[i];
                const t = a.title.split(' - ')[0];
                const d = new Date(a.pubDate).toLocaleDateString('id-ID', {day:'numeric', month:'short', year:'numeric'});
                grid.innerHTML += `
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden cursor-pointer hover:shadow-md transition" onclick="window.open('${a.link}','_blank')">
                        <div class="h-40 bg-cover bg-center" style="background-image:url('${imgs[i]}')"></div>
                        <div class="p-5">
                            <h4 class="font-bold text-slate-800 line-clamp-2">${t}</h4>
                            <p class="text-xs text-green-600 mt-2 font-bold">${d} <span class="text-slate-400 font-normal">· Google News</span></p>
                        </div>
                    </div>`;
            }
        }
    } catch(e) {
        document.getElementById('berita-live-container').innerHTML = '<p class="col-span-3 text-center text-slate-400 py-8">Gagal memuat berita. Silakan refresh.</p>';
    }
});
</script>
@endsection

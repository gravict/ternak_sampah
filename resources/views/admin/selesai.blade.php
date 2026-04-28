@extends('layouts.admin')
@section('title', 'Riwayat Transaksi | Admin TernakSampah')

@section('content')
<div class="mb-6">
    <h2 class="text-xl sm:text-2xl font-extrabold text-slate-800 flex items-center gap-2">Riwayat Transaksi <span class="bg-green-100 text-green-600 text-xs sm:text-sm px-3 py-1 rounded-full border border-green-200">Tahap 3</span></h2>
    <p class="text-slate-500 text-sm mt-1">Semua transaksi yang telah selesai atau ditolak.</p>
</div>

{{-- Tabs --}}
<div class="flex gap-2 mb-6">
    <button onclick="switchTab('complete')" id="tab-complete" class="px-4 sm:px-5 py-2 rounded-xl text-xs sm:text-sm font-bold transition border-2 border-green-500 bg-green-500 text-white">
		✅ Selesai ({{ $completeTransactions->count() }})
	</button>
    <button onclick="switchTab('rejected')" id="tab-rejected" class="px-4 sm:px-5 py-2 rounded-xl text-xs sm:text-sm font-bold transition border-2 border-slate-200 bg-white text-slate-500 hover:bg-red-50">
		❌ Ditolak ({{ $rejectedTransactions->count() }})
	</button>
</div>

{{-- Complete Section --}}
<div id="table-complete">
    {{-- Mobile Card Layout --}}
    <div class="md:hidden space-y-4">
        @forelse($completeTransactions as $t)
            <div class="bg-white border border-slate-200 p-4 rounded-2xl shadow-sm">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <h4 class="font-bold text-slate-800 text-sm">{{ $t->user->name }}</h4>
                        <p class="text-xs text-slate-400">#{{ $t->id }} · {{ $t->updated_at->translatedFormat('d M Y') }}</p>
                    </div>
                    <p class="font-extrabold text-green-600 text-sm">Rp {{ number_format($t->total_price, 0, ',', '.') }}</p>
                </div>
                <div class="flex flex-wrap gap-1.5 mb-3">
                    <span class="{{ $t->method === 'Pick-up' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }} px-2 py-0.5 rounded text-[10px] font-bold">{{ $t->method }}</span>
                    <span class="bg-slate-100 text-slate-600 px-2 py-0.5 rounded text-[10px] font-bold">{{ $t->category }}</span>
                    <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded text-[10px] font-bold">{{ $t->actual_weight }} Kg</span>
                </div>
                <div class="pt-3 border-t border-slate-100">
                    <button onclick="showNota({{ $t->id }}, '{{ $t->user->name }}', '{{ $t->user->nik }}', '{{ $t->category }}', '{{ $t->actual_weight }}', '{{ number_format($t->total_price, 0, ',', '.') }}', '{{ $t->updated_at->translatedFormat('d M Y H:i') }}', {{ $t->points_earned ?? 0 }})" class="w-full text-xs font-bold text-green-600 bg-green-50 px-3 py-2 rounded-lg border border-green-200 hover:bg-green-100 transition">📄 Lihat Nota</button>
                </div>
            </div>
        @empty
            <div class="p-8 text-center text-slate-400 italic bg-white rounded-2xl border border-slate-200">Belum ada riwayat selesai.</div>
        @endforelse
    </div>

    {{-- Desktop Table Layout --}}
    <div class="hidden md:block bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left whitespace-nowrap">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="p-4 text-slate-500 font-bold text-sm">Tgl Selesai</th>
                        <th class="p-4 text-slate-500 font-bold text-sm">Nama User</th>
                        <th class="p-4 text-slate-500 font-bold text-sm">Metode</th>
                        <th class="p-4 text-slate-500 font-bold text-sm">Kategori</th>
                        <th class="p-4 text-slate-500 font-bold text-sm">Berat Asli</th>
                        <th class="p-4 text-slate-500 font-bold text-sm">Total Bayar</th>
                        <th class="p-4 text-slate-500 font-bold text-sm text-center">Nota</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($completeTransactions as $t)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="p-4 text-sm font-semibold text-slate-500">{{ $t->updated_at->translatedFormat('d M Y') }}</td>
                            <td class="p-4 font-bold text-slate-800 text-sm">{{ $t->user->name }}<br><span class="text-xs text-slate-400 font-normal">#{{ $t->id }}</span></td>
                            <td class="p-4"><span class="{{ $t->method === 'Pick-up' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }} px-2 py-1 rounded-md text-xs font-bold">{{ $t->method }}</span></td>
                            <td class="p-4 text-sm font-semibold text-slate-600">{{ $t->category }}</td>
                            <td class="p-4 text-sm font-bold text-slate-800">{{ $t->actual_weight }} Kg</td>
                            <td class="p-4 font-extrabold text-green-600">Rp {{ number_format($t->total_price, 0, ',', '.') }}</td>
                            <td class="p-4 text-center">
                                <button onclick="showNota({{ $t->id }}, '{{ $t->user->name }}', '{{ $t->user->nik }}', '{{ $t->category }}', '{{ $t->actual_weight }}', '{{ number_format($t->total_price, 0, ',', '.') }}', '{{ $t->updated_at->translatedFormat('d M Y H:i') }}', {{ $t->points_earned ?? 0 }})" class="text-xs font-bold text-green-600 bg-green-50 px-3 py-1.5 rounded-lg border border-green-200 hover:bg-green-100 transition">📄 Nota</button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="p-8 text-center text-slate-400 italic">Belum ada riwayat selesai.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Rejected Section --}}
<div id="table-rejected" class="hidden">
    {{-- Mobile Card Layout --}}
    <div class="md:hidden space-y-4">
        @forelse($rejectedTransactions as $t)
            <div class="bg-white border border-red-100 p-4 rounded-2xl shadow-sm">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <h4 class="font-bold text-slate-800 text-sm">{{ $t->user->name }}</h4>
                        <p class="text-xs text-slate-400">#{{ $t->id }} · {{ $t->updated_at->translatedFormat('d M Y') }}</p>
                    </div>
                    <span class="{{ $t->method === 'Pick-up' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }} px-2 py-0.5 rounded text-[10px] font-bold">{{ $t->method }}</span>
                </div>
                <div class="flex flex-wrap gap-1.5 mb-3">
                    <span class="bg-slate-100 text-slate-600 px-2 py-0.5 rounded text-[10px] font-bold">{{ $t->category }}</span>
                    <span class="bg-orange-100 text-orange-600 px-2 py-0.5 rounded text-[10px] font-bold">Est: {{ $t->est_weight }} Kg</span>
                </div>
                <div class="bg-red-50 p-3 rounded-xl border border-red-100">
                    <p class="text-xs text-red-600 font-semibold">⚠️ {{ $t->reject_reason }}</p>
                </div>
            </div>
        @empty
            <div class="p-8 text-center text-slate-400 italic bg-white rounded-2xl border border-slate-200">Belum ada transaksi yang ditolak.</div>
        @endforelse
    </div>

    {{-- Desktop Table Layout --}}
    <div class="hidden md:block bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left whitespace-nowrap">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="p-4 text-slate-500 font-bold text-sm">Tgl Ditolak</th>
                        <th class="p-4 text-slate-500 font-bold text-sm">Nama User</th>
                        <th class="p-4 text-slate-500 font-bold text-sm">Metode</th>
                        <th class="p-4 text-slate-500 font-bold text-sm">Kategori</th>
                        <th class="p-4 text-slate-500 font-bold text-sm">Est. Berat</th>
                        <th class="p-4 text-slate-500 font-bold text-sm">Alasan Penolakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($rejectedTransactions as $t)
                        <tr class="hover:bg-red-50/30 transition">
                            <td class="p-4 text-sm font-semibold text-slate-500">{{ $t->updated_at->translatedFormat('d M Y') }}</td>
                            <td class="p-4 font-bold text-slate-800 text-sm">{{ $t->user->name }}<br><span class="text-xs text-slate-400 font-normal">#{{ $t->id }}</span></td>
                            <td class="p-4"><span class="{{ $t->method === 'Pick-up' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }} px-2 py-1 rounded-md text-xs font-bold">{{ $t->method }}</span></td>
                            <td class="p-4 text-sm font-semibold text-slate-600">{{ $t->category }}</td>
                            <td class="p-4 text-sm font-bold text-orange-500">{{ $t->est_weight }} Kg</td>
                            <td class="p-4 text-sm text-red-600 font-semibold max-w-xs whitespace-normal">{{ $t->reject_reason }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="p-8 text-center text-slate-400 italic">Belum ada transaksi yang ditolak.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

{{-- Nota Modal --}}
@section('modals')
<div id="nota-modal" class="fixed inset-0 z-[300] items-center justify-center bg-black/50 backdrop-blur-sm hidden" style="display:none;">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 p-6 relative" onclick="event.stopPropagation()">
        <button onclick="closeNota()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 text-xl font-bold">✕</button>

        <div class="text-center border-b border-dashed border-slate-200 pb-4 mb-4">
            <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center text-white font-bold text-xl mx-auto mb-2 shadow">T</div>
            <h3 class="text-lg font-extrabold text-slate-800">NOTA PEMBAYARAN</h3>
            <p class="text-xs text-slate-400">TernakSampah — Bank Sampah Digital</p>
        </div>

        <div class="space-y-3 text-sm" id="nota-content"></div>

        <div class="mt-4 pt-4 border-t border-dashed border-slate-200 bg-green-50 -mx-6 -mb-6 p-4 rounded-b-2xl">
            <p class="text-[10px] text-green-700 font-bold text-center">Sumber Dana: Rekening Sentral TernakSampah</p>
            <p class="text-[10px] text-green-600 text-center">Bank BCA — 5270 3456 78 a.n. PT TernakSampah Indonesia</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function switchTab(tab) {
    const complete = document.getElementById('table-complete');
    const rejected = document.getElementById('table-rejected');
    const btnComplete = document.getElementById('tab-complete');
    const btnRejected = document.getElementById('tab-rejected');

    if (tab === 'complete') {
        complete.classList.remove('hidden');
        rejected.classList.add('hidden');
        btnComplete.className = 'px-4 sm:px-5 py-2 rounded-xl text-xs sm:text-sm font-bold transition border-2 border-green-500 bg-green-500 text-white';
        btnRejected.className = 'px-4 sm:px-5 py-2 rounded-xl text-xs sm:text-sm font-bold transition border-2 border-slate-200 bg-white text-slate-500 hover:bg-red-50';
    } else {
        complete.classList.add('hidden');
        rejected.classList.remove('hidden');
        btnRejected.className = 'px-4 sm:px-5 py-2 rounded-xl text-xs sm:text-sm font-bold transition border-2 border-red-500 bg-red-500 text-white';
        btnComplete.className = 'px-4 sm:px-5 py-2 rounded-xl text-xs sm:text-sm font-bold transition border-2 border-slate-200 bg-white text-slate-500 hover:bg-green-50';
    }
}

function showNota(id, name, nik, kategori, berat, totalBayar, tanggal, pointsEarned) {
    const modal = document.getElementById('nota-modal');
    modal.style.display = 'flex';
    modal.classList.remove('hidden');

    let pointsHtml = '';
    if (pointsEarned > 0) {
        pointsHtml = `<div class="flex justify-between mt-1"><span class="text-slate-500">Poin Tambahan</span><span class="font-bold text-blue-600">+ ${pointsEarned} Pts</span></div>`;
    }

    document.getElementById('nota-content').innerHTML = `
        <div class="flex justify-between"><span class="text-slate-500">No. Transaksi</span><span class="font-bold text-slate-800">#${id}</span></div>
        <div class="flex justify-between"><span class="text-slate-500">Tanggal</span><span class="font-bold text-slate-800">${tanggal}</span></div>
        <div class="flex justify-between"><span class="text-slate-500">Penerima</span><span class="font-bold text-slate-800">${name}</span></div>
        <div class="flex justify-between"><span class="text-slate-500">NIK</span><span class="font-bold text-slate-600 text-xs">${nik}</span></div>
        <hr class="border-slate-100">
        <div class="flex justify-between"><span class="text-slate-500">Kategori</span><span class="font-bold text-slate-800">${kategori}</span></div>
        <div class="flex justify-between"><span class="text-slate-500">Berat Aktual</span><span class="font-bold text-slate-800">${berat} Kg</span></div>
        <hr class="border-slate-100">
        <div class="flex justify-between text-lg"><span class="font-extrabold text-slate-800">TOTAL BAYAR</span><span class="font-extrabold text-green-600">Rp ${totalBayar}</span></div>
        ${pointsHtml}
        <p class="text-[10px] text-slate-400 text-center mt-2">Dana ini dikreditkan ke saldo dompet digital nasabah.<br>Nasabah dapat melakukan penarikan ke rekening pribadi.</p>
    `;
}

function closeNota() {
    const modal = document.getElementById('nota-modal');
    modal.style.display = 'none';
    modal.classList.add('hidden');
}

// Close modal on backdrop click
document.getElementById('nota-modal')?.addEventListener('click', function(e) {
    if (e.target === this) closeNota();
});
</script>
@endsection

@extends('layouts.app')
@section('title', 'Riwayat | TernakSampah')

@section('content')
<div class="bg-white p-4 sm:p-6 md:p-8 rounded-3xl shadow-sm border border-slate-100">
    <h2 class="text-xl md:text-2xl font-extrabold mb-6">Riwayat Transaksi</h2>
    
    {{-- Tabs --}}
    <div class="flex gap-2 mb-6 overflow-x-auto pb-2">
        @php $currentStatus = request('status', 'semua'); @endphp
        <a href="{{ route('riwayat') }}" class="px-4 py-2 rounded-xl text-sm font-bold transition border-2 whitespace-nowrap {{ $currentStatus === 'semua' ? 'border-green-500 bg-green-500 text-white' : 'border-slate-200 bg-white text-slate-500 hover:bg-slate-50' }}">
            Semua
        </a>
        <a href="{{ route('riwayat', ['status' => 'dikirim']) }}" class="px-4 py-2 rounded-xl text-sm font-bold transition border-2 whitespace-nowrap {{ $currentStatus === 'dikirim' ? 'border-yellow-500 bg-yellow-500 text-white' : 'border-slate-200 bg-white text-slate-500 hover:bg-yellow-50' }}">
            Dikirim
        </a>
        <a href="{{ route('riwayat', ['status' => 'diterima']) }}" class="px-4 py-2 rounded-xl text-sm font-bold transition border-2 whitespace-nowrap {{ $currentStatus === 'diterima' ? 'border-blue-500 bg-blue-500 text-white' : 'border-slate-200 bg-white text-slate-500 hover:bg-blue-50' }}">
            Diterima
        </a>
        <a href="{{ route('riwayat', ['status' => 'ditolak']) }}" class="px-4 py-2 rounded-xl text-sm font-bold transition border-2 whitespace-nowrap {{ $currentStatus === 'ditolak' ? 'border-red-500 bg-red-500 text-white' : 'border-slate-200 bg-white text-slate-500 hover:bg-red-50' }}">
            Ditolak
        </a>
        <a href="{{ route('riwayat', ['status' => 'selesai']) }}" class="px-4 py-2 rounded-xl text-sm font-bold transition border-2 whitespace-nowrap {{ $currentStatus === 'selesai' ? 'border-green-500 bg-green-500 text-white' : 'border-slate-200 bg-white text-slate-500 hover:bg-green-50' }}">
            Selesai
        </a>
    </div>

    {{-- Mobile Card Layout --}}
    <div class="md:hidden space-y-4">
        @forelse($transactions as $trx)
            <div class="bg-white border border-slate-100 p-4 rounded-2xl shadow-sm">
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <h4 class="font-bold text-slate-800">{{ $trx->category }}</h4>
                        <p class="text-xs text-slate-500 mt-1">{{ $trx->created_at->translatedFormat('d M Y') }}</p>
                    </div>
                    <div class="text-right">
                        @if($trx->status === 'complete')
                            <p class="font-extrabold text-green-600">+ Rp {{ number_format($trx->total_price, 0, ',', '.') }}</p>
                        @else
                            <p class="font-bold text-slate-400">Rp 0</p>
                        @endif
                        <span class="{{ $trx->method_badge }} px-2 py-0.5 rounded text-[10px] font-bold mt-1 inline-block">{{ $trx->method }}</span>
                    </div>
                </div>
                <div class="pt-3 border-t border-slate-100 flex justify-between items-center">
                    @php $badge = $trx->status_badge; @endphp
                    <span class="{{ $badge['class'] }} px-2 py-1 rounded-full text-[10px] font-bold border">{{ $badge['label'] }}</span>
                    
                    @if($trx->status === 'rejected' && $trx->reject_reason)
                        <button onclick="document.getElementById('reason-mob-{{ $trx->id }}').classList.toggle('hidden')" class="text-[10px] bg-slate-200 hover:bg-slate-300 px-2 py-1 rounded font-bold text-slate-600 transition shadow-sm">Lihat Alasan</button>
                    @elseif($trx->status === 'complete')
                        <button onclick="showNota({{ $trx->id }}, '{{ Auth::user()->name }}', '{{ Auth::user()->nik }}', '{{ $trx->category }}', '{{ $trx->actual_weight }}', '{{ number_format($trx->total_price, 0, ',', '.') }}', '{{ $trx->updated_at->translatedFormat('d M Y H:i') }}')" class="text-[10px] bg-green-100 hover:bg-green-200 text-green-700 px-2 py-1 rounded font-bold border border-green-200 transition shadow-sm">📄 Lihat Nota</button>
                    @endif
                </div>
                @if($trx->status === 'rejected' && $trx->reject_reason)
                    <div id="reason-mob-{{ $trx->id }}" class="hidden mt-3 bg-red-50 p-3 rounded-xl text-xs text-red-700 italic border border-red-100">
                        ⚠️ {{ $trx->reject_reason }}
                    </div>
                @endif
            </div>
        @empty
            <div class="p-8 text-center text-slate-400 italic bg-slate-50 rounded-2xl border border-slate-100">Belum ada riwayat transaksi.</div>
        @endforelse
    </div>

    {{-- Desktop Table Layout --}}
    <div class="hidden md:block overflow-x-auto">
        <table class="w-full text-left whitespace-nowrap">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="p-4 text-slate-500">Tanggal</th>
                    <th class="p-4 text-slate-500">Kategori</th>
                    <th class="p-4 text-slate-500">Metode</th>
                    <th class="p-4 text-slate-500">Hasil (Rp)</th>
                    <th class="p-4 text-slate-500">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $trx)
                    <tr class="border-b border-slate-100 hover:bg-slate-50">
                        <td class="p-4 font-semibold text-slate-700 text-sm">{{ $trx->created_at->translatedFormat('d M Y') }}</td>
                        <td class="p-4 text-sm">{{ $trx->category }}</td>
                        <td class="p-4">
                            <span class="{{ $trx->method_badge }} px-2 py-1 rounded text-xs font-bold">{{ $trx->method }}</span>
                        </td>
                        <td class="p-4 font-bold {{ $trx->status === 'complete' ? 'text-green-600' : 'text-slate-400' }}">
                            @if($trx->status === 'complete')
                                + Rp {{ number_format($trx->total_price, 0, ',', '.') }}
                            @else
                                Rp 0
                            @endif
                        </td>
                        <td class="p-4 flex items-center gap-2">
                            @php $badge = $trx->status_badge; @endphp
                            <span class="{{ $badge['class'] }} px-3 py-1 rounded-full text-xs font-bold border">{{ $badge['label'] }}</span>
                            @if($trx->status === 'rejected' && $trx->reject_reason)
                                <button onclick="document.getElementById('reason-desk-{{ $trx->id }}').classList.toggle('hidden')" class="text-xs bg-slate-200 hover:bg-slate-300 px-2 py-1 rounded font-bold text-slate-600 transition shadow-sm">Lihat Alasan</button>
                            @elseif($trx->status === 'complete')
                                <button onclick="showNota({{ $trx->id }}, '{{ Auth::user()->name }}', '{{ Auth::user()->nik }}', '{{ $trx->category }}', '{{ $trx->actual_weight }}', '{{ number_format($trx->total_price, 0, ',', '.') }}', '{{ $trx->updated_at->translatedFormat('d M Y H:i') }}')" class="text-xs bg-green-100 hover:bg-green-200 text-green-700 px-2 py-1 rounded font-bold border border-green-200 transition shadow-sm">📄 Lihat Nota</button>
                            @endif
                        </td>
                    </tr>
                    @if($trx->status === 'rejected' && $trx->reject_reason)
                        <tr id="reason-desk-{{ $trx->id }}" class="hidden">
                            <td colspan="5" class="p-4 bg-red-50 text-sm text-red-700 italic border-l-4 border-red-500">⚠️ {{ $trx->reject_reason }}</td>
                        </tr>
                    @endif
                @empty
                    <tr><td colspan="5" class="p-8 text-center text-slate-400 italic">Belum ada riwayat transaksi.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $transactions->links() }}</div>
</div>
@endsection

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
function showNota(id, name, nik, kategori, berat, totalBayar, tanggal) {
    const modal = document.getElementById('nota-modal');
    modal.style.display = 'flex';
    modal.classList.remove('hidden');

    document.getElementById('nota-content').innerHTML = `
        <div class="flex justify-between"><span class="text-slate-500">No. Transaksi</span><span class="font-bold text-slate-800">#${id}</span></div>
        <div class="flex justify-between"><span class="text-slate-500">Tanggal Selesai</span><span class="font-bold text-slate-800">${tanggal}</span></div>
        <div class="flex justify-between"><span class="text-slate-500">Penerima</span><span class="font-bold text-slate-800">${name}</span></div>
        <div class="flex justify-between"><span class="text-slate-500">NIK</span><span class="font-bold text-slate-600 text-xs">${nik}</span></div>
        <hr class="border-slate-100">
        <div class="flex justify-between"><span class="text-slate-500">Kategori</span><span class="font-bold text-slate-800">${kategori}</span></div>
        <div class="flex justify-between"><span class="text-slate-500">Berat Aktual</span><span class="font-bold text-slate-800">${berat} Kg</span></div>
        <hr class="border-slate-100">
        <div class="flex justify-between text-lg"><span class="font-extrabold text-slate-800">TOTAL BAYAR</span><span class="font-extrabold text-green-600">Rp ${totalBayar}</span></div>
        <p class="text-[10px] text-slate-400 text-center mt-2">Dana ini telah dikreditkan ke saldo dompet digitalmu.<br>Kamu dapat melakukan penarikan ke rekening pribadi di menu Profil.</p>
    `;
}

function closeNota() {
    const modal = document.getElementById('nota-modal');
    modal.style.display = 'none';
    modal.classList.add('hidden');
}

document.getElementById('nota-modal')?.addEventListener('click', function(e) {
    if (e.target === this) closeNota();
});
</script>
@endsection

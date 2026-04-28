@extends('layouts.admin')
@section('title', 'Permintaan Baru | Admin TernakSampah')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-extrabold text-slate-800 flex items-center gap-2">Menunggu Konfirmasi <span class="bg-orange-100 text-orange-600 text-sm px-3 py-1 rounded-full border border-orange-200">Tahap 1</span></h2>
    <p class="text-slate-500 text-sm mt-1">Daftar pengguna yang baru saja mengirimkan permintaan setor sampah.</p>
</div>

<div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
    {{-- Mobile Card Layout --}}
    <div class="md:hidden space-y-4 p-4">
        @forelse($transactions as $t)
            <div class="bg-white border border-slate-200 p-4 rounded-2xl shadow-sm relative">
                <div class="flex justify-between items-start mb-3 border-b border-slate-100 pb-3">
                    <div>
                        <h4 class="font-extrabold text-slate-800 text-base">{{ $t->user->name }}</h4>
                        <p class="text-xs text-slate-500 mt-0.5">ID: #{{ $t->id }} • {{ $t->created_at->translatedFormat('d M Y') }}</p>
                    </div>
                    <div>
                        <span class="{{ $t->method === 'Pick-up' ? 'bg-purple-100 text-purple-700 border-purple-200' : 'bg-blue-100 text-blue-700 border-blue-200' }} border px-2 py-1 rounded text-[10px] font-bold">{{ $t->method }}</span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2 mb-4 text-sm">
                    <div class="bg-slate-50 p-2 rounded-xl border border-slate-100">
                        <p class="text-[10px] font-bold text-slate-400 uppercase">Kategori</p>
                        <p class="font-semibold text-slate-700 truncate">{{ $t->category }}</p>
                    </div>
                    <div class="bg-orange-50 p-2 rounded-xl border border-orange-100">
                        <p class="text-[10px] font-bold text-orange-400 uppercase">Est. Berat</p>
                        <p class="font-bold text-orange-600">{{ $t->est_weight }} Kg</p>
                    </div>
                </div>

                <div class="flex flex-col gap-2">
                    @if($t->photo)
                        <a href="{{ asset('storage/' . $t->photo) }}" target="_blank" class="w-full text-center bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold py-2 rounded-xl border border-slate-300 transition shadow-sm">📸 Lihat Bukti Foto</a>
                    @else
                        <span class="w-full text-center bg-slate-50 text-slate-400 text-xs font-bold py-2 rounded-xl border border-slate-200">Tidak ada foto</span>
                    @endif
                    
                    <div class="flex gap-2 mt-1">
                        <form action="{{ route('admin.terima', $t->id) }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit" class="w-full bg-orange-500 text-white text-xs font-bold py-2.5 rounded-xl hover:bg-orange-600 transition shadow-sm">✅ Terima</button>
                        </form>
                        <button type="button" onclick="openRejectModal({{ $t->id }}, '{{ $t->user->name }}')" class="flex-1 bg-red-500 text-white text-xs font-bold py-2.5 rounded-xl hover:bg-red-600 transition shadow-sm">🚫 Tolak</button>
                    </div>
                </div>
            </div>
        @empty
            <div class="p-6 text-center text-slate-400 italic bg-slate-50 rounded-2xl border border-slate-100 text-sm">Belum ada permintaan masuk baru.</div>
        @endforelse
    </div>

    {{-- Desktop Table Layout --}}
    <div class="hidden md:block overflow-x-auto">
        <table class="w-full text-left whitespace-nowrap">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="p-4 text-slate-500 font-bold text-sm">ID</th>
                    <th class="p-4 text-slate-500 font-bold text-sm">Nama User</th>
                    <th class="p-4 text-slate-500 font-bold text-sm text-center">Bukti Foto</th>
                    <th class="p-4 text-slate-500 font-bold text-sm">Metode</th>
                    <th class="p-4 text-slate-500 font-bold text-sm">Kategori</th>
                    <th class="p-4 text-slate-500 font-bold text-sm">Est. Berat</th>
                    <th class="p-4 text-slate-500 font-bold text-sm text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($transactions as $t)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="p-4 font-bold text-slate-800 text-sm">#{{ $t->id }}</td>
                        <td class="p-4 font-semibold text-slate-700">{{ $t->user->name }}<br><span class="text-xs text-slate-400 font-normal">{{ $t->created_at->translatedFormat('d M Y') }}</span></td>
                        <td class="p-4 text-center">
                            @if($t->photo)
                                <a href="{{ asset('storage/' . $t->photo) }}" target="_blank" class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold py-1.5 px-3 rounded-lg border border-slate-300 transition shadow-sm">📸 Lihat</a>
                            @else
                                <span class="text-xs text-slate-400">-</span>
                            @endif
                        </td>
                        <td class="p-4"><span class="{{ $t->method === 'Pick-up' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }} px-2 py-1 rounded-md text-xs font-bold">{{ $t->method }}</span></td>
                        <td class="p-4 text-sm font-semibold text-slate-600">{{ $t->category }}</td>
                        <td class="p-4 text-sm font-bold text-orange-500">{{ $t->est_weight }} Kg</td>
                        <td class="p-4 text-center flex gap-2 justify-center">
                            <form action="{{ route('admin.terima', $t->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="bg-orange-500 text-white text-xs font-bold px-4 py-2 rounded-lg hover:bg-orange-600 transition shadow-sm">Terima & Timbang</button>
                            </form>
                            <button type="button" onclick="openRejectModal({{ $t->id }}, '{{ $t->user->name }}')" class="bg-red-500 text-white text-xs font-bold px-4 py-2 rounded-lg hover:bg-red-600 transition shadow-sm">Tolak</button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="p-8 text-center text-slate-400 italic">Belum ada permintaan masuk baru.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

{{-- Reject Modal --}}
@section('modals')
<div id="reject-modal" class="fixed inset-0 z-[300] items-center justify-center bg-black/50 backdrop-blur-sm hidden" style="display:none;">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 p-6 relative" onclick="event.stopPropagation()">
        <button onclick="closeRejectModal()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 text-xl font-bold">✕</button>

        <div class="text-center mb-5">
            <div class="w-14 h-14 bg-red-100 text-red-500 rounded-2xl flex items-center justify-center text-2xl mx-auto mb-3">🚫</div>
            <h3 class="text-xl font-extrabold text-slate-800">Tolak Transaksi</h3>
            <p class="text-sm text-slate-500 mt-1">Transaksi <span id="modal-trx-info" class="font-bold text-slate-700"></span></p>
        </div>

        <form id="reject-form" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="text-xs font-bold text-slate-600 mb-2 block">Pilih Alasan Penolakan</label>
                <div class="space-y-2" id="reason-options">
                    @foreach([
                        'Foto tidak jelas atau tidak sesuai',
                        'Kategori sampah tidak sesuai',
                        'Sampah tidak memenuhi standar kualitas',
                        'Berat estimasi tidak realistis',
                        'Sampah terkontaminasi bahan berbahaya',
                        'Data transaksi tidak lengkap',
                    ] as $reason)
                        <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 cursor-pointer hover:bg-red-50 hover:border-red-200 transition has-[:checked]:bg-red-50 has-[:checked]:border-red-400">
                            <input type="radio" name="reject_preset" value="{{ $reason }}" class="accent-red-500" onchange="updateRejectReason()">
                            <span class="text-sm font-semibold text-slate-700">{{ $reason }}</span>
                        </label>
                    @endforeach
                    <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 cursor-pointer hover:bg-red-50 hover:border-red-200 transition has-[:checked]:bg-red-50 has-[:checked]:border-red-400">
                        <input type="radio" name="reject_preset" value="__other__" class="accent-red-500" onchange="updateRejectReason()">
                        <span class="text-sm font-semibold text-slate-700">Lainnya (tulis sendiri)</span>
                    </label>
                </div>
            </div>

            <div id="custom-reason-area" class="hidden">
                <textarea id="custom-reason-text" placeholder="Tulis alasan penolakan..." class="w-full p-3 border border-slate-200 rounded-xl h-20 outline-none focus:border-red-400 text-sm"></textarea>
            </div>

            <input type="hidden" name="reject_reason" id="final-reject-reason">

            <button type="submit" onclick="return submitReject()" class="w-full bg-red-500 text-white font-bold py-3 rounded-xl hover:bg-red-600 transition shadow-md">Konfirmasi Penolakan</button>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
function openRejectModal(id, name) {
    const modal = document.getElementById('reject-modal');
    modal.style.display = 'flex';
    modal.classList.remove('hidden');
    document.getElementById('reject-form').action = `/admin/tolak/${id}`;
    document.getElementById('modal-trx-info').innerText = `#${id} — ${name}`;
    // Reset
    document.querySelectorAll('input[name="reject_preset"]').forEach(r => r.checked = false);
    document.getElementById('custom-reason-area').classList.add('hidden');
    document.getElementById('custom-reason-text').value = '';
}

function closeRejectModal() {
    const modal = document.getElementById('reject-modal');
    modal.style.display = 'none';
    modal.classList.add('hidden');
}

function updateRejectReason() {
    const selected = document.querySelector('input[name="reject_preset"]:checked');
    if (selected && selected.value === '__other__') {
        document.getElementById('custom-reason-area').classList.remove('hidden');
    } else {
        document.getElementById('custom-reason-area').classList.add('hidden');
    }
}

function submitReject() {
    const selected = document.querySelector('input[name="reject_preset"]:checked');
    if (!selected) {
        alert('Pilih alasan penolakan terlebih dahulu.');
        return false;
    }
    let reason = selected.value;
    if (reason === '__other__') {
        reason = document.getElementById('custom-reason-text').value.trim();
        if (!reason) {
            alert('Tulis alasan penolakan.');
            return false;
        }
    }
    document.getElementById('final-reject-reason').value = reason;
    return true;
}

// Close modal on backdrop click
document.getElementById('reject-modal')?.addEventListener('click', function(e) {
    if (e.target === this) closeRejectModal();
});
</script>
@endsection

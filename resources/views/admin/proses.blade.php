@extends('layouts.admin')
@section('title', 'Permintaan Baru | Admin TernakSampah')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-extrabold text-slate-800 flex items-center gap-2">Menunggu Konfirmasi <span class="bg-orange-100 text-orange-600 text-sm px-3 py-1 rounded-full border border-orange-200">Tahap 1</span></h2>
    <p class="text-slate-500 text-sm mt-1">Daftar pengguna yang baru saja mengirimkan permintaan setor sampah.</p>
</div>

<div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
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
                            <form action="{{ route('admin.tolak', $t->id) }}" method="POST" onsubmit="return promptReject(this)">
                                @csrf
                                <input type="hidden" name="reject_reason" id="reject-{{ $t->id }}">
                                <button type="submit" class="bg-red-500 text-white text-xs font-bold px-4 py-2 rounded-lg hover:bg-red-600 transition shadow-sm">Tolak</button>
                            </form>
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

@section('scripts')
<script>
function promptReject(form) {
    const reason = prompt('Masukkan alasan penolakan:');
    if (!reason) return false;
    form.querySelector('input[name="reject_reason"]').value = reason;
    return true;
}
</script>
@endsection

@extends('layouts.admin')
@section('title', 'Riwayat Selesai | Admin TernakSampah')

@section('content')
<div class="mb-6 flex justify-between items-end">
    <div>
        <h2 class="text-2xl font-extrabold text-slate-800 flex items-center gap-2">Riwayat Selesai <span class="bg-green-100 text-green-600 text-sm px-3 py-1 rounded-full border border-green-200">Tahap 3</span></h2>
        <p class="text-slate-500 text-sm mt-1">Semua transaksi yang telah sukses dibayarkan.</p>
    </div>
</div>

<div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
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
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($transactions as $t)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="p-4 text-sm font-semibold text-slate-500">{{ $t->updated_at->translatedFormat('d M Y') }}</td>
                        <td class="p-4 font-bold text-slate-800 text-sm">{{ $t->user->name }}<br><span class="text-xs text-slate-400 font-normal">#{{ $t->id }}</span></td>
                        <td class="p-4"><span class="{{ $t->method === 'Pick-up' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }} px-2 py-1 rounded-md text-xs font-bold">{{ $t->method }}</span></td>
                        <td class="p-4 text-sm font-semibold text-slate-600">{{ $t->category }}</td>
                        <td class="p-4 text-sm font-bold text-slate-800">{{ $t->actual_weight }} Kg</td>
                        <td class="p-4 font-extrabold text-green-600">Rp {{ number_format($t->total_price, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="p-8 text-center text-slate-400 italic">Belum ada riwayat selesai.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

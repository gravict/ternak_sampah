@extends('layouts.app')
@section('title', 'Riwayat | TernakSampah')

@section('content')
<div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100">
    <h2 class="text-2xl font-extrabold mb-6">Riwayat Transaksi</h2>
    <div class="overflow-x-auto">
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
                                <button onclick="document.getElementById('reason-{{ $trx->id }}').classList.toggle('hidden')" class="text-xs bg-slate-200 hover:bg-slate-300 px-2 py-1 rounded font-bold text-slate-600 transition shadow-sm">Lihat Alasan</button>
                            @endif
                        </td>
                    </tr>
                    @if($trx->status === 'rejected' && $trx->reject_reason)
                        <tr id="reason-{{ $trx->id }}" class="hidden">
                            <td colspan="5" class="p-4 bg-red-50 text-sm text-red-700 italic">⚠️ {{ $trx->reject_reason }}</td>
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

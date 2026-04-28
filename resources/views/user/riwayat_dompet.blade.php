@extends('layouts.app')
@section('title', 'Riwayat Dompet | TernakSampah')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <h2 class="text-2xl font-extrabold text-slate-800 flex items-center gap-2">Riwayat Dompet</h2>
        <p class="text-slate-500 text-sm mt-1">Lacak seluruh aktivitas penarikan saldo Anda.</p>
    </div>

    <div class="bg-white p-4 sm:p-6 md:p-8 rounded-3xl shadow-sm border border-slate-100" id="wallet-history">
        <div class="md:hidden space-y-4">
            @forelse($walletHistories as $wh)
                <div class="bg-white border border-slate-100 p-4 rounded-2xl shadow-sm">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            @if($wh->type === 'withdraw')
                                <span class="text-red-600 flex items-center gap-1 font-bold text-sm"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg> Penarikan</span>
                            @else
                                <span class="text-green-600 flex items-center gap-1 font-bold text-sm"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg> Pemasukan</span>
                            @endif
                        </div>
                        <div class="text-right">
                            <span class="font-bold text-slate-800 text-sm">Rp {{ number_format($wh->amount, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        @if($wh->bank_name)
                            <p class="text-xs text-slate-600 font-semibold flex items-center gap-1">🏦 {{ $wh->bank_name }} - {{ $wh->account_number }}</p>
                            <p class="text-[10px] text-slate-400">a.n {{ $wh->account_name }}</p>
                        @else
                            <p class="text-xs text-slate-500 font-semibold">TernakSampah Central</p>
                        @endif
                    </div>
                    
                    <div class="pt-3 border-t border-slate-100 flex justify-between items-center">
                        <span class="text-xs text-slate-500">{{ $wh->created_at->format('d M Y, H:i') }}</span>
                        @if($wh->status === 'success')
                            <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-[10px] font-bold border border-green-200">Sukses</span>
                        @elseif($wh->status === 'pending')
                            <span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded text-[10px] font-bold border border-yellow-200">Pending</span>
                        @else
                            <span class="bg-red-100 text-red-700 px-2 py-1 rounded text-[10px] font-bold border border-red-200">Gagal</span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="p-6 text-center text-slate-400 italic bg-slate-50 rounded-2xl border border-slate-100 text-sm">Belum ada aktivitas dompet.</div>
            @endforelse
        </div>

        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead>
                    <tr class="text-slate-500 border-b border-slate-100 bg-slate-50">
                        <th class="p-4 font-bold text-center">Waktu</th>
                        <th class="p-4 font-bold text-center">Jenis</th>
                        <th class="p-4 font-bold text-center">Nominal</th>
                        <th class="p-4 font-bold text-center">Tujuan / Sumber</th>
                        <th class="p-4 font-bold text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($walletHistories as $wh)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="p-4 text-slate-600 text-center">{{ $wh->created_at->format('d M Y, H:i') }}</td>
                            <td class="p-4 font-semibold text-slate-700 capitalize text-center">
                                @if($wh->type === 'withdraw')
                                    <span class="text-red-600 flex items-center justify-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg> Penarikan</span>
                                @else
                                    <span class="text-green-600 flex items-center justify-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg> Pemasukan</span>
                                @endif
                            </td>
                            <td class="p-4 font-bold text-slate-800 text-center">Rp {{ number_format($wh->amount, 0, ',', '.') }}</td>
                            <td class="p-4 text-slate-600 text-center">
                                @if($wh->bank_name)
                                    <span class="font-semibold">{{ $wh->bank_name }}</span> - {{ $wh->account_number }}<br>
                                    <span class="text-xs text-slate-400">a.n {{ $wh->account_name }}</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="p-4 text-center">
                                @if($wh->status === 'success')
                                    <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold border border-green-200">Sukses</span>
                                @elseif($wh->status === 'pending')
                                    <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-xs font-bold border border-yellow-200">Pending</span>
                                @else
                                    <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-bold border border-red-200">Gagal</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-slate-400 italic">Belum ada aktivitas dompet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

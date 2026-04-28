@extends('layouts.app')
@section('title', 'Voucher | TernakSampah')

@section('content')
<div class="bg-gradient-to-r from-orange-500 to-orange-400 rounded-3xl p-6 md:p-8 text-white shadow-lg mb-8 flex justify-between items-center">
    <div>
        <p class="font-bold opacity-90 uppercase tracking-wider text-xs md:text-sm mb-1">Poin Tersedia</p>
        <h2 class="text-4xl md:text-5xl font-extrabold flex items-center gap-2">{{ Auth::user()->points }} <span class="text-xl md:text-2xl">Pts</span></h2>
    </div>
    <div class="text-6xl opacity-20 hidden sm:block">🎁</div>
</div>

<h3 class="text-xl font-extrabold mb-4">Daftar Voucher</h3>
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
    @foreach($vouchers as $v)
        <div class="bg-white border border-slate-100 rounded-3xl p-6 flex flex-col justify-between shadow-sm">
            <div>
                <div class="text-4xl mb-4">{{ $v->icon }}</div>
                <h4 class="font-bold text-lg mb-1">{{ $v->name }}</h4>
                <p class="text-orange-500 font-extrabold">{{ $v->cost_points }} Poin</p>
            </div>
            <form action="{{ route('voucher.redeem') }}" method="POST" class="mt-6">
                @csrf
                <input type="hidden" name="voucher_id" value="{{ $v->id }}">
                <button type="submit" class="w-full border-2 border-orange-500 text-orange-500 font-bold py-2 rounded-xl hover:bg-orange-500 hover:text-white transition" onclick="return confirm('Tukar {{ $v->cost_points }} poin untuk {{ $v->name }}?')">Tukar Poin</button>
            </form>
        </div>
    @endforeach
</div>

<div class="pt-8 border-t border-slate-200">
    <h3 class="text-xl font-extrabold mb-4 flex items-center gap-2">Voucher Saya <span class="text-sm font-normal text-slate-500">(Berhasil Diklaim)</span></h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @forelse($myVouchers as $mv)
            <div class="bg-slate-50 border border-slate-200 p-4 rounded-2xl flex items-center gap-4">
                <div class="text-3xl">{{ $mv->voucher->icon }}</div>
                <div>
                    <p class="font-bold text-slate-800">{{ $mv->voucher->name }}</p>
                    <p class="text-xs text-slate-500">Diklaim: {{ $mv->claimed_at->translatedFormat('d M Y') }}</p>
                </div>
            </div>
        @empty
            <p class="text-slate-400 italic text-sm">Belum ada voucher yang diklaim.</p>
        @endforelse
    </div>
</div>
@endsection

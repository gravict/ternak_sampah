@extends('layouts.admin')
@section('title', 'Admin Dashboard | TernakSampah')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-extrabold text-slate-800">Ringkasan Bank Sampah</h1>
    <p class="text-slate-500 text-sm">Data akumulasi seluruh transaksi yang telah Selesai.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-200 flex items-center gap-6">
        <div class="w-16 h-16 bg-green-100 text-green-600 rounded-2xl flex items-center justify-center text-3xl font-bold">⚖️</div>
        <div>
            <p class="text-sm text-slate-500 font-bold uppercase tracking-wider mb-1">Total Sampah Terkumpul</p>
            <h3 class="text-4xl font-extrabold text-slate-800">{{ number_format($totalKg, 1) }} <span class="text-lg text-slate-400 font-semibold">Kg</span></h3>
        </div>
    </div>
    <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-200 flex items-center gap-6">
        <div class="w-16 h-16 bg-orange-100 text-orange-600 rounded-2xl flex items-center justify-center text-3xl font-bold">💸</div>
        <div>
            <p class="text-sm text-slate-500 font-bold uppercase tracking-wider mb-1">Total Dana Dibayarkan</p>
            <h3 class="text-4xl font-extrabold text-slate-800">Rp {{ number_format($totalRp, 0, ',', '.') }}</h3>
        </div>
    </div>
</div>

<h3 class="text-xl font-extrabold text-slate-800 mb-4">Rincian Sampah Masuk</h3>
<div class="grid grid-cols-2 md:grid-cols-4 gap-4">
    <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 border-b-4 border-b-blue-500">
        <p class="text-sm text-slate-500 font-bold mb-2">Plastik / PET</p>
        <h4 class="text-2xl font-extrabold text-slate-800">{{ number_format($catPlastik, 1) }} <span class="text-xs text-slate-400">Kg</span></h4>
    </div>
    <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 border-b-4 border-b-yellow-500">
        <p class="text-sm text-slate-500 font-bold mb-2">Kertas / Kardus</p>
        <h4 class="text-2xl font-extrabold text-slate-800">{{ number_format($catKertas, 1) }} <span class="text-xs text-slate-400">Kg</span></h4>
    </div>
    <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 border-b-4 border-b-slate-800">
        <p class="text-sm text-slate-500 font-bold mb-2">Logam / Besi</p>
        <h4 class="text-2xl font-extrabold text-slate-800">{{ number_format($catLogam, 1) }} <span class="text-xs text-slate-400">Kg</span></h4>
    </div>
    <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 border-b-4 border-b-orange-500">
        <p class="text-sm text-slate-500 font-bold mb-2">Campuran / Residu</p>
        <h4 class="text-2xl font-extrabold text-slate-800">{{ number_format($catCampur, 1) }} <span class="text-xs text-slate-400">Kg</span></h4>
    </div>
</div>
@endsection

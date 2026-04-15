@extends('layouts.app')
@section('title', 'Panduan | TernakSampah')

@section('content')
<div class="max-w-4xl mx-auto">
    <h2 class="text-3xl font-extrabold mb-6">Tata Cara & Panduan Transaksi 📖</h2>
    <div class="space-y-6">
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 flex gap-6 items-start">
            <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center text-2xl font-bold flex-shrink-0">1</div>
            <div>
                <h3 class="text-xl font-bold text-slate-800 mb-2">Pilah & Bersihkan Sampah</h3>
                <p class="text-slate-500">Pisahkan sampah organik (basah) dan anorganik (kering). Pastikan botol plastik atau kaleng sudah dibilas bersih dan dikeringkan agar harganya tidak turun saat ditimbang admin.</p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 flex gap-6 items-start">
            <div class="w-16 h-16 bg-orange-100 text-orange-600 rounded-2xl flex items-center justify-center text-2xl font-bold flex-shrink-0">2</div>
            <div>
                <h3 class="text-xl font-bold text-slate-800 mb-2">Buat Permintaan Transaksi</h3>
                <p class="text-slate-500">Masuk ke menu <span class="font-bold">Transaksi</span>. <span class="text-green-600 font-bold">Wajib lampirkan foto fisik sampahmu</span>. Pilih metode penyerahan (Drop-off/Pick-up).</p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 flex gap-6 items-start">
            <div class="w-16 h-16 bg-yellow-100 text-yellow-600 rounded-2xl flex items-center justify-center text-2xl font-bold flex-shrink-0">3</div>
            <div>
                <h3 class="text-xl font-bold text-slate-800 mb-2">Status "Dikirim" & Penimbangan</h3>
                <p class="text-slate-500">Setelah transaksi dibuat, status di riwayat akan menjadi <span class="font-bold text-yellow-600">Dikirim</span>. Admin akan melakukan pengecekan kualitas dan penimbangan ulang secara akurat di lokasi.</p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 flex gap-6 items-start">
            <div class="w-16 h-16 bg-green-100 text-green-600 rounded-2xl flex items-center justify-center text-2xl font-bold flex-shrink-0">4</div>
            <div>
                <h3 class="text-xl font-bold text-slate-800 mb-2">Saldo Cair (Status Selesai)</h3>
                <p class="text-slate-500">Jika sesuai, status berubah menjadi <span class="font-bold text-green-600">Selesai</span>. Saldo rupiah dan Poin akan otomatis masuk ke akunmu dan siap ditarik (Withdraw) ke Rekening/E-Wallet!</p>
            </div>
        </div>
    </div>
</div>
@endsection

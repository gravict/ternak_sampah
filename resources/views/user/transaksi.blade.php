@extends('layouts.app')
@section('title', 'Setor Sampah | TernakSampah')

@section('content')
<div class="max-w-3xl mx-auto">
    <h2 class="text-3xl font-extrabold mb-6">Form Setor Sampah ♻️</h2>

    <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100">
        <div class="mb-6 bg-blue-50 p-4 rounded-xl border border-blue-100 flex gap-3 items-start">
            <span class="text-blue-500 text-xl">ℹ️</span>
            <div>
                <p class="text-sm font-bold text-blue-800">Penilaian Parametrik Bank Sampah</p>
                <p class="text-xs text-blue-600 mt-1">Harga akhir ditentukan admin berdasarkan kualitas, kebersihan, dan berat asli hasil timbangan.</p>
            </div>
        </div>

        <form action="{{ route('transaksi.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            <input type="hidden" name="location_lat" id="location_lat">
            <input type="hidden" name="location_lng" id="location_lng">

            {{-- Photo Upload --}}
            <div>
                <label class="block text-sm font-bold text-slate-600 mb-2">Foto Fisik Sampah <span class="text-red-500">*</span></label>
                <p class="text-xs text-slate-500 mb-3">Lampirkan foto sampah yang akan disetor.</p>
                <input type="file" name="photo" accept="image/*" capture="environment" class="w-full p-3 border-2 border-dashed border-slate-300 rounded-2xl bg-slate-50 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-green-50 file:text-green-700 hover:file:bg-green-100 cursor-pointer" required>
                @error('photo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Category & Weight --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-slate-600 mb-2">Kategori Utama</label>
                    <select name="category" id="trans-cat" class="w-full p-3 border border-slate-200 rounded-xl bg-slate-50 outline-none focus:border-green-500" onchange="updateEstFee()">
                        <option value="Plastik / PET">Plastik / Botol PET</option>
                        <option value="Kardus / Kertas">Kardus / Kertas</option>
                        <option value="Besi / Logam">Besi / Logam</option>
                        <option value="Minyak Jelantah">Minyak Jelantah</option>
                        <option value="Campuran">Campuran / Residu</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-600 mb-2">Est. Berat (Kg)</label>
                    <input type="number" name="est_weight" id="trans-kg" step="0.1" min="0.1" placeholder="Misal: 2.5" class="w-full p-3 border border-slate-200 rounded-xl bg-slate-50 outline-none focus:border-green-500" onkeyup="updateEstFee()" required>
                </div>
            </div>

            {{-- Method --}}
            <div>
                <label class="block text-sm font-bold text-slate-600 mb-2">Metode Penyerahan</label>
                <select name="method" id="trans-method" class="w-full p-3 border border-slate-200 rounded-xl bg-slate-50 outline-none focus:border-green-500" onchange="toggleMethod()">
                    <option value="Drop-off">Drop-off (Antar Sendiri - Gratis)</option>
                    <option value="Pick-up">Pick-up (Dijemput Petugas)</option>
                </select>
            </div>

            {{-- Drop-off Location --}}
            <div id="dropoff-area" class="bg-slate-50 p-4 rounded-xl border border-slate-200 space-y-3">
                <label class="block text-sm font-bold text-slate-600">Pilih Lokasi Bank Sampah</label>
                <select name="dropoff_location" class="w-full p-3 border border-slate-200 rounded-xl outline-none bg-white">
                    <option>Bank Sampah Untar (Kampus 1, Jl. Letjen S. Parman)</option>
                    <option>Bank Sampah Tomang (Jl. Tomang Raya No 10)</option>
                </select>
            </div>

            {{-- Pick-up Details --}}
            <div id="pickup-area" class="bg-orange-50 p-4 rounded-xl border border-orange-200 hidden">
                <p class="text-xs font-bold text-orange-700 mb-3">⚠️ Biaya Pick-up: Potongan 20% dari estimasi pendapatan</p>
                <div class="space-y-3">
                    <input type="datetime-local" name="pickup_datetime" class="w-full p-3 border border-orange-200 rounded-xl outline-none bg-white text-sm">
                    <textarea name="pickup_address" placeholder="Alamat lengkap penjemputan..." class="w-full p-3 border border-orange-200 rounded-xl h-20 outline-none bg-white text-sm"></textarea>
                </div>
            </div>

            {{-- Estimate & Submit --}}
            <div class="bg-slate-800 p-5 rounded-xl flex justify-between items-center text-white shadow-md mt-4">
                <div>
                    <p class="text-xs text-slate-400 font-bold uppercase mb-1">Estimasi Pendapatan</p>
                    <h4 id="trans-est-display" class="text-2xl font-extrabold text-green-400">Rp 0</h4>
                </div>
                <button type="submit" class="bg-green-600 text-white font-extrabold px-6 py-3 rounded-xl shadow-lg hover:bg-green-500 transition active:scale-95 text-sm">Kirim Data</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
function updateEstFee() {
    const kg = parseFloat(document.getElementById('trans-kg').value) || 0;
    const cat = document.getElementById('trans-cat').value;
    const method = document.getElementById('trans-method').value;
    let price = cat.includes('Plastik') ? 3000 : (cat.includes('Kardus') ? 2500 : (cat.includes('Besi') ? 4500 : (cat.includes('Minyak') ? 5000 : 1000)));
    let est = kg * price;
    if (method === 'Pick-up' && est > 0) est -= (est * 0.20);
    document.getElementById('trans-est-display').innerText = 'Rp ' + Math.max(0, est).toLocaleString('id-ID');
}
function toggleMethod() {
    const m = document.getElementById('trans-method').value;
    document.getElementById('dropoff-area').style.display = m === 'Drop-off' ? 'block' : 'none';
    document.getElementById('pickup-area').style.display = m === 'Pick-up' ? 'block' : 'none';
    updateEstFee();
}
// Try to get geolocation
if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(p => {
        document.getElementById('location_lat').value = p.coords.latitude;
        document.getElementById('location_lng').value = p.coords.longitude;
    });
}
</script>
@endsection

@extends('layouts.app')
@section('title', 'Setor Sampah | TernakSampah')

@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endsection

@section('content')
<div class="max-w-3xl mx-auto">
    <h2 class="text-3xl font-extrabold mb-6">Form Setor Sampah ♻️</h2>

    <div class="bg-white p-4 sm:p-6 md:p-8 rounded-3xl shadow-sm border border-slate-100">
        <div class="mb-6 bg-blue-50 p-4 rounded-xl border border-blue-100 flex flex-col md:flex-row gap-3 items-start">
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

            <div>
                <label class="block text-sm font-bold text-slate-600 mb-2">Foto Fisik Sampah <span class="text-red-500">*</span></label>
                <p class="text-xs text-slate-500 mb-3">Sistem mewajibkan penggunaan kamera perangkat langsung (File Explorer dimatikan).</p>
                
                <input type="file" id="photo_input" name="photo" accept="image/*" class="hidden" required>
                
                <div id="camera_container" class="w-full bg-slate-900 rounded-2xl overflow-hidden relative" style="height: 300px;">
                    <video id="camera_stream" autoplay playsinline class="w-full h-full object-cover"></video>
                    <div id="camera_overlay" class="absolute inset-0 flex items-center justify-center bg-black/50 z-10 transition">
                        <button type="button" id="start_camera_btn" class="bg-green-600 text-white px-6 py-3 rounded-xl font-bold shadow-lg hover:bg-green-500 transition active:scale-95">Buka Kamera</button>
                    </div>
                </div>

                <div class="mt-3 flex flex-col sm:flex-row gap-2 hidden" id="camera_actions">
                    <button type="button" id="snap_btn" class="w-full sm:flex-1 bg-green-600 text-white px-4 py-3 rounded-xl font-bold shadow hover:bg-green-500 transition">📸 Jepret Foto</button>
                    <button type="button" id="retake_btn" class="w-full sm:flex-1 bg-slate-200 text-slate-700 px-4 py-3 rounded-xl font-bold shadow hover:bg-slate-300 transition hidden">🔄 Ulangi Memotret</button>
                </div>
                
                <canvas id="camera_canvas" class="w-full h-full object-cover hidden bg-black"></canvas>
                <p id="location_status" class="text-xs font-bold mt-3 text-slate-500 border-l-4 border-slate-300 pl-3">📍 Menunggu pengambilan foto...</p>
                @error('photo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-sm font-bold text-slate-600">Kategori Utama</label>
                        <a href="{{ route('daftar_harga') }}" target="_blank" class="text-xs font-bold text-green-600 hover:text-green-700 hover:underline transition">📋 Lihat Daftar Harga →</a>
                    </div>
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

            <div>
                <label class="block text-sm font-bold text-slate-600 mb-2">Metode Penyerahan</label>
                <select name="method" id="trans-method" class="w-full p-3 border border-slate-200 rounded-xl bg-slate-50 outline-none focus:border-green-500" onchange="toggleMethod()">
                    <option value="Drop-off">Drop-off (Antar Sendiri - Gratis)</option>
                    <option value="Pick-up">Pick-up (Dijemput Petugas)</option>
                </select>
            </div>

            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 space-y-3">
                <label class="block text-sm font-bold text-slate-600">Pilih Cabang Bank Sampah Tujuan</label>
                <select name="dropoff_location" class="w-full p-3 border border-slate-200 rounded-xl outline-none bg-white" required>
                    <option value="Bank Sampah Untar">Bank Sampah Untar (Kampus 1, Jl. Letjen S. Parman)</option>
                    <option value="Bank Sampah Tomang">Bank Sampah Tomang (Jl. Tomang Raya No. 10)</option>
                    <option value="Bank Sampah Grogol">Bank Sampah Grogol (Jl. Grogol Petamburan No. 88)</option>
                    <option value="Bank Sampah Kebon Jeruk">Bank Sampah Kebon Jeruk (Jl. Kebon Jeruk Raya No. 5)</option>
                    <option value="Bank Sampah Tanjung Duren">Bank Sampah Tanjung Duren (Jl. Tanjung Duren Barat No. 12)</option>
                </select>
            </div>

            <div id="pickup-area" class="bg-slate-50 p-4 rounded-xl border border-slate-200 hidden">
                <p class="text-xs font-bold text-red-500 mb-3">⚠️ Biaya Pick-up: Potongan 20% dari estimasi pendapatan</p>
                <div class="space-y-3">
                    <input type="datetime-local" name="pickup_datetime" class="w-full p-3 border border-slate-200 rounded-xl outline-none bg-white text-sm">
                    <div>
                        <label class="text-xs font-bold text-slate-600 mb-1 block">📍 Lokasi Penjemputan (Geser pin untuk mengubah)</label>
                        <div id="pickup-map" class="w-full h-48 rounded-xl border border-slate-200 overflow-hidden z-0"></div>
                        <p id="pickup-map-status" class="text-[10px] text-slate-500 font-bold mt-1">Mendeteksi lokasi GPS...</p>
                    </div>

                    <textarea name="pickup_address" id="pickup_address_input" placeholder="Alamat lengkap penjemputan..." class="w-full p-3 border border-slate-200 rounded-xl h-20 outline-none bg-white text-sm"></textarea>
                </div>
            </div>

            <div class="bg-slate-800 p-5 rounded-xl flex flex-col md:flex-row justify-between md:items-center text-white shadow-md mt-4 gap-4">
                <div>
                    <p class="text-xs text-slate-400 font-bold uppercase mb-1">Estimasi Pendapatan</p>
                    <h4 id="trans-est-display" class="text-2xl font-extrabold text-green-400">Rp 0</h4>
                </div>
                <button type="submit" class="w-full md:w-auto bg-green-600 text-white font-extrabold px-6 py-3 rounded-xl shadow-lg hover:bg-green-500 transition active:scale-95 text-sm">Kirim Data</button>
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
    document.getElementById('pickup-area').style.display = m === 'Pick-up' ? 'block' : 'none';
    updateEstFee();
}

const MAX_DIMENSION = 1280;
const JPEG_QUALITY = 0.6;

function formatFileSize(bytes) {
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
    return (bytes / 1048576).toFixed(2) + ' MB';
}

function compressCanvasImage(sourceCanvas, maxDim, quality) {
    return new Promise((resolve) => {
        let w = sourceCanvas.width;
        let h = sourceCanvas.height;
        if (w > maxDim || h > maxDim) {
            if (w > h) {
                h = Math.round(h * (maxDim / w));
                w = maxDim;
            } else {
                w = Math.round(w * (maxDim / h));
                h = maxDim;
            }
        }
        const tempCanvas = document.createElement('canvas');
        tempCanvas.width = w;
        tempCanvas.height = h;
        const tempCtx = tempCanvas.getContext('2d');
        tempCtx.drawImage(sourceCanvas, 0, 0, w, h);

        tempCanvas.toBlob((blob) => {
            resolve(blob);
        }, 'image/jpeg', quality);
    });
}

const video = document.getElementById('camera_stream');
const canvas = document.getElementById('camera_canvas');
const photoInput = document.getElementById('photo_input');
const startCameraBtn = document.getElementById('start_camera_btn');
const snapBtn = document.getElementById('snap_btn');
const retakeBtn = document.getElementById('retake_btn');
const cameraActions = document.getElementById('camera_actions');
const overlay = document.getElementById('camera_overlay');
const statusText = document.getElementById('location_status');

let stream = null;

startCameraBtn.addEventListener('click', async () => {
    try {
        stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
        video.srcObject = stream;
        overlay.classList.add('hidden');
        cameraActions.classList.remove('hidden');
    } catch (err) {
        alert("Kamera ditolak atau tidak didukung di perangkat ini. Info: " + err.message);
    }
});

snapBtn.addEventListener('click', () => {
    video.pause();
    
    statusText.innerText = "⏳ Mendapatkan data satelit dan API lokasi geografis...";
    statusText.className = "text-xs font-bold mt-3 text-orange-500 border-l-4 border-orange-500 pl-3";
    
    const processScreenshot = async (lat, lng) => {
        let addressText = "Mencari lokasi...";
        if(lat !== null && lng !== null) {
            try {
                const res = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`);
                const data = await res.json();
                if(data && data.display_name) addressText = data.display_name;
                else addressText = lat + ", " + lng;
            } catch(e) {
                addressText = "Gagal memuat alamat dari API OpenStreetMap";
            }
        } else {
            addressText = "Lokasi tidak diketahui / Akses GPS Ditolak";
        }
        
        canvas.width = video.videoWidth || 640;
        canvas.height = video.videoHeight || 480;
        const ctx = canvas.getContext('2d');
        
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        ctx.fillStyle = "rgba(0, 0, 0, 0.6)";
        ctx.fillRect(0, canvas.height - 90, canvas.width, 90);
        
        ctx.fillStyle = "#ffffff";
        ctx.font = "12px sans-serif";
        const now = new Date();
        const dateStr = now.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) + ' - ' + now.toLocaleTimeString('id-ID');
        
        ctx.fillText("Waktu: " + dateStr, 15, canvas.height - 65);
        if(lat !== null && lng !== null) {
            ctx.fillText("Koordinat: " + lat.toFixed(6) + ", " + lng.toFixed(6), 15, canvas.height - 43);
        }
        
        const shortAddr = addressText.length > 110 ? addressText.substring(0, 110) + "..." : addressText;
        ctx.fillText("Lokasi: " + shortAddr, 15, canvas.height - 21);
        const origBlob = await new Promise(r => canvas.toBlob(r, 'image/jpeg', 0.95));
        const originalSize = origBlob.size;

        statusText.innerText = "🗜️ Mengompres gambar...";
        statusText.className = "text-xs font-bold mt-3 text-blue-500 border-l-4 border-blue-500 pl-3";

        const compressedBlob = await compressCanvasImage(canvas, MAX_DIMENSION, JPEG_QUALITY);
        const compressedSize = compressedBlob.size;
        const savings = Math.round((1 - compressedSize / originalSize) * 100);

        const file = new File([compressedBlob], "sampah_capture_" + Date.now() + ".jpg", { type: "image/jpeg" });
        const dt = new DataTransfer();
        dt.items.add(file);
        photoInput.files = dt.files;
        
        video.classList.add('hidden');
        canvas.classList.remove('hidden');

        snapBtn.classList.add('hidden');
        retakeBtn.classList.remove('hidden');
        
        statusText.innerHTML = `✅ Foto diproses & dikompres otomatis!<br>` +
            `<span class="inline-flex items-center gap-2 mt-1">` +
            `<span class="bg-red-100 text-red-700 px-2 py-0.5 rounded-full text-[10px] font-bold">Asli: ${formatFileSize(originalSize)}</span>` +
            `<span>→</span>` +
            `<span class="bg-green-100 text-green-700 px-2 py-0.5 rounded-full text-[10px] font-bold">Kompres: ${formatFileSize(compressedSize)}</span>` +
            `<span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full text-[10px] font-bold">Hemat ${savings}%</span>` +
            `</span>`;
        statusText.className = "text-xs font-bold mt-3 text-green-600 border-l-4 border-green-600 pl-3";
    };

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition((p) => {
            document.getElementById('location_lat').value = p.coords.latitude;
            document.getElementById('location_lng').value = p.coords.longitude;
            processScreenshot(p.coords.latitude, p.coords.longitude);
        }, (err) => {
            processScreenshot(null, null);
        }, { enableHighAccuracy: true });
    } else {
        processScreenshot(null, null);
    }
});

retakeBtn.addEventListener('click', () => {
    video.classList.remove('hidden');
    canvas.classList.add('hidden');
    video.play();
    photoInput.value = ""; 
    document.getElementById('location_lat').value = "";
    document.getElementById('location_lng').value = "";
    snapBtn.classList.remove('hidden');
    retakeBtn.classList.add('hidden');
    statusText.innerText = "📍 Menunggu pengambilan foto ulang...";
    statusText.className = "text-xs font-bold mt-3 text-slate-500 border-l-4 border-slate-300 pl-3";
});

let pickupMap = null;
let pickupMarker = null;
let mapInitialized = false;

function initPickupMap() {
    if (mapInitialized) {
        pickupMap.invalidateSize();
        return;
    }
    mapInitialized = true;

    const defaultLat = -6.1684;
    const defaultLng = 106.7638;

    pickupMap = L.map('pickup-map', { zoomControl: true }).setView([defaultLat, defaultLng], 15);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap',
        maxZoom: 19,
    }).addTo(pickupMap);

    pickupMarker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(pickupMap);
    pickupMarker.bindPopup('Geser pin ke lokasi penjemputan').openPopup();

    pickupMarker.on('dragend', function(e) {
        const pos = e.target.getLatLng();
        reverseGeocodePickup(pos.lat, pos.lng);
    });

    if (navigator.geolocation) {
        document.getElementById('pickup-map-status').innerText = '📡 Mencari posisi GPS...';
        navigator.geolocation.getCurrentPosition(
            (p) => {
                const lat = p.coords.latitude;
                const lng = p.coords.longitude;
                pickupMap.setView([lat, lng], 16);
                pickupMarker.setLatLng([lat, lng]);
                reverseGeocodePickup(lat, lng);
            },
            () => {
                document.getElementById('pickup-map-status').innerText = '⚠️ GPS ditolak. Geser pin secara manual.';
                reverseGeocodePickup(defaultLat, defaultLng);
            },
            { enableHighAccuracy: true }
        );
    } else {
        document.getElementById('pickup-map-status').innerText = '⚠️ GPS tidak tersedia di perangkat ini.';
        reverseGeocodePickup(defaultLat, defaultLng);
    }
}

async function reverseGeocodePickup(lat, lng) {
    const statusEl = document.getElementById('pickup-map-status');
    statusEl.innerText = '🔍 Mencari alamat...';
    try {
        const res = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`);
        const data = await res.json();
        if (data && data.display_name) {
            document.getElementById('pickup_address_input').value = data.display_name;
            statusEl.innerText = '✅ Alamat ditemukan. Bisa diedit manual di bawah.';
        } else {
            statusEl.innerText = '⚠️ Alamat tidak ditemukan. Isi manual di bawah.';
        }
    } catch(e) {
        statusEl.innerText = '⚠️ Gagal memuat alamat. Isi manual di bawah.';
    }
}

const origToggleMethod = toggleMethod;
window.toggleMethod = function() {
    origToggleMethod();
    const m = document.getElementById('trans-method').value;
    if (m === 'Pick-up') {
        setTimeout(() => initPickupMap(), 200);
    }
};
</script>
@endsection


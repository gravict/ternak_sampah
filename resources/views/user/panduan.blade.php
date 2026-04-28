@extends('layouts.app')
@section('title', 'Panduan & Ketentuan | TernakSampah')

@section('content')
<div class="max-w-4xl mx-auto">
    <h2 class="text-3xl font-extrabold mb-2">Tata Cara & Panduan Transaksi 📖</h2>
    <p class="text-slate-500 mb-8">Panduan lengkap menggunakan platform TernakSampah, dari pendaftaran hingga pencairan saldo.</p>

    <div class="space-y-6">
        <div class="bg-white p-4 sm:p-6 rounded-3xl shadow-sm border border-slate-100 flex gap-4 sm:gap-6 items-start">
            <div class="w-16 h-16 bg-indigo-100 text-indigo-600 rounded-2xl flex items-center justify-center text-2xl font-bold flex-shrink-0">1</div>
            <div>
                <h3 class="text-xl font-bold text-slate-800 mb-2">Daftar Akun</h3>
                <p class="text-slate-500">Buka halaman <span class="font-bold">Daftar</span> dan isi data diri lengkap:</p>
                <ul class="text-slate-500 text-sm mt-2 space-y-1 list-disc ml-5">
                    <li><span class="font-semibold text-slate-700">NIK KTP</span> (16 digit) — bersifat permanen, tidak bisa diubah setelah daftar</li>
                    <li>Nama Lengkap, Email, Username, dan Password</li>
                    <li>Tanggal Lahir dan Jenis Kelamin</li>
                    <li><span class="font-semibold text-slate-700">Data Rekening Utama</span> — pilih Bank/E-Wallet (BCA, Mandiri, GoPay, OVO) dan isi nomor rekening. Data ini digunakan untuk pencairan saldo</li>
                </ul>
                <p class="text-xs text-slate-400 mt-2">Login menggunakan <span class="font-bold">Username</span> yang sudah dibuat, bukan email.</p>
            </div>
        </div>

        <div class="bg-white p-4 sm:p-6 rounded-3xl shadow-sm border border-slate-100 flex gap-4 sm:gap-6 items-start">
            <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center text-2xl font-bold flex-shrink-0">2</div>
            <div>
                <h3 class="text-xl font-bold text-slate-800 mb-2">Pilah & Bersihkan Sampah</h3>
                <p class="text-slate-500">Pisahkan sampah berdasarkan jenisnya sebelum disetor:</p>
                <ul class="text-slate-500 text-sm mt-2 space-y-1 list-disc ml-5">
                    <li><span class="font-semibold text-slate-700">Plastik / PET</span> — Botol plastik, gelas plastik, dll</li>
                    <li><span class="font-semibold text-slate-700">Kardus / Kertas</span> — Karton, kertas HVS, koran</li>
                    <li><span class="font-semibold text-slate-700">Besi / Logam</span> — Kaleng, besi tua, aluminium</li>
                    <li><span class="font-semibold text-slate-700">Minyak Jelantah</span> — Minyak goreng bekas</li>
                    <li><span class="font-semibold text-slate-700">Campuran / Residu</span> — Sampah campur yang masih bernilai</li>
                </ul>
                <div class="bg-green-50 p-3 rounded-xl mt-3 border border-green-100">
                    <p class="text-xs text-green-700 font-bold">💡 Tips: Pastikan botol plastik dan kaleng sudah dibilas bersih & dikeringkan agar harga jual tidak turun saat penilaian admin.</p>
                </div>
                <p class="text-xs text-slate-400 mt-2">Cek harga terbaru di menu <a href="{{ route('daftar_harga') }}" class="text-green-600 font-bold hover:underline">Daftar Harga</a>.</p>
            </div>
        </div>

        <div class="bg-white p-4 sm:p-6 rounded-3xl shadow-sm border border-slate-100 flex gap-4 sm:gap-6 items-start">
            <div class="w-16 h-16 bg-orange-100 text-orange-600 rounded-2xl flex items-center justify-center text-2xl font-bold flex-shrink-0">3</div>
            <div>
                <h3 class="text-xl font-bold text-slate-800 mb-2">Buat Permintaan Setor Sampah</h3>
                <p class="text-slate-500">Masuk ke menu <a href="{{ route('transaksi') }}" class="text-green-600 font-bold hover:underline">Transaksi</a> dan lengkapi form:</p>
                <ul class="text-slate-500 text-sm mt-2 space-y-1 list-disc ml-5">
                    <li><span class="font-semibold text-green-600">Wajib foto fisik sampah</span> — Sistem akan membuka kamera perangkat langsung. Foto akan otomatis ditambahkan watermark (tanggal, waktu, koordinat GPS, dan alamat) serta <span class="font-semibold">dikompres otomatis</span> agar hemat kuota</li>
                    <li>Pilih <span class="font-semibold text-slate-700">Kategori</span> dan masukkan <span class="font-semibold text-slate-700">estimasi berat</span> (Kg)</li>
                    <li>Pilih <span class="font-semibold text-slate-700">Metode Penyerahan</span>:
                        <ul class="list-disc ml-5 mt-1">
                            <li><span class="font-semibold">Drop-off</span> — Antar sendiri ke Bank Sampah (Gratis)</li>
                            <li><span class="font-semibold">Pick-up</span> — Dijemput petugas ke lokasi kamu. Ada <span class="text-orange-600 font-bold">potongan 20%</span> dari estimasi pendapatan. Tersedia peta interaktif untuk menentukan lokasi penjemputan</li>
                        </ul>
                    </li>
                </ul>
                <p class="text-xs text-slate-400 mt-2">Estimasi pendapatan ditampilkan secara real-time di bagian bawah form.</p>
            </div>
        </div>

        <div class="bg-white p-4 sm:p-6 rounded-3xl shadow-sm border border-slate-100 flex gap-4 sm:gap-6 items-start">
            <div class="w-16 h-16 bg-yellow-100 text-yellow-600 rounded-2xl flex items-center justify-center text-2xl font-bold flex-shrink-0">4</div>
            <div>
                <h3 class="text-xl font-bold text-slate-800 mb-2">Penilaian & Konfirmasi Admin</h3>
                <p class="text-slate-500">Setelah transaksi dikirim, admin Bank Sampah akan memproses permintaanmu:</p>
                <ul class="text-slate-500 text-sm mt-2 space-y-1 list-disc ml-5">
                    <li>Status awal: <span class="font-bold text-yellow-600 bg-yellow-50 px-2 py-0.5 rounded">Menunggu</span></li>
                    <li>Admin memeriksa foto dan data, lalu memutuskan <span class="font-bold text-green-600">Terima</span> atau <span class="font-bold text-red-500">Tolak</span></li>
                    <li>Jika <span class="font-bold text-red-500">Ditolak</span> — Admin <span class="font-semibold">wajib memberikan alasan</span> penolakan. Kamu bisa melihat alasan tersebut di halaman <a href="{{ route('riwayat') }}" class="text-green-600 font-bold hover:underline">Riwayat</a></li>
                    <li>Jika <span class="font-bold text-green-600">Diterima</span> — Proses lanjut ke tahap penimbangan</li>
                </ul>
            </div>
        </div>

        <div class="bg-white p-4 sm:p-6 rounded-3xl shadow-sm border border-slate-100 flex gap-4 sm:gap-6 items-start">
            <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center text-2xl font-bold flex-shrink-0">5</div>
            <div>
                <h3 class="text-xl font-bold text-slate-800 mb-2">Penimbangan & Penilaian Parametrik</h3>
                <p class="text-slate-500">Admin melakukan penimbangan ulang secara akurat dan penilaian parametrik:</p>
                <ul class="text-slate-500 text-sm mt-2 space-y-1 list-disc ml-5">
                    <li>Berat asli diinput oleh admin menggunakan timbangan fisik di lapangan</li>
                    <li>Harga akhir ditentukan berdasarkan <span class="font-semibold text-slate-700">kualitas, kebersihan, dan berat aktual</span></li>
                    <li>Status berubah menjadi: <span class="font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded">Ditimbang</span></li>
                </ul>
            </div>
        </div>

        <div class="bg-white p-4 sm:p-6 rounded-3xl shadow-sm border border-slate-100 flex gap-4 sm:gap-6 items-start">
            <div class="w-16 h-16 bg-green-100 text-green-600 rounded-2xl flex items-center justify-center text-2xl font-bold flex-shrink-0">6</div>
            <div>
                <h3 class="text-xl font-bold text-slate-800 mb-2">Saldo & Poin Masuk</h3>
                <p class="text-slate-500">Setelah admin menyelesaikan proses, kamu akan menerima:</p>
                <ul class="text-slate-500 text-sm mt-2 space-y-1 list-disc ml-5">
                    <li><span class="font-semibold text-green-600">Saldo Rupiah</span> — Masuk ke dompet digital di akunmu</li>
                    <li><span class="font-semibold text-orange-500">Poin</span> — Bisa ditukar dengan voucher di halaman <a href="{{ route('voucher') }}" class="text-green-600 font-bold hover:underline">Voucher</a></li>
                    <li>Status berubah menjadi: <span class="font-bold text-green-600 bg-green-50 px-2 py-0.5 rounded">Selesai</span></li>
                </ul>
            </div>
        </div>

        <div class="bg-white p-4 sm:p-6 rounded-3xl shadow-sm border border-slate-100 flex gap-4 sm:gap-6 items-start">
            <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-2xl flex items-center justify-center text-2xl font-bold flex-shrink-0">7</div>
            <div>
                <h3 class="text-xl font-bold text-slate-800 mb-2">Tarik Saldo ke Rekening</h3>
                <p class="text-slate-500">Cairkan saldo dari akunmu ke rekening bank atau e-wallet:</p>
                <ul class="text-slate-500 text-sm mt-2 space-y-1 list-disc ml-5">
                    <li>Buka halaman <a href="{{ route('profile') }}#withdraw" class="text-green-600 font-bold hover:underline">Profil</a> dan scroll ke bagian "Tarik Saldo"</li>
                    <li>Pilih <span class="font-semibold text-slate-700">rekening tujuan</span> dari daftar rekening yang sudah didaftarkan, atau masukkan rekening baru langsung</li>
                    <li>Masukkan nominal penarikan (minimal Rp 10.000)</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="mt-12">
        <h3 class="text-2xl font-extrabold mb-6">Fitur Tambahan 🌟</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100">
                <div class="text-3xl mb-3">🤖</div>
                <h4 class="font-bold text-slate-800 mb-1">AI Daily Trivia</h4>
                <p class="text-sm text-slate-500">Pertanyaan kuis harian yang digenerate AI berdasarkan berita lingkungan terkini. Jawab setiap hari untuk menjaga streak!</p>
            </div>
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100">
                <div class="text-3xl mb-3">🔥</div>
                <h4 class="font-bold text-slate-800 mb-1">Streak Harian</h4>
                <p class="text-sm text-slate-500">Buka aplikasi dan jawab trivia setiap hari untuk menjaga streak. Semakin panjang streak, semakin aktif kontribusimu!</p>
            </div>
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100">
                <div class="text-3xl mb-3">🌱</div>
                <h4 class="font-bold text-slate-800 mb-1">Pohon Virtual</h4>
                <p class="text-sm text-slate-500">Setiap kg sampah yang berhasil divalidasi menumbuhkan pohon virtualmu di dashboard, dari 🌱 hingga 🌳!</p>
            </div>
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100">
                <div class="text-3xl mb-3">🎁</div>
                <h4 class="font-bold text-slate-800 mb-1">Tukar Voucher</h4>
                <p class="text-sm text-slate-500">Gunakan poin yang terkumpul untuk menukar voucher menarik di halaman Voucher.</p>
            </div>
        </div>
    </div>
</div>
@endsection

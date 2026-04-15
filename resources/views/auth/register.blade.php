<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar | TernakSampah</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="text-slate-800">
    <div class="min-h-screen flex items-center justify-center p-6 bg-gradient-to-br from-green-50 to-emerald-100">
        <div class="bg-white p-8 rounded-3xl shadow-xl w-full max-w-lg border border-white max-h-[95vh] overflow-y-auto">
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-green-500 rounded-2xl flex items-center justify-center text-white font-bold text-3xl mx-auto mb-4 shadow-lg shadow-green-200">T</div>
                <h1 class="text-3xl font-extrabold text-green-700">TernakSampah</h1>
                <p class="text-slate-500 text-sm mt-2">Daftar akun baru</p>
            </div>

            <form action="{{ route('register.post') }}" method="POST" class="space-y-3">
                @csrf
                <input type="text" name="nik" value="{{ old('nik') }}" placeholder="NIK KTP (16 Digit)" class="w-full p-3 border border-slate-200 rounded-xl bg-slate-50 outline-none text-sm focus:border-green-500" required maxlength="16">
                @error('nik') <p class="text-red-500 text-xs -mt-2">{{ $message }}</p> @enderror

                <input type="text" name="name" value="{{ old('name') }}" placeholder="Nama Lengkap" class="w-full p-3 border border-slate-200 rounded-xl bg-slate-50 outline-none text-sm focus:border-green-500" required>
                @error('name') <p class="text-red-500 text-xs -mt-2">{{ $message }}</p> @enderror

                <input type="email" name="email" value="{{ old('email') }}" placeholder="Alamat Email" class="w-full p-3 border border-slate-200 rounded-xl bg-slate-50 outline-none text-sm focus:border-green-500" required>
                @error('email') <p class="text-red-500 text-xs -mt-2">{{ $message }}</p> @enderror

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <input type="text" name="username" value="{{ old('username') }}" placeholder="Buat Username" class="w-full p-3 border border-slate-200 rounded-xl bg-slate-50 outline-none text-sm focus:border-green-500" required>
                        @error('username') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <input type="password" name="password" placeholder="Buat Password" class="w-full p-3 border border-slate-200 rounded-xl bg-slate-50 outline-none text-sm focus:border-green-500" required>
                        @error('password') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <input type="date" name="dob" value="{{ old('dob') }}" class="w-full p-3 border border-slate-200 rounded-xl bg-slate-50 outline-none text-slate-500 text-sm focus:border-green-500" required>
                    <select name="gender" class="w-full p-3 border border-slate-200 rounded-xl bg-slate-50 outline-none text-slate-600 text-sm focus:border-green-500" required>
                        <option value="" disabled {{ old('gender') ? '' : 'selected' }}>Pilih Jenis Kelamin</option>
                        <option value="Laki-laki" {{ old('gender') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="Perempuan" {{ old('gender') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>

                <div class="border-t border-slate-100 pt-4 mt-2">
                    <p class="text-xs font-bold text-slate-500 mb-2 uppercase">Data Rekening Utama (Untuk Tarik Saldo)</p>
                    <div class="grid grid-cols-2 gap-3">
                        <select name="bank" class="p-3 border border-slate-200 rounded-xl bg-slate-50 outline-none text-sm focus:border-green-500">
                            <option value="BCA">Bank BCA</option>
                            <option value="Mandiri">Bank Mandiri</option>
                            <option value="GoPay">GoPay</option>
                            <option value="OVO">OVO</option>
                        </select>
                        <input type="text" name="account_number" value="{{ old('account_number') }}" placeholder="No. Rekening / HP" class="p-3 border border-slate-200 rounded-xl bg-slate-50 outline-none text-sm focus:border-green-500" required>
                        <input type="text" name="account_name" value="{{ old('account_name') }}" placeholder="Nama Pemilik Rekening" class="col-span-2 p-3 border border-slate-200 rounded-xl bg-slate-50 outline-none text-sm focus:border-green-500" required>
                    </div>
                </div>

                <button type="submit" class="w-full bg-slate-800 text-white font-bold py-3 rounded-xl shadow-lg hover:bg-slate-900 transition active:scale-95 mt-4">Selesaikan Pendaftaran</button>
            </form>

            <p class="text-center mt-4 text-sm text-slate-500">
                Sudah punya akun? <a href="{{ route('login') }}" class="text-green-600 font-bold hover:underline">Sign In</a>
            </p>
        </div>
    </div>
</body>
</html>

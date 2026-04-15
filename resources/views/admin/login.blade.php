<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | TernakSampah</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="text-slate-800">
    <div class="min-h-screen flex items-center justify-center p-6 bg-slate-800">
        <div class="bg-white p-8 rounded-3xl shadow-2xl w-full max-w-md">
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-slate-800 rounded-2xl flex items-center justify-center text-white font-bold text-3xl mx-auto mb-4 shadow-lg">🛠️</div>
                <h1 class="text-2xl font-extrabold text-slate-800">Portal Mitra Bank Sampah</h1>
                <p class="text-slate-500 mt-2 text-sm">Masuk untuk mengelola transaksi masuk.</p>
            </div>

            <form action="{{ route('admin.login.post') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-bold text-slate-600 mb-2">Pilih Cabang Bank Sampah</label>
                    <select name="branch" class="w-full p-3 border border-slate-200 rounded-xl bg-slate-50 outline-none focus:border-green-500">
                        <option value="Bank Sampah Untar">Bank Sampah Untar (Kampus 1)</option>
                        <option value="Bank Sampah Tomang">Bank Sampah Tomang Raya</option>
                        <option value="Bank Sampah Grogol">Bank Sampah Grogol</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-600 mb-2">Password Admin</label>
                    <input type="password" name="password" placeholder="••••••••" class="w-full p-3 border border-slate-200 rounded-xl bg-slate-50 outline-none focus:border-green-500" required>
                    @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <button type="submit" class="w-full bg-green-600 text-white font-extrabold py-3 rounded-xl shadow-lg hover:bg-green-700 transition active:scale-95 mt-4">Masuk ke Dashboard</button>
            </form>

            <div class="text-center mt-4">
                <a href="{{ route('login') }}" class="text-xs text-slate-400 hover:text-slate-600">← Kembali ke Login User</a>
            </div>
        </div>
    </div>
</body>
</html>

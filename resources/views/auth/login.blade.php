<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | TernakSampah</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="text-slate-800">
    @if(session('success'))
        <div class="alert-toast fixed top-4 right-4 z-[200] bg-green-600 text-white px-6 py-3 rounded-xl shadow-lg font-bold text-sm">✅ {{ session('success') }}</div>
    @endif

    <div class="min-h-screen flex items-center justify-center p-6 bg-gradient-to-br from-green-50 to-emerald-100">
        <div class="bg-white p-8 rounded-3xl shadow-xl w-full max-w-lg border border-white">
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-green-500 rounded-2xl flex items-center justify-center text-white font-bold text-3xl mx-auto mb-4 shadow-lg shadow-green-200">T</div>
                <h1 class="text-3xl font-extrabold text-green-700">TernakSampah</h1>
                <p class="text-slate-500 text-sm mt-2">Masuk ke akun kamu</p>
            </div>

            <form action="{{ route('login.post') }}" method="POST">
                @csrf
                <input type="text" name="username" value="{{ old('username') }}" placeholder="Username" class="w-full p-3 mb-4 border border-slate-200 rounded-xl bg-slate-50 outline-none focus:border-green-500" required>
                @error('username')
                    <p class="text-red-500 text-xs mb-4 -mt-3">{{ $message }}</p>
                @enderror
                <input type="password" name="password" placeholder="Password" class="w-full p-3 mb-6 border border-slate-200 rounded-xl bg-slate-50 outline-none focus:border-green-500" required>
                <button type="submit" class="w-full bg-green-600 text-white font-bold py-3 rounded-xl shadow-lg hover:bg-green-700 transition active:scale-95">Sign In</button>
            </form>

            <p class="text-center mt-6 text-sm text-slate-500">
                Belum punya akun? <a href="{{ route('register') }}" class="text-green-600 font-bold hover:underline">Daftar Sekarang</a>
            </p>
            <div class="text-center mt-3">
                <a href="{{ route('admin.login') }}" class="text-xs text-slate-400 hover:text-slate-600">Login sebagai Admin →</a>
            </div>
        </div>
    </div>
</body>
</html>

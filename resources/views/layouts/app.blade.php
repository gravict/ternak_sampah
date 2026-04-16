<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="TernakSampah - Platform Bank Sampah Digital Indonesia">
    <title>@yield('title', 'TernakSampah | Dashboard Cerdas')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="text-slate-800">

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert-toast fixed top-4 right-4 z-[200] bg-green-600 text-white px-6 py-3 rounded-xl shadow-lg font-bold text-sm">
            ✅ {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert-toast fixed top-4 right-4 z-[200] bg-red-600 text-white px-6 py-3 rounded-xl shadow-lg font-bold text-sm">
            ⚠️ {{ session('error') }}
        </div>
    @endif

    {{-- Navbar --}}
    <nav class="bg-white/80 backdrop-blur-md border-b sticky top-0 z-50">
        <div class="max-w-[90rem] mx-auto px-6 h-16 flex items-center justify-between overflow-x-auto">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 cursor-pointer flex-shrink-0 mr-8">
                <div class="w-8 h-8 bg-green-500 rounded-lg flex items-center justify-center text-white font-bold text-xl shadow-md">T</div>
                <span class="text-xl font-extrabold text-green-700 tracking-tight hidden lg:block">TernakSampah</span>
            </a>
            <div class="flex gap-6 md:gap-8 text-sm font-semibold text-slate-500 h-full flex-nowrap whitespace-nowrap">
                <a href="{{ route('dashboard') }}" class="nav-link py-5 border-b-2 transition hover:text-green-600 {{ request()->routeIs('dashboard') ? 'nav-link-active' : 'border-transparent' }}">Dashboard</a>
                <a href="{{ route('transaksi') }}" class="nav-link py-5 border-b-2 transition hover:text-green-600 {{ request()->routeIs('transaksi') ? 'nav-link-active' : 'border-transparent' }}">Transaksi</a>
                <a href="{{ route('panduan') }}" class="nav-link py-5 border-b-2 transition hover:text-green-600 {{ request()->routeIs('panduan') ? 'nav-link-active' : 'border-transparent' }}">Panduan</a>
                <a href="{{ route('daftar_harga') }}" class="nav-link py-5 border-b-2 transition hover:text-green-600 {{ request()->routeIs('daftar_harga') ? 'nav-link-active' : 'border-transparent' }}">Daftar Harga</a>
                <a href="{{ route('riwayat') }}" class="nav-link py-5 border-b-2 transition hover:text-green-600 {{ request()->routeIs('riwayat') ? 'nav-link-active' : 'border-transparent' }}">Riwayat</a>
                <a href="{{ route('voucher') }}" class="nav-link py-5 border-b-2 transition hover:text-green-600 {{ request()->routeIs('voucher') ? 'nav-link-active' : 'border-transparent' }}">Voucher</a>
                <a href="{{ route('berita') }}" class="nav-link py-5 border-b-2 transition hover:text-green-600 {{ request()->routeIs('berita') ? 'nav-link-active' : 'border-transparent' }}">Berita & Edukasi</a>
            </div>
            <a href="{{ route('profile') }}" class="flex items-center gap-3 cursor-pointer p-1 rounded-full hover:bg-slate-50 transition flex-shrink-0 ml-4">
                <div class="w-10 h-10 bg-gradient-to-tr from-green-400 to-emerald-600 rounded-full border-2 border-white shadow-md flex items-center justify-center text-white font-bold overflow-hidden relative">
                    @if(Auth::user()->profile_photo)
                        <img src="{{ asset('storage/' . Auth::user()->profile_photo) }}" class="w-full h-full object-cover">
                    @else
                        <span class="uppercase">{{ substr(Auth::user()->username, 0, 1) }}</span>
                    @endif
                </div>
            </a>
        </div>
    </nav>

    {{-- Main Content --}}
    <main class="max-w-7xl mx-auto px-6 py-8 page-fade-in">
        @yield('content')
    </main>

    @yield('modals')
    @yield('scripts')
</body>
</html>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="TernakSampah - Platform Bank Sampah Digital Indonesia">
    <title>@yield('title', 'TernakSampah | Dashboard Cerdas')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @yield('styles')
</head>
<body class="text-slate-800">

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert-toast fixed top-4 right-4 bg-green-600 text-white px-6 py-3 rounded-xl shadow-lg font-bold text-sm" style="z-index: 200;">
            ✅ {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert-toast fixed top-4 right-4 bg-red-600 text-white px-6 py-3 rounded-xl shadow-lg font-bold text-sm" style="z-index: 200;">
            ⚠️ {{ session('error') }}
        </div>
    @endif

    {{-- Navbar --}}
    <nav class="bg-white/80 backdrop-blur-md border-b sticky top-0 z-50">
        <div class="max-w-[90rem] mx-auto px-6 h-16 flex items-center justify-between">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 cursor-pointer flex-shrink-0">
                <div class="w-8 h-8 bg-green-500 rounded-lg flex items-center justify-center text-white font-bold text-xl shadow-md">T</div>
                <span class="text-xl font-extrabold text-green-700 tracking-tight hidden lg:block">TernakSampah</span>
            </a>
            
            {{-- Main Links (Always Visible, Scrollable on Mobile) --}}
            <div class="flex gap-6 md:gap-8 text-sm font-semibold text-slate-500 h-full overflow-x-auto whitespace-nowrap hide-scrollbar">
                <a href="{{ route('dashboard') }}" class="nav-link py-5 border-b-2 transition hover:text-green-600 {{ request()->routeIs('dashboard') ? 'nav-link-active border-green-500 text-green-600' : 'border-transparent' }}">Dashboard</a>
                <a href="{{ route('transaksi') }}" class="nav-link py-5 border-b-2 transition hover:text-green-600 {{ request()->routeIs('transaksi') ? 'nav-link-active border-green-500 text-green-600' : 'border-transparent' }}">Transaksi</a>
                <a href="{{ route('panduan') }}" class="nav-link py-5 border-b-2 transition hover:text-green-600 {{ request()->routeIs('panduan') ? 'nav-link-active border-green-500 text-green-600' : 'border-transparent' }}">Panduan</a>
                <a href="{{ route('daftar_harga') }}" class="nav-link py-5 border-b-2 transition hover:text-green-600 {{ request()->routeIs('daftar_harga') ? 'nav-link-active border-green-500 text-green-600' : 'border-transparent' }}">Daftar Harga</a>
                <a href="{{ route('berita') }}" class="nav-link py-5 border-b-2 transition hover:text-green-600 {{ request()->routeIs('berita') ? 'nav-link-active border-green-500 text-green-600' : 'border-transparent' }}">Berita & Edukasi</a>
            </div>

            <div class="flex items-center gap-4">
                {{-- Profile Trigger --}}
                <div class="relative">
                    <button onclick="toggleSidebar()" class="flex items-center gap-3 cursor-pointer p-1 rounded-full hover:bg-slate-50 transition focus:outline-none">
                        <div class="w-10 h-10 bg-gradient-to-tr from-green-400 to-emerald-600 rounded-full border-2 border-white shadow-md flex items-center justify-center text-white font-bold overflow-hidden relative">
                            @if(Auth::user()->profile_photo)
                                <img src="{{ asset('storage/' . Auth::user()->profile_photo) }}" class="w-full h-full object-cover">
                            @else
                                <span class="uppercase">{{ substr(Auth::user()->username, 0, 1) }}</span>
                            @endif
                        </div>
                    </button>
                </div>

                {{-- Hamburger Mobile Trigger --}}
                <button onclick="toggleSidebar()" class="md:hidden text-slate-500 hover:text-green-600 focus:outline-none">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
                </button>
            </div>
        </div>
    </nav>

    {{-- Off-Canvas Sidebar --}}
    <div id="mobileSidebar" class="fixed inset-0 hidden" style="z-index: 100;">
        <!-- Backdrop -->
        <div id="sidebarBackdrop" class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm opacity-0 transition-opacity duration-300" onclick="toggleSidebar()"></div>
        
        <!-- Sidebar Content -->
        <div id="sidebarContent" class="absolute top-0 right-0 h-full w-72 bg-white shadow-2xl transform translate-x-full transition-transform duration-300 ease-in-out flex flex-col">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="font-extrabold text-slate-800">{{ Auth::user()->name }}</h2>
                    <p class="text-xs text-slate-500">{{ '@' . Auth::user()->username }}</p>
                </div>
                <button onclick="toggleSidebar()" class="text-slate-400 hover:text-red-500 transition bg-slate-100 rounded-full p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto p-4 space-y-2">
                <a href="{{ route('profile') }}" class="block p-3 rounded-xl text-sm font-semibold text-slate-600 hover:bg-green-50 hover:text-green-700 transition">⚙️ Setting Profile</a>
                <a href="{{ route('voucher') }}" class="block p-3 rounded-xl text-sm font-semibold text-slate-600 hover:bg-green-50 hover:text-green-700 transition">🎟️ Penukaran Voucher</a>
                <a href="{{ route('riwayat') }}" class="block p-3 rounded-xl text-sm font-semibold text-slate-600 hover:bg-green-50 hover:text-green-700 transition">📜 Riwayat Transaksi</a>
                <a href="{{ route('riwayat_dompet') }}" class="block p-3 rounded-xl text-sm font-semibold text-slate-600 hover:bg-green-50 hover:text-green-700 transition">💳 Riwayat Dompet</a>
            </div>
            <div class="p-4 border-t border-slate-100 bg-slate-50">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full bg-red-100 text-red-600 font-bold p-3 rounded-xl hover:bg-red-200 transition">🚪 Keluar</button>
                </form>
            </div>
        </div>
    </div>



    {{-- Main Content --}}
    <main class="max-w-7xl mx-auto px-6 py-8 page-fade-in">
        @yield('content')
    </main>

    @yield('modals')
    @yield('scripts')
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('mobileSidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            const content = document.getElementById('sidebarContent');
            
            if (sidebar.classList.contains('hidden')) {
                // Open sidebar
                sidebar.classList.remove('hidden');
                // Use a tiny timeout to allow display block to apply before transition
                setTimeout(() => {
                    backdrop.classList.remove('opacity-0');
                    backdrop.classList.add('opacity-100');
                    content.classList.remove('translate-x-full');
                }, 10);
            } else {
                // Close sidebar
                backdrop.classList.remove('opacity-100');
                backdrop.classList.add('opacity-0');
                content.classList.add('translate-x-full');
                
                // Wait for transition to finish before hiding
                setTimeout(() => {
                    sidebar.classList.add('hidden');
                }, 300);
            }
        }
    </script>
</body>
</html>

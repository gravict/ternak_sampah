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
            
            {{-- Main Links (Hidden on mobile, visible on tablet/desktop) --}}
            <div class="hidden md:flex gap-6 lg:gap-8 text-sm font-semibold text-slate-500 h-full">
                <a href="{{ route('dashboard') }}" class="nav-link py-5 border-b-2 transition hover:text-green-600 {{ request()->routeIs('dashboard') ? 'nav-link-active border-green-500 text-green-600' : 'border-transparent' }}">Dashboard</a>
                <a href="{{ route('transaksi') }}" class="nav-link py-5 border-b-2 transition hover:text-green-600 {{ request()->routeIs('transaksi') ? 'nav-link-active border-green-500 text-green-600' : 'border-transparent' }}">Transaksi</a>
                <a href="{{ route('panduan') }}" class="nav-link py-5 border-b-2 transition hover:text-green-600 {{ request()->routeIs('panduan') ? 'nav-link-active border-green-500 text-green-600' : 'border-transparent' }}">Panduan</a>
                <a href="{{ route('daftar_harga') }}" class="nav-link py-5 border-b-2 transition hover:text-green-600 {{ request()->routeIs('daftar_harga') ? 'nav-link-active border-green-500 text-green-600' : 'border-transparent' }}">Daftar Harga</a>
                <a href="{{ route('berita') }}" class="nav-link py-5 border-b-2 transition hover:text-green-600 {{ request()->routeIs('berita') ? 'nav-link-active border-green-500 text-green-600' : 'border-transparent' }}">Berita & Edukasi</a>
            </div>

            <div class="flex items-center gap-3">
                {{-- Profile Trigger --}}
                <div class="relative">
                    <button onclick="toggleProfileDropdown()" class="flex items-center gap-3 cursor-pointer p-1 rounded-full hover:bg-slate-50 transition focus:outline-none relative z-10" id="profileBtn">
                        <div class="w-10 h-10 bg-gradient-to-tr from-green-400 to-emerald-600 rounded-full border-2 border-white shadow-md flex items-center justify-center text-white font-bold overflow-hidden relative">
                            @if(Auth::user()->profile_photo)
                                <img src="{{ asset('storage/' . Auth::user()->profile_photo) }}" class="w-full h-full object-cover">
                            @else
                                <span class="uppercase">{{ substr(Auth::user()->username, 0, 1) }}</span>
                            @endif
                        </div>
                    </button>
                    
                    {{-- Profile Dropdown Modal --}}
                    <div id="profileDropdown" class="absolute right-0 mt-2 w-64 bg-white rounded-2xl shadow-xl border border-slate-100 hidden transform origin-top-right transition-all duration-200 opacity-0 scale-95" style="z-index: 110;">
                        <div class="p-4 border-b border-slate-100 flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-tr from-green-400 to-emerald-600 rounded-full flex items-center justify-center text-white font-bold overflow-hidden flex-shrink-0">
                                @if(Auth::user()->profile_photo)
                                    <img src="{{ asset('storage/' . Auth::user()->profile_photo) }}" class="w-full h-full object-cover">
                                @else
                                    <span class="uppercase">{{ substr(Auth::user()->username, 0, 1) }}</span>
                                @endif
                            </div>
                            <div class="overflow-hidden">
                                <h2 class="font-extrabold text-slate-800 text-sm truncate">{{ Auth::user()->name }}</h2>
                                <p class="text-xs text-slate-500 truncate">{{ '@' . Auth::user()->username }}</p>
                            </div>
                        </div>
                        <div class="p-2 space-y-1">
                            <a href="{{ route('profile') }}" class="block px-4 py-2.5 rounded-xl text-sm font-semibold text-slate-600 hover:bg-green-50 hover:text-green-700 transition">⚙️ Setting Profile</a>
                            <a href="{{ route('voucher') }}" class="block px-4 py-2.5 rounded-xl text-sm font-semibold text-slate-600 hover:bg-green-50 hover:text-green-700 transition">🎟️ Penukaran Voucher</a>
                            <a href="{{ route('riwayat') }}" class="block px-4 py-2.5 rounded-xl text-sm font-semibold text-slate-600 hover:bg-green-50 hover:text-green-700 transition">📜 Riwayat Transaksi</a>
                            <a href="{{ route('riwayat_dompet') }}" class="block px-4 py-2.5 rounded-xl text-sm font-semibold text-slate-600 hover:bg-green-50 hover:text-green-700 transition">💳 Riwayat Dompet</a>
                        </div>
                        <div class="p-2 border-t border-slate-100 bg-slate-50 rounded-b-2xl">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full bg-red-100 text-red-600 font-bold px-4 py-2.5 rounded-xl hover:bg-red-200 transition text-sm text-left">🚪 Keluar</button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Hamburger Mobile Trigger --}}
                <button onclick="toggleSidebar()" class="md:hidden text-slate-500 hover:text-green-600 focus:outline-none p-2 rounded-lg bg-slate-50 border border-slate-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
                </button>
            </div>
        </div>
    </nav>

    {{-- Off-Canvas Sidebar (Mobile Main Navigation) --}}
    <div id="mobileSidebar" class="fixed inset-0 hidden md:hidden" style="z-index: 100;">
        <!-- Backdrop -->
        <div id="sidebarBackdrop" class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm opacity-0 transition-opacity duration-300" onclick="toggleSidebar()"></div>
        
        <!-- Sidebar Content -->
        <div id="sidebarContent" class="absolute top-0 right-0 h-full w-72 bg-white shadow-2xl transform translate-x-full transition-transform duration-300 ease-in-out flex flex-col">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-green-500 rounded-lg flex items-center justify-center text-white font-bold text-xl shadow-sm">T</div>
                    <h2 class="font-extrabold text-green-700 text-lg">TernakSampah</h2>
                </div>
                <button onclick="toggleSidebar()" class="text-slate-400 hover:text-red-500 transition bg-slate-100 rounded-full p-1.5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto p-5 space-y-4 mt-2">
                <a href="{{ route('dashboard') }}" class="block p-4 rounded-2xl text-[15px] font-bold transition {{ request()->routeIs('dashboard') ? 'bg-green-50 text-green-700 border border-green-200' : 'text-slate-600 hover:bg-slate-50 hover:text-green-600' }}">🏠 Dashboard</a>
                <a href="{{ route('transaksi') }}" class="block p-4 rounded-2xl text-[15px] font-bold transition {{ request()->routeIs('transaksi') ? 'bg-green-50 text-green-700 border border-green-200' : 'text-slate-600 hover:bg-slate-50 hover:text-green-600' }}">♻️ Transaksi</a>
                <a href="{{ route('panduan') }}" class="block p-4 rounded-2xl text-[15px] font-bold transition {{ request()->routeIs('panduan') ? 'bg-green-50 text-green-700 border border-green-200' : 'text-slate-600 hover:bg-slate-50 hover:text-green-600' }}">📖 Panduan</a>
                <a href="{{ route('daftar_harga') }}" class="block p-4 rounded-2xl text-[15px] font-bold transition {{ request()->routeIs('daftar_harga') ? 'bg-green-50 text-green-700 border border-green-200' : 'text-slate-600 hover:bg-slate-50 hover:text-green-600' }}">💰 Daftar Harga</a>
                <a href="{{ route('berita') }}" class="block p-4 rounded-2xl text-[15px] font-bold transition {{ request()->routeIs('berita') ? 'bg-green-50 text-green-700 border border-green-200' : 'text-slate-600 hover:bg-slate-50 hover:text-green-600' }}">📰 Berita & Edukasi</a>
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
        // Profile Dropdown Toggle
        function toggleProfileDropdown() {
            const dropdown = document.getElementById('profileDropdown');
            if (dropdown.classList.contains('hidden')) {
                dropdown.classList.remove('hidden');
                setTimeout(() => {
                    dropdown.classList.remove('opacity-0', 'scale-95');
                    dropdown.classList.add('opacity-100', 'scale-100');
                }, 10);
            } else {
                dropdown.classList.remove('opacity-100', 'scale-100');
                dropdown.classList.add('opacity-0', 'scale-95');
                setTimeout(() => {
                    dropdown.classList.add('hidden');
                }, 200);
            }
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('profileDropdown');
            const trigger = document.getElementById('profileBtn');
            if (dropdown && trigger && !dropdown.contains(event.target) && !trigger.contains(event.target) && !dropdown.classList.contains('hidden')) {
                toggleProfileDropdown();
            }
        });

        // Mobile Sidebar Toggle
        function toggleSidebar() {
            const sidebar = document.getElementById('mobileSidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            const content = document.getElementById('sidebarContent');
            
            if (sidebar.classList.contains('hidden')) {
                sidebar.classList.remove('hidden');
                setTimeout(() => {
                    backdrop.classList.remove('opacity-0');
                    backdrop.classList.add('opacity-100');
                    content.classList.remove('translate-x-full');
                }, 10);
            } else {
                backdrop.classList.remove('opacity-100');
                backdrop.classList.add('opacity-0');
                content.classList.add('translate-x-full');
                
                setTimeout(() => {
                    sidebar.classList.add('hidden');
                }, 300);
            }
        }
    </script>
</body>
</html>

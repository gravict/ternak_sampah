<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel | TernakSampah')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @yield('styles')
</head>
<body class="text-slate-800" style="background-color: #f1f5f9;">

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

    <nav class="bg-white border-b border-slate-200 sticky top-0 z-50 shadow-sm">
        <div class="max-w-[90rem] mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 cursor-pointer flex-shrink-0 mr-4 sm:mr-8">
                <div class="w-8 h-8 bg-green-500 rounded-lg flex items-center justify-center text-white font-bold text-xl shadow-md">T</div>
                <span class="text-xl font-extrabold text-green-700 tracking-tight hidden sm:block">
                    TernakSampah <span class="text-xs text-slate-500 font-bold ml-1 bg-slate-100 px-2 py-1 rounded-md align-middle">ADMIN</span>
                </span>
            </a>

            <div class="hidden md:flex gap-4 lg:gap-8 text-sm h-full flex-nowrap whitespace-nowrap">
                @php
                    $branch = Auth::user()->admin_branch;
                    $pendingCount = \App\Models\Transaction::where('status', 'pending')->where('dropoff_location', $branch)->count();
                    $weighingCount = \App\Models\Transaction::where('status', 'weighing')->where('dropoff_location', $branch)->count();
                @endphp
                <a href="{{ route('admin.dashboard') }}" class="nav-link py-5 border-b-[3px] font-semibold text-slate-500 transition hover:text-slate-900 {{ request()->routeIs('admin.dashboard') ? 'nav-link-active' : 'border-transparent' }}">Dashboard</a>
                <a href="{{ route('admin.proses') }}" class="nav-link py-5 border-b-[3px] font-semibold text-slate-500 transition hover:text-slate-900 {{ request()->routeIs('admin.proses') ? 'nav-link-active' : 'border-transparent' }}">
                    Permintaan Baru <span class="bg-orange-500 text-white text-[10px] px-2 py-0.5 rounded-full ml-1">{{ $pendingCount }}</span>
                </a>
                <a href="{{ route('admin.diterima') }}" class="nav-link py-5 border-b-[3px] font-semibold text-slate-500 transition hover:text-slate-900 {{ request()->routeIs('admin.diterima') ? 'nav-link-active' : 'border-transparent' }}">
                    Sedang Ditimbang <span class="bg-blue-500 text-white text-[10px] px-2 py-0.5 rounded-full ml-1">{{ $weighingCount }}</span>
                </a>
                <a href="{{ route('admin.selesai') }}" class="nav-link py-5 border-b-[3px] font-semibold text-slate-500 transition hover:text-slate-900 {{ request()->routeIs('admin.selesai') ? 'nav-link-active' : 'border-transparent' }}">Riwayat</a>
            </div>

            <div class="flex items-center gap-3 sm:gap-4 ml-auto sm:ml-4 flex-shrink-0">
                <div class="flex items-center gap-3">
                    <div class="text-right hidden sm:block">
                        <p class="text-sm font-bold text-slate-800">Admin Petugas</p>
                        <p class="text-xs text-green-600 font-semibold bg-green-50 px-2 rounded-full border border-green-100">{{ Auth::user()->admin_branch ?? 'Bank Sampah' }}</p>
                    </div>
                    <div class="w-10 h-10 bg-slate-800 rounded-full border-2 border-white shadow-md flex items-center justify-center text-white font-bold text-lg">🛠️</div>
                </div>
                <form action="{{ route('admin.logout') }}" method="POST" class="hidden sm:block">
                    @csrf
                    <button type="submit" class="text-xs font-bold text-red-500 border border-red-200 bg-red-50 px-3 py-2 rounded-lg hover:bg-red-500 hover:text-white transition">Keluar</button>
                </form>

                <button onclick="toggleAdminSidebar()" class="md:hidden text-slate-500 hover:text-green-600 focus:outline-none">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
                </button>
            </div>
        </div>
    </nav>

    <div id="adminSidebar" class="fixed inset-0 hidden" style="z-index: 100;">
        <div id="adminSidebarBackdrop" class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm opacity-0 transition-opacity duration-300" onclick="toggleAdminSidebar()"></div>
        <div id="adminSidebarContent" class="absolute top-0 right-0 h-full w-72 bg-white shadow-2xl transform translate-x-full transition-transform duration-300 ease-in-out flex flex-col">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="font-extrabold text-slate-800">Admin Panel</h2>
                    <p class="text-xs text-green-600 font-semibold">{{ Auth::user()->admin_branch ?? 'Bank Sampah' }}</p>
                </div>
                <button onclick="toggleAdminSidebar()" class="text-slate-400 hover:text-red-500 transition bg-slate-100 rounded-full p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto p-4 space-y-2">
                @php
                    if (!isset($pendingCount)) {
                        $branch = Auth::user()->admin_branch;
                        $pendingCount = \App\Models\Transaction::where('status', 'pending')->where('dropoff_location', $branch)->count();
                        $weighingCount = \App\Models\Transaction::where('status', 'weighing')->where('dropoff_location', $branch)->count();
                    }
                @endphp
                <a href="{{ route('admin.dashboard') }}" class="block p-3 rounded-xl text-sm font-semibold transition {{ request()->routeIs('admin.dashboard') ? 'bg-green-50 text-green-700' : 'text-slate-600 hover:bg-green-50 hover:text-green-700' }}">
                    📊 Dashboard
                </a>
                <a href="{{ route('admin.proses') }}" class="flex items-center justify-between p-3 rounded-xl text-sm font-semibold transition {{ request()->routeIs('admin.proses') ? 'bg-green-50 text-green-700' : 'text-slate-600 hover:bg-green-50 hover:text-green-700' }}">
                    <span>📥 Permintaan Baru</span>
                    @if($pendingCount > 0)
                        <span class="bg-orange-500 text-white text-[10px] px-2 py-0.5 rounded-full font-bold">{{ $pendingCount }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.diterima') }}" class="flex items-center justify-between p-3 rounded-xl text-sm font-semibold transition {{ request()->routeIs('admin.diterima') ? 'bg-green-50 text-green-700' : 'text-slate-600 hover:bg-green-50 hover:text-green-700' }}">
                    <span>⚖️ Sedang Ditimbang</span>
                    @if($weighingCount > 0)
                        <span class="bg-blue-500 text-white text-[10px] px-2 py-0.5 rounded-full font-bold">{{ $weighingCount }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.selesai') }}" class="block p-3 rounded-xl text-sm font-semibold transition {{ request()->routeIs('admin.selesai') ? 'bg-green-50 text-green-700' : 'text-slate-600 hover:bg-green-50 hover:text-green-700' }}">
                    📜 Riwayat
                </a>
            </div>
            <div class="p-4 border-t border-slate-100 bg-slate-50">
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full bg-red-100 text-red-600 font-bold p-3 rounded-xl hover:bg-red-200 transition">🚪 Keluar</button>
                </form>
            </div>
        </div>
    </div>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 py-6 sm:py-8 page-fade-in">
        @yield('content')
    </main>

    @yield('modals')
    @yield('scripts')
    <script>
        function toggleAdminSidebar() {
            const sidebar = document.getElementById('adminSidebar');
            const backdrop = document.getElementById('adminSidebarBackdrop');
            const content = document.getElementById('adminSidebarContent');
            
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

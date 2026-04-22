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

    <nav class="bg-white border-b border-slate-200 sticky top-0 z-50 shadow-sm">
        <div class="max-w-[90rem] mx-auto px-6 h-16 flex items-center justify-between overflow-x-auto">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 cursor-pointer flex-shrink-0 mr-8">
                <div class="w-8 h-8 bg-green-500 rounded-lg flex items-center justify-center text-white font-bold text-xl shadow-md">T</div>
                <span class="text-xl font-extrabold text-green-700 tracking-tight hidden lg:block">
                    TernakSampah <span class="text-xs text-slate-500 font-bold ml-1 bg-slate-100 px-2 py-1 rounded-md align-middle">ADMIN</span>
                </span>
            </a>

            <div class="flex gap-4 md:gap-8 text-sm h-full flex-nowrap whitespace-nowrap">
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

            <div class="flex items-center gap-4 ml-4 flex-shrink-0">
                <div class="flex items-center gap-3">
                    <div class="text-right hidden sm:block">
                        <p class="text-sm font-bold text-slate-800">Admin Petugas</p>
                        <p class="text-xs text-green-600 font-semibold bg-green-50 px-2 rounded-full border border-green-100">{{ Auth::user()->admin_branch ?? 'Bank Sampah' }}</p>
                    </div>
                    <div class="w-10 h-10 bg-slate-800 rounded-full border-2 border-white shadow-md flex items-center justify-center text-white font-bold text-lg">🛠️</div>
                </div>
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-xs font-bold text-red-500 border border-red-200 bg-red-50 px-3 py-2 rounded-lg hover:bg-red-500 hover:text-white transition">Keluar</button>
                </form>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-6 py-8 page-fade-in">
        @yield('content')
    </main>

    @yield('modals')
    @yield('scripts')
</body>
</html>

@extends('layouts.app')
@section('title', 'Profil | TernakSampah')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100">
        {{-- Profile Photo --}}
        <div class="text-center mb-8">
            <div class="relative inline-block mx-auto">
                <div class="w-28 h-28 bg-gradient-to-tr from-green-400 to-emerald-600 rounded-full flex items-center justify-center text-4xl font-bold text-white shadow-lg overflow-hidden border-4 border-white">
                    @if($user->profile_photo)
                        <img src="{{ asset('storage/' . $user->profile_photo) }}" class="w-full h-full object-cover">
                    @else
                        <span class="uppercase">{{ substr($user->username, 0, 1) }}</span>
                    @endif
                </div>
            </div>
            <h2 class="text-xl font-extrabold mt-4">{{ $user->name }}</h2>
            <p class="text-sm text-slate-500">{{ '@' . $user->username }}</p>
        </div>

        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="space-y-4 mb-8">
                {{-- NIK (Read-only) --}}
                <div>
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider ml-1">NIK KTP (Permanen)</label>
                    <input type="text" value="{{ $user->nik }}" disabled class="w-full text-center font-bold text-slate-400 bg-transparent py-2 border border-transparent outline-none cursor-not-allowed">
                </div>

                {{-- Profile Photo Upload --}}
                <div>
                    <label class="text-xs font-bold text-slate-500 mb-1 block">Ubah Foto Profil</label>
                    <input type="file" name="profile_photo" accept="image/*" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:font-bold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                </div>

                {{-- Editable Fields --}}
                <div class="bg-slate-50 p-5 rounded-2xl border border-slate-100 space-y-4">
                    <div>
                        <label class="text-xs font-bold text-slate-500 mb-1 block">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full font-bold text-lg outline-none p-2 rounded-lg border border-slate-200 focus:border-green-500" required>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-bold text-slate-500 mb-1 block">Username</label>
                            <input type="text" name="username" value="{{ old('username', $user->username) }}" class="w-full font-bold outline-none p-2 rounded-lg border border-slate-200 focus:border-green-500" required>
                            @error('username') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-500 mb-1 block">Email</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full font-semibold outline-none p-2 rounded-lg border border-slate-200 focus:border-green-500" required>
                            @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-500 mb-1 block">Tanggal Lahir</label>
                            <input type="date" name="dob" value="{{ old('dob', $user->dob?->format('Y-m-d')) }}" class="w-full font-bold outline-none p-2 rounded-lg border border-slate-200 focus:border-green-500">
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-500 mb-1 block">Jenis Kelamin</label>
                            <select name="gender" class="w-full font-bold outline-none p-2 rounded-lg border border-slate-200 focus:border-green-500">
                                <option value="Laki-laki" {{ $user->gender === 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="Perempuan" {{ $user->gender === 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Bank Account --}}
                <div class="bg-slate-50 p-5 rounded-2xl border border-slate-100 space-y-4" id="withdraw">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider border-b border-slate-200 pb-2">Rekening Penarikan Utama</p>
                    @php $bank = $user->bankAccounts->first(); @endphp
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                        <div>
                            <label class="text-xs font-bold text-slate-500 mb-1 block">Bank / E-Wallet</label>
                            <select name="bank" class="w-full font-bold outline-none p-2 rounded-lg border border-slate-200 focus:border-green-500">
                                @foreach(['BCA' => 'Bank BCA', 'Mandiri' => 'Bank Mandiri', 'GoPay' => 'GoPay', 'OVO' => 'OVO'] as $val => $label)
                                    <option value="{{ $val }}" {{ ($bank && $bank->bank === $val) ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-500 mb-1 block">No. Rekening / HP</label>
                            <input type="text" name="account_number" value="{{ old('account_number', $bank?->account_number) }}" class="w-full font-bold outline-none p-2 rounded-lg border border-slate-200 focus:border-green-500">
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-xs font-bold text-slate-500 mb-1 block">Atas Nama</label>
                            <input type="text" name="account_name" value="{{ old('account_name', $bank?->account_name) }}" class="w-full font-bold outline-none p-2 rounded-lg border border-slate-200 focus:border-green-500">
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex flex-col gap-3 border-t border-slate-100 pt-6">
                <button type="submit" class="w-full bg-slate-800 text-white font-bold py-4 rounded-xl hover:bg-slate-900 transition shadow-md">Simpan Perubahan</button>
            </div>
        </form>

        {{-- Withdraw Section --}}
        <div class="mt-6 bg-green-50 p-5 rounded-2xl border border-green-200">
            <h3 class="font-extrabold text-green-800 mb-3">💳 Tarik Saldo</h3>
            <p class="text-sm text-green-700 mb-4">Saldo tersedia: <span class="font-bold">Rp {{ number_format($user->balance, 0, ',', '.') }}</span></p>
            <form action="{{ route('withdraw') }}" method="POST" class="flex gap-3">
                @csrf
                <input type="hidden" name="bank_account_id" value="{{ $bank?->id }}">
                <input type="number" name="amount" placeholder="Minimal Rp 10.000" min="10000" class="flex-1 p-3 border border-green-200 rounded-xl bg-white outline-none focus:border-green-500" required>
                <button type="submit" class="bg-green-600 text-white font-bold px-6 py-3 rounded-xl hover:bg-green-700 transition shadow-lg" onclick="return confirm('Yakin tarik saldo?')">Tarik</button>
            </form>
        </div>

        {{-- Logout --}}
        <form action="{{ route('logout') }}" method="POST" class="mt-4">
            @csrf
            <button type="submit" class="w-full border-2 border-red-500 text-red-500 font-bold py-3 rounded-xl hover:bg-red-50 hover:text-red-600 transition">Keluar (Log Out)</button>
        </form>
    </div>
</div>
@endsection

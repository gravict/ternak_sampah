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

        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" id="profile-form">
            @csrf
            @method('PUT')

            <div class="space-y-4 mb-6">
                {{-- NIK (Always Read-only) --}}
                <div>
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider ml-1">NIK KTP</label>
                    <input type="text" value="{{ $user->nik }}" disabled class="w-full text-left font-bold text-slate-400 bg-transparent py-2 border border-transparent outline-none cursor-not-allowed">
                </div>

                {{-- Profile Photo Upload (hidden by default) --}}
                <div id="photo-upload-area" class="hidden">
                    <label class="text-xs font-bold text-slate-500 mb-1 block">Ubah Foto Profil</label>
                    <input type="file" name="profile_photo" accept="image/*" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:font-bold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                </div>

                {{-- Data Fields --}}
                <div class="bg-slate-50 p-5 rounded-2xl border border-slate-100 space-y-4">
                    <div>
                        <label class="text-xs font-bold text-slate-500 mb-1 block">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" class="profile-field w-full font-bold text-lg outline-none p-2 rounded-lg border border-slate-200 bg-slate-50 focus:border-green-500" required readonly>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-bold text-slate-500 mb-1 block">Username</label>
                            <input type="text" name="username" value="{{ old('username', $user->username) }}" class="profile-field w-full font-bold outline-none p-2 rounded-lg border border-slate-200 bg-slate-50 focus:border-green-500" required readonly>
                            @error('username') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-500 mb-1 block">Email</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="profile-field w-full font-semibold outline-none p-2 rounded-lg border border-slate-200 bg-slate-50 focus:border-green-500" required readonly>
                            @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-500 mb-1 block">Tanggal Lahir</label>
                            <input type="date" name="dob" value="{{ old('dob', $user->dob?->format('Y-m-d')) }}" class="profile-field w-full font-bold outline-none p-2 rounded-lg border border-slate-200 bg-slate-50 focus:border-green-500" readonly>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-500 mb-1 block">Jenis Kelamin</label>
                            <select name="gender" class="profile-field w-full font-bold outline-none p-2 rounded-lg border border-slate-200 bg-slate-50 focus:border-green-500" disabled>
                                <option value="Laki-laki" {{ $user->gender === 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="Perempuan" {{ $user->gender === 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Bank Account --}}
                <div class="bg-slate-50 p-5 rounded-2xl border border-slate-100 space-y-4">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider border-b border-slate-200 pb-2">Rekening Penarikan Utama</p>
                    @php $bank = $user->bankAccounts->first(); @endphp
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                        <div>
                            <label class="text-xs font-bold text-slate-500 mb-1 block">Bank / E-Wallet</label>
                            <select name="bank" class="profile-field w-full font-bold outline-none p-2 rounded-lg border border-slate-200 bg-slate-50 focus:border-green-500" disabled>
                                @foreach(['BCA' => 'Bank BCA', 'Mandiri' => 'Bank Mandiri', 'BNI' => 'Bank BNI', 'BRI' => 'Bank BRI', 'BSI' => 'Bank BSI', 'CIMB Niaga' => 'CIMB Niaga', 'GoPay' => 'GoPay', 'OVO' => 'OVO', 'Dana' => 'Dana', 'ShopeePay' => 'ShopeePay'] as $val => $label)
                                    <option value="{{ $val }}" {{ ($bank && $bank->bank === $val) ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-500 mb-1 block">No. Rekening / HP</label>
                            <input type="text" name="account_number" value="{{ old('account_number', $bank?->account_number) }}" class="profile-field w-full font-bold outline-none p-2 rounded-lg border border-slate-200 bg-slate-50 focus:border-green-500" readonly>
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-xs font-bold text-slate-500 mb-1 block">Atas Nama</label>
                            <input type="text" name="account_name" value="{{ old('account_name', $bank?->account_name) }}" class="profile-field w-full font-bold outline-none p-2 rounded-lg border border-slate-200 bg-slate-50 focus:border-green-500" readonly>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Ubah Data button (shown by default) --}}
            <div id="edit-trigger-area">
                <button type="button" onclick="toggleEditMode()" class="w-full border-2 border-slate-200 text-slate-500 font-bold py-3 rounded-xl hover:bg-slate-50 hover:border-slate-300 transition flex items-center justify-center gap-2">
                    ✏️ Ubah Data Profil
                </button>
            </div>

            {{-- Save / Batal (hidden by default) --}}
            <div id="save-btn-area" class="hidden flex flex-col gap-3">
                <button type="submit" class="w-full bg-slate-800 text-white font-bold py-4 rounded-xl hover:bg-slate-900 transition shadow-md">Simpan Perubahan</button>
                <button type="button" onclick="toggleEditMode()" class="w-full border-2 border-slate-300 text-slate-500 font-bold py-3 rounded-xl hover:bg-slate-50 transition">Batal</button>
            </div>
        </form>

        {{-- Withdraw Section --}}
        <div class="mt-6 bg-green-50 p-5 rounded-2xl border border-green-200" id="withdraw">
            <h3 class="font-extrabold text-green-800 mb-3">💳 Tarik Saldo</h3>
            <p class="text-sm text-green-700 mb-4">Saldo tersedia: <span class="font-bold">Rp {{ number_format($user->balance, 0, ',', '.') }}</span></p>
            <form action="{{ route('withdraw') }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="text-xs font-bold text-green-700 mb-2 block">Rekening Tujuan</label>
                    <select name="bank_account_id" id="wd-account-select" class="w-full p-3 border border-green-200 rounded-xl bg-white outline-none focus:border-green-500 font-semibold text-sm" onchange="handleWithdrawSelect(this)">
                        @foreach($user->bankAccounts as $ba)
                            <option value="{{ $ba->id }}">{{ $ba->bank }} — {{ $ba->account_number }} ({{ $ba->account_name }})</option>
                        @endforeach
                        <option value="__new__">＋ Gunakan rekening / nomor lain...</option>
                    </select>
                </div>

                {{-- Inline new account fields (auto-show when "rekening lain" is selected) --}}
                <div id="wd-new-fields" class="hidden bg-white p-4 rounded-xl border border-green-200 space-y-2">
                    <p class="text-xs font-bold text-green-700 mb-1">Masukkan data rekening baru:</p>
                    <select name="new_bank" class="w-full p-3 border border-green-200 rounded-xl bg-white outline-none focus:border-green-500 text-sm font-semibold">
                        <option value="">Pilih Bank / E-Wallet</option>
                        @foreach(['BCA' => 'Bank BCA', 'Mandiri' => 'Bank Mandiri', 'BNI' => 'Bank BNI', 'BRI' => 'Bank BRI', 'GoPay' => 'GoPay', 'OVO' => 'OVO', 'Dana' => 'Dana', 'ShopeePay' => 'ShopeePay'] as $val => $label)
                            <option value="{{ $val }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    <input type="text" name="new_account_number" placeholder="No. Rekening / No. HP" class="w-full p-3 border border-green-200 rounded-xl bg-white outline-none focus:border-green-500 text-sm">
                    <input type="text" name="new_account_name" placeholder="Atas Nama" class="w-full p-3 border border-green-200 rounded-xl bg-white outline-none focus:border-green-500 text-sm">
                </div>

                <div class="flex gap-3">
                    <input type="number" name="amount" placeholder="Minimal Rp 10.000" min="10000" class="flex-1 p-3 border border-green-200 rounded-xl bg-white outline-none focus:border-green-500" required>
                    <button type="submit" class="bg-green-600 text-white font-bold px-6 py-3 rounded-xl hover:bg-green-700 transition shadow-lg" onclick="return confirm('Yakin tarik saldo?')">Tarik</button>
                </div>
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

@section('scripts')
<script>
let editMode = false;

function toggleEditMode() {
    editMode = !editMode;

    document.querySelectorAll('.profile-field').forEach(el => {
        if (el.tagName === 'SELECT') {
            el.disabled = !editMode;
        } else {
            el.readOnly = !editMode;
        }
        el.classList.toggle('bg-slate-50', !editMode);
        el.classList.toggle('bg-white', editMode);
        el.classList.toggle('cursor-not-allowed', !editMode);
    });

    document.getElementById('photo-upload-area').classList.toggle('hidden', !editMode);
    document.getElementById('save-btn-area').classList.toggle('hidden', !editMode);
    document.getElementById('edit-trigger-area').classList.toggle('hidden', editMode);
}

function handleWithdrawSelect(sel) {
    const newFields = document.getElementById('wd-new-fields');
    if (sel.value === '__new__') {
        newFields.classList.remove('hidden');
    } else {
        newFields.classList.add('hidden');
    }
}
</script>
@endsection

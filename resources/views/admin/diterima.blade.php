@extends('layouts.admin')
@section('title', 'Validasi & Timbang | Admin TernakSampah')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-extrabold text-slate-800 flex items-center gap-2">Validasi & Timbang <span
                class="bg-blue-100 text-blue-600 text-sm px-3 py-1 rounded-full border border-blue-200">Tahap 2</span></h2>
        <p class="text-slate-500 text-sm mt-1">Masukkan berat asli hasil timbangan di lapangan untuk mencairkan saldo user.
        </p>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
        {{-- Mobile Card Layout --}}
        <div class="md:hidden space-y-4 p-4">
            @forelse($transactions as $t)
                <div class="bg-blue-50/50 border border-blue-100 p-4 rounded-2xl shadow-sm relative">
                    <div class="flex justify-between items-start mb-3 border-b border-blue-100 pb-3">
                        <div>
                            <h4 class="font-extrabold text-slate-800 text-base">{{ $t->user->name }}</h4>
                            <p class="text-xs text-slate-500 mt-0.5">ID: #{{ $t->id }}</p>
                        </div>
                        @if($t->photo)
                            <a href="{{ asset('storage/' . $t->photo) }}" target="_blank" class="bg-white text-slate-700 text-xs font-bold py-1.5 px-3 rounded-lg border border-slate-200 shadow-sm flex items-center gap-1">📸 Foto</a>
                        @endif
                    </div>

                    <div class="mb-4 text-sm">
                        <div class="bg-white p-3 rounded-xl border border-slate-100">
                            <p class="text-[10px] font-bold text-slate-400 uppercase">Kategori</p>
                            <div class="flex justify-between items-center mt-1">
                                <p class="font-bold text-slate-700">{{ $t->category }}</p>
                                <span class="text-xs font-bold text-slate-500 bg-slate-100 px-2 py-1 rounded">Est: {{ $t->est_weight }} Kg</span>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('admin.selesaikan', $t->id) }}" method="POST" class="flex flex-col gap-3" id="form-mob-{{ $t->id }}">
                        @csrf
                        <div class="bg-white p-3 rounded-xl border border-blue-200">
                            <label class="text-[10px] font-bold text-blue-500 uppercase mb-1 block">Timbangan Asli (Kg)</label>
                            <input type="number" name="actual_weight" placeholder="{{ $t->est_weight }}"
                                class="w-full p-2.5 bg-blue-50/50 border border-blue-300 rounded-lg outline-none focus:border-blue-600 font-bold text-blue-800 text-lg text-center"
                                step="0.1" min="0.1" required>
                            
                            <div class="flex flex-col gap-2 text-xs text-slate-700 mt-3 pt-3 border-t border-slate-100">
                                <label class="flex items-center gap-2 cursor-pointer bg-slate-50 p-2 rounded border border-slate-100">
                                    <input type="checkbox" name="is_above_5kg" class="w-4 h-4 text-blue-600 rounded border-slate-300">
                                    <span class="font-semibold">Berat > 5 Kg (+10 Poin)</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer bg-slate-50 p-2 rounded border border-slate-100">
                                    <input type="checkbox" name="is_categorized" class="w-4 h-4 text-blue-600 rounded border-slate-300">
                                    <span class="font-semibold">Sudah Dikategorikan (+10 Poin)</span>
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="w-full bg-blue-600 text-white text-sm font-bold py-3 rounded-xl hover:bg-blue-700 transition shadow-md flex items-center justify-center gap-2">
                            <span>Selesaikan & Bayar</span> 💸
                        </button>
                    </form>
                </div>
            @empty
                <div class="p-6 text-center text-slate-400 italic bg-slate-50 rounded-2xl border border-slate-100 text-sm">Tidak ada transaksi yang sedang ditimbang.</div>
            @endforelse
        </div>

        {{-- Desktop Table Layout --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left whitespace-nowrap">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="p-4 text-slate-500 font-bold text-sm">ID</th>
                        <th class="p-4 text-slate-500 font-bold text-sm">Nama User</th>
                        <th class="p-4 text-slate-500 font-bold text-sm text-center">Foto</th>
                        <th class="p-4 text-slate-500 font-bold text-sm">Kategori</th>
                        <th class="p-4 text-slate-500 font-bold text-sm bg-blue-50">Timbangan Asli (Kg)</th>
                        <th class="p-4 text-slate-500 font-bold text-sm text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($transactions as $t)
                        <tr class="hover:bg-slate-50 transition bg-blue-50/30">
                            <td class="p-4 font-bold text-slate-800 text-sm">#{{ $t->id }}</td>
                            <td class="p-4 font-semibold text-slate-700">{{ $t->user->name }}</td>
                            <td class="p-4 text-center">
                                @if ($t->photo)
                                    <a href="{{ asset('storage/' . $t->photo) }}" target="_blank"
                                        class="bg-white hover:bg-slate-100 text-slate-700 text-xs font-bold py-1 px-2 rounded border border-slate-300 transition">📸</a>
                                @else
                                    <span class="text-xs text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="p-4 text-sm font-semibold text-slate-600">{{ $t->category }}<br><span
                                    class="text-xs text-slate-400 font-normal">Est: {{ $t->est_weight }} Kg</span></td>
                            <td class="p-4 bg-blue-50">
                                <form action="{{ route('admin.selesaikan', $t->id) }}" method="POST"
                                    class="flex flex-col gap-2" id="form-{{ $t->id }}">
                                    @csrf
                                    <input type="number" name="actual_weight" placeholder="{{ $t->est_weight }}"
                                        class="w-full p-2 border border-blue-300 rounded-lg outline-none focus:border-blue-600 font-bold text-blue-700"
                                        step="0.1" min="0.1" required>
                                    
                                    <div class="flex flex-col gap-1 text-xs text-slate-600 mt-1">
                                        <label class="flex items-center gap-1 cursor-pointer">
                                            <input type="checkbox" name="is_above_5kg" class="w-3 h-3 text-blue-600 rounded">
                                            Berat > 5 Kg (+10 Poin)
                                        </label>
                                        <label class="flex items-center gap-1 cursor-pointer">
                                            <input type="checkbox" name="is_categorized" class="w-3 h-3 text-blue-600 rounded">
                                            Sudah Dikategorikan (+10 Poin)
                                        </label>
                                    </div>
                            </td>
                            <td class="p-4 text-center align-top">
                                <button type="submit"
                                    class="bg-blue-600 text-white text-xs font-bold px-4 py-2 rounded-lg hover:bg-blue-700 transition shadow-sm">Selesaikan
                                    & Bayar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-400 italic">Tidak ada transaksi yang sedang
                                ditimbang.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

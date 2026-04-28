@extends('layouts.admin')
@section('title', 'Validasi & Timbang | Admin TernakSampah')

@section('content')
    <div class="mb-6">
        <h2 class="text-xl sm:text-2xl font-extrabold text-slate-800 flex items-center gap-2">Validasi & Timbang <span
                class="bg-blue-100 text-blue-600 text-xs sm:text-sm px-3 py-1 rounded-full border border-blue-200">Tahap 2</span></h2>
        <p class="text-slate-500 text-sm mt-1">Masukkan berat asli hasil timbangan di lapangan untuk mencairkan saldo user.
        </p>
    </div>

    {{-- Mobile Card Layout --}}
    <div class="md:hidden space-y-4">
        @forelse($transactions as $t)
            <div class="bg-white border border-slate-200 p-4 rounded-2xl shadow-sm">
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <h4 class="font-bold text-slate-800 text-sm">#{{ $t->id }} — {{ $t->user->name }}</h4>
                        <p class="text-xs text-slate-500 mt-0.5">{{ $t->category }}</p>
                        <p class="text-xs text-slate-400">Est: {{ $t->est_weight }} Kg</p>
                    </div>
                    @if ($t->photo)
                        <a href="{{ asset('storage/' . $t->photo) }}" target="_blank"
                            class="bg-white hover:bg-slate-100 text-slate-700 text-xs font-bold py-1 px-2 rounded border border-slate-300 transition flex-shrink-0">📸</a>
                    @endif
                </div>

                <form action="{{ route('admin.selesaikan', $t->id) }}" method="POST" class="pt-3 border-t border-slate-100 space-y-3">
                    @csrf
                    <div>
                        <label class="text-xs font-bold text-blue-700 mb-1 block">Timbangan Asli (Kg)</label>
                        <input type="number" name="actual_weight" placeholder="{{ $t->est_weight }}"
                            class="w-full p-3 border border-blue-300 rounded-xl outline-none focus:border-blue-600 font-bold text-blue-700 bg-blue-50"
                            step="0.1" min="0.1" required>
                    </div>

                    <div class="flex flex-col gap-2 text-xs text-slate-600">
                        <label class="flex items-center gap-2 cursor-pointer bg-slate-50 p-2.5 rounded-lg border border-slate-200">
                            <input type="checkbox" name="is_above_5kg" class="w-4 h-4 text-blue-600 rounded">
                            <span>Berat > 5 Kg <span class="text-green-600 font-bold">(+10 Poin)</span></span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer bg-slate-50 p-2.5 rounded-lg border border-slate-200">
                            <input type="checkbox" name="is_categorized" class="w-4 h-4 text-blue-600 rounded">
                            <span>Sudah Dikategorikan <span class="text-green-600 font-bold">(+10 Poin)</span></span>
                        </label>
                    </div>

                    <button type="submit"
                        class="w-full bg-blue-600 text-white text-sm font-bold px-4 py-3 rounded-xl hover:bg-blue-700 transition shadow-sm">
                        Selesaikan & Bayar
                    </button>
                </form>
            </div>
        @empty
            <div class="p-8 text-center text-slate-400 italic bg-white rounded-2xl border border-slate-200">Tidak ada transaksi yang sedang ditimbang.</div>
        @endforelse
    </div>

    {{-- Desktop Table Layout --}}
    <div class="hidden md:block bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
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

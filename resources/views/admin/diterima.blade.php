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
                                    class="flex items-center gap-3" id="form-{{ $t->id }}">
                                    @csrf
                                    <input type="number" name="actual_weight" placeholder="{{ $t->est_weight }}"
                                        class="w-24 p-2 border border-blue-300 rounded-lg outline-none focus:border-blue-600 font-bold text-blue-700"
                                        step="0.1" min="0.1" required>
                            </td>
                            <td class="p-4 text-center">
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

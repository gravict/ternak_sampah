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

                <div class="pt-3 border-t border-slate-100">
                    <button type="button" onclick="openSelesaikanModal({{ $t->id }}, '{{ $t->category }}', {{ $t->est_weight }}, '{{ $t->user->name }}')"
                        class="w-full bg-blue-600 text-white text-sm font-bold px-4 py-3 rounded-xl hover:bg-blue-700 transition shadow-sm">
                        Selesaikan & Bayar
                    </button>
                </div>
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
                            <td class="p-4 text-center align-middle">
                                <button type="button" onclick="openSelesaikanModal({{ $t->id }}, '{{ $t->category }}', {{ $t->est_weight }}, '{{ $t->user->name }}')"
                                    class="bg-blue-600 text-white text-xs font-bold px-4 py-2 rounded-lg hover:bg-blue-700 transition shadow-sm">Selesaikan
                                    & Bayar</button>
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

@section('modals')
    {{-- Selesaikan Modal --}}
    <div id="selesaikanModal" class="fixed inset-0 items-center justify-center bg-slate-900/60 backdrop-blur-sm hidden" style="display:none; z-index: 9999;">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 p-6 relative max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
            <button onclick="closeSelesaikanModal()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 font-bold text-xl">&times;</button>
            <h3 class="text-xl font-extrabold text-slate-800 mb-1">Selesaikan Transaksi <span id="modal-user-name" class="text-blue-600"></span></h3>
            <p class="text-xs text-slate-500 mb-4" id="modal-category-info"></p>

            <form id="selesaikanForm" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="text-sm font-bold text-slate-700 mb-1 block">Timbangan Asli (Kg)</label>
                    <input type="number" name="actual_weight" id="modal-actual-weight" placeholder="0.0"
                        class="w-full p-3 border border-slate-300 rounded-xl outline-none focus:border-blue-600 font-bold bg-slate-50"
                        step="0.1" min="0.1" required>
                </div>
                
                <div class="flex flex-col gap-2 text-sm text-slate-700 bg-slate-50 p-3 rounded-xl border border-slate-200">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_above_5kg" class="w-4 h-4 text-blue-600 rounded">
                        <span>Berat > 5 Kg <span class="text-green-600 font-bold">(+10 Poin)</span></span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_categorized" class="w-4 h-4 text-blue-600 rounded">
                        <span>Sudah Dikategorikan <span class="text-green-600 font-bold">(+10 Poin)</span></span>
                    </label>
                </div>

                <div class="bg-blue-50 border border-blue-100 p-4 rounded-xl text-sm">
                    <p class="font-bold text-blue-800 mb-1">💳 Instruksi Transfer Pusat</p>
                    <p class="text-blue-700 text-xs mb-2">Mohon transfer dana ke rekening berikut untuk menyetorkan saldo transaksi:</p>
                    <div class="bg-white p-2 rounded border border-blue-200 font-mono font-bold text-blue-900 text-center mb-3">
                        BCA - 5270 3456 78<br>
                        <span class="text-xs font-normal">a.n. PT TernakSampah Indonesia</span>
                    </div>
                    <label class="font-bold text-blue-800 text-xs block mb-1">Unggah Bukti Transfer (Wajib)</label>
                    <input type="file" name="transfer_proof" accept="image/*" required
                        class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer border border-blue-200 rounded-lg bg-white">
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full bg-green-600 text-white text-sm font-bold px-4 py-3 rounded-xl hover:bg-green-700 transition shadow-sm">
                        Konfirmasi Selesai & Kirim Saldo
                    </button>
                </div>
            </form>
        </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function openSelesaikanModal(id, category, estWeight, userName) {
            const modal = document.getElementById('selesaikanModal');
            const form = document.getElementById('selesaikanForm');
            const nameLabel = document.getElementById('modal-user-name');
            const infoLabel = document.getElementById('modal-category-info');
            const weightInput = document.getElementById('modal-actual-weight');

            form.action = `/admin/selesaikan/${id}`;
            nameLabel.innerText = `— ${userName}`;
            infoLabel.innerText = `Kategori: ${category} | Estimasi: ${estWeight} Kg`;
            weightInput.placeholder = estWeight;

            modal.style.display = 'flex';
            modal.classList.remove('hidden');
        }

        function closeSelesaikanModal() {
            const modal = document.getElementById('selesaikanModal');
            modal.style.display = 'none';
            modal.classList.add('hidden');
        }

        // Tutup modal jika klik di luar box
        window.onclick = function(event) {
            const modal = document.getElementById('selesaikanModal');
            if (event.target == modal) {
                closeSelesaikanModal();
            }
        }
    </script>
@endsection

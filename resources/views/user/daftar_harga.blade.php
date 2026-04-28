@extends('layouts.app')
@section('title', 'Daftar Harga | TernakSampah')

@section('content')
<div class="max-w-5xl mx-auto">
    <h2 class="text-3xl font-extrabold mb-6">Daftar Harga Beli ♻️</h2>
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="bg-slate-800 p-6">
            <h3 class="text-white font-bold text-xl">Harga Beli Bank Sampah (Per Kg/Liter)</h3>
        </div>
        <div class="overflow-y-auto max-h-[80vh]">
            <div class="flex flex-col text-sm sm:text-base font-semibold text-slate-700">
                @foreach($prices as $category => $items)
                    <div class="border-b bg-slate-50 p-3 sm:p-4 text-xs sm:text-sm uppercase text-slate-400 font-bold">
                        {{ $category }}
                    </div>
                    @foreach($items as $item)
                        <div class="flex justify-between items-center border-b hover:bg-slate-50 transition p-3 sm:p-4 pl-5 sm:pl-8 gap-2">
                            <span>{{ $item->sub_category }}</span>
                            <span class="{{ $item->price_per_unit >= 2000 ? 'text-green-600' : 'text-orange-500' }} text-right whitespace-nowrap">
                                Rp {{ number_format($item->price_per_unit, 0, ',', '.') }} / {{ $item->unit }}
                            </span>
                        </div>
                    @endforeach
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

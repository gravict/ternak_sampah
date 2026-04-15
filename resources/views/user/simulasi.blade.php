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
            <table class="w-full text-left text-base font-semibold text-slate-700">
                @foreach($prices as $category => $items)
                    <tr class="border-b bg-slate-50">
                        <td colspan="2" class="p-4 text-sm uppercase text-slate-400 font-bold">{{ $category }}</td>
                    </tr>
                    @foreach($items as $item)
                        <tr class="border-b hover:bg-slate-50 transition">
                            <td class="p-4 pl-8">{{ $item->sub_category }}</td>
                            <td class="p-4 {{ $item->price_per_unit >= 2000 ? 'text-green-600' : 'text-orange-500' }} text-right">
                                Rp {{ number_format($item->price_per_unit, 0, ',', '.') }} / {{ $item->unit }}
                            </td>
                        </tr>
                    @endforeach
                @endforeach
            </table>
        </div>
    </div>
</div>
@endsection

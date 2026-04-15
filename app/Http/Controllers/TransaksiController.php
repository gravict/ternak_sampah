<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\WastePrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransaksiController extends Controller
{
    public function index()
    {
        $prices = WastePrice::all()->groupBy('category');
        return view('user.transaksi', compact('prices'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category' => 'required|string',
            'est_weight' => 'required|numeric|min:0.1',
            'method' => 'required|in:Drop-off,Pick-up',
            'photo' => 'nullable|image|max:5120',
            'dropoff_location' => 'nullable|string',
            'pickup_address' => 'nullable|string',
            'pickup_datetime' => 'nullable|date',
            'location_lat' => 'nullable|numeric',
            'location_lng' => 'nullable|numeric',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('transaction-photos', 'public');
        }

        $user = Auth::user();

        // Calculate points based on category
        $ptsMultiplier = str_contains($request->category, 'Plastik') ? 10 : 2;
        $earnedPoints = (int) floor($request->est_weight * $ptsMultiplier);
        $user->increment('points', $earnedPoints);

        Transaction::create([
            'user_id' => $user->id,
            'category' => $request->category,
            'est_weight' => $request->est_weight,
            'method' => $request->method,
            'status' => 'pending',
            'photo' => $photoPath,
            'location_lat' => $request->location_lat,
            'location_lng' => $request->location_lng,
            'dropoff_location' => $request->dropoff_location,
            'pickup_address' => $request->pickup_address,
            'pickup_datetime' => $request->pickup_datetime,
        ]);

        return redirect('/riwayat')->with('success', 'Permintaan transaksi dikirim! Tunggu admin mengonfirmasi.');
    }
}

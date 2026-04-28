<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\WastePrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

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
            'photo' => 'nullable|image|max:10240',
            'dropoff_location' => 'nullable|string',
            'pickup_address' => 'nullable|string',
            'pickup_datetime' => 'nullable|date',
            'location_lat' => 'nullable|numeric',
            'location_lng' => 'nullable|numeric',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $this->storeCompressedPhoto($request->file('photo'));
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();
        

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

    private function storeCompressedPhoto($uploadedFile): string
    {
        try {
            $manager = new ImageManager(new Driver());
            $image = $manager->read($uploadedFile->getPathname());

            $image->scaleDown(1280, 1280);

            $filename = 'transaction-photos/' . uniqid('tx_') . '.jpg';
            $fullPath = storage_path('app/public/' . $filename);

            $dir = dirname($fullPath);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            $image->toJpeg(60)->save($fullPath);

            Log::info("Photo compressed and saved: {$filename} (" . round(filesize($fullPath) / 1024) . " KB)");

            return $filename;
        } catch (\Exception $e) {
            Log::warning("Image compression failed, storing original: " . $e->getMessage());
            return $uploadedFile->store('transaction-photos', 'public');
        }
    }
}

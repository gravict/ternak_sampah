<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\WalletHistory;
use App\Models\BankAccount;

class ProfileController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->load('bankAccounts');
        return view('user.profile', compact('user'));
    }

    public function riwayatDompet()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $walletHistories = WalletHistory::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();
        return view('user.riwayat_dompet', compact('walletHistories'));
    }

    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:users,username,' . $user->id,
            'email' => 'required|email|unique:users,email,' . $user->id,
            'dob' => 'required|date',
            'gender' => 'required|in:Laki-laki,Perempuan',
            'bank' => 'required|string',
            'account_number' => 'required|string',
            'account_name' => 'required|string',
            'profile_photo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('profile_photo')) {
            if ($user->profile_photo) {
                Storage::disk('public')->delete($user->profile_photo);
            }
            $user->profile_photo = $request->file('profile_photo')->store('profile-photos', 'public');
        }

        $user->update($request->only(['name', 'username', 'email', 'dob', 'gender']));

        $user->primaryBankAccount()?->update([
            'bank' => $request->bank,
            'account_number' => $request->account_number,
            'account_name' => $request->account_name,
        ]);

        return back()->with('success', 'Data profil dan rekening berhasil diperbarui!');
    }

    public function withdraw(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10000',
            'bank_account_id' => 'required|exists:bank_accounts,id',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($request->amount > $user->balance) {
            return back()->with('error', 'Saldo tidak mencukupi!');
        }

        $user->decrement('balance', $request->amount);
        $bankAccount = BankAccount::find($request->bank_account_id);

        WalletHistory::create([
            'user_id' => $user->id,
            'amount' => $request->amount,
            'type' => 'withdraw',
            'status' => 'success', // Assuming auto-success for now
            'bank_name' => $bankAccount ? $bankAccount->bank : null,
            'account_number' => $bankAccount ? $bankAccount->account_number : null,
            'account_name' => $bankAccount ? $bankAccount->account_name : null,
        ]);

        return back()->with('success', 'Dana Rp ' . number_format($request->amount, 0, ',', '.') . ' berhasil diproses untuk penarikan!');
    }
}

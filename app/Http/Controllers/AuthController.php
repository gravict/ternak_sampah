<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\BankAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect('/dashboard');
        }
        return view('auth.login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        if (Auth::attempt(['username' => $request->username, 'password' => $request->password])) {
            $request->session()->regenerate();
            return redirect('/dashboard');
        }

        return back()->withErrors(['username' => 'Username atau password salah.'])->withInput();
    }

    public function register(Request $request)
    {
        $request->validate([
            'nik' => 'required|string|size:16|unique:users',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'username' => 'required|string|max:50|unique:users',
            'password' => 'required|string|min:6',
            'dob' => 'required|date',
            'gender' => 'required|in:Laki-laki,Perempuan',
            'bank' => 'required|string',
            'account_number' => 'required|string',
            'account_name' => 'required|string',
        ]);

        $user = User::create([
            'nik' => $request->nik,
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'dob' => $request->dob,
            'gender' => $request->gender,
        ]);

        BankAccount::create([
            'user_id' => $user->id,
            'bank' => $request->bank,
            'account_number' => $request->account_number,
            'account_name' => $request->account_name,
            'is_primary' => true,
        ]);

        return redirect('/')->with('success', 'Pendaftaran berhasil! Silakan Sign In.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}

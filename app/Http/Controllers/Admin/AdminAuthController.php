<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthController extends Controller
{
    public function showLogin()
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if (Auth::check() && $user->isAdmin()) {
            return redirect('/admin/dashboard');
        }
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'branch' => 'required|string',
            'password' => 'required',
        ]);

        
        // Try admin login with a fixed admin username
        if (Auth::attempt(['username' => 'admin', 'password' => $request->password])) {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            if ($user->isAdmin()) {
                $user->update(['admin_branch' => $request->branch]);
                $request->session()->regenerate();
                return redirect('/admin/dashboard');
            }
            Auth::logout();
        }

        return back()->withErrors(['password' => 'Password admin salah.'])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}

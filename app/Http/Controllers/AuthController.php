<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        // Kalau sudah login, langsung redirect ke dashboard
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
            'role'     => 'required|in:owner,cashier,warehouse',
        ]);

        // Map nilai dropdown form ke nilai role di database
        $roleMap = [
            'owner'     => 'owner',
            'cashier'   => 'kasir',
            'warehouse' => 'gudang',
        ];
        $expectedRole = $roleMap[$request->role];

        // Coba login dengan email + password
        if (!Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            return back()->withErrors(['email' => 'Email atau password salah.'])->withInput();
        }

        // Cek apakah role yang dipilih sesuai dengan role di database
        if (Auth::user()->role !== $expectedRole) {
            Auth::logout();
            return back()->withErrors(['role' => 'Role tidak sesuai dengan akun Anda.'])->withInput();
        }

        // Simpan role di session (dipakai oleh layout untuk navigasi)
        $request->session()->regenerate();
        session(['role' => Auth::user()->role]);

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
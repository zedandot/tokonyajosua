<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    // Menampilkan daftar user asli dari database
    public function index()
    {
        $users = User::latest()->get();
        return view('users.index', compact('users'));
    }

    // Menampilkan halaman form tambah user
    public function create()
    {
        return view('users.create');
    }

    // Memproses penyimpanan data user baru
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => ['required', 'string', 'email', 'max:255', Rule::unique('users')->withoutTrashed()],
            'password' => 'required|string|min:8',
            'role'     => 'required|in:owner,kasir,gudang', // Sesuai hak akses yang kita buat
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password), // Enkripsi password agar aman
            'role'     => $request->role,
        ]);

        return redirect()->route('users.index')->with('success', 'Prajurit baru berhasil direkrut!');
    }

    // Delete user (soft delete, jadi data masih ada tapi dianggap sudah "diberhentikan")
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // Proteksi: Mencegah Jendral menghapus dirinya sendiri saat sedang login
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')->with('error', 'Lapor! Anda tidak bisa memecat diri sendiri, Jendral!');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'Personel berhasil diberhentikan dari sistem!');
    }
}
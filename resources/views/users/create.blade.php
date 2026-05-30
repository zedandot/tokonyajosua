@extends('layouts.app')

@section('title', 'Add New User')

@section('content')
<div class="space-y-6 max-w-2xl mx-auto">
    <div class="flex items-center justify-between">
        <h1 class="text-xl sm:text-2xl font-bold text-slate-800">Add New User</h1>
        <a href="{{ route('users.index') }}" class="text-slate-500 hover:text-slate-700 font-medium text-sm">&larr; Kembali</a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden p-6 md:p-8">
        <form action="{{ route('users.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Nama Lengkap</label>
                <input type="text" name="name" required class="w-full px-4 py-2.5 rounded-lg border border-slate-200 focus:border-sky-500 outline-none" placeholder="Masukkan nama user">
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Email</label>
                <input type="email" name="email" required class="w-full px-4 py-2.5 rounded-lg border border-slate-200 focus:border-sky-500 outline-none" placeholder="email@simtoko.com">
                @error('email') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Password</label>
                <input type="password" name="password" required minlength="8" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 focus:border-sky-500 outline-none" placeholder="Minimal 8 karakter">
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Hak Akses (Role)</label>
                <select name="role" required class="w-full px-4 py-2.5 rounded-lg border border-slate-200 focus:border-sky-500 outline-none">
                    <option value="">-- Pilih Role --</option>
                    <option value="owner">Owner (Admin)</option>
                    <option value="kasir">Kasir (POS)</option>
                    <option value="gudang">Gudang (Inventory)</option>
                </select>
            </div>

            <div class="pt-4 flex justify-end">
                <button type="submit" class="px-6 py-2.5 rounded-lg bg-sky-600 hover:bg-sky-700 text-white font-medium transition-colors">
                    Simpan User Baru
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
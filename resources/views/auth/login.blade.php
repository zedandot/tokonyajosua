<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - SIMTOKO</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 flex items-center justify-center p-4 font-['Plus_Jakarta_Sans']">
    <div class="w-full max-w-md">
        {{-- Logo & Branding --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-sky-500 text-white text-2xl font-bold shadow-lg shadow-sky-500/30 mb-4">
                S
            </div>
            <h1 class="text-2xl font-bold text-white">SIMTOKO</h1>
            <p class="text-slate-400 text-sm mt-1">Sistem Informasi Manajemen Toko</p>
            <p class="text-slate-500 text-xs mt-2">Point of Sale & Inventory Management</p>
        </div>

        {{-- Login Card --}}
        <div class="bg-white/95 backdrop-blur rounded-2xl shadow-2xl p-8 border border-white/10">
            <h2 class="text-lg font-semibold text-slate-800 mb-6">Masuk ke akun Anda</h2>

            <form action="{{ route('login') }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700 mb-2">Username / Email</label>
                    <input type="text" name="email" id="email" value="{{ old('email') }}" required autofocus
                        placeholder="Masukkan username atau email"
                        class="w-full px-4 py-3 rounded-lg border border-slate-200 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-colors">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700 mb-2">Password</label>
                    <input type="password" name="password" id="password" required
                        placeholder="Masukkan password"
                        class="w-full px-4 py-3 rounded-lg border border-slate-200 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-colors">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="role" class="block text-sm font-medium text-slate-700 mb-2">Login sebagai</label>
                    <select name="role" id="role" required
                        class="w-full px-4 py-3 rounded-lg border border-slate-200 focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        <option value="">Pilih peran</option>
                        <option value="owner" {{ old('role') === 'owner' ? 'selected' : '' }}>Owner</option>
                        <option value="cashier" {{ old('role') === 'cashier' ? 'selected' : '' }}>Kasir</option>
                        <option value="warehouse" {{ old('role') === 'warehouse' ? 'selected' : '' }}>Petugas Gudang</option>
                    </select>
                    @error('role')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                @if(session('error'))
                    <p class="text-sm text-red-600 bg-red-50 p-3 rounded-lg">{{ session('error') }}</p>
                @endif

                <button type="submit"
                    class="w-full py-3 px-4 rounded-lg bg-sky-600 hover:bg-sky-700 text-white font-medium transition-colors shadow-lg shadow-sky-500/25">
                    Login
                </button>
            </form>

            <p class="text-center text-slate-500 text-xs mt-6">
                SIMTOKO &copy; {{ date('Y') }} — Retail Management System
            </p>
        </div>
    </div>
</body>
</html>

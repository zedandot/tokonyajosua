\@extends('layouts.app')

@section('title', 'Users')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-800">User Management</h1>
            <p class="text-slate-500 text-sm mt-1">Manage system users and access</p>
        </div>
        <a href="{{ route('users.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-sky-600 hover:bg-sky-700 text-white font-medium transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add User
        </a>
    </div>

    {{-- Notifikasi Sukses --}}
    @if(session('success'))
    <div class="p-4 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200">
        {{ session('success') }}
    </div>
    @endif

    {{-- Notifikasi Error --}}
    @if(session('error'))
    <div class="p-4 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200">
        {{ session('error') }}
    </div>
    @endif

    {{-- User Table (Menarik Data Asli) --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 text-slate-600">
                        <th class="px-6 py-4 text-left font-semibold">Name</th>
                        <th class="px-6 py-4 text-left font-semibold">Email</th>
                        <th class="px-6 py-4 text-left font-semibold">Role</th>
                        {{-- 🟢 TAMBAHAN: Header Action --}}
                        <th class="px-6 py-4 text-right font-semibold">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr class="border-t border-slate-100 hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4 font-medium text-slate-800">{{ $user->name }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ $user->email }}</td>
                        <td class="px-6 py-4">
                            @if($user->role === 'owner')
                                <span class="px-2 py-1 rounded bg-violet-100 text-violet-700 text-xs font-medium">Owner</span>
                            @elseif($user->role === 'kasir')
                                <span class="px-2 py-1 rounded bg-sky-100 text-sky-700 text-xs font-medium">Kasir</span>
                            @elseif($user->role === 'gudang')
                                <span class="px-2 py-1 rounded bg-amber-100 text-amber-700 text-xs font-medium">Gudang</span>
                            @else
                                <span class="px-2 py-1 rounded bg-slate-100 text-slate-700 text-xs font-medium">{{ $user->role }}</span>
                            @endif
                        </td>
                        {{-- 🟢 TAMBAHAN: Tombol Hapus --}}
                        <td class="px-6 py-4 text-right">
                            <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user {{ $user->name }}?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 rounded-lg bg-red-50 hover:bg-red-100 hover:text-red-600 text-red-400 transition-colors focus:ring-2 focus:ring-red-200" title="Pecat User">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-slate-500">Belum ada user yang terdaftar.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-slate-50 rounded-xl border border-slate-200 p-4">
        <h3 class="font-medium text-slate-700 mb-2">Role Descriptions</h3>
        <ul class="space-y-1 text-sm text-slate-600">
            <li><span class="font-medium">Owner:</span> Full access to dashboard, reports, users</li>
            <li><span class="font-medium">Kasir:</span> Access to Sales/POS and product view</li>
            <li><span class="font-medium">Gudang:</span> Access to Inventory and product management</li>
        </ul>
    </div>
</div>
@endsection
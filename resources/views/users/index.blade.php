@extends('layouts.app')

@section('title', 'Users')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-800">User Management</h1>
            <p class="text-slate-500 text-sm mt-1">Manage system users and access</p>
        </div>
        <button class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-sky-600 hover:bg-sky-700 text-white font-medium transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add User
        </button>
    </div>

    {{-- User Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 text-slate-600">
                        <th class="px-6 py-4 text-left font-semibold">Name</th>
                        <th class="px-6 py-4 text-left font-semibold">Email</th>
                        <th class="px-6 py-4 text-left font-semibold">Role</th>
                        <th class="px-6 py-4 text-center font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-t border-slate-100 hover:bg-slate-50">
                        <td class="px-6 py-4 font-medium text-slate-800">Ahmad Wijaya</td>
                        <td class="px-6 py-4 text-slate-600">ahmad@simtoko.com</td>
                        <td class="px-6 py-4"><span class="px-2 py-1 rounded bg-violet-100 text-violet-700 text-xs font-medium">Owner</span></td>
                        <td class="px-6 py-4 text-center"><button class="text-sky-600 hover:underline text-sm">Edit</button></td>
                    </tr>
                    <tr class="border-t border-slate-100 hover:bg-slate-50">
                        <td class="px-6 py-4 font-medium text-slate-800">Budi Santoso</td>
                        <td class="px-6 py-4 text-slate-600">budi@simtoko.com</td>
                        <td class="px-6 py-4"><span class="px-2 py-1 rounded bg-sky-100 text-sky-700 text-xs font-medium">Cashier</span></td>
                        <td class="px-6 py-4 text-center"><button class="text-sky-600 hover:underline text-sm">Edit</button></td>
                    </tr>
                    <tr class="border-t border-slate-100 hover:bg-slate-50">
                        <td class="px-6 py-4 font-medium text-slate-800">Citra Dewi</td>
                        <td class="px-6 py-4 text-slate-600">citra@simtoko.com</td>
                        <td class="px-6 py-4"><span class="px-2 py-1 rounded bg-amber-100 text-amber-700 text-xs font-medium">Warehouse</span></td>
                        <td class="px-6 py-4 text-center"><button class="text-sky-600 hover:underline text-sm">Edit</button></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-slate-50 rounded-xl border border-slate-200 p-4">
        <h3 class="font-medium text-slate-700 mb-2">Role Descriptions</h3>
        <ul class="space-y-1 text-sm text-slate-600">
            <li><span class="font-medium">Owner:</span> Full access to dashboard, reports, users</li>
            <li><span class="font-medium">Cashier:</span> Access to Sales/POS and product view</li>
            <li><span class="font-medium">Warehouse:</span> Access to Inventory and product management</li>
        </ul>
    </div>
</div>
@endsection

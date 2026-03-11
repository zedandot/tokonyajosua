@extends('layouts.app')

@section('title', 'Products')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-800">Product Management</h1>
            <p class="text-slate-500 text-sm mt-1">Manage your product catalog</p>
        </div>
        <button class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-sky-600 hover:bg-sky-700 text-white font-medium">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Product
        </button>
    </div>

    <div class="flex flex-col sm:flex-row gap-4">
        <input type="search" placeholder="Search products..." class="flex-1 px-4 py-2.5 rounded-lg border border-slate-200">
        <select class="px-4 py-2.5 rounded-lg border border-slate-200 min-w-[140px]">
            <option value="">All Categories</option>
            <option>Beverages</option>
            <option>Snacks</option>
            <option>Dairy</option>
            <option>Groceries</option>
        </select>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 text-slate-600">
                        <th class="px-6 py-4 text-left font-semibold">Product Name</th>
                        <th class="px-6 py-4 text-left font-semibold">Category</th>
                        <th class="px-6 py-4 text-right font-semibold">Current Stock</th>
                        <th class="px-6 py-4 text-right font-semibold">Purchase Price</th>
                        <th class="px-6 py-4 text-right font-semibold">Selling Price</th>
                        <th class="px-6 py-4 text-center font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-t border-slate-100 hover:bg-slate-50">
                        <td class="px-6 py-4 font-medium text-slate-800">Kopi Sachet Premium</td>
                        <td class="px-6 py-4 text-slate-600">Beverages</td>
                        <td class="px-6 py-4 text-right text-slate-700">120 pcs</td>
                        <td class="px-6 py-4 text-right text-slate-600">Rp 2.000</td>
                        <td class="px-6 py-4 text-right font-medium text-slate-800">Rp 2.500</td>
                        <td class="px-6 py-4 text-center"><button class="p-2 rounded-lg text-sky-600 hover:bg-sky-50">Edit</button><button class="p-2 rounded-lg text-red-600 hover:bg-red-50">Delete</button></td>
                    </tr>
                    <tr class="border-t border-slate-100 hover:bg-slate-50">
                        <td class="px-6 py-4 font-medium text-slate-800">Susu UHT 1L</td>
                        <td class="px-6 py-4 text-slate-600">Dairy</td>
                        <td class="px-6 py-4 text-right text-slate-700">45 pcs</td>
                        <td class="px-6 py-4 text-right text-slate-600">Rp 12.000</td>
                        <td class="px-6 py-4 text-right font-medium text-slate-800">Rp 15.000</td>
                        <td class="px-6 py-4 text-center"><button class="p-2 rounded-lg text-sky-600 hover:bg-sky-50">Edit</button><button class="p-2 rounded-lg text-red-600 hover:bg-red-50">Delete</button></td>
                    </tr>
                    <tr class="border-t border-slate-100 hover:bg-slate-50">
                        <td class="px-6 py-4 font-medium text-slate-800">Mie Instan Goreng</td>
                        <td class="px-6 py-4 text-slate-600">Groceries</td>
                        <td class="px-6 py-4 text-right text-slate-700">200 pcs</td>
                        <td class="px-6 py-4 text-right text-slate-600">Rp 2.500</td>
                        <td class="px-6 py-4 text-right font-medium text-slate-800">Rp 3.500</td>
                        <td class="px-6 py-4 text-center"><button class="p-2 rounded-lg text-sky-600 hover:bg-sky-50">Edit</button><button class="p-2 rounded-lg text-red-600 hover:bg-red-50">Delete</button></td>
                    </tr>
                    <tr class="border-t border-slate-100 hover:bg-slate-50">
                        <td class="px-6 py-4 font-medium text-slate-800">Teh Botol 350ml</td>
                        <td class="px-6 py-4 text-slate-600">Beverages</td>
                        <td class="px-6 py-4 text-right text-amber-600 font-semibold">5 pcs</td>
                        <td class="px-6 py-4 text-right text-slate-600">Rp 3.000</td>
                        <td class="px-6 py-4 text-right font-medium text-slate-800">Rp 4.000</td>
                        <td class="px-6 py-4 text-center"><button class="p-2 rounded-lg text-sky-600 hover:bg-sky-50">Edit</button><button class="p-2 rounded-lg text-red-600 hover:bg-red-50">Delete</button></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

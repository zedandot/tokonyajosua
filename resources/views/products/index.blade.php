@extends('layouts.app')

@section('title', 'Products')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-800">Product Management</h1>
            <p class="text-slate-500 text-sm mt-1">Manage your product catalog</p>
        </div>
        <a href="{{ route('products.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-sky-600 hover:bg-sky-700 text-white font-medium">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Product
        </a>
    </div>

    @if(session('success'))
    <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200">
        {{ session('success') }}
    </div>
    @endif

    {{-- 🟢 UBAH MENJADI FORM FILTER & SEARCH --}}
    <form action="{{ route('products.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4">
        {{-- Input Search --}}
        <input type="search" name="search" value="{{ request('search') }}" placeholder="Search products (tekan enter)..." 
               class="flex-1 px-4 py-2.5 rounded-lg border border-slate-200 focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
        
        {{-- Dropdown Kategori (Otomatis submit saat opsi diubah) --}}
        <select name="category" onchange="this.form.submit()" 
                class="px-4 py-2.5 rounded-lg border border-slate-200 min-w-[140px] focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
            <option value="">All Categories</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
        
        {{-- Tombol submit tersembunyi (untuk trigger pencarian search bar saat ditekan Enter) --}}
        <button type="submit" class="hidden"></button>
    </form>

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
                    @foreach($products as $item)
                    <tr class="border-t border-slate-100 hover:bg-slate-50">
                        <td class="px-6 py-4 font-medium text-slate-800">{{ $item->name }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ $item->category->name ?? '-' }}</td>
                        
                        <td class="px-6 py-4 text-right @if($item->stock <= $item->min_stock) text-amber-600 font-semibold @else text-slate-700 @endif">
                            {{ $item->stock }} pcs
                        </td>
                        
                        <td class="px-6 py-4 text-right text-slate-600">Rp {{ number_format($item->purchase_price, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-right font-medium text-slate-800">Rp {{ number_format($item->selling_price, 0, ',', '.') }}</td>
                        
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('products.edit', $item->id) }}" class="p-2 rounded-lg text-sky-600 hover:bg-sky-50 inline-block">Edit</a>
                            
                            {{-- 🟢 Form Hapus yang Sudah Dimodifikasi (SweetAlert2) --}}
                            <form action="{{ route('products.destroy', $item->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="button" 
                                        onclick="konfirmasiHapus(this, '{{ addslashes($item->name) }}')" 
                                        class="p-2 rounded-lg text-red-600 hover:bg-red-50">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                    
                    @if($products->isEmpty())
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-slate-500">
                            Belum ada data produk atau pencarian tidak ditemukan.
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- 🟢 Senjata Utama: Script SweetAlert2 --}}
@push('scripts')
<script>
function konfirmasiHapus(button, namaProduk) {
    Swal.fire({
        title: 'Hapus Produk?',
        text: "Yakin ingin memusnahkan data '" + namaProduk + "' dari gudang?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444', // Warna Merah (Bahaya)
        cancelButtonColor: '#94a3b8',  // Warna Abu-abu (Batal)
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Jika tombol 'Ya, Hapus!' diklik, form akan otomatis dikirim (submit)
            button.closest('form').submit();
        }
    });
}
</script>
@endpush
@endsection
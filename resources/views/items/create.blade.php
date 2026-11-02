@extends('layouts.app')

@section('title', 'Tambah Bahan Baku')
@section('page-title', 'Tambah Bahan Baku Baru')
@section('page-subtitle', 'Daftarkan bahan baku atau perlengkapan ke inventori Gudang Half Kopi')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-zinc-900/80 border border-zinc-800 rounded-2xl p-6 sm:p-8">
        <form action="{{ route('items.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <!-- Kategori -->
                <div>
                    <label for="category_id" class="block text-xs font-semibold text-zinc-300 mb-1.5">Kategori Bahan <span class="text-rose-400">*</span></label>
                    <select name="category_id" id="category_id" required
                        class="w-full bg-zinc-950 border border-zinc-700/80 rounded-xl px-4 py-2.5 text-xs text-zinc-100 focus:outline-none focus:border-amber-500 transition-all">
                        <option value="">Pilih Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id') <p class="text-rose-400 text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Nama Bahan -->
                <div class="sm:col-span-2">
                    <label for="name" class="block text-xs font-semibold text-zinc-300 mb-1.5">Nama Bahan Baku / Barang <span class="text-rose-400">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" placeholder="Cth: Roasted Beans Brazil Cerrado 1kg" required
                        class="w-full bg-zinc-950 border border-zinc-700/80 rounded-xl px-4 py-2.5 text-xs text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-amber-500 transition-all">
                    @error('name') <p class="text-rose-400 text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Satuan Unit -->
                <div>
                    <label for="unit" class="block text-xs font-semibold text-zinc-300 mb-1.5">Satuan (Unit) <span class="text-rose-400">*</span></label>
                    <input type="text" name="unit" id="unit" value="{{ old('unit') }}" placeholder="Cth: kg, liter, botol, pack, dus, pcs" required
                        class="w-full bg-zinc-950 border border-zinc-700/80 rounded-xl px-4 py-2.5 text-xs text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-amber-500 transition-all">
                    @error('unit') <p class="text-rose-400 text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Harga Beli / Biaya Satuan -->
                <div>
                    <label for="cost_price" class="block text-xs font-semibold text-zinc-300 mb-1.5">Harga Beli Satuan (Rp) <span class="text-rose-400">*</span></label>
                    <input type="number" step="100" min="0" name="cost_price" id="cost_price" value="{{ old('cost_price', 0) }}" required
                        class="w-full bg-zinc-950 border border-zinc-700/80 rounded-xl px-4 py-2.5 text-xs text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-amber-500 transition-all">
                    @error('cost_price') <p class="text-rose-400 text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Stok Awal -->
                <div>
                    <label for="current_stock" class="block text-xs font-semibold text-zinc-300 mb-1.5">Stok Awal Saat Ini <span class="text-rose-400">*</span></label>
                    <input type="number" step="0.01" min="0" name="current_stock" id="current_stock" value="{{ old('current_stock', 0) }}" required
                        class="w-full bg-zinc-950 border border-zinc-700/80 rounded-xl px-4 py-2.5 text-xs text-zinc-100 focus:outline-none focus:border-amber-500 transition-all">
                    @error('current_stock') <p class="text-rose-400 text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Batas Minimum Stok -->
                <div>
                    <label for="minimum_stock" class="block text-xs font-semibold text-zinc-300 mb-1.5">Batas Minimum Stok (Alert) <span class="text-rose-400">*</span></label>
                    <input type="number" step="0.01" min="0" name="minimum_stock" id="minimum_stock" value="{{ old('minimum_stock', 5) }}" required
                        class="w-full bg-zinc-950 border border-zinc-700/80 rounded-xl px-4 py-2.5 text-xs text-zinc-100 focus:outline-none focus:border-amber-500 transition-all">
                    @error('minimum_stock') <p class="text-rose-400 text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Deskripsi / Catatan Resep -->
                <div class="sm:col-span-2">
                    <label for="description" class="block text-xs font-semibold text-zinc-300 mb-1.5">Catatan Spesifikasi / Deskripsi</label>
                    <textarea name="description" id="description" rows="3" placeholder="Informasi notes rasa, vendor langganan, petunjuk penyimpanan (suhu chiller/ruang)..."
                        class="w-full bg-zinc-950 border border-zinc-700/80 rounded-xl p-3 text-xs text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-amber-500 transition-all">{{ old('description') }}</textarea>
                </div>
            </div>

            <div class="pt-4 border-t border-zinc-800 flex items-center justify-end gap-3">
                <a href="{{ route('items.index') }}" class="px-4 py-2.5 rounded-xl bg-zinc-800 hover:bg-zinc-700 text-zinc-300 text-xs font-semibold transition-all">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-zinc-950 text-xs font-bold shadow-lg shadow-amber-950/40 transition-all">
                    Simpan Bahan Baku
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

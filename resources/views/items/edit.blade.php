@extends('layouts.app')

@section('title', 'Edit Bahan Baku')
@section('page-title', 'Edit Bahan Baku')
@section('page-subtitle', 'Perbarui spesifikasi atau data bahan baku')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-zinc-900/80 border border-zinc-800 rounded-2xl p-6 sm:p-8">
        <form action="{{ route('items.update', $item) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <!-- SKU -->
                <div>
                    <label for="sku" class="block text-xs font-semibold text-zinc-300 mb-1.5">Kode SKU <span class="text-rose-400">*</span></label>
                    <input type="text" name="sku" id="sku" value="{{ old('sku', $item->sku) }}" required
                        class="w-full bg-zinc-950 border border-zinc-700/80 rounded-xl px-4 py-2.5 text-xs text-zinc-100 font-mono focus:outline-none focus:border-amber-500 transition-all">
                    @error('sku') <p class="text-rose-400 text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Kategori -->
                <div>
                    <label for="category_id" class="block text-xs font-semibold text-zinc-300 mb-1.5">Kategori Bahan <span class="text-rose-400">*</span></label>
                    <select name="category_id" id="category_id" required
                        class="w-full bg-zinc-950 border border-zinc-700/80 rounded-xl px-4 py-2.5 text-xs text-zinc-100 focus:outline-none focus:border-amber-500 transition-all">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $item->category_id) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id') <p class="text-rose-400 text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Nama Bahan -->
                <div class="sm:col-span-2">
                    <label for="name" class="block text-xs font-semibold text-zinc-300 mb-1.5">Nama Bahan Baku / Barang <span class="text-rose-400">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name', $item->name) }}" required
                        class="w-full bg-zinc-950 border border-zinc-700/80 rounded-xl px-4 py-2.5 text-xs text-zinc-100 focus:outline-none focus:border-amber-500 transition-all">
                    @error('name') <p class="text-rose-400 text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Satuan Unit -->
                <div>
                    <label for="unit" class="block text-xs font-semibold text-zinc-300 mb-1.5">Satuan (Unit) <span class="text-rose-400">*</span></label>
                    <input type="text" name="unit" id="unit" value="{{ old('unit', $item->unit) }}" required
                        class="w-full bg-zinc-950 border border-zinc-700/80 rounded-xl px-4 py-2.5 text-xs text-zinc-100 focus:outline-none focus:border-amber-500 transition-all">
                    @error('unit') <p class="text-rose-400 text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Harga Beli -->
                <div>
                    <label for="cost_price" class="block text-xs font-semibold text-zinc-300 mb-1.5">Harga Beli Satuan (Rp) <span class="text-rose-400">*</span></label>
                    <input type="number" step="100" min="0" name="cost_price" id="cost_price" value="{{ old('cost_price', $item->cost_price) }}" required
                        class="w-full bg-zinc-950 border border-zinc-700/80 rounded-xl px-4 py-2.5 text-xs text-zinc-100 focus:outline-none focus:border-amber-500 transition-all">
                    @error('cost_price') <p class="text-rose-400 text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Batas Minimum Stok -->
                <div>
                    <label for="minimum_stock" class="block text-xs font-semibold text-zinc-300 mb-1.5">Batas Minimum Stok (Alert) <span class="text-rose-400">*</span></label>
                    <input type="number" step="0.01" min="0" name="minimum_stock" id="minimum_stock" value="{{ old('minimum_stock', $item->minimum_stock) }}" required
                        class="w-full bg-zinc-950 border border-zinc-700/80 rounded-xl px-4 py-2.5 text-xs text-zinc-100 focus:outline-none focus:border-amber-500 transition-all">
                    @error('minimum_stock') <p class="text-rose-400 text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Informasi Stok Saat Ini (Readonly info) -->
                <div>
                    <label class="block text-[11px] font-semibold text-zinc-400 mb-1.5">Stok Fisik Saat Ini (Sistem)</label>
                    <div class="px-4 py-2.5 rounded-xl bg-zinc-950/60 border border-zinc-800 text-xs text-zinc-300 font-mono flex items-center justify-between">
                        <span>{{ $item->current_stock }} {{ $item->unit }}</span>
                        <a href="{{ route('adjustments.create') }}" class="text-[10px] text-amber-400 hover:underline">Ubah via Opname &rarr;</a>
                    </div>
                </div>

                <!-- Deskripsi -->
                <div class="sm:col-span-2">
                    <label for="description" class="block text-xs font-semibold text-zinc-300 mb-1.5">Catatan Spesifikasi / Deskripsi</label>
                    <textarea name="description" id="description" rows="3"
                        class="w-full bg-zinc-950 border border-zinc-700/80 rounded-xl p-3 text-xs text-zinc-100 focus:outline-none focus:border-amber-500 transition-all">{{ old('description', $item->description) }}</textarea>
                </div>
            </div>

            <div class="pt-4 border-t border-zinc-800 flex items-center justify-end gap-3">
                <a href="{{ route('items.show', $item) }}" class="px-4 py-2.5 rounded-xl bg-zinc-800 hover:bg-zinc-700 text-zinc-300 text-xs font-semibold transition-all">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-zinc-950 text-xs font-bold shadow-lg shadow-amber-950/40 transition-all">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

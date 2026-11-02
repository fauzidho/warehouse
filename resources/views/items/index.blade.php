@extends('layouts.app')

@section('title', 'Katalog Bahan Baku')
@section('page-title', 'Bahan Baku Gudang')
@section('page-subtitle', 'Daftar seluruh bahan baku, biji kopi, dairy, sirup, dan packaging')

@section('content')
<div class="space-y-6">
    <!-- Header with Action Button -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-white tracking-tight">Katalog Stok Bahan Baku</h2>
            <p class="text-xs text-zinc-400">Kelola dan pantau stok bahan espresso, kitchen, dan packaging.</p>
        </div>

        @if(auth()->user()->isAdmin())
            <a href="{{ route('items.create') }}"
                class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-zinc-950 font-bold text-xs flex items-center gap-2 shadow-lg shadow-amber-950/40 transition-all self-start sm:self-auto">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Bahan Baku Baru
            </a>
        @endif
    </div>

    <!-- Filters & Search Bar -->
    <div class="bg-zinc-900/70 border border-zinc-800 rounded-2xl p-4 sm:p-5">
        <form method="GET" action="{{ route('items.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <!-- Search Keyword -->
            <div class="lg:col-span-2">
                <label class="block text-[11px] font-semibold text-zinc-400 mb-1">Cari Nama Bahan</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cth: Espresso, Susu, BN-HB..."
                        class="w-full bg-zinc-950 border border-zinc-700/80 rounded-xl px-4 py-2 text-xs text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-amber-500 transition-all">
                </div>
            </div>

            <!-- Filter Kategori -->
            <div>
                <label class="block text-[11px] font-semibold text-zinc-400 mb-1">Kategori</label>
                <select name="category" class="w-full bg-zinc-950 border border-zinc-700/80 rounded-xl px-3 py-2 text-xs text-zinc-100 focus:outline-none focus:border-amber-500">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Status Stok -->
            <div>
                <label class="block text-[11px] font-semibold text-zinc-400 mb-1">Status Stok</label>
                <div class="flex gap-2">
                    <select name="status" class="w-full bg-zinc-950 border border-zinc-700/80 rounded-xl px-3 py-2 text-xs text-zinc-100 focus:outline-none focus:border-amber-500">
                        <option value="">Semua Status</option>
                        <option value="low" {{ request('status') == 'low' ? 'selected' : '' }}>Stok Menipis (&le; Min)</option>
                        <option value="out" {{ request('status') == 'out' ? 'selected' : '' }}>Stok Habis (0)</option>
                        <option value="safe" {{ request('status') == 'safe' ? 'selected' : '' }}>Stok Aman</option>
                    </select>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-zinc-800 hover:bg-zinc-700 text-zinc-200 text-xs font-semibold shrink-0">
                        Filter
                    </button>
                    @if(request()->hasAny(['search', 'category', 'status']))
                        <a href="{{ route('items.index') }}" class="p-2 rounded-xl bg-zinc-800 hover:bg-zinc-700 text-zinc-400 hover:text-zinc-200 text-xs shrink-0 flex items-center justify-center" title="Reset filter">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Items Grid / Table -->
    <div class="bg-zinc-900/70 border border-zinc-800 rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-zinc-900 border-b border-zinc-800 text-zinc-400 font-semibold uppercase tracking-wider text-[10px]">
                        <th class="py-3.5 px-4">Bahan Baku</th>
                        <th class="py-3.5 px-4">Kategori</th>
                        <th class="py-3.5 px-4 text-center">Stok Saat Ini</th>
                        <th class="py-3.5 px-4 text-center">Batas Min</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-right">Harga Beli</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800/60">
                    @forelse($items as $item)
                        @php
                            $isOut = $item->current_stock <= 0;
                            $isLow = $item->current_stock <= $item->minimum_stock && !$isOut;
                        @endphp
                        <tr class="hover:bg-zinc-850/50 transition-colors">
                            <!-- Name -->
                            <td class="py-4 px-4">
                                <div class="font-bold text-white text-sm">{{ $item->name }}</div>
                                <div class="text-[11px] text-zinc-400 font-mono flex items-center gap-2 mt-0.5">
                                    <span>Satuan: <strong class="text-zinc-300 font-sans">{{ $item->unit }}</strong></span>
                                </div>
                            </td>

                            <!-- Category -->
                            <td class="py-4 px-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-medium bg-zinc-800 text-zinc-300 border border-zinc-700/60">
                                    {{ $item->category->name }}
                                </span>
                            </td>

                            <!-- Current Stock -->
                            <td class="py-4 px-4 text-center">
                                <div class="font-black text-sm {{ $isOut ? 'text-rose-500' : ($isLow ? 'text-amber-400' : 'text-emerald-400') }}">
                                    {{ number_format($item->current_stock, 1, ',', '.') }}
                                </div>
                                <div class="text-[10px] text-zinc-500">{{ $item->unit }}</div>
                            </td>

                            <!-- Min Stock -->
                            <td class="py-4 px-4 text-center font-mono text-zinc-400">
                                {{ number_format($item->minimum_stock, 1, ',', '.') }} {{ $item->unit }}
                            </td>

                            <!-- Status Badge -->
                            <td class="py-4 px-4 text-center">
                                @if($isOut)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-rose-500/10 text-rose-400 border border-rose-500/30">
                                        HABIS TOTAL
                                    </span>
                                @elseif($isLow)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/30">
                                        MENIPIS
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">
                                        AMAN
                                    </span>
                                @endif
                            </td>

                            <!-- Cost Price -->
                            <td class="py-4 px-4 text-right font-medium text-zinc-300">
                                Rp {{ number_format($item->cost_price, 0, ',', '.') }}
                                <div class="text-[10px] text-zinc-500">per {{ $item->unit }}</div>
                            </td>

                            <!-- Actions -->
                            <td class="py-4 px-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('items.show', $item) }}"
                                        class="px-2.5 py-1.5 rounded-lg bg-zinc-800 hover:bg-zinc-700 text-zinc-300 hover:text-white text-xs font-semibold transition-all"
                                        title="Lihat Kartu Stok">
                                        Kartu Stok
                                    </a>

                                    @if(auth()->user()->isAdmin())
                                        <a href="{{ route('items.edit', $item) }}"
                                            class="p-1.5 rounded-lg bg-zinc-850 hover:bg-zinc-800 text-amber-400 hover:text-amber-300 transition-all"
                                            title="Edit Bahan">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                            </svg>
                                        </a>

                                        <form action="{{ route('items.destroy', $item) }}" method="POST" onsubmit="return confirm('Hapus bahan baku {{ $item->name }} dari sistem?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-lg bg-zinc-850 hover:bg-rose-500/10 text-zinc-500 hover:text-rose-400 transition-all" title="Hapus Bahan">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-zinc-500">
                                Tidak ada bahan baku yang sesuai dengan kriteria pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($items->hasPages())
            <div class="p-4 border-t border-zinc-800">
                {{ $items->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

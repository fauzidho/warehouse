@extends('layouts.app')

@section('title', 'Supplier & Vendor')
@section('page-title', 'Master Data Supplier')
@section('page-subtitle', 'Pemasok biji kopi, susu segar, sirup perasa, dan kemasan kedai')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Form Tambah Supplier -->
    <div class="bg-zinc-900/80 border border-zinc-800 rounded-2xl p-6 h-fit">
        <h3 class="text-sm font-bold text-white mb-4 flex items-center gap-2">
            <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
            </svg>
            Tambah Supplier Baru
        </h3>

        <form action="{{ route('suppliers.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="name" class="block text-xs font-semibold text-zinc-300 mb-1.5">Nama Vendor / PT <span class="text-rose-400">*</span></label>
                <input type="text" name="name" id="name" placeholder="Cth: Roastery Nusantara, PT Dairy Segar" required
                    class="w-full bg-zinc-950 border border-zinc-700/80 rounded-xl px-3.5 py-2.5 text-xs text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-amber-500 transition-all">
                @error('name') <p class="text-rose-400 text-[11px] mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="contact_person" class="block text-xs font-semibold text-zinc-300 mb-1.5">Contact Person (Sales/PIC)</label>
                <input type="text" name="contact_person" id="contact_person" placeholder="Cth: Bpk. Hendrik"
                    class="w-full bg-zinc-950 border border-zinc-700/80 rounded-xl px-3.5 py-2 text-xs text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-amber-500 transition-all">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="phone" class="block text-xs font-semibold text-zinc-300 mb-1.5">No WhatsApp/Telp</label>
                    <input type="text" name="phone" id="phone" placeholder="08..."
                        class="w-full bg-zinc-950 border border-zinc-700/80 rounded-xl px-3 py-2 text-xs text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-amber-500 transition-all">
                </div>
                <div>
                    <label for="email" class="block text-xs font-semibold text-zinc-300 mb-1.5">Email Vendor</label>
                    <input type="email" name="email" id="email" placeholder="order@..."
                        class="w-full bg-zinc-950 border border-zinc-700/80 rounded-xl px-3 py-2 text-xs text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-amber-500 transition-all">
                </div>
            </div>

            <div>
                <label for="address" class="block text-xs font-semibold text-zinc-300 mb-1.5">Alamat Gudang / Kantor Vendor</label>
                <textarea name="address" id="address" rows="3" placeholder="Alamat pengiriman / pickup..."
                    class="w-full bg-zinc-950 border border-zinc-700/80 rounded-xl p-3 text-xs text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-amber-500 transition-all"></textarea>
            </div>

            <button type="submit"
                class="w-full py-2.5 px-4 rounded-xl font-bold text-xs bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-zinc-950 shadow-lg shadow-amber-950/40 transition-all">
                Simpan Supplier
            </button>
        </form>
    </div>

    <!-- Tabel Daftar Supplier -->
    <div class="lg:col-span-2 bg-zinc-900/70 border border-zinc-800 rounded-2xl overflow-hidden">
        <div class="p-5 border-b border-zinc-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <h3 class="text-sm font-bold text-white tracking-tight">Daftar Rekanan & Supplier</h3>
            <form method="GET" action="{{ route('suppliers.index') }}" class="flex items-center gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari vendor..."
                    class="bg-zinc-950 border border-zinc-700/80 rounded-xl px-3 py-1.5 text-xs text-zinc-100 focus:outline-none focus:border-amber-500">
                <button type="submit" class="px-3 py-1.5 rounded-xl bg-zinc-800 text-zinc-300 text-xs font-semibold">Cari</button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-zinc-900 border-b border-zinc-800 text-zinc-400 font-semibold uppercase tracking-wider text-[10px]">
                        <th class="py-3 px-4">Nama Vendor</th>
                        <th class="py-3 px-4">Kontak Person</th>
                        <th class="py-3 px-4">Alamat</th>
                        <th class="py-3 px-4 text-center">Riwayat PO</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800/60">
                    @forelse($suppliers as $sup)
                        <tr class="hover:bg-zinc-850/50 transition-colors">
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-white text-sm">{{ $sup->name }}</div>
                                @if($sup->email)
                                    <div class="text-[11px] text-zinc-400">{{ $sup->email }}</div>
                                @endif
                            </td>

                            <td class="py-3.5 px-4">
                                <div class="text-zinc-200 font-semibold">{{ $sup->contact_person ?: '-' }}</div>
                                <div class="text-[11px] text-zinc-400 font-mono">{{ $sup->phone ?: '-' }}</div>
                            </td>

                            <td class="py-3.5 px-4 text-zinc-400 max-w-xs truncate">
                                {{ $sup->address ?: '-' }}
                            </td>

                            <td class="py-3.5 px-4 text-center">
                                <span class="px-2 py-0.5 rounded-full bg-zinc-800 text-emerald-400 font-bold text-xs">
                                    {{ $sup->stock_transactions_count }} PO
                                </span>
                            </td>

                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <form action="{{ route('suppliers.destroy', $sup) }}" method="POST" onsubmit="return confirm('Hapus supplier {{ $sup->name }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg bg-zinc-800 hover:bg-rose-500/10 text-zinc-400 hover:text-rose-400 transition-all" title="Hapus Supplier">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-zinc-500">Belum ada vendor terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($suppliers->hasPages())
            <div class="p-4 border-t border-zinc-800">
                {{ $suppliers->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

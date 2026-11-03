@extends('layouts.app')

@section('title', 'Kategori Bahan')
@section('page-title', 'Kategori Bahan')
@section('page-subtitle', 'Kelompokkan bahan baku berdasarkan jenis &mdash; kopi, susu, sirup, kemasan')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Form Tambah Kategori -->
    <div class="bg-zinc-900/80 border border-zinc-800 rounded-2xl p-6 h-fit">
        <h3 class="text-sm font-bold text-white mb-4 flex items-center gap-2">
            <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Kategori Baru
        </h3>

        <form action="{{ route('categories.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="name" class="block text-xs font-semibold text-zinc-300 mb-1.5">Nama Kategori <span class="text-rose-400">*</span></label>
                <input type="text" name="name" id="name" placeholder="Cth: Biji Kopi (Beans), Dairy & Susu" required
                    class="w-full bg-zinc-950 border border-zinc-700/80 rounded-xl px-3.5 py-2.5 text-xs text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-amber-500 transition-all">
                @error('name') <p class="text-rose-400 text-[11px] mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="description" class="block text-xs font-semibold text-zinc-300 mb-1.5">Deskripsi / Keterangan</label>
                <textarea name="description" id="description" rows="3" placeholder="Penjelasan jenis bahan dalam kelompok ini..."
                    class="w-full bg-zinc-950 border border-zinc-700/80 rounded-xl p-3 text-xs text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-amber-500 transition-all"></textarea>
            </div>

            <button type="submit" id="catSubmitBtn"
                class="w-full py-2.5 px-4 rounded-xl font-bold text-xs bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-zinc-950 shadow-lg shadow-amber-950/30 transition-all active:scale-[0.98]">
                Simpan Kategori
            </button>
        </form>
    </div>

    <!-- Tabel Daftar Kategori -->
    <div class="lg:col-span-2 bg-zinc-900/70 border border-zinc-800 rounded-2xl overflow-hidden">
        <div class="p-5 border-b border-zinc-800 flex items-center justify-between">
            <h3 class="text-sm font-bold text-white tracking-tight">Daftar Kategori Terdaftar</h3>
            <span class="text-xs text-zinc-400 font-medium">{{ $categories->count() }} Kategori</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-zinc-900 border-b border-zinc-800 text-zinc-400 font-semibold uppercase tracking-wider text-[10px]">
                        <th class="py-3 px-4">Nama & Deskripsi</th>
                        <th class="py-3 px-4 text-center">Jumlah Bahan</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800/60">
                    @forelse($categories as $cat)
                        <tr class="hover:bg-zinc-850/50 transition-colors">
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-white text-sm">{{ $cat->name }}</div>
                                <div class="text-[11px] text-zinc-400 mt-0.5">{{ $cat->description ?: 'Tidak ada deskripsi' }}</div>
                            </td>

                            <td class="py-3.5 px-4 text-center">
                                <span class="px-2 py-0.5 rounded-full bg-zinc-800 text-amber-400 font-bold text-xs">
                                    {{ $cat->items_count }} Bahan
                                </span>
                            </td>

                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <form action="{{ route('categories.destroy', $cat) }}" method="POST" onsubmit="return confirm('Hapus kategori {{ $cat->name }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg bg-zinc-800 hover:bg-rose-500/10 text-zinc-400 hover:text-rose-400 transition-all" title="Hapus Kategori">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-8 text-center text-zinc-500">Belum ada kategori bahan baku.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.querySelector('form[action="{{ route('categories.store') }}"]').addEventListener('submit', function() {
        const btn = document.getElementById('catSubmitBtn');
        if (btn) { btn.disabled = true; btn.textContent = 'Menyimpan...'; }
    });
</script>
@endsection

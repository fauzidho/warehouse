@extends('layouts.app')

@section('title', 'Manajemen Pengguna')
@section('page-title', 'Manajemen Pengguna & Staf')
@section('page-subtitle', 'Kelola hak akses role Administrator dan Pegawai (Barista / Gudang)')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Form Tambah User -->
    <div class="bg-zinc-900/80 border border-zinc-800 rounded-2xl p-6 h-fit">
        <h3 class="text-sm font-bold text-white mb-4 flex items-center gap-2">
            <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
            </svg>
            Tambah Pengguna Baru
        </h3>

        <form action="{{ route('users.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="name" class="block text-xs font-semibold text-zinc-300 mb-1.5">Nama Lengkap <span class="text-rose-400">*</span></label>
                <input type="text" name="name" id="name" placeholder="Cth: Ahmad Fikri" required
                    class="w-full bg-zinc-950 border border-zinc-700/80 rounded-xl px-3.5 py-2.5 text-xs text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-amber-500 transition-all">
                @error('name') <p class="text-rose-400 text-[11px] mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="email" class="block text-xs font-semibold text-zinc-300 mb-1.5">Alamat Email <span class="text-rose-400">*</span></label>
                <input type="email" name="email" id="email" placeholder="Cth: barista2@halfkopi.com" required
                    class="w-full bg-zinc-950 border border-zinc-700/80 rounded-xl px-3.5 py-2.5 text-xs text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-amber-500 transition-all">
                @error('email') <p class="text-rose-400 text-[11px] mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="role" class="block text-xs font-semibold text-zinc-300 mb-1.5">Hak Akses (Role) <span class="text-rose-400">*</span></label>
                <select name="role" id="role" required
                    class="w-full bg-zinc-950 border border-zinc-700/80 rounded-xl px-3.5 py-2.5 text-xs text-zinc-100 focus:outline-none focus:border-amber-500 transition-all">
                    <option value="pegawai">Pegawai (Barista / Staff Gudang)</option>
                    <option value="admin">Admin (Full Access Master & Finance)</option>
                </select>
                @error('role') <p class="text-rose-400 text-[11px] mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="phone" class="block text-xs font-semibold text-zinc-300 mb-1.5">No Handphone</label>
                <input type="text" name="phone" id="phone" placeholder="08..."
                    class="w-full bg-zinc-950 border border-zinc-700/80 rounded-xl px-3.5 py-2 text-xs text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-amber-500 transition-all">
            </div>

            <div>
                <label for="password" class="block text-xs font-semibold text-zinc-300 mb-1.5">Kata Sandi Awal <span class="text-rose-400">*</span></label>
                <input type="password" name="password" id="password" placeholder="Minimal 6 karakter" required
                    class="w-full bg-zinc-950 border border-zinc-700/80 rounded-xl px-3.5 py-2 text-xs text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-amber-500 transition-all">
                @error('password') <p class="text-rose-400 text-[11px] mt-1">{{ $message }}</p> @enderror
            </div>

            <button type="submit"
                class="w-full py-2.5 px-4 rounded-xl font-bold text-xs bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-zinc-950 shadow-lg shadow-amber-950/40 transition-all">
                Daftarkan Pengguna
            </button>
        </form>
    </div>

    <!-- Tabel Daftar Pengguna -->
    <div class="lg:col-span-2 bg-zinc-900/70 border border-zinc-800 rounded-2xl overflow-hidden">
        <div class="p-5 border-b border-zinc-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <h3 class="text-sm font-bold text-white tracking-tight">Daftar Akun Pengguna</h3>
            <span class="text-xs text-zinc-400 font-medium">{{ $users->count() }} Pengguna Terdaftar</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-zinc-900 border-b border-zinc-800 text-zinc-400 font-semibold uppercase tracking-wider text-[10px]">
                        <th class="py-3 px-4">Nama & Email</th>
                        <th class="py-3 px-4 text-center">Role / Jabatan</th>
                        <th class="py-3 px-4">Kontak</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800/60">
                    @forelse($users as $u)
                        <tr class="hover:bg-zinc-850/50 transition-colors">
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-white text-sm flex items-center gap-2">
                                    {{ $u->name }}
                                    @if($u->id === auth()->id())
                                        <span class="text-[9px] px-1.5 py-0.2 rounded bg-amber-500/10 text-amber-400 border border-amber-500/20 font-semibold">Anda</span>
                                    @endif
                                </div>
                                <div class="text-[11px] text-zinc-400">{{ $u->email }}</div>
                            </td>

                            <td class="py-3.5 px-4 text-center">
                                @if($u->role === 'admin')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/30">
                                        ADMINISTRATOR
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-cyan-500/10 text-cyan-400 border border-cyan-500/30">
                                        PEGAWAI
                                    </span>
                                @endif
                            </td>

                            <td class="py-3.5 px-4 text-zinc-300 font-mono text-[11px]">
                                {{ $u->phone ?: '-' }}
                            </td>

                            <td class="py-3.5 px-4 text-center">
                                @if($u->is_active)
                                    <span class="inline-flex items-center gap-1 text-[11px] text-emerald-400 font-semibold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-[11px] text-rose-400 font-semibold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span>
                                        Nonaktif
                                    </span>
                                @endif
                            </td>

                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if($u->id !== auth()->id())
                                        <form action="{{ route('users.destroy', $u) }}" method="POST" onsubmit="return confirm('Hapus akun {{ $u->name }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-lg bg-zinc-800 hover:bg-rose-500/10 text-zinc-400 hover:text-rose-400 transition-all" title="Hapus Pengguna">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-zinc-500">Belum ada pengguna terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="p-4 border-t border-zinc-800">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

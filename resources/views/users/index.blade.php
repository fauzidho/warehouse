@extends('layouts.app')

@section('title', 'Manajemen Pengguna')
@section('page-title', 'Manajemen Pengguna')
@section('page-subtitle', 'Kelola akun dan hak akses staf gudang & barista')

@section('content')
<div class="space-y-6">

    {{-- Info Banner --}}
    <div class="p-4 rounded-xl bg-amber-950/20 border border-amber-500/20 flex items-start gap-3">
        <div class="w-7 h-7 rounded-lg bg-amber-500/10 text-amber-400 flex items-center justify-center shrink-0 mt-0.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div class="text-xs text-zinc-300">
            <span class="font-bold text-white">Panduan Hak Akses Sistem:</span>
            <div class="mt-1 space-y-0.5">
                <div><span class="text-amber-400 font-semibold">Admin Gudang:</span> Memiliki akses penuh ke Gudang Utama dan seluruh 5 outlet, mengelola seluruh stok, laporan, serta akun Admin Outlet.</div>
                <div><span class="text-emerald-400 font-semibold">Admin Outlet:</span> Hanya dapat melihat dan mengelola stok pada outlet yang ditugaskan; tidak dapat mengakses gudang utama atau outlet lain.</div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Form Tambah Pengguna --}}
        <div class="bg-zinc-900/80 border border-zinc-800 rounded-2xl p-6 h-fit">
            <div class="flex items-center gap-2.5 mb-5">
                <div class="w-8 h-8 rounded-lg bg-amber-500/10 text-amber-400 flex items-center justify-center border border-amber-500/20">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-white">Tambah Akun Pengguna</h3>
                    <p class="text-[11px] text-zinc-500">Buat akun staf baru (tanpa email)</p>
                </div>
            </div>

            <form action="{{ route('users.store') }}" method="POST" class="space-y-4" id="addUserForm">
                @csrf

                <div>
                    <label for="name" class="block text-xs font-semibold text-zinc-300 mb-1.5">
                        Nama Lengkap <span class="text-rose-400">*</span>
                    </label>
                    <input type="text" name="name" id="name"
                        placeholder="Cth: Budi Santoso"
                        value="{{ old('name') }}"
                        required
                        class="w-full bg-zinc-950 border border-zinc-700/60 rounded-xl px-3.5 py-2.5 text-xs text-zinc-100 placeholder-zinc-600 focus:outline-none focus:border-amber-500 transition-all">
                    @error('name') <p class="text-rose-400 text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="username" class="block text-xs font-semibold text-zinc-300 mb-1.5">
                        Username <span class="text-rose-400">*</span>
                    </label>
                    <input type="text" name="username" id="username"
                        placeholder="Cth: admin_kemang / spv_gudang"
                        value="{{ old('username') }}"
                        required
                        class="w-full bg-zinc-950 border border-zinc-700/60 rounded-xl px-3.5 py-2.5 text-xs text-zinc-100 placeholder-zinc-600 focus:outline-none focus:border-amber-500 transition-all">
                    @error('username') <p class="text-rose-400 text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="role" class="block text-xs font-semibold text-zinc-300 mb-1.5">
                        Hak Akses (Role) <span class="text-rose-400">*</span>
                    </label>
                    <select name="role" id="role" required
                        class="w-full bg-zinc-950 border border-zinc-700/60 rounded-xl px-3.5 py-2.5 text-xs text-zinc-100 focus:outline-none focus:border-amber-500 transition-all">
                        <option value="admin_outlet" {{ old('role') == 'admin_outlet' ? 'selected' : '' }}>
                            Admin Outlet &mdash; Khusus Outlet Ditugaskan
                        </option>
                        <option value="admin_gudang" {{ old('role') == 'admin_gudang' || old('role') == 'admin' ? 'selected' : '' }}>
                            Admin Gudang &mdash; Akses Gudang Utama & Seluruh Outlet
                        </option>
                    </select>
                    @error('role') <p class="text-rose-400 text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="location" class="block text-xs font-semibold text-zinc-300 mb-1.5">
                        Lokasi Penugasan <span class="text-rose-400">*</span>
                    </label>
                    <select name="location" id="location" required
                        class="w-full bg-zinc-950 border border-zinc-700/60 rounded-xl px-3.5 py-2.5 text-xs text-zinc-100 focus:outline-none focus:border-amber-500 transition-all">
                        <option value="Gudang Utama (Pusat)" {{ old('location') == 'Gudang Utama (Pusat)' ? 'selected' : '' }}>
                            🏢 Gudang Utama (Pusat)
                        </option>
                        <option value="Outlet 1 - Kemang" {{ old('location') == 'Outlet 1 - Kemang' ? 'selected' : '' }}>
                            ☕ Outlet 1 - Kemang
                        </option>
                        <option value="Outlet 2 - Sudirman" {{ old('location') == 'Outlet 2 - Sudirman' ? 'selected' : '' }}>
                            ☕ Outlet 2 - Sudirman
                        </option>
                        <option value="Outlet 3 - Senopati" {{ old('location') == 'Outlet 3 - Senopati' ? 'selected' : '' }}>
                            ☕ Outlet 3 - Senopati
                        </option>
                        <option value="Outlet 4 - Bintaro" {{ old('location') == 'Outlet 4 - Bintaro' ? 'selected' : '' }}>
                            ☕ Outlet 4 - Bintaro
                        </option>
                        <option value="Outlet 5 - Kelapa Gading" {{ old('location') == 'Outlet 5 - Kelapa Gading' ? 'selected' : '' }}>
                            ☕ Outlet 5 - Kelapa Gading
                        </option>
                    </select>
                    @error('location') <p class="text-rose-400 text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="phone" class="block text-xs font-semibold text-zinc-300 mb-1.5">
                        No. Handphone <span class="text-zinc-600 font-normal">(opsional)</span>
                    </label>
                    <input type="text" name="phone" id="phone"
                        placeholder="08xxxxxxxxxx"
                        value="{{ old('phone') }}"
                        class="w-full bg-zinc-950 border border-zinc-700/60 rounded-xl px-3.5 py-2.5 text-xs text-zinc-100 placeholder-zinc-600 focus:outline-none focus:border-amber-500 transition-all">
                </div>

                <div>
                    <label for="password" class="block text-xs font-semibold text-zinc-300 mb-1.5">
                        Kata Sandi Awal <span class="text-rose-400">*</span>
                    </label>
                    <input type="password" name="password" id="password"
                        placeholder="Minimal 6 karakter"
                        required
                        class="w-full bg-zinc-950 border border-zinc-700/60 rounded-xl px-3.5 py-2.5 text-xs text-zinc-100 placeholder-zinc-600 focus:outline-none focus:border-amber-500 transition-all">
                    <p class="text-[11px] text-zinc-600 mt-1">Pengguna dapat mengganti kata sandi setelah login.</p>
                    @error('password') <p class="text-rose-400 text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>

                <button type="submit" id="addUserBtn"
                    class="w-full py-2.5 px-4 rounded-xl font-bold text-xs bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-zinc-950 btn-primary-glow active:scale-[0.98] flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    Daftarkan Pengguna
                </button>
            </form>
        </div>

        {{-- Tabel Daftar Pengguna --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- Summary --}}
            <div class="grid grid-cols-3 gap-3">
                @php
                    $adminCount = $users->filter(fn($u) => in_array($u->role, ['admin', 'admin_gudang']))->count();
                    $staffCount = $users->filter(fn($u) => in_array($u->role, ['pegawai', 'admin_outlet']))->count();
                    $activeCount = $users->where('is_active', true)->count();
                @endphp
                <div class="p-4 rounded-xl bg-zinc-900/70 border border-zinc-800 text-center">
                    <div class="text-2xl font-black text-white">{{ $users->count() }}</div>
                    <div class="text-[11px] text-zinc-500 mt-0.5">Total Pengguna</div>
                </div>
                <div class="p-4 rounded-xl bg-zinc-900/70 border border-zinc-800 text-center">
                    <div class="text-2xl font-black text-amber-400">{{ $adminCount }}</div>
                    <div class="text-[11px] text-zinc-500 mt-0.5">Admin Gudang</div>
                </div>
                <div class="p-4 rounded-xl bg-zinc-900/70 border border-zinc-800 text-center">
                    <div class="text-2xl font-black text-emerald-400">{{ $staffCount }}</div>
                    <div class="text-[11px] text-zinc-500 mt-0.5">Admin Outlet</div>
                </div>
            </div>

            {{-- Tabel --}}
            <div class="bg-zinc-900/70 border border-zinc-800 rounded-2xl overflow-hidden">
                <div class="p-4 border-b border-zinc-800 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-white">Daftar Akun Pengguna</h3>
                    <span class="text-xs text-zinc-500">{{ $users->count() }} terdaftar</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-xs" style="min-width:600px;">
                        <thead>
                            <tr class="border-b border-zinc-800 text-zinc-500 font-semibold uppercase tracking-wider text-[10px]">
                                <th class="py-3 px-4 text-left" style="width:30%;">Pengguna</th>
                                <th class="py-3 px-4 text-center" style="width:20%;">Role</th>
                                <th class="py-3 px-4 text-left" style="width:25%;">Lokasi Penugasan</th>
                                <th class="py-3 px-4 text-center" style="width:12%;">Status</th>
                                <th class="py-3 px-4 text-right" style="width:13%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-800/50">
                            @forelse($users as $u)
                                @php
                                    $isAdminGudang = in_array($u->role, ['admin', 'admin_gudang']);
                                    $uname = $u->username ?? strtolower(str_replace(' ', '_', $u->name));
                                    $assignedLoc = $u->location ?? ($isAdminGudang ? 'Gudang Utama (Pusat)' : 'Outlet 1 - Kemang');
                                @endphp
                                <tr class="hover:bg-zinc-800/20 transition-colors">
                                    <td class="py-3.5 px-4" style="max-width:0;">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-7 h-7 rounded-lg bg-zinc-800 flex items-center justify-center text-xs font-bold
                                                {{ $isAdminGudang ? 'text-amber-400' : 'text-emerald-400' }} shrink-0 border border-zinc-700">
                                                {{ strtoupper(substr($u->name, 0, 1)) }}
                                            </div>
                                            <div class="min-w-0">
                                                <div class="font-semibold text-white text-xs flex items-center gap-1.5 overflow-hidden">
                                                    <span class="overflow-hidden text-ellipsis whitespace-nowrap">{{ $u->name }}</span>
                                                    @if($u->id === auth()->id())
                                                        <span class="text-[9px] px-1.5 py-0.5 rounded bg-amber-500/10 text-amber-400 border border-amber-500/20 font-bold shrink-0">Anda</span>
                                                    @endif
                                                </div>
                                                <div class="text-[11px] text-zinc-400 font-mono overflow-hidden text-ellipsis whitespace-nowrap">{{ '@' . $uname }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="py-3.5 px-4 text-center">
                                        @if($isAdminGudang)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/25">
                                                ADMIN GUDANG
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/25">
                                                ADMIN OUTLET
                                            </span>
                                        @endif
                                    </td>

                                    <td class="py-3.5 px-4">
                                        <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md text-[11px] font-medium bg-zinc-800/90 text-zinc-200 border border-zinc-700/60">
                                            <span>{{ $isAdminGudang ? '🏢' : '☕' }}</span>
                                            <span>{{ $assignedLoc }}</span>
                                        </div>
                                    </td>

                                    <td class="py-3.5 px-4 text-center">
                                        @if($u->is_active)
                                            <span class="inline-flex items-center gap-1 text-[11px] text-emerald-400 font-medium">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                                Aktif
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 text-[11px] text-zinc-500 font-medium">
                                                <span class="w-1.5 h-1.5 rounded-full bg-zinc-600"></span>
                                                Nonaktif
                                            </span>
                                        @endif
                                    </td>

                                    <td class="py-3.5 px-4 text-right">
                                        @if($u->id !== auth()->id())
                                            <form action="{{ route('users.destroy', $u) }}" method="POST"
                                                onsubmit="return confirm('Hapus akun {{ addslashes($u->name) }}? Tindakan ini tidak dapat dibatalkan.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="p-1.5 rounded-lg hover:bg-rose-500/10 text-zinc-600 hover:text-rose-400 transition-all"
                                                    title="Hapus Pengguna">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-[11px] text-zinc-700">&mdash;</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-12 text-center text-zinc-500 text-xs">
                                        <svg class="w-8 h-8 mx-auto mb-2 text-zinc-700" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        Belum ada pengguna terdaftar.
                                    </td>
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
    </div>
</div>

<script>
    document.getElementById('addUserForm').addEventListener('submit', function() {
        const btn = document.getElementById('addUserBtn');
        btn.disabled = true;
        btn.innerHTML = `<svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Mendaftarkan...`;
    });
</script>
@endsection

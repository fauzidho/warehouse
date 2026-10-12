<!DOCTYPE html>
<html lang="id" class="h-full bg-zinc-950 text-zinc-100 antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Sistem Gudang Half Kopi</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>
<body class="h-full bg-zinc-950 flex items-center justify-center p-4 relative overflow-hidden selection:bg-amber-600 selection:text-white">
    <!-- Ambient Background Glows -->
    <div class="absolute -top-32 -left-32 w-96 h-96 bg-amber-600/15 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-orange-700/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-2xl h-80 bg-amber-900/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="w-full max-w-md relative z-10">
        <!-- Logo & Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-amber-500 via-amber-600 to-amber-700 shadow-xl shadow-amber-950/60 border border-amber-400/30 mb-4 transform hover:scale-105 transition-transform">
                <svg class="w-9 h-9 text-zinc-950" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 8h1a4 4 0 0 1 0 8h-1M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8zM6 1v3M10 1v3M14 1v3" />
                </svg>
            </div>
            <h1 class="text-2xl font-extrabold tracking-tight text-white flex items-center justify-center gap-2">
                HALF KOPI <span class="text-xs px-2 py-0.5 rounded bg-amber-500/10 text-amber-400 font-semibold border border-amber-500/30">GUDANG</span>
            </h1>
            <p class="text-xs text-zinc-400 mt-1">Sistem Manajemen Stok & Supply Chain Kedai Kopi</p>
        </div>

        <!-- Login Card -->
        <div class="bg-zinc-900/80 backdrop-blur-2xl rounded-2xl border border-zinc-800 shadow-2xl p-7 relative">
            @if(session('success'))
                <div class="mb-5 p-3.5 rounded-xl bg-emerald-950/40 border border-emerald-500/30 text-emerald-300 text-xs">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-5 p-3.5 rounded-xl bg-rose-950/40 border border-rose-500/30 text-rose-300 text-xs space-y-1">
                    @foreach($errors->all() as $error)
                        <div>• {{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST" class="space-y-4" id="loginForm">
                @csrf
                <div>
                    <label for="email" class="block text-xs font-semibold text-zinc-300 mb-1.5">Alamat Email</label>
                    <div class="relative">
                        <input type="email" name="email" id="email" value="{{ old('email', 'admin@halfkopi.com') }}" required
                            class="w-full bg-zinc-950 border border-zinc-700/80 rounded-xl px-4 py-2.5 text-sm text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-all">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-xs font-semibold text-zinc-300 mb-1.5">Kata Sandi</label>
                    <input type="password" name="password" id="password" value="password" required
                        class="w-full bg-zinc-950 border border-zinc-700/80 rounded-xl px-4 py-2.5 text-sm text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-all">
                </div>

                <div class="flex items-center justify-between text-xs pt-1">
                    <label class="flex items-center gap-2 text-zinc-400 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded bg-zinc-950 border-zinc-700 text-amber-600 focus:ring-amber-500">
                        <span>Ingat saya di perangkat ini</span>
                    </label>
                </div>

                <button type="submit"
                    class="w-full mt-2 py-3 px-4 rounded-xl font-bold text-sm bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-zinc-950 shadow-lg shadow-amber-900/40 transform active:scale-95 transition-all">
                    Masuk ke Sistem Gudang
                </button>
            </form>

            <!-- Quick Demo Credentials Box -->
            <div class="mt-6 pt-5 border-t border-zinc-800/80">
                <div class="text-[11px] font-semibold text-zinc-400 uppercase tracking-wider mb-2.5 text-center">
                    Akses Cepat Demo (1-Klik):
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <button type="button" onclick="setLogin('admin@halfkopi.com', 'password')"
                        class="p-2.5 rounded-xl bg-zinc-950/80 hover:bg-zinc-800/80 border border-zinc-700/60 hover:border-amber-500/50 text-left transition-all group">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs font-bold text-zinc-200 group-hover:text-amber-400">Akun Admin</span>
                            <span class="text-[9px] px-1.5 py-0.2 rounded bg-amber-500/10 text-amber-400 font-semibold border border-amber-500/20">Full</span>
                        </div>
                        <div class="text-[10px] text-zinc-400">admin@halfkopi.com</div>
                    </button>

                    <button type="button" onclick="setLogin('barista@halfkopi.com', 'password')"
                        class="p-2.5 rounded-xl bg-zinc-950/80 hover:bg-zinc-800/80 border border-zinc-700/60 hover:border-cyan-500/50 text-left transition-all group">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs font-bold text-zinc-200 group-hover:text-cyan-400">Akun Pegawai</span>
                            <span class="text-[9px] px-1.5 py-0.2 rounded bg-cyan-500/10 text-cyan-400 font-semibold border border-cyan-500/20">Staff</span>
                        </div>
                        <div class="text-[10px] text-zinc-400">barista@halfkopi.com</div>
                    </button>
                </div>
            </div>
        </div>

        <div class="text-center mt-6 text-xs text-zinc-500">
            Gudang Half Kopi &bull; Password demo: <code class="text-zinc-400 bg-zinc-900 px-1.5 py-0.5 rounded border border-zinc-800">password</code>
        </div>
    </div>

    <script>
        function setLogin(email, pass) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = pass;
            document.getElementById('loginForm').submit();
        }
    </script>
</body>
</html>

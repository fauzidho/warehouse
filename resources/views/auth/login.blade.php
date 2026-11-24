<!DOCTYPE html>
<html lang="id" class="h-full antialiased" style="background:#d9c4ab;">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk &mdash; Sistem Gudang Half Kopi</title>
    <meta name="description" content="Login ke Sistem Manajemen Gudang Half Kopi.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #d9c4ab !important;
            color: #3e231c !important;
            margin: 0;
            min-height: 100vh;
        }
        .login-card { animation: slideUp 0.3s cubic-bezier(0.16, 1, 0.3, 1); }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px) scale(0.98); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }
    </style>
</head>
<body class="h-full flex items-center justify-center p-4 relative overflow-hidden" style="background:#d9c4ab !important;">

    {{-- Ambient Coffee Background Pattern --}}
    <div class="absolute inset-0 overflow-hidden pointer-events-none" style="background: radial-gradient(ellipse at 85% 10%, rgba(62,35,28,0.12), transparent 45%), radial-gradient(ellipse at 15% 90%, rgba(62,35,28,0.14), transparent 45%), #d9c4ab;">
        <div class="absolute -top-40 -left-40 w-96 h-96 rounded-full blur-3xl" style="background:rgba(62,35,28,0.1);"></div>
        <div class="absolute -bottom-40 -right-40 w-[30rem] h-[30rem] rounded-full blur-3xl" style="background:rgba(62,35,28,0.12);"></div>
        {{-- Subtle grid pattern --}}
        <div class="absolute inset-0 opacity-[0.035]" style="background-image: linear-gradient(#3e231c 1px, transparent 1px), linear-gradient(90deg, #3e231c 1px, transparent 1px); background-size: 36px 36px;"></div>
    </div>

    <div class="w-full max-w-md relative z-10 login-card">

        {{-- Logo & Header (Deep Brown #3E231C - 100% Kontras Tinggi) --}}
        <div class="text-center mb-7">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl shadow-xl mb-4" style="background:#3e231c; border: 2px solid #d9c4ab; box-shadow: 0 8px 24px rgba(62,35,28,0.25);">
                <svg class="w-9 h-9" style="color:#d9c4ab;" fill="none" stroke="currentColor" stroke-width="2.3" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 8h1a4 4 0 0 1 0 8h-1M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8zM6 1v3M10 1v3M14 1v3" />
                </svg>
            </div>
            <h1 class="text-2xl font-black tracking-tight" style="color:#3e231c !important; font-size:1.75rem; line-height:1.2;">
                HALF KOPI
                <span class="text-xs px-2.5 py-0.5 rounded-md font-bold align-middle" style="background:#3e231c; color:#d9c4ab; border:1px solid #3e231c; margin-left:4px;">WMS</span>
            </h1>
            <p class="text-xs font-medium mt-1.5" style="color:#633b30 !important;">Sistem Manajemen Inventori Gudang</p>
        </div>

        {{-- Login Card (Putih Bersih dengan Border Cokelat Beige) --}}
        <div class="rounded-2xl p-7 shadow-2xl" style="background:#ffffff !important; border: 2px solid #c4ac91 !important; box-shadow: 0 16px 36px -6px rgba(62,35,28,0.22) !important;">
            <div class="mb-5">
                <h2 class="text-lg font-extrabold" style="color:#3e231c !important;">Selamat Datang</h2>
                <p class="text-xs mt-1" style="color:#633b30 !important;">Silakan masuk dengan akun terdaftar untuk melanjutkan</p>
            </div>

            @if(session('success'))
                <div class="mb-4 p-3 rounded-xl flex items-center gap-2 text-xs" style="background:#ecfdf5; border:1px solid #a7f3d0; color:#047857;">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 p-3 rounded-xl text-xs space-y-1" style="background:#fff1f2; border:1px solid #fecdd3; color:#be123c;">
                    @foreach($errors->all() as $error)
                        <div class="flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full shrink-0" style="background:#e11d48;"></span>
                            <span>{{ $error }}</span>
                        </div>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST" class="space-y-4" id="loginForm">
                @csrf

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-xs font-bold mb-1.5" style="color:#3e231c !important;">
                        Alamat Email
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none" style="color:#7d5245;">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                            </svg>
                        </div>
                        <input type="email" name="email" id="email"
                            value="{{ old('email') }}"
                            required
                            placeholder="nama@halfkopi.com"
                            class="w-full rounded-xl pl-10 pr-4 py-2.5 text-sm font-medium transition-all"
                            style="background:#fdfbf9 !important; border:1.5px solid #d9c4ab !important; color:#3e231c !important;">
                    </div>
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="block text-xs font-bold mb-1.5" style="color:#3e231c !important;">
                        Kata Sandi
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none" style="color:#7d5245;">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <input type="password" name="password" id="password"
                            required
                            placeholder="••••••••"
                            class="w-full rounded-xl pl-10 pr-11 py-2.5 text-sm font-medium transition-all"
                            style="background:#fdfbf9 !important; border:1.5px solid #d9c4ab !important; color:#3e231c !important;">
                        <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-3.5 flex items-center transition-colors" style="color:#7d5245;" onmouseover="this.style.color='#3e231c'" onmouseout="this.style.color='#7d5245'">
                            <svg id="eyeIcon" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Remember Me --}}
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="remember" id="remember"
                        class="w-4 h-4 rounded cursor-pointer" style="accent-color:#3e231c;">
                    <label for="remember" class="text-xs font-medium cursor-pointer" style="color:#633b30 !important;">Ingat saya di perangkat ini</label>
                </div>

                {{-- Submit Button (Warna Cokelat Kopi #3E231C Mewah & Kontras) --}}
                <button type="submit" id="submitBtn"
                    class="w-full py-2.5 px-4 rounded-xl font-bold text-sm flex items-center justify-center gap-2 transition-all cursor-pointer shadow-lg active:scale-95"
                    style="background:#3e231c !important; color:#ffffff !important; border:1px solid #2a1712 !important; box-shadow:0 4px 16px rgba(62,35,28,0.3) !important;"
                    onmouseover="this.style.background='#2a1712';"
                    onmouseout="this.style.background='#3e231c';">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                    </svg>
                    Masuk ke Sistem
                </button>
            </form>
        </div>

        {{-- Footer --}}
        <div class="text-center mt-6 text-xs" style="color:#633b30 !important;">
            <div class="flex items-center justify-center gap-1.5 text-[11px] mb-1 font-semibold" style="color:#3e231c !important;">
                <svg class="w-3.5 h-3.5 text-emerald-700" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                <span>Koneksi aman & terenkripsi</span>
            </div>
            <div>&copy; {{ date('Y') }} Half Kopi Enterprise. Hak Cipta Dilindungi.</div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const pass = document.getElementById('password');
            pass.type = pass.type === 'password' ? 'text' : 'password';
        }

        // Show loading state on submit
        document.getElementById('loginForm').addEventListener('submit', function() {
            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            btn.style.opacity = '0.85';
            btn.innerHTML = `<svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Memproses...`;
        });
    </script>
</body>
</html>

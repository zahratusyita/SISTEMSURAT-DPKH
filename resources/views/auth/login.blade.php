<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - SIPAS NTB</title>
    @vite(['resources/css/app.css'])
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass-panel {
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }
        .bg-image {
            /* Background Arsip AI */
            background-image: url('{{ asset('assets/bg_login_ai.png') }}');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fadeIn 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
    </style>
</head>
<body class="min-h-screen bg-image flex items-center justify-center p-4 sm:p-8 selection:bg-indigo-500 selection:text-white relative">
    <!-- Overlay gelap yang sangat tipis pada body agar background AI tetap sangat terlihat -->
    <div class="absolute inset-0 bg-slate-900/30"></div>

    <main class="relative z-10 w-full max-w-5xl grid lg:grid-cols-2 rounded-[2rem] overflow-hidden glass-panel animate-fade-in">
        
        <!-- Bagian Kiri: Banner Info (Hanya terlihat di Desktop) -->
        <!-- Menggunakan bg-black/40 agar gambar AI terlihat jelas tembus namun teks putih tetap terbaca -->
        <section class="hidden lg:flex flex-col justify-between p-12 lg:p-14 relative overflow-hidden bg-black/40 border-r border-white/10">
            <div class="relative z-10 flex flex-col justify-between h-full">
                <div>
                    <!-- Logo NTB -->
                    <img src="{{ asset('assets/logo_ntb.png') }}" alt="Logo Provinsi NTB" class="h-20 w-auto mb-6 drop-shadow-lg bg-white/10 p-2 rounded-xl backdrop-blur-md border border-white/20">
                    <div class="text-4xl lg:text-5xl font-extrabold tracking-tight text-white drop-shadow-md">SIPAS NTB</div>
                    <p class="mt-3 text-lg font-semibold text-indigo-200 drop-shadow-sm">Sistem Agenda Surat Masuk Digital.</p>
                </div>
                
                <div class="max-w-md my-10">
                    <h1 class="text-2xl font-bold leading-tight text-white drop-shadow-sm">Efisiensi Arsip Birokrasi.</h1>
                    <p class="mt-4 text-slate-200 font-medium leading-relaxed drop-shadow-sm">Platform pengelolaan data surat masuk, disposisi, dan arsip digital secara terpusat, modern, dan aman.</p>
                </div>
                
                <div class="flex items-center gap-4">
                    <div class="h-1 w-12 bg-indigo-400 rounded-full shadow-md"></div>
                    <p class="text-sm font-bold text-white tracking-wide uppercase drop-shadow-sm">Dinas Peternakan & Kesehatan Hewan</p>
                </div>
            </div>
        </section>

        <!-- Bagian Kanan: Form Login -->
        <!-- Menggunakan bg-white/70 agar gambar AI tetap tembus (frosted glass) tapi teks hitam form tetap terbaca jelas -->
        <section class="flex flex-col justify-center p-8 sm:p-12 lg:p-16 bg-white/70">
            <!-- Mobile Header (Hanya terlihat di HP) -->
            <div class="lg:hidden flex flex-col items-center mb-10 text-center">
                <img src="{{ asset('assets/logo_ntb.png') }}" alt="Logo Provinsi NTB" class="h-16 w-auto mb-4 drop-shadow-md">
                <div class="text-2xl font-bold text-slate-900 tracking-tight">SIPAS NTB</div>
                <p class="text-sm text-slate-700 font-semibold mt-1">Dinas Peternakan & Kesehatan Hewan</p>
            </div>

            <div class="mb-10">
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight drop-shadow-sm">Selamat Datang 👋</h2>
                <p class="mt-2 text-sm text-slate-700 font-medium">Silakan masuk menggunakan kredensial admin Anda untuk mengelola arsip.</p>
            </div>

            @if($errors->any())
                <div class="mb-6 rounded-xl bg-rose-500/10 border border-rose-500/20 backdrop-blur-md p-4 flex items-start gap-3">
                    <svg class="h-5 w-5 text-rose-600 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <p class="text-sm text-rose-700 font-bold">{{ $errors->first() }}</p>
                </div>
            @endif

            <form action="{{ route('login.store') }}" method="POST" class="space-y-6">
                @csrf
                <div>
                    <label for="email" class="block text-sm font-bold text-slate-800">Email Address</label>
                    <div class="mt-2 relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none z-10">
                            <svg class="h-5 w-5 text-slate-800" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                            </svg>
                        </div>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus class="block w-full rounded-xl border-0 py-3.5 pl-11 pr-4 text-slate-900 bg-white/70 backdrop-blur-md ring-1 ring-inset ring-slate-300 placeholder:text-slate-500 focus:ring-2 focus:ring-inset focus:ring-indigo-600 focus:bg-white sm:text-sm font-semibold transition-all hover:bg-white/90 hover:ring-slate-400" placeholder="admin@sipas.local">
                    </div>
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-bold text-slate-800">Password</label>
                    <div class="mt-2 relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none z-10">
                            <svg class="h-5 w-5 text-slate-800" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                            </svg>
                        </div>
                        <input id="password" name="password" type="password" required class="block w-full rounded-xl border-0 py-3.5 pl-11 pr-12 text-slate-900 bg-white/70 backdrop-blur-md ring-1 ring-inset ring-slate-300 placeholder:text-slate-500 focus:ring-2 focus:ring-inset focus:ring-indigo-600 focus:bg-white sm:text-sm font-semibold transition-all hover:bg-white/90 hover:ring-slate-400" placeholder="••••••••">
                        
                        <!-- Toggle Show/Hide Password -->
                        <button type="button" onclick="const p = document.getElementById('password'); p.type = p.type === 'password' ? 'text' : 'password';" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-600 hover:text-indigo-600 transition-colors focus:outline-none z-10" title="Tampilkan/Sembunyikan Password">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="flex items-center">
                    <label class="flex items-center gap-3 cursor-pointer group relative">
                        <!-- Native Checkbox (Hidden) -->
                        <input type="checkbox" name="remember" value="1" class="peer sr-only">
                        <!-- Custom Checkbox UI -->
                        <div class="h-5 w-5 rounded-md border-2 border-slate-400 bg-white/50 peer-checked:bg-indigo-600 peer-checked:border-indigo-600 peer-focus:ring-2 peer-focus:ring-indigo-600/50 transition-all flex items-center justify-center">
                            <svg class="h-3.5 w-3.5 text-white opacity-0 peer-checked:opacity-100 transition-opacity" fill="none" viewBox="0 0 24 24" stroke-width="3.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                        </div>
                        <span class="text-sm font-bold text-slate-700 group-hover:text-slate-900 transition-colors">Ingat Sesi Saya</span>
                    </label>
                </div>

                <button type="submit" class="w-full flex justify-center items-center gap-2 rounded-xl bg-gradient-to-r from-indigo-600/90 to-indigo-500/90 backdrop-blur-md px-4 py-3.5 text-sm font-bold text-white shadow-lg shadow-indigo-500/30 hover:from-indigo-600 hover:to-indigo-500 transition-all duration-300 transform hover:-translate-y-0.5 mt-2 border border-indigo-400/30">
                    Masuk ke Sistem
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                    </svg>
                </button>
            </form>
        </section>
    </main>
</body>
</html>

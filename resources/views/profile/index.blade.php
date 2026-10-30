@extends('layouts.app')

@section('content')
{{-- Page Header --}}
<div class="mb-8">
    <div class="flex items-center gap-3 mb-1">
        <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Profil & Pengaturan Akun</h1>
    </div>
    <p class="text-sm text-slate-500">Kelola informasi akun, password, dan daftar administrator sistem.</p>
</div>

<div class="grid gap-6 lg:grid-cols-3 items-start">

    {{-- ========================================================== --}}
    {{-- Kolom Kiri: Avatar & Info Ringkas --}}
    {{-- ========================================================== --}}
    <div class="space-y-6">
        {{-- Avatar Card --}}
        <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-100 text-center">
            <div class="relative inline-block">
                <img class="h-24 w-24 rounded-full object-cover ring-4 ring-indigo-100 shadow-lg mx-auto"
                     src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=6366f1&color=fff&bold=true&size=128"
                     alt="Avatar {{ auth()->user()->name }}">
                <div class="absolute bottom-0 right-0 flex h-7 w-7 items-center justify-center rounded-full bg-indigo-500 ring-2 ring-white shadow-sm">
                    <svg class="h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                </div>
            </div>
            <h2 class="mt-4 text-lg font-bold text-slate-900">{{ auth()->user()->name }}</h2>
            <p class="text-sm text-slate-500">{{ auth()->user()->email }}</p>
            <span class="mt-3 inline-flex items-center rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700 ring-1 ring-inset ring-indigo-600/20">
                Administrator
            </span>
        </div>

        {{-- Stat Card --}}
        <div class="rounded-2xl bg-gradient-to-br from-indigo-600 to-indigo-700 p-6 shadow-lg shadow-indigo-500/20 text-white">
            <div class="flex items-center gap-3 mb-4">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/10 backdrop-blur-sm">
                    <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" /></svg>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-indigo-200">Total Admin</p>
                    <p class="text-3xl font-bold text-white">{{ \App\Models\User::count() }}</p>
                </div>
            </div>
            <p class="text-xs text-indigo-200 leading-relaxed">
                Semua akun administrator yang terdaftar di sistem ini.
            </p>
        </div>
    </div>

    {{-- ========================================================== --}}
    {{-- Kolom Kanan: Form-Form Pengaturan --}}
    {{-- ========================================================== --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- Form: Update Informasi Profil --}}
        <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-100">
            <div class="flex items-center gap-3 mb-6 border-b border-slate-100 pb-4">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-slate-800 tracking-tight">Informasi Profil</h2>
                    <p class="text-xs text-slate-500">Perbarui nama dan alamat email akun Anda.</p>
                </div>
            </div>

            <form action="{{ route('profile.update') }}" method="POST" data-turbo="false">
                @csrf
                @method('PATCH')
                <div class="grid gap-6 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label for="name" class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Lengkap</label>
                        <input type="text" name="name" id="name" value="{{ old('name', auth()->user()->name) }}" required class="block w-full rounded-xl border-0 bg-slate-50/50 py-2.5 pl-4 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-200 placeholder:text-slate-400 focus:bg-white focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm font-medium transition-all hover:bg-slate-50">
                    </div>
                    <div class="sm:col-span-2">
                        <label for="email" class="block text-sm font-semibold text-slate-700 mb-1.5">Alamat Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email', auth()->user()->email) }}" required class="block w-full rounded-xl border-0 bg-slate-50/50 py-2.5 pl-4 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-200 placeholder:text-slate-400 focus:bg-white focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm font-medium transition-all hover:bg-slate-50">
                    </div>
                </div>
                <div class="mt-6 flex justify-end">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-indigo-500 transition-colors">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </section>

        {{-- Form: Ganti Password --}}
        <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-100">
            <div class="flex items-center gap-3 mb-6 border-b border-slate-100 pb-4">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-50 text-amber-600">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" /></svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-slate-800 tracking-tight">Ganti Password</h2>
                    <p class="text-xs text-slate-500">Pastikan menggunakan password yang kuat dan tidak mudah ditebak.</p>
                </div>
            </div>

            <form action="{{ route('profile.password') }}" method="POST" data-turbo="false">
                @csrf
                @method('PATCH')
                <div class="space-y-5">
                    <div>
                        <label for="current_password" class="block text-sm font-semibold text-slate-700 mb-1.5">Password Saat Ini</label>
                        <input type="password" name="current_password" id="current_password" required autocomplete="current-password" placeholder="Masukkan password Anda saat ini" class="block w-full rounded-xl border-0 bg-slate-50/50 py-2.5 pl-4 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-200 placeholder:text-slate-400 focus:bg-white focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm font-medium transition-all hover:bg-slate-50">
                    </div>
                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label for="password" class="block text-sm font-semibold text-slate-700 mb-1.5">Password Baru</label>
                            <input type="password" name="password" id="password" required autocomplete="new-password" placeholder="Min. 8 karakter" class="block w-full rounded-xl border-0 bg-slate-50/50 py-2.5 pl-4 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-200 placeholder:text-slate-400 focus:bg-white focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm font-medium transition-all hover:bg-slate-50">
                        </div>
                        <div>
                            <label for="password_confirmation" class="block text-sm font-semibold text-slate-700 mb-1.5">Konfirmasi Password Baru</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" required autocomplete="new-password" placeholder="Ulangi password baru" class="block w-full rounded-xl border-0 bg-slate-50/50 py-2.5 pl-4 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-200 placeholder:text-slate-400 focus:bg-white focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm font-medium transition-all hover:bg-slate-50">
                        </div>
                    </div>
                </div>
                <div class="mt-6 flex justify-end">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-amber-500 px-5 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-amber-400 transition-colors">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" /></svg>
                        Perbarui Password
                    </button>
                </div>
            </form>
        </section>

        {{-- Form: Tambah Admin Baru --}}
        <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-100">
            <div class="flex items-center gap-3 mb-6 border-b border-slate-100 pb-4">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z" /></svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-slate-800 tracking-tight">Tambah Administrator</h2>
                    <p class="text-xs text-slate-500">Daftarkan akun admin baru yang bisa mengakses sistem ini.</p>
                </div>
            </div>

            <form action="{{ route('profile.admins.store') }}" method="POST" data-turbo="false">
                @csrf
                <div class="grid gap-5 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label for="admin_name" class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Admin Baru</label>
                        <input type="text" name="name" id="admin_name" required placeholder="Nama lengkap admin" class="block w-full rounded-xl border-0 bg-slate-50/50 py-2.5 pl-4 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-200 placeholder:text-slate-400 focus:bg-white focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm font-medium transition-all hover:bg-slate-50">
                    </div>
                    <div class="sm:col-span-2">
                        <label for="admin_email" class="block text-sm font-semibold text-slate-700 mb-1.5">Email Admin Baru</label>
                        <input type="email" name="email" id="admin_email" required placeholder="email@kantor.go.id" class="block w-full rounded-xl border-0 bg-slate-50/50 py-2.5 pl-4 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-200 placeholder:text-slate-400 focus:bg-white focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm font-medium transition-all hover:bg-slate-50">
                    </div>
                    <div>
                        <label for="admin_password" class="block text-sm font-semibold text-slate-700 mb-1.5">Password</label>
                        <input type="password" name="password" id="admin_password" required placeholder="Min. 8 karakter" class="block w-full rounded-xl border-0 bg-slate-50/50 py-2.5 pl-4 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-200 placeholder:text-slate-400 focus:bg-white focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm font-medium transition-all hover:bg-slate-50">
                    </div>
                    <div>
                        <label for="admin_password_confirmation" class="block text-sm font-semibold text-slate-700 mb-1.5">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" id="admin_password_confirmation" required placeholder="Ulangi password" class="block w-full rounded-xl border-0 bg-slate-50/50 py-2.5 pl-4 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-200 placeholder:text-slate-400 focus:bg-white focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm font-medium transition-all hover:bg-slate-50">
                    </div>
                </div>
                <div class="mt-6 flex justify-end">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-emerald-500 transition-colors">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                        Daftarkan Admin
                    </button>
                </div>
            </form>
        </section>

        {{-- Tabel: Daftar Admin Lain --}}
        @if($users->isNotEmpty())
        <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-100">
            <div class="flex items-center gap-3 mb-6 border-b border-slate-100 pb-4">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-rose-50 text-rose-600">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" /></svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-slate-800 tracking-tight">Daftar Administrator Lain</h2>
                    <p class="text-xs text-slate-500">Total {{ $users->count() }} admin lain terdaftar di sistem.</p>
                </div>
            </div>

            <div class="overflow-hidden rounded-xl border border-slate-100">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Admin</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Email</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Bergabung</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @foreach($users as $adminUser)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <img class="h-8 w-8 rounded-full object-cover ring-1 ring-slate-200"
                                         src="https://ui-avatars.com/api/?name={{ urlencode($adminUser->name) }}&background=6366f1&color=fff&bold=true&size=64"
                                         alt="{{ $adminUser->name }}">
                                    <span class="text-sm font-semibold text-slate-800">{{ $adminUser->name }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $adminUser->email }}</td>
                            <td class="px-4 py-3 text-sm text-slate-500">{{ $adminUser->created_at->format('d M Y') }}</td>
                            <td class="px-4 py-3 text-right">
                                <form action="{{ route('profile.admins.destroy', $adminUser) }}" method="POST" data-turbo="false"
                                      data-confirm="Hapus akses akun {{ $adminUser->name }}? Identitas pencatat pada surat tetap tersimpan."
                                      onsubmit="return confirm(this.dataset.confirm)">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg border border-rose-200 bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-700 hover:bg-rose-100 transition-colors">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
        @endif

    </div>{{-- end col kanan --}}
</div>
@endsection

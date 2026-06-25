<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Roti Maros Hikmah</title>

    <link rel="icon" type="image/png" href="{{ asset('images/icons/icon-512.png') }}">

    <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">

    <meta name="theme-color" content="#1F444C">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="application-name" content="Roti Maros Hikmah">

    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="RMHKM POS">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    <link rel="apple-touch-icon" href="{{ asset('icons/icon-192.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="min-h-screen overflow-x-hidden bg-[#F7F6F4] text-[#2B1A10] antialiased lg:h-screen lg:overflow-hidden">
    <main class="relative flex min-h-screen lg:h-screen">
        {{-- Background Ornament --}}
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute -right-32 -top-32 h-96 w-96 rounded-full bg-[#F4B044]/25 blur-3xl"></div>
            <div class="absolute -bottom-40 -left-40 h-[28rem] w-[28rem] rounded-full bg-[#1F444C]/20 blur-3xl"></div>
            <div class="absolute left-1/2 top-1/3 h-80 w-80 -translate-x-1/2 rounded-full bg-[#F4D3B0]/40 blur-3xl">
            </div>
        </div>

        <section class="relative z-10 grid w-full lg:grid-cols-2">
            {{-- Left Visual --}}
            <div class="hidden bg-[#1F444C] p-10 text-white lg:flex lg:flex-col lg:justify-between">
                <div>
                    <a href="{{ url('/') }}" class="inline-flex items-center gap-4">
                        <div
                            class="flex h-16 w-16 items-center justify-center overflow-hidden rounded-2xl bg-white p-1.5 shadow-lg">
                            <img src="{{ asset('images/logo-roti-maros-hikmah.png') }}" alt="Logo Roti Maros Hikmah"
                                class="h-full w-full object-contain">
                        </div>

                        <div>
                            <p class="text-xl font-black text-[#F4B044]">
                                Roti Maros Hikmah
                            </p>
                            <p class="mt-1 text-xs font-bold uppercase tracking-[0.25em] text-[#F4D3B0]">
                                Sistem Kasir
                            </p>
                        </div>
                    </a>
                </div>

                <div class="max-w-xl">
                    <div
                        class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/10 px-4 py-2">
                        <span class="h-2.5 w-2.5 rounded-full bg-[#F4B044]"></span>
                        <span class="text-xs font-black uppercase tracking-[0.24em] text-[#F4D3B0]">
                            Hikmah POS
                        </span>
                    </div>

                    <h1 class="mt-6 text-5xl font-black leading-tight tracking-tight text-white">
                        Masuk ke Sistem Kasir Roti Maros Hikmah
                    </h1>

                    <p class="mt-5 text-base leading-relaxed text-[#F4D3B0]">
                        Kelola transaksi, laporan, dan akses pengguna dengan sistem kasir yang rapi, cepat, dan
                        terstruktur.
                    </p>

                    <div class="mt-8 grid grid-cols-3 gap-3">
                        <div class="rounded-2xl bg-white/10 p-4 ring-1 ring-white/10">
                            <p class="text-2xl font-black text-[#F4B044]">4</p>
                            <p class="mt-1 text-xs font-semibold text-[#F4D3B0]">Role Akses</p>
                        </div>

                        <div class="rounded-2xl bg-white/10 p-4 ring-1 ring-white/10">
                            <p class="text-2xl font-black text-[#F4B044]">POS</p>
                            <p class="mt-1 text-xs font-semibold text-[#F4D3B0]">Kasir Digital</p>
                        </div>

                        <div class="rounded-2xl bg-white/10 p-4 ring-1 ring-white/10">
                            <p class="text-2xl font-black text-[#F4B044]">24/7</p>
                            <p class="mt-1 text-xs font-semibold text-[#F4D3B0]">Siap Pakai</p>
                        </div>
                    </div>
                </div>

                <p class="text-xs font-medium text-[#F4D3B0]/80">
                    © {{ date('Y') }} Roti Maros Hikmah. All rights reserved.
                </p>
            </div>

            {{-- Login Form --}}
            <div class="flex min-h-screen items-center justify-center px-5 py-8 sm:px-6 lg:min-h-0 lg:px-10">
                <div class="w-full max-w-md">
                    {{-- Mobile Brand --}}
                    <div class="mb-8 flex items-center justify-center gap-3 lg:hidden">
                        <div
                            class="flex h-16 w-16 items-center justify-center overflow-hidden rounded-2xl bg-white p-1 shadow-md ring-1 ring-[#F4D3B0]/70">
                            <img src="{{ asset('images/logo-roti-maros-hikmah.png') }}" alt="Logo Roti Maros Hikmah"
                                class="h-full w-full object-contain">
                        </div>

                        <div>
                            <p class="text-lg font-black text-[#1F444C]">
                                Roti Maros Hikmah
                            </p>
                            <p class="text-xs font-bold uppercase tracking-[0.22em] text-[#6B3E12]">
                                Sistem Kasir
                            </p>
                        </div>
                    </div>

                    <div
                        class="rounded-[2rem] border border-[#F4D3B0]/80 bg-white/85 p-6 shadow-[0_30px_90px_-35px_rgba(31,68,76,0.55)] backdrop-blur-xl sm:p-8">
                        <div>
                            <p class="text-xs font-black uppercase tracking-[0.25em] text-[#6B3E12]">
                                Login Sistem
                            </p>
                            <h2 class="mt-2 text-3xl font-black tracking-tight text-[#2B1A10]">
                                Selamat Datang
                            </h2>
                            <p class="mt-2 text-sm leading-relaxed text-[#6B3E12]">
                                Masukkan email dan password untuk mengakses dashboard.
                            </p>
                        </div>

                        {{-- Session Status --}}
                        @if (session('status'))
                            <div
                                class="mt-6 rounded-2xl border border-[#1F444C]/20 bg-[#1F444C]/10 px-4 py-3 text-sm font-semibold text-[#1F444C]">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-5"
                            x-data="{ showPassword: false }">
                            @csrf

                            {{-- Login Error Message --}}
                            @if ($errors->has('email') || $errors->has('password'))
                                <div class="rounded-2xl border border-[#A92A35]/20 bg-[#A92A35]/10 px-4 py-2">
                                    <p class="text-sm font-semibold leading-relaxed text-[#A92A35]">
                                        {{ $errors->first('email') ?: $errors->first('password') }}
                                    </p>
                                </div>
                            @endif

                            {{-- Email --}}
                            <div>
                                <label for="email" class="text-sm font-bold text-[#2B1A10]">
                                    Email
                                </label>

                                <div class="relative mt-2">
                                    <span
                                        class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-[#6B3E12]/70">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.2"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M21.75 6.75v10.5A2.25 2.25 0 0119.5 19.5h-15a2.25 2.25 0 01-2.25-2.25V6.75" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M21.75 6.75L12 13.5 2.25 6.75" />
                                        </svg>
                                    </span>

                                    <input id="email" type="email" name="email" value="{{ old('email') }}"
                                        required autofocus autocomplete="username" placeholder="Masukkan email"
                                        class="block w-full rounded-2xl border border-[#F4D3B0] bg-[#F7F6F4] py-3 pl-12 pr-4 text-sm font-medium text-[#2B1A10] shadow-sm transition placeholder:text-[#6B3E12]/45 focus:border-[#F4B044] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#F4B044]/20">
                                </div>
                            </div>

                            {{-- Password --}}
                            <div>
                                <label for="password" class="text-sm font-bold text-[#2B1A10]">
                                    Password
                                </label>

                                <div class="relative mt-2">
                                    <span
                                        class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-[#6B3E12]/70">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.2"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M16.5 10.5V6.75a4.5 4.5 0 00-9 0v3.75" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M6.75 10.5h10.5A1.5 1.5 0 0118.75 12v6.75a1.5 1.5 0 01-1.5 1.5H6.75a1.5 1.5 0 01-1.5-1.5V12a1.5 1.5 0 011.5-1.5z" />
                                        </svg>
                                    </span>

                                    <input id="password" name="password" :type="showPassword ? 'text' : 'password'"
                                        required autocomplete="current-password" placeholder="Masukkan password"
                                        class="block w-full rounded-2xl border border-[#F4D3B0] bg-[#F7F6F4] py-3 pl-12 pr-12 text-sm font-medium text-[#2B1A10] shadow-sm transition placeholder:text-[#6B3E12]/45 focus:border-[#F4B044] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#F4B044]/20">

                                    <button type="button"
                                        class="absolute inset-y-0 right-4 flex items-center text-[#6B3E12] transition hover:text-[#1F444C]"
                                        @click="showPassword = !showPassword">

                                        <svg x-show="!showPassword" x-cloak class="h-5 w-5" fill="none"
                                            stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12 18 18.75 12 18.75 2.25 12 2.25 12z" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>

                                        <svg x-show="showPassword" x-cloak class="h-5 w-5" fill="none"
                                            stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M10.58 10.58a2 2 0 002.84 2.84" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M9.88 4.24A10.43 10.43 0 0112 4c6 0 9.75 8 9.75 8a18.1 18.1 0 01-3.22 4.31" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M6.61 6.61A18.3 18.3 0 002.25 12S6 20 12 20a10.4 10.4 0 004.39-.96" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            {{-- Remember + Forgot --}}
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                <label for="remember_me" class="inline-flex items-center gap-2">
                                    <input id="remember_me" type="checkbox" name="remember"
                                        class="h-4 w-4 rounded border-[#F4D3B0] bg-[#F7F6F4] text-[#F4B044] shadow-sm focus:ring-[#F4B044]">

                                    <span class="text-sm font-semibold text-[#6B3E12]">
                                        Ingat saya
                                    </span>
                                </label>
                            </div>

                            {{-- Submit --}}
                            <button type="submit"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-[#1F444C] px-6 py-4 text-sm font-black text-white shadow-xl shadow-[#1F444C]/20 transition hover:-translate-y-0.5 hover:shadow-2xl focus:outline-none focus:ring-4 focus:ring-[#1F444C]/20">
                                Login
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.4"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                </svg>
                            </button>
                        </form>
                    </div>

                    <div class="mt-6 text-center">
                        <a href="{{ url('/') }}"
                            class="text-sm font-bold text-[#6B3E12] transition hover:text-[#1F444C]">
                            Kembali ke halaman utama
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </main>
</body>

</html>

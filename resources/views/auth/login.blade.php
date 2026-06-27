<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Roti Maros Hikmah</title>

    <link rel="icon" type="image/png" href="{{ asset('images/icons/logo-rounded.png') }}">
    <link rel="manifest" href="{{ asset('manifest.webmanifest') }}?v=2">

    <meta name="theme-color" content="#1F444C">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="application-name" content="Roti Maros Hikmah">

    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="RMHKM POS">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="apple-touch-icon" href="{{ asset('images/icons/icon-512.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body
    class="min-h-dvh overflow-x-hidden bg-[#F7F6F4] text-[#2B1A10] antialiased min-[900px]:h-dvh min-[900px]:overflow-hidden">
    <main class="relative isolate flex min-h-dvh flex-col min-[900px]:h-dvh min-[900px]:min-h-0">
        {{-- Background --}}
        <div class="pointer-events-none absolute inset-0 -z-10 overflow-hidden">
            <div class="absolute -right-32 -top-32 h-80 w-80 rounded-full bg-[#F4B044]/25 blur-3xl sm:h-96 sm:w-96">
            </div>
            <div
                class="absolute -bottom-40 -left-40 h-96 w-96 rounded-full bg-[#1F444C]/15 blur-3xl sm:h-[28rem] sm:w-[28rem]">
            </div>
            <div
                class="absolute left-1/2 top-1/3 h-72 w-72 -translate-x-1/2 rounded-full bg-[#F4D3B0]/35 blur-3xl sm:h-96 sm:w-96">
            </div>
        </div>

        <section
            class="grid min-h-dvh w-full flex-1 min-[900px]:min-h-0 min-[900px]:grid-cols-[minmax(0,1fr)_minmax(360px,460px)] xl:grid-cols-[minmax(0,1fr)_minmax(430px,520px)]">
            {{-- Left Visual --}}
            <div
                class="relative hidden overflow-hidden bg-[#1F444C] text-white min-[900px]:flex min-[900px]:flex-col min-[900px]:justify-between min-[900px]:p-8 xl:p-10">
                <div
                    class="pointer-events-none absolute -left-24 -top-24 h-72 w-72 rounded-full bg-[#F4B044]/20 blur-3xl">
                </div>
                <div
                    class="pointer-events-none absolute -bottom-32 -right-28 h-96 w-96 rounded-full bg-white/10 blur-3xl">
                </div>

                {{-- Brand --}}
                <div class="relative z-10">
                    <a href="{{ url('/') }}" class="inline-flex max-w-full items-center gap-4">
                        <div
                            class="flex h-14 w-14 shrink-0 items-center justify-center overflow-hidden rounded-2xl bg-white p-1.5 shadow-lg xl:h-16 xl:w-16">
                            <img src="{{ asset('images/logo-roti-maros-hikmah.png') }}" alt="Logo Roti Maros Hikmah"
                                class="h-full w-full object-contain">
                        </div>

                        <div class="min-w-0">
                            <p class="truncate text-lg font-black text-[#F4B044] xl:text-xl">
                                Roti Maros Hikmah
                            </p>
                            <p class="mt-1 truncate text-xs font-bold uppercase tracking-[0.25em] text-[#F4D3B0]">
                                Sistem Kasir
                            </p>
                        </div>
                    </a>
                </div>

                {{-- Content --}}
                <div class="relative z-10 max-w-xl">
                    <div
                        class="inline-flex max-w-full items-center gap-2 rounded-full border border-white/10 bg-white/10 px-4 py-2">
                        <span class="h-2.5 w-2.5 shrink-0 rounded-full bg-[#F4B044]"></span>
                        <span class="truncate text-xs font-black uppercase tracking-[0.24em] text-[#F4D3B0]">
                            Hikmah POS
                        </span>
                    </div>

                    <h1
                        class="mt-5 text-4xl font-black leading-tight tracking-tight text-white min-[900px]:text-[2.5rem] xl:mt-6 xl:text-5xl">
                        Masuk ke Sistem Kasir Roti Maros Hikmah
                    </h1>

                    <p class="mt-4 text-sm leading-relaxed text-[#F4D3B0] xl:mt-5 xl:text-base">
                        Kelola transaksi, laporan, dan akses pengguna dengan sistem kasir yang rapi, cepat, dan
                        terstruktur.
                    </p>

                    <div class="mt-6 grid grid-cols-3 gap-3 xl:mt-8">
                        <div class="rounded-2xl bg-white/10 p-3 ring-1 ring-white/10 xl:p-4">
                            <p class="text-xl font-black text-[#F4B044] xl:text-2xl">4</p>
                            <p class="mt-1 text-xs font-semibold text-[#F4D3B0]">Role Akses</p>
                        </div>

                        <div class="rounded-2xl bg-white/10 p-3 ring-1 ring-white/10 xl:p-4">
                            <p class="text-xl font-black text-[#F4B044] xl:text-2xl">POS</p>
                            <p class="mt-1 text-xs font-semibold text-[#F4D3B0]">Kasir Digital</p>
                        </div>

                        <div class="rounded-2xl bg-white/10 p-3 ring-1 ring-white/10 xl:p-4">
                            <p class="text-xl font-black text-[#F4B044] xl:text-2xl">24/7</p>
                            <p class="mt-1 text-xs font-semibold text-[#F4D3B0]">Siap Pakai</p>
                        </div>
                    </div>
                </div>

                <p class="relative z-10 text-xs font-medium text-[#F4D3B0]/80">
                    © {{ date('Y') }} Roti Maros Hikmah. All rights reserved.
                </p>
            </div>

            {{-- Login Form --}}
            <div
                class="flex min-h-dvh items-center justify-center px-5 py-8 sm:px-6 min-[900px]:min-h-0 min-[900px]:px-8 min-[900px]:py-4 xl:px-10">
                <div class="w-full max-w-md">
                    {{-- Mobile / Tablet Portrait Brand --}}
                    <div class="mb-7 flex items-center justify-center gap-3 min-[900px]:hidden">
                        <a href="{{ url('/') }}" class="flex items-center gap-3">
                            <div
                                class="flex h-14 w-14 shrink-0 items-center justify-center overflow-hidden rounded-2xl bg-white p-1 shadow-md ring-1 ring-[#F4D3B0]/70 sm:h-16 sm:w-16">
                                <img src="{{ asset('images/logo-roti-maros-hikmah.png') }}" alt="Logo Roti Maros Hikmah"
                                    class="h-full w-full object-contain">
                            </div>

                            <div class="min-w-0">
                                <p class="truncate text-lg font-black text-[#1F444C]">
                                    Roti Maros Hikmah
                                </p>
                                <p class="truncate text-xs font-bold uppercase tracking-[0.22em] text-[#6B3E12]">
                                    Sistem Kasir
                                </p>
                            </div>
                        </a>
                    </div>

                    <div
                        class="rounded-[2rem] border border-[#F4D3B0]/80 bg-white/85 p-6 shadow-[0_30px_90px_-35px_rgba(31,68,76,0.55)] backdrop-blur-xl sm:p-8 min-[900px]:p-6 xl:p-8">
                        <div>
                            <p class="text-xs font-black uppercase tracking-[0.25em] text-[#6B3E12]">
                                Login Sistem
                            </p>

                            <h2
                                class="mt-2 text-3xl font-black tracking-tight text-[#2B1A10] min-[900px]:text-2xl xl:text-3xl">
                                Selamat Datang
                            </h2>

                            <p class="mt-2 text-sm leading-relaxed text-[#6B3E12]">
                                Masukkan email dan password untuk mengakses dashboard.
                            </p>
                        </div>

                        {{-- Session Status --}}
                        @if (session('status'))
                            <div
                                class="mt-5 rounded-2xl border border-[#1F444C]/20 bg-[#1F444C]/10 px-4 py-3 text-sm font-semibold text-[#1F444C]">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login') }}" class="mt-5 space-y-4 xl:mt-6 xl:space-y-5"
                            x-data="{ showPassword: false }">
                            @csrf

                            {{-- Login Error Message --}}
                            @if ($errors->has('email') || $errors->has('password'))
                                <div class="rounded-2xl border border-[#A92A35]/20 bg-[#A92A35]/10 px-4 py-3">
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
                                        class="block h-12 w-full rounded-2xl border border-[#F4D3B0] bg-[#F7F6F4] py-0 pl-12 pr-4 text-sm font-medium text-[#2B1A10] shadow-sm transition placeholder:text-[#6B3E12]/45 focus:border-[#F4B044] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#F4B044]/20">
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
                                        class="block h-12 w-full rounded-2xl border border-[#F4D3B0] bg-[#F7F6F4] py-0 pl-12 pr-12 text-sm font-medium text-[#2B1A10] shadow-sm transition placeholder:text-[#6B3E12]/45 focus:border-[#F4B044] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#F4B044]/20">

                                    <button type="button"
                                        class="absolute inset-y-0 right-4 flex items-center text-[#6B3E12] transition hover:text-[#1F444C] focus:outline-none"
                                        @click="showPassword = !showPassword"
                                        aria-label="Tampilkan atau sembunyikan password">
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

                            {{-- Submit --}}
                            <button type="submit"
                                class="inline-flex h-12 w-full items-center justify-center gap-2 rounded-2xl bg-[#1F444C] px-6 text-sm font-black text-white shadow-xl shadow-[#1F444C]/20 transition hover:-translate-y-0.5 hover:shadow-2xl focus:outline-none focus:ring-4 focus:ring-[#1F444C]/20">
                                Login
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.4"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                </svg>
                            </button>
                        </form>
                    </div>

                    <div class="mt-5 text-center min-[900px]:mt-4 xl:mt-6">
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

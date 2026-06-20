<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Roti Maros Hikmah</title>

    <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">

    <meta name="theme-color" content="#1F444C">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="application-name" content="Roti Maros Hikmah">

    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="RMHKM POS">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    <link rel="apple-touch-icon" href="{{ asset('icons/icon-192.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen overflow-x-hidden bg-[#F7F6F4] text-[#2B1A10] antialiased lg:h-screen lg:overflow-hidden">
    @php
        $dashboardUrl = '#';

        if (auth()->check()) {
            $dashboardUrl = match (auth()->user()->role) {
                'super_admin' => route('super-admin.dashboard'),
                'admin' => route('admin.dashboard'),
                'kasir' => route('kasir.dashboard'),
                'keuangan' => route('keuangan.dashboard'),
                default => route('profile.edit'),
            };
        }
    @endphp

    <main class="relative flex min-h-screen flex-col lg:h-screen lg:min-h-0">
        {{-- Background Ornament --}}
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute -right-32 -top-32 h-96 w-96 rounded-full bg-[#F4B044]/25 blur-3xl"></div>
            <div class="absolute -bottom-40 -left-40 h-[28rem] w-[28rem] rounded-full bg-[#1F444C]/20 blur-3xl"></div>
            <div class="absolute left-1/2 top-1/3 h-80 w-80 -translate-x-1/2 rounded-full bg-[#F4D3B0]/40 blur-3xl">
            </div>
        </div>

        {{-- Navbar --}}
        <header class="relative z-20 shrink-0">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-5 py-4 sm:px-6 lg:px-8">
                <div class="flex items-center gap-3">
                    <div
                        class="flex h-12 w-12 items-center justify-center overflow-hidden rounded-2xl bg-white p-1 shadow-md ring-1 ring-[#F4D3B0]/70">
                        <img src="{{ asset('images/logo-roti-maros-hikmah.png') }}" alt="Logo Roti Maros Hikmah"
                            class="h-full w-full object-contain">
                    </div>

                    <div>
                        <p class="text-base font-black leading-tight text-[#1F444C] sm:text-lg">
                            Roti Maros Hikmah
                        </p>
                        <p class="text-[11px] font-bold uppercase tracking-[0.22em] text-[#6B3E12] sm:text-xs">
                            Sistem Kasir
                        </p>
                    </div>
                </div>

                @if (Route::has('login'))
                    @auth
                        <a href="{{ $dashboardUrl }}"
                            class="inline-flex items-center justify-center rounded-2xl bg-[#1F444C] px-5 py-3 text-sm font-black text-white shadow-lg shadow-[#1F444C]/20 transition hover:-translate-y-0.5 hover:shadow-xl focus:outline-none focus:ring-4 focus:ring-[#1F444C]/20">
                            Masuk Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                            class="inline-flex items-center justify-center rounded-2xl bg-[#F4B044] px-5 py-3 text-sm font-black text-[#2B1A10] shadow-lg shadow-[#F4B044]/25 transition hover:-translate-y-0.5 hover:shadow-xl focus:outline-none focus:ring-4 focus:ring-[#F4B044]/25">
                            Login
                        </a>
                    @endauth
                @endif
            </div>
        </header>

        {{-- Hero --}}
        <section
            class="relative z-10 mx-auto grid w-full max-w-7xl flex-1 items-center gap-6 px-5 py-6 sm:px-6 lg:min-h-0 lg:grid-cols-2 lg:overflow-hidden lg:px-8 lg:py-3">

            {{-- Left Content --}}
            <div class="lg:min-h-0">
                <div
                    class="inline-flex items-center gap-2 rounded-full border border-[#F4D3B0]/80 bg-white/70 px-4 py-2 shadow-sm backdrop-blur">
                    <span class="h-2.5 w-2.5 rounded-full bg-[#F4B044]"></span>
                    <span class="text-xs font-black uppercase tracking-[0.24em] text-[#6B3E12]">
                        Roti Maros Hikmah POS
                    </span>
                </div>

                <h1
                    class="mt-5 max-w-3xl text-3xl font-black leading-tight tracking-tight text-[#2B1A10] sm:text-4xl lg:text-5xl xl:text-6xl">
                    Kelola Penjualan Roti dengan Sistem Kasir yang Lebih Rapi
                </h1>

                <p class="mt-4 max-w-2xl text-sm leading-relaxed text-[#6B3E12] sm:text-base lg:text-[15px] xl:text-lg">
                    Sistem kasir internal untuk membantu pencatatan transaksi, pengelolaan data, dan aktivitas
                    operasional Roti Maros Hikmah menjadi lebih praktis, cepat, dan terstruktur.
                </p>

                <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ $dashboardUrl }}"
                                class="inline-flex items-center justify-center gap-2 rounded-2xl bg-[#1F444C] px-6 py-4 text-sm font-black text-white shadow-xl shadow-[#1F444C]/20 transition hover:-translate-y-0.5 hover:shadow-2xl focus:outline-none focus:ring-4 focus:ring-[#1F444C]/20">
                                Masuk Dashboard
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.4"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                </svg>
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                                class="inline-flex items-center justify-center gap-2 rounded-2xl bg-[#1F444C] px-6 py-4 text-sm font-black text-white shadow-xl shadow-[#1F444C]/20 transition hover:-translate-y-0.5 hover:shadow-2xl focus:outline-none focus:ring-4 focus:ring-[#1F444C]/20">
                                Login ke Sistem
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.4"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                </svg>
                            </a>
                        @endauth
                    @endif

                    <div
                        class="inline-flex items-center justify-center rounded-2xl border border-[#F4D3B0]/80 bg-white/70 px-6 py-4 text-sm font-bold text-[#6B3E12] shadow-sm backdrop-blur">
                        Cepat • Aman • Terstruktur
                    </div>
                </div>

                <div class="mt-6 grid max-w-xl grid-cols-3 gap-3">
                    <div class="rounded-2xl border border-[#F4D3B0]/70 bg-white/70 p-4 shadow-sm backdrop-blur">
                        <p class="text-xl font-black text-[#1F444C]">4</p>
                        <p class="mt-1 text-xs font-semibold text-[#6B3E12]">Role Akses</p>
                    </div>

                    <div class="rounded-2xl border border-[#F4D3B0]/70 bg-white/70 p-4 shadow-sm backdrop-blur">
                        <p class="text-xl font-black text-[#1F444C]">POS</p>
                        <p class="mt-1 text-xs font-semibold text-[#6B3E12]">Kasir Digital</p>
                    </div>

                    <div class="rounded-2xl border border-[#F4D3B0]/70 bg-white/70 p-4 shadow-sm backdrop-blur">
                        <p class="text-xl font-black text-[#1F444C]">24/7</p>
                        <p class="mt-1 text-xs font-semibold text-[#6B3E12]">Siap Pakai</p>
                    </div>
                </div>
            </div>

            {{-- Right Visual --}}
            <div class="relative lg:min-h-0">
                <div class="absolute -inset-6 rounded-[2rem] bg-[#F4B044]/20 blur-3xl"></div>

                <div
                    class="relative overflow-hidden rounded-[2rem] border border-[#F4D3B0]/80 bg-white/80 p-4 shadow-[0_30px_90px_-35px_rgba(31,68,76,0.55)] backdrop-blur-xl xl:p-5">

                    <div class="rounded-[1.5rem] bg-[#1F444C] p-5 text-white xl:p-6">
                        <div class="flex items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-12 w-12 items-center justify-center overflow-hidden rounded-2xl bg-white p-1 shadow-lg xl:h-14 xl:w-14">
                                    <img src="{{ asset('images/logo-roti-maros-hikmah.png') }}"
                                        alt="Logo Roti Maros Hikmah" class="h-full w-full object-contain">
                                </div>

                                <div>
                                    <p class="text-sm font-black text-[#F4B044]">
                                        Roti Maros Hikmah
                                    </p>
                                    <p class="mt-1 text-xs font-medium text-[#F4D3B0]">
                                        Ringkasan Sistem Kasir
                                    </p>
                                </div>
                            </div>

                            <span class="rounded-full bg-[#F4B044]/20 px-3 py-1 text-xs font-bold text-[#F4B044]">
                                Online
                            </span>
                        </div>

                        <div class="mt-5 grid gap-3 sm:grid-cols-2">
                            <div class="rounded-2xl bg-white/10 p-4 ring-1 ring-white/10">
                                <p class="text-xs font-semibold text-[#F4D3B0]">Transaksi</p>
                                <p class="mt-2 text-xl font-black text-white xl:text-2xl">Cepat</p>
                            </div>

                            <div class="rounded-2xl bg-white/10 p-4 ring-1 ring-white/10">
                                <p class="text-xs font-semibold text-[#F4D3B0]">Laporan</p>
                                <p class="mt-2 text-xl font-black text-white xl:text-2xl">Rapi</p>
                            </div>

                            <div class="rounded-2xl bg-white/10 p-4 ring-1 ring-white/10">
                                <p class="text-xs font-semibold text-[#F4D3B0]">Pengguna</p>
                                <p class="mt-2 text-xl font-black text-white xl:text-2xl">Terkontrol</p>
                            </div>

                            <div class="rounded-2xl bg-[#F4B044] p-4 text-[#2B1A10]">
                                <p class="text-xs font-black">Akses Sistem</p>
                                <p class="mt-2 text-xl font-black xl:text-2xl">Login</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 grid gap-3 sm:grid-cols-3">
                        <div class="rounded-2xl border border-[#F4D3B0]/70 bg-[#F7F6F4] p-3 xl:p-4">
                            <p class="text-xs font-bold text-[#6B3E12]">Kasir</p>
                            <div class="mt-3 h-2 rounded-full bg-[#F4D3B0]">
                                <div class="h-2 w-10/12 rounded-full bg-[#F4B044]"></div>
                            </div>
                        </div>

                        <div class="rounded-2xl border border-[#F4D3B0]/70 bg-[#F7F6F4] p-3 xl:p-4">
                            <p class="text-xs font-bold text-[#6B3E12]">Admin</p>
                            <div class="mt-3 h-2 rounded-full bg-[#F4D3B0]">
                                <div class="h-2 w-8/12 rounded-full bg-[#1F444C]"></div>
                            </div>
                        </div>

                        <div class="rounded-2xl border border-[#F4D3B0]/70 bg-[#F7F6F4] p-3 xl:p-4">
                            <p class="text-xs font-bold text-[#6B3E12]">Keuangan</p>
                            <div class="mt-3 h-2 rounded-full bg-[#F4D3B0]">
                                <div class="h-2 w-9/12 rounded-full bg-[#A92A35]"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Footer --}}
        <footer class="relative z-10 shrink-0 border-t border-[#F4D3B0]/70 bg-white/50 backdrop-blur">
            <div
                class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-1 px-5 py-3 text-center text-xs font-medium text-[#6B3E12] sm:flex-row sm:px-6 lg:px-8">
                <p>© {{ date('Y') }} Roti Maros Hikmah. All rights reserved.</p>
                <p>Sistem Kasir Internal</p>
            </div>
        </footer>
    </main>
</body>

</html>

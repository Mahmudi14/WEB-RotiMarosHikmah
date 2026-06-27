<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Roti Maros Hikmah</title>

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
</head>

<body
    class="min-h-dvh overflow-x-hidden bg-[#F7F6F4] text-[#2B1A10] antialiased min-[900px]:h-dvh min-[900px]:overflow-hidden">
    @php
        $dashboardUrl = '#';

        if (auth()->check()) {
            $dashboardUrl = match (auth()->user()->role) {
                'super_admin' => route('super-admin.dashboard'),
                'admin' => route('admin.dashboard'),
                'kasir' => route('cashier.dashboard'),
                'keuangan' => route('finance.dashboard'),
                default => route('profile.edit'),
            };
        }
    @endphp

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

        {{-- Navbar --}}
        <header class="relative z-20 shrink-0 border-b border-[#F4D3B0]/70 bg-[#F7F6F4]/75 backdrop-blur-xl">
            <div
                class="mx-auto flex w-full max-w-7xl items-center justify-between gap-4 px-5 py-4 sm:px-6 min-[900px]:px-8 min-[900px]:py-3 xl:py-4">
                <div class="flex min-w-0 items-center gap-3">
                    <div
                        class="flex h-12 w-12 shrink-0 items-center justify-center overflow-hidden rounded-2xl bg-white p-1 shadow-md ring-1 ring-[#F4D3B0]/70 min-[900px]:h-11 min-[900px]:w-11 xl:h-12 xl:w-12">
                        <img src="{{ asset('images/logo-roti-maros-hikmah.png') }}" alt="Logo Roti Maros Hikmah"
                            class="h-full w-full object-contain">
                    </div>

                    <div class="min-w-0">
                        <p class="truncate text-base font-black leading-tight text-[#1F444C] sm:text-lg">
                            Roti Maros Hikmah
                        </p>
                        <p class="truncate text-[11px] font-bold uppercase tracking-[0.22em] text-[#6B3E12] sm:text-xs">
                            Sistem Kasir
                        </p>
                    </div>
                </div>

                @if (Route::has('login'))
                    @auth
                        <a href="{{ $dashboardUrl }}"
                            class="inline-flex h-11 shrink-0 items-center justify-center rounded-2xl bg-[#1F444C] px-4 text-sm font-black text-white shadow-lg shadow-[#1F444C]/20 transition hover:-translate-y-0.5 hover:shadow-xl focus:outline-none focus:ring-4 focus:ring-[#1F444C]/20 sm:h-12 sm:px-5 min-[900px]:h-11 xl:h-12">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                            class="inline-flex h-11 shrink-0 items-center justify-center rounded-2xl bg-[#F4B044] px-5 text-sm font-black text-[#2B1A10] shadow-lg shadow-[#F4B044]/25 transition hover:-translate-y-0.5 hover:shadow-xl focus:outline-none focus:ring-4 focus:ring-[#F4B044]/25 sm:h-12 min-[900px]:h-11 xl:h-12">
                            Login
                        </a>
                    @endauth
                @endif
            </div>
        </header>

        {{-- Hero --}}
        <section
            class="relative z-10 mx-auto grid w-full max-w-7xl flex-1 items-center gap-8 px-5 py-8 sm:px-6 sm:py-10 min-[900px]:min-h-0 min-[900px]:grid-cols-[minmax(0,1fr)_minmax(320px,420px)] min-[900px]:gap-6 min-[900px]:overflow-hidden min-[900px]:px-8 min-[900px]:py-4 xl:grid-cols-[minmax(0,1fr)_minmax(430px,520px)] xl:gap-10 xl:py-8">
            {{-- Left --}}
            <div class="min-w-0">
                <div
                    class="inline-flex max-w-full items-center gap-2 rounded-full border border-[#F4D3B0]/80 bg-white/75 px-4 py-2 shadow-sm backdrop-blur min-[900px]:py-1.5 xl:py-2">
                    <span class="h-2.5 w-2.5 shrink-0 rounded-full bg-[#F4B044]"></span>
                    <span class="truncate text-xs font-black uppercase tracking-[0.24em] text-[#6B3E12]">
                        Roti Maros Hikmah POS
                    </span>
                </div>

                <h1
                    class="mt-5 max-w-3xl text-3xl font-black leading-tight tracking-tight text-[#2B1A10] sm:text-4xl min-[900px]:mt-3 min-[900px]:text-[2.35rem] min-[900px]:leading-[1.08] xl:text-6xl">
                    Kelola Penjualan Roti dengan Sistem Kasir yang Lebih Rapi
                </h1>

                <p
                    class="mt-4 max-w-2xl text-sm leading-relaxed text-[#6B3E12] sm:text-base min-[900px]:mt-3 min-[900px]:text-sm xl:text-lg">
                    Sistem kasir internal untuk membantu pencatatan transaksi, pengelolaan data, dan aktivitas
                    operasional Roti Maros Hikmah menjadi lebih praktis, cepat, dan terstruktur.
                </p>

                <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:flex-wrap min-[900px]:mt-4 xl:mt-6">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ $dashboardUrl }}"
                                class="inline-flex items-center justify-center gap-2 rounded-2xl bg-[#1F444C] px-6 py-4 text-sm font-black text-white shadow-xl shadow-[#1F444C]/20 transition hover:-translate-y-0.5 hover:shadow-2xl focus:outline-none focus:ring-4 focus:ring-[#1F444C]/20 min-[900px]:py-3 xl:py-4">
                                Masuk Dashboard
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.4"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                </svg>
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                                class="inline-flex items-center justify-center gap-2 rounded-2xl bg-[#1F444C] px-6 py-4 text-sm font-black text-white shadow-xl shadow-[#1F444C]/20 transition hover:-translate-y-0.5 hover:shadow-2xl focus:outline-none focus:ring-4 focus:ring-[#1F444C]/20 min-[900px]:py-3 xl:py-4">
                                Login ke Sistem
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.4"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                </svg>
                            </a>
                        @endauth
                    @endif

                    <div
                        class="inline-flex items-center justify-center rounded-2xl border border-[#F4D3B0]/80 bg-white/75 px-6 py-4 text-sm font-bold text-[#6B3E12] shadow-sm backdrop-blur min-[900px]:py-3 xl:py-4">
                        Cepat • Aman • Terstruktur
                    </div>
                </div>

                <div class="mt-6 grid max-w-xl grid-cols-1 gap-3 sm:grid-cols-3 min-[900px]:mt-4 xl:mt-6">
                    <div
                        class="rounded-2xl border border-[#F4D3B0]/70 bg-white/75 p-4 shadow-sm backdrop-blur min-[900px]:p-3 xl:p-4">
                        <p class="text-xl font-black text-[#1F444C]">4</p>
                        <p class="mt-1 text-xs font-semibold text-[#6B3E12]">Role Akses</p>
                    </div>

                    <div
                        class="rounded-2xl border border-[#F4D3B0]/70 bg-white/75 p-4 shadow-sm backdrop-blur min-[900px]:p-3 xl:p-4">
                        <p class="text-xl font-black text-[#1F444C]">POS</p>
                        <p class="mt-1 text-xs font-semibold text-[#6B3E12]">Kasir Digital</p>
                    </div>

                    <div
                        class="rounded-2xl border border-[#F4D3B0]/70 bg-white/75 p-4 shadow-sm backdrop-blur min-[900px]:p-3 xl:p-4">
                        <p class="text-xl font-black text-[#1F444C]">24/7</p>
                        <p class="mt-1 text-xs font-semibold text-[#6B3E12]">Siap Pakai</p>
                    </div>
                </div>
            </div>

            {{-- Right --}}
            <div class="relative min-w-0 min-[900px]:max-h-[520px] xl:max-h-none">
                <div
                    class="relative overflow-hidden rounded-[2rem] border border-[#F4D3B0]/80 bg-white/85 p-4 shadow-[0_18px_55px_-35px_rgba(31,68,76,0.45)] backdrop-blur-xl min-[900px]:p-3 xl:p-5">
                    <div class="rounded-[1.5rem] bg-[#1F444C] p-5 text-white min-[900px]:p-4 xl:p-6">
                        <div class="flex items-center justify-between gap-4">
                            <div class="flex min-w-0 items-center gap-3">
                                <div
                                    class="flex h-12 w-12 shrink-0 items-center justify-center overflow-hidden rounded-2xl bg-white p-1 shadow-lg min-[900px]:h-11 min-[900px]:w-11 xl:h-14 xl:w-14">
                                    <img src="{{ asset('images/logo-roti-maros-hikmah.png') }}"
                                        alt="Logo Roti Maros Hikmah" class="h-full w-full object-contain">
                                </div>

                                <div class="min-w-0">
                                    <p class="truncate text-sm font-black text-[#F4B044]">
                                        Roti Maros Hikmah
                                    </p>
                                    <p class="mt-1 truncate text-xs font-medium text-[#F4D3B0]">
                                        Ringkasan Sistem Kasir
                                    </p>
                                </div>
                            </div>

                            <span
                                class="shrink-0 rounded-full bg-[#F4B044]/20 px-3 py-1 text-xs font-bold text-[#F4B044]">
                                Online
                            </span>
                        </div>

                        <div class="mt-5 grid gap-3 sm:grid-cols-2 min-[900px]:mt-4 min-[900px]:gap-2 xl:gap-3">
                            <div class="rounded-2xl bg-white/10 p-4 ring-1 ring-white/10 min-[900px]:p-3 xl:p-4">
                                <p class="text-xs font-semibold text-[#F4D3B0]">Transaksi</p>
                                <p class="mt-2 text-xl font-black text-white min-[900px]:text-lg xl:text-2xl">Cepat</p>
                            </div>

                            <div class="rounded-2xl bg-white/10 p-4 ring-1 ring-white/10 min-[900px]:p-3 xl:p-4">
                                <p class="text-xs font-semibold text-[#F4D3B0]">Laporan</p>
                                <p class="mt-2 text-xl font-black text-white min-[900px]:text-lg xl:text-2xl">Rapi</p>
                            </div>

                            <div class="rounded-2xl bg-white/10 p-4 ring-1 ring-white/10 min-[900px]:p-3 xl:p-4">
                                <p class="text-xs font-semibold text-[#F4D3B0]">Pengguna</p>
                                <p class="mt-2 text-xl font-black text-white min-[900px]:text-lg xl:text-2xl">
                                    Terkontrol</p>
                            </div>

                            <div class="rounded-2xl bg-[#F4B044] p-4 text-[#2B1A10] min-[900px]:p-3 xl:p-4">
                                <p class="text-xs font-black">Akses Sistem</p>
                                <p class="mt-2 text-xl font-black min-[900px]:text-lg xl:text-2xl">Login</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 grid gap-3 sm:grid-cols-3 min-[900px]:mt-3 min-[900px]:gap-2 xl:mt-4 xl:gap-3">
                        <div class="rounded-2xl border border-[#F4D3B0]/70 bg-[#F7F6F4] p-4 min-[900px]:p-3 xl:p-4">
                            <p class="text-xs font-bold text-[#6B3E12]">Kasir</p>
                            <div class="mt-3 h-2 rounded-full bg-[#F4D3B0]">
                                <div class="h-2 w-10/12 rounded-full bg-[#F4B044]"></div>
                            </div>
                        </div>

                        <div class="rounded-2xl border border-[#F4D3B0]/70 bg-[#F7F6F4] p-4 min-[900px]:p-3 xl:p-4">
                            <p class="text-xs font-bold text-[#6B3E12]">Admin</p>
                            <div class="mt-3 h-2 rounded-full bg-[#F4D3B0]">
                                <div class="h-2 w-8/12 rounded-full bg-[#1F444C]"></div>
                            </div>
                        </div>

                        <div class="rounded-2xl border border-[#F4D3B0]/70 bg-[#F7F6F4] p-4 min-[900px]:p-3 xl:p-4">
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
        <footer class="relative z-10 shrink-0 border-t border-[#F4D3B0]/70 bg-white/60 backdrop-blur-xl">
            <div
                class="mx-auto flex w-full max-w-7xl flex-col items-center justify-between gap-1 px-5 py-3 text-center text-xs font-medium text-[#6B3E12] sm:flex-row sm:px-6 min-[900px]:px-8 min-[900px]:py-2 xl:py-3">
                <p>© {{ date('Y') }} Roti Maros Hikmah. All rights reserved.</p>
                <p>Sistem Kasir Internal</p>
            </div>
        </footer>
    </main>
</body>

</html>

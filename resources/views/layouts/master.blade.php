<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
        @hasSection('page_title')
            @yield('page_title') - Roti Maros Hikmah
        @else
            {{ $title ?? 'Roti Maros Hikmah' }}
        @endif
    </title>

    <link rel="icon" type="image/png" href="{{ asset('images/icons/logo-rounded.png') }}">
    <link rel="manifest" href="{{ asset('manifest.webmanifest') }}?v=2">

    <meta name="theme-color" content="#1F444C">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="application-name" content="Roti Maros Hikmah">

    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="RMHKM POS">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    <link rel="apple-touch-icon" href="{{ asset('images/icons/logo-rounded.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] {
            display: none !important;
        }

        .app-viewport {
            height: 100vh;
            height: 100dvh;
        }

        .layout-desktop-sidebar {
            display: none;
        }

        @media (min-width: 1280px) and (hover: hover) and (pointer: fine) {
            .layout-desktop-sidebar {
                display: block;
            }

            .layout-drawer-sidebar,
            .layout-drawer-overlay,
            .layout-sidebar-toggle {
                display: none !important;
            }
        }

        .app-scrollbar::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        .app-scrollbar::-webkit-scrollbar-track {
            background: rgba(244, 211, 176, 0.35);
            border-radius: 999px;
        }

        .app-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(107, 62, 18, 0.25);
            border-radius: 999px;
        }

        .app-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(107, 62, 18, 0.45);
        }

        .sidebar-scroll::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar-scroll::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.08);
            border-radius: 999px;
        }

        .sidebar-scroll::-webkit-scrollbar-thumb {
            background: rgba(244, 176, 68, 0.65);
            border-radius: 999px;
        }

        .sidebar-scroll::-webkit-scrollbar-thumb:hover {
            background: rgba(244, 176, 68, 0.9);
        }
    </style>
</head>

<body class="app-viewport overflow-hidden bg-[#F7F6F4] text-[#2B1A10] antialiased">
    @php
        $user = auth()->user();
        $role = $user?->role;

        $sidebarView = match ($role) {
            'kasir' => 'layouts.partials.sidebar-cashier',
            'admin' => 'layouts.partials.sidebar-admin',
            'keuangan' => 'layouts.partials.sidebar-finance',
            'super_admin' => 'layouts.partials.sidebar-super-admin',
            default => null,
        };

        $toastType = null;
        $toastMessage = null;

        if (session('success')) {
            $toastType = 'success';
            $toastMessage = session('success');
        } elseif (session('error')) {
            $toastType = 'error';
            $toastMessage = session('error');
        } elseif (session('warning')) {
            $toastType = 'warning';
            $toastMessage = session('warning');
        } elseif (session('info')) {
            $toastType = 'info';
            $toastMessage = session('info');
        } elseif (session('status')) {
            $toastType = 'success';
            $toastMessage = session('status');
        } elseif ($errors->any()) {
            $toastType = 'error';
            $toastMessage = $errors->first();
        }

        $toastStyle = match ($toastType) {
            'success' => [
                'border' => 'border-[#1F444C]/20',
                'iconBg' => 'bg-[#1F444C]/10',
                'iconText' => 'text-[#1F444C]',
                'title' => 'Berhasil',
                'titleText' => 'text-[#1F444C]',
                'bar' => 'bg-[#1F444C]',
            ],
            'error' => [
                'border' => 'border-[#A92A35]/20',
                'iconBg' => 'bg-[#A92A35]/10',
                'iconText' => 'text-[#A92A35]',
                'title' => 'Gagal',
                'titleText' => 'text-[#A92A35]',
                'bar' => 'bg-[#A92A35]',
            ],
            'warning' => [
                'border' => 'border-[#F4B044]/40',
                'iconBg' => 'bg-[#F4B044]/20',
                'iconText' => 'text-[#6B3E12]',
                'title' => 'Perhatian',
                'titleText' => 'text-[#6B3E12]',
                'bar' => 'bg-[#F4B044]',
            ],
            'info' => [
                'border' => 'border-[#F4D3B0]/70',
                'iconBg' => 'bg-[#F4B044]/20',
                'iconText' => 'text-[#6B3E12]',
                'title' => 'Informasi',
                'titleText' => 'text-[#6B3E12]',
                'bar' => 'bg-[#F4B044]',
            ],
            default => null,
        };
    @endphp

    <div x-data="{ sidebarOpen: false }" class="app-viewport overflow-hidden">
        {{-- Toast --}}
        @if ($toastMessage && $toastStyle)
            <div x-data="{ toastOpen: true }" x-show="toastOpen" x-cloak
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 scale-95"
                class="fixed right-4 top-4 z-[9999] w-[calc(100%-2rem)] max-w-md sm:right-6 sm:top-6">
                <div
                    class="relative overflow-hidden rounded-[1.4rem] border {{ $toastStyle['border'] }} bg-white shadow-[0_30px_90px_-35px_rgba(31,68,76,0.75)]">
                    <div class="absolute left-0 top-0 h-full w-1.5 {{ $toastStyle['bar'] }}"></div>

                    <div class="flex gap-4 p-5 pl-6">
                        <div
                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl {{ $toastStyle['iconBg'] }} {{ $toastStyle['iconText'] }}">
                            @if ($toastType === 'success')
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2.5"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                            @elseif ($toastType === 'error')
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2.5"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 9v3.75m0 3.75h.008v.008H12V16.5z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M10.29 3.86L1.82 18a1.5 1.5 0 001.29 2.25h17.78A1.5 1.5 0 0022.18 18L13.71 3.86a1.5 1.5 0 00-2.42 0z" />
                                </svg>
                            @else
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2.5"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 8.25h.008v.008H12V8.25z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            @endif
                        </div>

                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-black {{ $toastStyle['titleText'] }}">
                                {{ $toastStyle['title'] }}
                            </p>

                            <p class="mt-1 text-sm font-semibold leading-relaxed text-[#6B3E12]">
                                {{ $toastMessage }}
                            </p>
                        </div>

                        <button type="button" @click="toastOpen = false"
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl text-[#6B3E12]/70 transition hover:bg-[#F7F6F4] hover:text-[#2B1A10] focus:outline-none focus:ring-4 focus:ring-[#F4B044]/20">
                            <span class="sr-only">Tutup notifikasi</span>
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.5"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        @endif

        {{-- Mobile / Tablet Overlay --}}
        <div x-show="sidebarOpen" x-cloak x-transition.opacity
            class="layout-drawer-overlay fixed inset-0 z-40 bg-[#1F444C]/55 backdrop-blur-sm"
            @click="sidebarOpen = false">
        </div>

        {{-- Mobile / Tablet Sidebar --}}
        <aside x-show="sidebarOpen" x-cloak x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="-translate-x-full opacity-0" x-transition:enter-end="translate-x-0 opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="translate-x-0 opacity-100"
            x-transition:leave-end="-translate-x-full opacity-0"
            class="layout-drawer-sidebar fixed inset-y-0 left-0 z-50"
            @click="if ($event.target.closest('a')) sidebarOpen = false">
            @if ($sidebarView)
                @include($sidebarView)
            @else
                <div
                    class="flex h-full w-72 items-center justify-center bg-[#1F444C] p-6 text-center text-sm font-bold text-white">
                    Role tidak dikenali.
                </div>
            @endif
        </aside>

        <div class="flex app-viewport overflow-hidden">
            {{-- Desktop Sidebar --}}
            <div class="layout-desktop-sidebar h-full shrink-0">
                @if ($sidebarView)
                    @include($sidebarView)
                @else
                    <div
                        class="flex h-full w-72 items-center justify-center bg-[#1F444C] p-6 text-center text-sm font-bold text-white">
                        Role tidak dikenali.
                    </div>
                @endif
            </div>

            {{-- Main Area --}}
            <div class="flex h-full min-w-0 flex-1 flex-col overflow-hidden">
                {{-- Topbar --}}
                <header class="z-30 shrink-0 border-b border-[#F4D3B0]/70 bg-white/90 backdrop-blur-xl">
                    <div class="flex h-16 items-center justify-between gap-4 px-4 sm:h-[4.5rem] sm:px-6 lg:px-8">
                        <div class="flex min-w-0 items-center gap-4">
                            <button type="button"
                                class="layout-sidebar-toggle inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border border-[#F4D3B0] bg-[#F7F6F4] text-[#1F444C] shadow-sm transition hover:bg-[#F4D3B0]/60 focus:outline-none focus:ring-4 focus:ring-[#F4B044]/20"
                                @click="sidebarOpen = true">
                                <span class="sr-only">Buka sidebar</span>
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            </button>

                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="h-2.5 w-2.5 shrink-0 rounded-full bg-[#F4B044]"></span>
                                    <p
                                        class="truncate text-[11px] font-semibold uppercase tracking-[0.22em] text-[#6B3E12] sm:text-xs">
                                        Roti Maros Hikmah
                                    </p>
                                </div>

                                <h1 class="mt-1 truncate text-lg font-black tracking-tight text-[#2B1A10] sm:text-xl">
                                    @yield('page_title', 'Dashboard')
                                </h1>
                            </div>
                        </div>

                        {{-- Profile --}}
                        <div x-data="{ profileOpen: false }" class="relative shrink-0">
                            <button type="button" @click="profileOpen = !profileOpen"
                                class="group flex items-center gap-2 rounded-full border border-[#F4D3B0]/70 bg-[#F7F6F4] p-1.5 pr-2 shadow-sm transition hover:border-[#F4B044]/70 hover:bg-white hover:shadow-md focus:outline-none focus:ring-4 focus:ring-[#F4B044]/20 sm:pr-3">
                                <div
                                    class="relative flex h-10 w-10 items-center justify-center rounded-full bg-[#1F444C] text-sm font-black text-[#F4B044] shadow-sm">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                    <span
                                        class="absolute bottom-0 right-0 h-3 w-3 rounded-full border-2 border-[#F7F6F4] bg-[#F4B044]"></span>
                                </div>

                                <div class="hidden min-w-0 text-left sm:block">
                                    <p class="max-w-32 truncate text-sm font-black leading-tight text-[#2B1A10]">
                                        {{ $user->name }}
                                    </p>
                                    <p class="mt-0.5 truncate text-xs font-semibold capitalize text-[#6B3E12]">
                                        {{ str_replace('_', ' ', $user->role) }}
                                    </p>
                                </div>

                                <span
                                    class="flex h-8 w-8 items-center justify-center rounded-full bg-white text-[#6B3E12] shadow-sm transition group-hover:bg-[#F4B044]/20">
                                    <svg class="h-4 w-4 transition duration-200"
                                        :class="profileOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                                        stroke-width="2.4" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </span>
                            </button>

                            <div x-show="profileOpen" x-cloak @click.outside="profileOpen = false"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 scale-95 translate-y-3"
                                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                x-transition:leave-end="opacity-0 scale-95 translate-y-3"
                                class="absolute right-0 z-50 mt-4 w-[calc(100vw-2rem)] max-w-80 overflow-hidden rounded-[1.6rem] border border-[#F4D3B0]/80 bg-white shadow-[0_30px_80px_-30px_rgba(31,68,76,0.55)]">
                                <div class="relative overflow-hidden bg-[#1F444C] px-5 py-5">
                                    <div class="absolute -right-8 -top-10 h-28 w-28 rounded-full bg-[#F4B044]/20">
                                    </div>
                                    <div class="absolute -bottom-12 -left-10 h-32 w-32 rounded-full bg-white/10"></div>

                                    <div class="relative flex items-center gap-4">
                                        <div
                                            class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-[#F4B044] text-lg font-black text-[#2B1A10] shadow-lg shadow-black/10 ring-4 ring-white/10">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>

                                        <div class="min-w-0">
                                            <p class="truncate text-base font-black text-white">
                                                {{ $user->name }}
                                            </p>

                                            <div
                                                class="mt-2 inline-flex items-center rounded-full bg-white/10 px-3 py-1">
                                                <span class="mr-2 h-2 w-2 rounded-full bg-[#F4B044]"></span>
                                                <span class="text-xs font-bold capitalize text-[#F4D3B0]">
                                                    {{ str_replace('_', ' ', $user->role) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-2 bg-gradient-to-br from-white to-[#F7F6F4] p-3">
                                    <a href="{{ route('profile.edit') }}"
                                        class="group flex items-center gap-4 rounded-2xl px-4 py-3.5 text-sm font-bold text-[#2B1A10] transition hover:bg-[#F4B044]/15">
                                        <span
                                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-[#F4B044]/20 text-[#6B3E12] transition group-hover:bg-[#F4B044] group-hover:text-[#2B1A10]">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor"
                                                stroke-width="2.2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M4.5 20.25a8.25 8.25 0 0115 0" />
                                            </svg>
                                        </span>

                                        <span class="min-w-0 flex-1">
                                            Profil Saya
                                            <span class="mt-0.5 block text-xs font-medium text-[#6B3E12]">
                                                Lihat dan ubah data akun
                                            </span>
                                        </span>
                                    </a>

                                    <div class="h-px bg-[#F4D3B0]/70"></div>

                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf

                                        <button type="submit"
                                            class="group flex w-full items-center gap-4 rounded-2xl px-4 py-3.5 text-left text-sm font-bold text-[#A92A35] transition hover:bg-[#A92A35]/10">
                                            <span
                                                class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-[#A92A35]/10 text-[#A92A35] transition group-hover:bg-[#A92A35] group-hover:text-white">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor"
                                                    stroke-width="2.2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M17 16l4-4m0 0l-4-4m4 4H9" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M13 5H6a2 2 0 00-2 2v10a2 2 0 002 2h7" />
                                                </svg>
                                            </span>

                                            <span class="min-w-0 flex-1">
                                                Logout
                                                <span class="mt-0.5 block text-xs font-medium text-[#A92A35]/70">
                                                    Keluar dari sistem
                                                </span>
                                            </span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>

                {{-- Content --}}
                <main
                    class="app-scrollbar min-h-0 flex-1 overflow-y-auto bg-gradient-to-br from-[#F7F6F4] via-[#F7F6F4] to-[#F4D3B0]/35">
                    <div
                        class="mx-auto w-full max-w-[1500px] px-4 py-5 pb-[calc(1.25rem+env(safe-area-inset-bottom))] sm:px-6 sm:py-6 lg:px-8">
                        @yield('content')
                    </div>
                </main>
            </div>
        </div>
    </div>
</body>

</html>

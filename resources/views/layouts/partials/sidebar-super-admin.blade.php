@php
    $superAdminMenus = [
        [
            'label' => 'Dashboard',
            'route' => 'super-admin.dashboard',
            'active' => 'super-admin.dashboard',
            'icon' => 'home',
        ],
        [
            'label' => 'Pengguna',
            'route' => 'super-admin.users.index',
            'active' => 'super-admin.users.*',
            'icon' => 'users',
        ],
    ];
@endphp

<aside class="flex h-full min-h-0 w-72 flex-col overflow-hidden bg-[#1F444C] text-white shadow-2xl shadow-[#1F444C]/20">
    {{-- Brand --}}
    <div class="flex h-20 shrink-0 items-center border-b border-white/10 px-5">
        <a href="{{ route('super-admin.dashboard') }}" class="flex min-w-0 items-center gap-4">
            <div
                class="flex h-14 w-14 shrink-0 items-center justify-center overflow-hidden rounded-2xl bg-white p-1 shadow-lg ring-1 ring-white/15">
                <img src="{{ asset('images/logo-roti-maros-hikmah.png') }}" alt="Logo Roti Maros Hikmah"
                    class="h-full w-full object-contain">
            </div>

            <div class="min-w-0">
                <h2 class="truncate text-lg font-black leading-none text-[#F4B044]">
                    Roti Maros
                </h2>

                <p class="mt-1.5 truncate text-2xl font-black leading-none tracking-[0.12em] text-[#F4B044]">
                    Hikmah
                </p>
            </div>
        </a>
    </div>

    {{-- Navigation --}}
    <nav class="sidebar-scroll min-h-0 flex-1 space-y-1 overflow-y-auto overscroll-contain px-4 py-5 pr-3">
        @foreach ($superAdminMenus as $menu)
            @php
                $isActive = request()->routeIs($menu['active']);
            @endphp

            <a href="{{ route($menu['route']) }}"
                class="{{ $isActive
                    ? 'bg-[#F4B044] text-[#2B1A10] shadow-lg shadow-[#F4B044]/20'
                    : 'text-[#F7F6F4] hover:bg-white/10 hover:text-white' }}
                group flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold transition">
                <span
                    class="{{ $isActive ? 'bg-[#2B1A10]/10 text-[#2B1A10]' : 'bg-white/10 text-white group-hover:bg-white/15' }}
                    flex h-9 w-9 shrink-0 items-center justify-center rounded-xl transition">
                    @switch($menu['icon'])
                        @case('home')
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-9 9 9M5 10v10h5v-6h4v6h5V10" />
                            </svg>
                        @break

                        @case('users')
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1m8-4a4 4 0 100-8 4 4 0 000 8zM9 10a4 4 0 100-8 4 4 0 000 8z" />
                            </svg>
                        @break
                    @endswitch
                </span>

                <span class="min-w-0 truncate">
                    {{ $menu['label'] }}
                </span>
            </a>
        @endforeach
    </nav>

    {{-- Footer --}}
    <div class="shrink-0 border-t border-white/10 px-5 py-3 pb-[calc(0.75rem+env(safe-area-inset-bottom))]">
        <p class="text-center text-xs leading-relaxed text-[#F4D3B0]/85">
            © {{ date('Y') }} Roti Maros Hikmah.
            <br>
            All rights reserved.
        </p>
    </div>
</aside>

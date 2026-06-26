<aside class="flex h-full min-h-0 w-72 flex-col overflow-hidden bg-[#1F444C] text-white shadow-2xl shadow-[#1F444C]/20">
    {{-- Brand --}}
    <div class="flex h-20 shrink-0 items-center border-b border-white/10 px-6">
        <div class="flex items-center gap-4">
            <div
                class="flex h-16 w-16 items-center justify-center overflow-hidden rounded-2xl p-1 shadow-lg bg-[#ffffff]">
                <img src="{{ asset('images/logo-roti-maros-hikmah.png') }}" alt="Logo Roti Maros Hikmah"
                    class="h-full w-full object-contain">
            </div>

            <div>
                <h2 class="text-lg font-black leading-tight text-[#F4B044]">
                    Roti Maros Hikmah
                </h2>
                <p class="mt-1 text-xs font-semibold text-[#F4D3B0]/80">
                    Panel Kasir
                </p>
            </div>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="sidebar-scroll min-h-0 flex-1 space-y-1 overflow-y-auto overscroll-contain px-4 py-6">
        {{-- Dashboard Kasir --}}
        <a href="{{ route('cashier.dashboard') }}"
            class="{{ request()->routeIs('cashier.dashboard')
                ? 'bg-[#F4B044] text-[#2B1A10] shadow-lg shadow-[#F4B044]/20'
                : 'text-[#F7F6F4] hover:bg-white/10 hover:text-white' }}
                group flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold transition">
            <span
                class="{{ request()->routeIs('cashier.dashboard') ? 'bg-[#2B1A10]/10' : 'bg-white/10 group-hover:bg-white/15' }}
                flex h-9 w-9 items-center justify-center rounded-xl transition">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-9 9 9M5 10v10h5v-6h4v6h5V10" />
                </svg>
            </span>

            <span>Dashboard Kasir</span>
        </a>

        {{-- POS --}}
        <a href="{{ route('cashier.pos.index') }}"
            class="{{ request()->routeIs('cashier.pos.*')
                ? 'bg-[#F4B044] text-[#2B1A10] shadow-lg shadow-[#F4B044]/20'
                : 'text-[#F7F6F4] hover:bg-white/10 hover:text-white' }}
                group flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold transition">
            <span
                class="{{ request()->routeIs('cashier.pos.*') ? 'bg-[#2B1A10]/10' : 'bg-white/10 group-hover:bg-white/15' }}
                flex h-9 w-9 items-center justify-center rounded-xl transition">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M2.25 8.25h19.5M4.5 8.25v10.5A2.25 2.25 0 006.75 21h10.5a2.25 2.25 0 002.25-2.25V8.25M7.5 8.25V6a4.5 4.5 0 019 0v2.25" />
                </svg>
            </span>

            <span>POS</span>
        </a>

        {{-- Riwayat Transaksi Saya --}}
        <a href="{{ route('cashier.transactions.index') }}"
            class="{{ request()->routeIs('cashier.transactions.*')
                ? 'bg-[#F4B044] text-[#2B1A10] shadow-lg shadow-[#F4B044]/20'
                : 'text-[#F7F6F4] hover:bg-white/10 hover:text-white' }}
                group flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold transition">
            <span
                class="{{ request()->routeIs('cashier.transactions.*') ? 'bg-[#2B1A10]/10' : 'bg-white/10 group-hover:bg-white/15' }}
                flex h-9 w-9 items-center justify-center rounded-xl transition">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 7.5h6M9 12h6m-6 4.5h3M6.75 3.75h10.5A1.75 1.75 0 0119 5.5v15.25l-3-1.5-3 1.5-3-1.5-3 1.5V5.5A1.75 1.75 0 016.75 3.75z" />
                </svg>
            </span>

            <span>Riwayat Transaksi Saya</span>
        </a>

        {{-- Pengeluaran --}}
        <a href="{{ route('cashier.expenses.index') }}"
            class="{{ request()->routeIs('cashier.expenses.*')
                ? 'bg-[#F4B044] text-[#2B1A10] shadow-lg shadow-[#F4B044]/20'
                : 'text-[#F7F6F4] hover:bg-white/10 hover:text-white' }}
                group flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold transition">
            <span
                class="{{ request()->routeIs('cashier.expenses.*') ? 'bg-[#2B1A10]/10' : 'bg-white/10 group-hover:bg-white/15' }}
                flex h-9 w-9 items-center justify-center rounded-xl transition">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15.75h7.5M8.25 8.25h7.5" />
                </svg>
            </span>

            <span>Pengeluaran</span>
        </a>

        {{-- Shift --}}
        <a href="{{ route('cashier.shifts.index') }}"
            class="{{ request()->routeIs('cashier.shifts.*')
                ? 'bg-[#F4B044] text-[#2B1A10] shadow-lg shadow-[#F4B044]/20'
                : 'text-[#F7F6F4] hover:bg-white/10 hover:text-white' }}
                group flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold transition">
            <span
                class="{{ request()->routeIs('cashier.shifts.*') ? 'bg-[#2B1A10]/10' : 'bg-white/10 group-hover:bg-white/15' }}
                flex h-9 w-9 items-center justify-center rounded-xl transition">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 6v6l4 2M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </span>

            <span>Shift</span>
        </a>
    </nav>

    {{-- Footer --}}
    <div class="shrink-0 border-t border-white/10 px-6 py-3 pb-[calc(0.75rem+env(safe-area-inset-bottom))]">
        <p class="text-center text-xs leading-relaxed text-[#F4D3B0]/85">
            © {{ date('Y') }} Roti Maros Hikmah.
            <br>
            All rights reserved.
        </p>
    </div>
</aside>

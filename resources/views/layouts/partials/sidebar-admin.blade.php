<aside
    class="flex h-screen min-h-0 w-72 flex-col overflow-hidden bg-[#1F444C] text-white shadow-2xl shadow-[#1F444C]/20">
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
            </div>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="sidebar-scroll min-h-0 flex-1 space-y-1 overflow-y-auto overscroll-contain px-4 py-6 pr-3">
        {{-- Dashboard --}}
        <a href="{{ route('admin.dashboard') }}"
            class="{{ request()->routeIs('admin.dashboard')
                ? 'bg-[#F4B044] text-[#2B1A10] shadow-lg shadow-[#F4B044]/20'
                : 'text-[#F7F6F4] hover:bg-white/10 hover:text-white' }}
        group flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold transition">
            <span
                class="{{ request()->routeIs('admin.dashboard') ? 'bg-[#2B1A10]/10' : 'bg-white/10 group-hover:bg-white/15' }}
        flex h-9 w-9 items-center justify-center rounded-xl transition">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-9 9 9M5 10v10h5v-6h4v6h5V10" />
                </svg>
            </span>

            <span>Dashboard</span>
        </a>

        {{-- Kategori Produk --}}
        <a href="{{ route('admin.categories.index') }}"
            class="{{ request()->routeIs('admin.categories.*')
                ? 'bg-[#F4B044] text-[#2B1A10] shadow-lg shadow-[#F4B044]/20'
                : 'text-[#F7F6F4] hover:bg-white/10 hover:text-white' }}
        group flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold transition">
            <span
                class="{{ request()->routeIs('admin.categories.*') ? 'bg-[#2B1A10]/10' : 'bg-white/10 group-hover:bg-white/15' }}
        flex h-9 w-9 items-center justify-center rounded-xl transition">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M4 12h16M4 17h16" />
                </svg>
            </span>

            <span>Kategori Produk</span>
        </a>

        {{-- Produk --}}
        <a href="{{ route('admin.products.index') }}"
            class="{{ request()->routeIs('admin.products.*')
                ? 'bg-[#F4B044] text-[#2B1A10] shadow-lg shadow-[#F4B044]/20'
                : 'text-[#F7F6F4] hover:bg-white/10 hover:text-white' }}
        group flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold transition">
            <span
                class="{{ request()->routeIs('admin.products.*') ? 'bg-[#2B1A10]/10' : 'bg-white/10 group-hover:bg-white/15' }}
        flex h-9 w-9 items-center justify-center rounded-xl transition">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M20 7.5L12 3 4 7.5m16 0L12 12m8-4.5v9L12 21m0-9L4 7.5m8 4.5v9M4 7.5v9L12 21" />
                </svg>
            </span>

            <span>Produk</span>
        </a>

        {{-- Promo --}}
        <a href="{{ route('admin.promos.index') }}"
            class="{{ request()->routeIs('admin.promos.*')
                ? 'bg-[#F4B044] text-[#2B1A10] shadow-lg shadow-[#F4B044]/20'
                : 'text-[#F7F6F4] hover:bg-white/10 hover:text-white' }}
    group flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold transition">
            <span
                class="{{ request()->routeIs('admin.promos.*') ? 'bg-[#2B1A10]/10' : 'bg-white/10 group-hover:bg-white/15' }}
    flex h-9 w-9 items-center justify-center rounded-xl transition">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.169.659 1.591l8.182 8.182a2.25 2.25 0 003.182 0l4.318-4.318a2.25 2.25 0 000-3.182L11.159 3.659A2.25 2.25 0 009.568 3z" />
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M6.75 6.75h.008v.008H6.75V6.75zM15 9l-6 6" />
                </svg>
            </span>

            <span>Promo</span>
        </a>

        {{-- Pajak --}}
        <a href="{{ route('admin.taxes.index') }}"
            class="{{ request()->routeIs('admin.taxes.*')
                ? 'bg-[#F4B044] text-[#2B1A10] shadow-lg shadow-[#F4B044]/20'
                : 'text-[#F7F6F4] hover:bg-white/10 hover:text-white' }}
    group flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold transition">
            <span
                class="{{ request()->routeIs('admin.taxes.*') ? 'bg-[#2B1A10]/10' : 'bg-white/10 group-hover:bg-white/15' }}
        flex h-9 w-9 items-center justify-center rounded-xl transition">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 7.5h6m-6 4.5h6m-6 4.5h3M6.75 3.75h10.5A1.75 1.75 0 0119 5.5v15.25l-3-1.5-3 1.5-3-1.5-3 1.5V5.5A1.75 1.75 0 016.75 3.75z" />
                </svg>
            </span>

            <span>Pajak</span>
        </a>

        {{-- Terminal Kasir --}}
        <a href="{{ route('admin.pos-terminals.index') }}"
            class="{{ request()->routeIs('admin.pos-terminals.*')
                ? 'bg-[#F4B044] text-[#2B1A10] shadow-lg shadow-[#F4B044]/20'
                : 'text-[#F7F6F4] hover:bg-white/10 hover:text-white' }}
    group flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold transition">
            <span
                class="{{ request()->routeIs('admin.pos-terminals.*') ? 'bg-[#2B1A10]/10' : 'bg-white/10 group-hover:bg-white/15' }}
        flex h-9 w-9 items-center justify-center rounded-xl transition">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M6 20h12M8 20v-3.5A2.5 2.5 0 0110.5 14h3A2.5 2.5 0 0116 16.5V20M6.75 4h10.5A1.75 1.75 0 0119 5.75v5.5A1.75 1.75 0 0117.25 13H6.75A1.75 1.75 0 015 11.25v-5.5A1.75 1.75 0 016.75 4z" />
                </svg>
            </span>

            <span>Terminal Kasir</span>
        </a>
        {{-- Manajemen Kasir --}}
        <a href="{{ route('admin.cashiers.index') }}"
            class="{{ request()->routeIs('admin.cashiers.*')
                ? 'bg-[#F4B044] text-[#2B1A10] shadow-lg shadow-[#F4B044]/20'
                : 'text-[#F7F6F4] hover:bg-white/10 hover:text-white' }}
    group flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold transition">
            <span
                class="{{ request()->routeIs('admin.cashiers.*') ? 'bg-[#2B1A10]/10' : 'bg-white/10 group-hover:bg-white/15' }}
        flex h-9 w-9 items-center justify-center rounded-xl transition">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.25a8.25 8.25 0 0115 0M18 8.25h3m-1.5-1.5v3" />
                </svg>
            </span>

            <span>Manajemen Kasir</span>
        </a>

        {{-- Riwayat Transaksi --}}
        <a href="{{ route('admin.transactions.index') }}"
            class="{{ request()->routeIs('admin.transactions.*')
                ? 'bg-[#F4B044] text-[#2B1A10] shadow-lg shadow-[#F4B044]/20'
                : 'text-[#F7F6F4] hover:bg-white/10 hover:text-white' }}
    group flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold transition">
            <span
                class="flex h-9 w-9 items-center justify-center rounded-xl bg-white/10 transition group-hover:bg-white/15">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 14.25h6M9 10.5h6M7.5 3.75h9A1.5 1.5 0 0118 5.25v15l-3-1.5-3 1.5-3-1.5-3 1.5v-15A1.5 1.5 0 017.5 3.75z" />
                </svg>
            </span>

            <span>Riwayat Transaksi</span>
        </a>

        {{-- Analisis Pendapatan --}}
        <a href="{{ route('admin.income-analysis.index') }}"
            class="{{ request()->routeIs('admin.income-analysis.*')
                ? 'bg-[#F4B044] text-[#2B1A10] shadow-lg shadow-[#F4B044]/20'
                : 'text-[#F7F6F4] hover:bg-white/10 hover:text-white' }}
    group flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold transition">
            <span
                class="flex h-9 w-9 items-center justify-center rounded-xl bg-white/10 transition group-hover:bg-white/15">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M4 19.5h16M7 16V9m5 7V5m5 11v-4M5.75 19.5h12.5A1.75 1.75 0 0020 17.75V5.75A1.75 1.75 0 0018.25 4H5.75A1.75 1.75 0 004 5.75v12A1.75 1.75 0 005.75 19.5z" />
                </svg>
            </span>

            <span>Analisis Pendapatan</span>
        </a>
    </nav>

    {{-- Footer --}}
    <div class="shrink-0 border-t border-white/10 px-6 py-2">
        <p class="text-center text-xs leading-relaxed text-[#F4D3B0]/85">
            © {{ date('Y') }} Roti Maros Hikmah.
            <br>
            All rights reserved.
        </p>
    </div>
</aside>

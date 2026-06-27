@php
    $adminMenus = [
        [
            'label' => 'Dashboard',
            'route' => 'admin.dashboard',
            'active' => 'admin.dashboard',
            'icon' => 'home',
        ],
        [
            'label' => 'Kategori Produk',
            'route' => 'admin.categories.index',
            'active' => 'admin.categories.*',
            'icon' => 'list',
        ],
        [
            'label' => 'Produk',
            'route' => 'admin.products.index',
            'active' => 'admin.products.*',
            'icon' => 'box',
        ],
        [
            'label' => 'Stok',
            'route' => 'admin.stocks.index',
            'active' => 'admin.stocks.*',
            'icon' => 'stock',
        ],
        [
            'label' => 'Promo',
            'route' => 'admin.promos.index',
            'active' => 'admin.promos.*',
            'icon' => 'tag',
        ],
        [
            'label' => 'Pajak',
            'route' => 'admin.taxes.index',
            'active' => 'admin.taxes.*',
            'icon' => 'receipt',
        ],
        [
            'label' => 'Terminal Kasir',
            'route' => 'admin.pos-terminals.index',
            'active' => 'admin.pos-terminals.*',
            'icon' => 'terminal',
        ],
        [
            'label' => 'Manajemen Kasir',
            'route' => 'admin.cashiers.index',
            'active' => 'admin.cashiers.*',
            'icon' => 'user-plus',
        ],
        [
            'label' => 'Riwayat Transaksi',
            'route' => 'admin.transactions.index',
            'active' => 'admin.transactions.*',
            'icon' => 'history',
        ],
        [
            'label' => 'Analisis Pendapatan',
            'route' => 'admin.income-analysis.index',
            'active' => 'admin.income-analysis.*',
            'icon' => 'chart',
        ],
    ];
@endphp

<aside class="flex h-full min-h-0 w-72 flex-col overflow-hidden bg-[#1F444C] text-white shadow-2xl shadow-[#1F444C]/20">
    {{-- Brand --}}
    <div class="flex h-20 shrink-0 items-center border-b border-white/10 px-5">
        <a href="{{ route('admin.dashboard') }}" class="flex min-w-0 items-center gap-4">
            <div
                class="flex h-14 w-14 shrink-0 items-center justify-center overflow-hidden rounded-2xl bg-white p-1 shadow-lg ring-1 ring-white/15">
                <img src="{{ asset('images/logo-roti-maros-hikmah.png') }}" alt="Logo Roti Maros Hikmah"
                    class="h-full w-full object-contain">
            </div>

            <div class="min-w-0">
                <h2 class="truncate text-lg font-black leading-none text-[#F4B044]">
                    Roti Maros
                </h2>

                <p class="mt-1.5 truncate text-2xl font-black leading-none tracking-[0.06em] text-[#F4B044]">
                    Hikmah
                </p>
            </div>
        </a>
    </div>

    {{-- Navigation --}}
    <nav class="sidebar-scroll min-h-0 flex-1 space-y-1 overflow-y-auto overscroll-contain px-4 py-5 pr-3">
        @foreach ($adminMenus as $menu)
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

                        @case('list')
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M4 12h16M4 17h16" />
                            </svg>
                        @break

                        @case('box')
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M20 7.5L12 3 4 7.5m16 0L12 12m8-4.5v9L12 21m0-9L4 7.5m8 4.5v9M4 7.5v9L12 21" />
                            </svg>
                        @break

                        @case('stock')
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 7.5L12 3l9 4.5M3 7.5l9 4.5m-9-4.5v9L12 21m0-9l9-4.5m-9 4.5v9m9-13.5v9L12 21" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 10.25v4.5m9-7.5v4.5" />
                            </svg>
                        @break

                        @case('tag')
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.169.659 1.591l8.182 8.182a2.25 2.25 0 003.182 0l4.318-4.318a2.25 2.25 0 000-3.182L11.159 3.659A2.25 2.25 0 009.568 3z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M6.75 6.75h.008v.008H6.75V6.75zM15 9l-6 6" />
                            </svg>
                        @break

                        @case('receipt')
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 7.5h6m-6 4.5h6m-6 4.5h3M6.75 3.75h10.5A1.75 1.75 0 0119 5.5v15.25l-3-1.5-3 1.5-3-1.5-3 1.5V5.5A1.75 1.75 0 016.75 3.75z" />
                            </svg>
                        @break

                        @case('terminal')
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M6 20h12M8 20v-3.5A2.5 2.5 0 0110.5 14h3A2.5 2.5 0 0116 16.5V20M6.75 4h10.5A1.75 1.75 0 0119 5.75v5.5A1.75 1.75 0 0117.25 13H6.75A1.75 1.75 0 015 11.25v-5.5A1.75 1.75 0 016.75 4z" />
                            </svg>
                        @break

                        @case('user-plus')
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.25a8.25 8.25 0 0115 0M18 8.25h3m-1.5-1.5v3" />
                            </svg>
                        @break

                        @case('history')
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 14.25h6M9 10.5h6M7.5 3.75h9A1.5 1.5 0 0118 5.25v15l-3-1.5-3 1.5-3-1.5-3 1.5v-15A1.5 1.5 0 017.5 3.75z" />
                            </svg>
                        @break

                        @case('chart')
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M4 19.5h16M7 16V9m5 7V5m5 11v-4M5.75 19.5h12.5A1.75 1.75 0 0020 17.75V5.75A1.75 1.75 0 0018.25 4H5.75A1.75 1.75 0 004 5.75v12A1.75 1.75 0 005.75 19.5z" />
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

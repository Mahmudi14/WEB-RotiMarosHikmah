@extends('layouts.master')

@section('page_title', $pageTitle)

@section('content')
    <div class="space-y-6">
        <div class="overflow-hidden rounded-3xl bg-[#1F444C] p-6 text-white shadow-lg shadow-[#1F444C]/10">
            <div class="relative">
                <div class="absolute -right-10 -top-12 h-32 w-32 rounded-full bg-[#F4B044]/20"></div>
                <div class="absolute -bottom-16 -left-12 h-36 w-36 rounded-full bg-white/10"></div>

                <div class="relative">
                    <p class="text-xs font-bold uppercase tracking-[0.28em] text-[#F4D3B0]">
                        {{ $canManage ? 'Admin' : 'Keuangan' }}
                    </p>
                    <h2 class="mt-2 text-2xl font-black tracking-tight text-white">
                        {{ $pageTitle }}
                    </h2>
                    <p class="mt-2 max-w-2xl text-sm leading-relaxed text-[#F4D3B0]">
                        {{ $pageSubtitle }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Filter --}}
        <div class="relative overflow-visible rounded-3xl border border-[#F4D3B0]/70 bg-white p-5 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]"
            x-data="{
                categoryOpen: false,
                conditionOpen: false,
            
                selectedCategory: @js((string) request('category_id', '')),
                selectedCondition: @js((string) request('condition', '')),
            
                categories: @js($categories->mapWithKeys(fn($category) => [(string) $category->id => $category->nama_kategori])->toArray()),
            
                conditions: {
                    '': 'Semua Kondisi',
                    'available': 'Ada Stok',
                    'out': 'Habis',
                },
            
                get selectedCategoryLabel() {
                    return this.selectedCategory ? this.categories[this.selectedCategory] : 'Semua Kategori'
                },
            
                get selectedConditionLabel() {
                    return this.conditions[this.selectedCondition] ?? 'Semua Kondisi'
                },
            
                closeDropdowns(except = '') {
                    if (except !== 'category') this.categoryOpen = false;
                    if (except !== 'condition') this.conditionOpen = false;
                }
            }">

            <form method="GET" action="{{ route($routePrefix . '.index') }}"
                class="grid gap-3
           min-[835px]:grid-cols-[minmax(0,1fr)_180px_160px_auto]
           min-[835px]:items-center
           min-[1024px]:grid-cols-[minmax(0,1fr)_230px_220px_auto]
           xl:grid-cols-[minmax(0,1fr)_280px_230px_auto]">

                {{-- Search --}}
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-[#6B3E12]/60">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21 21l-4.35-4.35m1.35-5.65a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>

                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari produk, kode, atau kategori..."
                        class="block h-12 w-full rounded-2xl border border-[#F4D3B0] bg-[#F7F6F4] py-0 pl-12 pr-4 text-sm font-medium text-[#2B1A10] shadow-sm transition placeholder:text-[#6B3E12]/45 focus:border-[#F4B044] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#F4B044]/20">
                </div>

                {{-- Custom Category Dropdown --}}
                <div>
                    <input type="hidden" name="category_id" x-model="selectedCategory">

                    <div class="relative">
                        <button type="button" @click="categoryOpen = !categoryOpen; closeDropdowns('category')"
                            class="flex h-12 w-full items-center justify-between rounded-2xl border border-[#F4D3B0] bg-[#F7F6F4] px-4 py-0 text-left text-sm font-bold text-[#2B1A10] shadow-sm transition hover:bg-white focus:border-[#F4B044] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#F4B044]/20">

                            <div class="flex min-w-0 items-center gap-3">
                                <span
                                    class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-[#F4B044]/20 text-[#6B3E12]">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.2"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5" />
                                    </svg>
                                </span>

                                <span x-text="selectedCategoryLabel" class="truncate"
                                    :class="selectedCategory ? 'text-[#2B1A10]' : 'text-[#6B3E12]/60'"></span>
                            </div>

                            <svg class="h-5 w-5 shrink-0 text-[#6B3E12] transition duration-200"
                                :class="categoryOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                                stroke-width="2.4" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="categoryOpen" x-cloak @click.outside="categoryOpen = false"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                            x-transition:leave-end="opacity-0 scale-95 translate-y-1"
                            class="absolute left-0 top-full z-20 mt-3 max-h-72 w-full overflow-y-auto rounded-2xl border border-[#F4D3B0]/80 bg-white shadow-[0_25px_70px_-35px_rgba(31,68,76,0.55)]">

                            <div class="p-2">
                                <button type="button" @click="selectedCategory = ''; categoryOpen = false"
                                    class="group flex w-full items-center justify-between rounded-xl px-4 py-3 text-left text-sm font-bold transition"
                                    :class="selectedCategory === ''
                                        ?
                                        'bg-[#F4B044] text-[#2B1A10]' :
                                        'text-[#2B1A10] hover:bg-[#F4B044]/15'">
                                    <span class="truncate">Semua Kategori</span>

                                    <svg x-show="selectedCategory === ''" x-cloak class="h-5 w-5 shrink-0" fill="none"
                                        stroke="currentColor" stroke-width="2.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                </button>

                                @foreach ($categories as $category)
                                    <button type="button"
                                        @click="selectedCategory = @js((string) $category->id); categoryOpen = false"
                                        class="group flex w-full items-center justify-between rounded-xl px-4 py-3 text-left text-sm font-bold transition"
                                        :class="selectedCategory === @js((string) $category->id) ?
                                            'bg-[#F4B044] text-[#2B1A10]' :
                                            'text-[#2B1A10] hover:bg-[#F4B044]/15'">
                                        <span class="truncate">{{ $category->nama_kategori }}</span>

                                        <svg x-show="selectedCategory === @js((string) $category->id)" x-cloak
                                            class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2.8"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Custom Condition Dropdown --}}
                <div>
                    <input type="hidden" name="condition" x-model="selectedCondition">

                    <div class="relative">
                        <button type="button" @click="conditionOpen = !conditionOpen; closeDropdowns('condition')"
                            class="flex h-12 w-full items-center justify-between rounded-2xl border border-[#F4D3B0] bg-[#F7F6F4] px-4 py-0 text-left text-sm font-bold text-[#2B1A10] shadow-sm transition hover:bg-white focus:border-[#F4B044] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#F4B044]/20">

                            <div class="flex min-w-0 items-center gap-3">
                                <span
                                    class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-[#F4B044]/20 text-[#6B3E12]">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.2"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M20 7.5 12 3 4 7.5m16 0L12 12m8-4.5v9L12 21m0-9L4 7.5m8 4.5v9M4 7.5v9L12 21" />
                                    </svg>
                                </span>

                                <span x-text="selectedConditionLabel" class="truncate"
                                    :class="selectedCondition ? 'text-[#2B1A10]' : 'text-[#6B3E12]/60'"></span>
                            </div>

                            <svg class="h-5 w-5 shrink-0 text-[#6B3E12] transition duration-200"
                                :class="conditionOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                                stroke-width="2.4" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="conditionOpen" x-cloak @click.outside="conditionOpen = false"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                            x-transition:leave-end="opacity-0 scale-95 translate-y-1"
                            class="absolute left-0 top-full z-20 mt-3 w-full overflow-hidden rounded-2xl border border-[#F4D3B0]/80 bg-white shadow-[0_25px_70px_-35px_rgba(31,68,76,0.55)]">

                            <div class="p-2">
                                <button type="button" @click="selectedCondition = ''; conditionOpen = false"
                                    class="group flex w-full items-center justify-between rounded-xl px-4 py-3 text-left text-sm font-bold transition"
                                    :class="selectedCondition === ''
                                        ?
                                        'bg-[#F4B044] text-[#2B1A10]' :
                                        'text-[#2B1A10] hover:bg-[#F4B044]/15'">
                                    <span>Semua Kondisi</span>

                                    <svg x-show="selectedCondition === ''" x-cloak class="h-5 w-5 shrink-0"
                                        fill="none" stroke="currentColor" stroke-width="2.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                </button>

                                <button type="button" @click="selectedCondition = 'available'; conditionOpen = false"
                                    class="group flex w-full items-center justify-between rounded-xl px-4 py-3 text-left text-sm font-bold transition"
                                    :class="selectedCondition === 'available'
                                        ?
                                        'bg-[#F4B044] text-[#2B1A10]' :
                                        'text-[#2B1A10] hover:bg-[#F4B044]/15'">
                                    <span>Ada Stok</span>

                                    <svg x-show="selectedCondition === 'available'" x-cloak class="h-5 w-5 shrink-0"
                                        fill="none" stroke="currentColor" stroke-width="2.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                </button>

                                <button type="button" @click="selectedCondition = 'out'; conditionOpen = false"
                                    class="group flex w-full items-center justify-between rounded-xl px-4 py-3 text-left text-sm font-bold transition"
                                    :class="selectedCondition === 'out'
                                        ?
                                        'bg-[#F4B044] text-[#2B1A10]' :
                                        'text-[#2B1A10] hover:bg-[#F4B044]/15'">
                                    <span>Habis</span>

                                    <svg x-show="selectedCondition === 'out'" x-cloak class="h-5 w-5 shrink-0"
                                        fill="none" stroke="currentColor" stroke-width="2.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action --}}
                <div class="flex shrink-0 gap-3">
                    <button type="submit"
                        class="inline-flex h-12 shrink-0 items-center justify-center gap-2 rounded-2xl bg-[#1F444C] px-5 py-0 text-sm font-black text-white shadow-lg shadow-[#1F444C]/20 transition hover:-translate-y-0.5 hover:shadow-xl">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.3"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21 21l-4.35-4.35m1.35-5.65a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Cari
                    </button>

                    <a href="{{ route($routePrefix . '.index') }}"
                        class="inline-flex h-12 shrink-0 items-center justify-center gap-2 rounded-2xl border border-[#F4D3B0] bg-white px-5 py-0 text-sm font-black text-[#6B3E12] transition hover:bg-[#F7F6F4]">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.3"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0013.803-3.7M7.977 14.652H2.985m18.03-5.304-3.181-3.183a8.25 8.25 0 00-13.803 3.7" />
                        </svg>
                        Reset
                    </a>
                </div>
            </form>
        </div>

        @php
            $currentSort = request('sort');
            $currentDirection = request('direction', 'desc');

            $sortUrl = function (string $column) use ($currentSort, $currentDirection) {
                $nextDirection = $currentSort === $column && $currentDirection === 'asc' ? 'desc' : 'asc';

                return request()->fullUrlWithQuery([
                    'sort' => $column,
                    'direction' => $nextDirection,
                    'page' => 1,
                ]);
            };

            $sortIcon = function (string $column) use ($currentSort, $currentDirection) {
                $isActive = $currentSort === $column;

                if (!$isActive) {
                    return '
                <svg class="h-3.5 w-3.5 opacity-45" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10 3.5a.75.75 0 01.53.22l3 3a.75.75 0 11-1.06 1.06L10 5.31 7.53 7.78a.75.75 0 01-1.06-1.06l3-3A.75.75 0 0110 3.5z"/>
                    <path d="M10 16.5a.75.75 0 01-.53-.22l-3-3a.75.75 0 111.06-1.06L10 14.69l2.47-2.47a.75.75 0 111.06 1.06l-3 3a.75.75 0 01-.53.22z"/>
                </svg>
            ';
                }

                if ($currentDirection === 'asc') {
                    return '
                <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10 3.5a.75.75 0 01.53.22l4 4a.75.75 0 11-1.06 1.06L10 5.31 6.53 8.78a.75.75 0 01-1.06-1.06l4-4A.75.75 0 0110 3.5z"/>
                    <path d="M10 4.5a.75.75 0 01.75.75v10a.75.75 0 01-1.5 0v-10A.75.75 0 0110 4.5z"/>
                </svg>
            ';
                }

                return '
            <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                <path d="M10 16.5a.75.75 0 01-.53-.22l-4-4a.75.75 0 111.06-1.06L10 14.69l3.47-3.47a.75.75 0 111.06 1.06l-4 4a.75.75 0 01-.53.22z"/>
                <path d="M10 3.75a.75.75 0 01.75.75v10a.75.75 0 01-1.5 0v-10A.75.75 0 0110 3.75z"/>
            </svg>
        ';
            };
        @endphp
        <div
            class="overflow-hidden rounded-3xl border border-[#F4D3B0]/70 bg-white shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-[#F4D3B0]/70">
                    <thead class="bg-[#F7F6F4]">
                        <tr>
                            <th class="px-5 py-4 text-left">
                                <span class="text-[11px] font-black uppercase tracking-[0.22em] text-[#6B3E12]/80">
                                    No
                                </span>
                            </th>

                            <th class="px-5 py-4 text-left">
                                <a href="{{ $sortUrl('product') }}"
                                    class="group inline-flex items-center gap-2 rounded-full px-3 py-2 text-[11px] font-black uppercase tracking-[0.22em] text-[#6B3E12]/80 transition hover:bg-white hover:text-[#1F444C] hover:shadow-sm">
                                    <span>Produk</span>

                                    <span
                                        class="inline-flex h-6 w-6 items-center justify-center rounded-full transition
                    {{ request('sort') === 'product'
                        ? 'bg-[#1F444C] text-white shadow-sm'
                        : 'bg-[#F4D3B0]/45 text-[#6B3E12] group-hover:bg-[#F4B044]/25' }}">
                                        {!! $sortIcon('product') !!}
                                    </span>
                                </a>
                            </th>

                            <th class="px-5 py-4 text-left">
                                <a href="{{ $sortUrl('category') }}"
                                    class="group inline-flex items-center gap-2 rounded-full px-3 py-2 text-[11px] font-black uppercase tracking-[0.22em] text-[#6B3E12]/80 transition hover:bg-white hover:text-[#1F444C] hover:shadow-sm">
                                    <span>Kategori</span>

                                    <span
                                        class="inline-flex h-6 w-6 items-center justify-center rounded-full transition
                    {{ request('sort') === 'category'
                        ? 'bg-[#1F444C] text-white shadow-sm'
                        : 'bg-[#F4D3B0]/45 text-[#6B3E12] group-hover:bg-[#F4B044]/25' }}">
                                        {!! $sortIcon('category') !!}
                                    </span>
                                </a>
                            </th>

                            <th class="px-5 py-4 text-left">
                                <a href="{{ $sortUrl('stock') }}"
                                    class="group inline-flex items-center gap-2 rounded-full px-3 py-2 text-[11px] font-black uppercase tracking-[0.22em] text-[#6B3E12]/80 transition hover:bg-white hover:text-[#1F444C] hover:shadow-sm">
                                    <span>Stok</span>

                                    <span
                                        class="inline-flex h-6 w-6 items-center justify-center rounded-full transition
                    {{ request('sort') === 'stock'
                        ? 'bg-[#1F444C] text-white shadow-sm'
                        : 'bg-[#F4D3B0]/45 text-[#6B3E12] group-hover:bg-[#F4B044]/25' }}">
                                        {!! $sortIcon('stock') !!}
                                    </span>
                                </a>
                            </th>

                            <th class="px-5 py-4 text-left">
                                <span class="text-[11px] font-black uppercase tracking-[0.22em] text-[#6B3E12]/80">
                                    Kondisi
                                </span>
                            </th>

                            <th class="w-[180px] px-5 py-4 text-right">
                                <span class="text-[11px] font-black uppercase tracking-[0.22em] text-[#6B3E12]/80">
                                    Aksi
                                </span>
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-[#F4D3B0]/70 bg-white">
                        @forelse ($products as $product)
                            @php
                                $stock = (int) $product->stock;

                                if ($stock <= 0) {
                                    $conditionLabel = 'Habis';
                                    $conditionClass = 'bg-[#A92A35]/10 text-[#A92A35]';
                                } else {
                                    $conditionLabel = 'Tersedia';
                                    $conditionClass = 'bg-[#1F444C]/10 text-[#1F444C]';
                                }
                            @endphp

                            <tr x-data="{ editOpen: false }">
                                <td class="px-5 py-4 text-sm font-black text-[#6B3E12]">
                                    {{ $products->firstItem() + $loop->index }}
                                </td>

                                <td class="px-5 py-4">
                                    <div class="font-black text-[#2B1A10]">
                                        {{ $product->nama_produk }}
                                    </div>
                                    <div class="mt-1 text-xs font-semibold text-[#6B3E12]/70">
                                        {{ $product->kode_produk ?? '-' }}
                                    </div>
                                </td>

                                <td class="px-5 py-4 text-sm font-bold text-[#2B1A10]">
                                    {{ $product->category?->nama_kategori ?? '-' }}
                                </td>

                                <td class="px-5 py-4 text-sm font-black text-[#2B1A10]">
                                    {{ number_format($stock, 0, ',', '.') }}
                                </td>

                                <td class="px-5 py-4">
                                    <span
                                        class="inline-flex rounded-full px-3 py-1 text-xs font-black {{ $conditionClass }}">
                                        {{ $conditionLabel }}
                                    </span>
                                </td>

                                <td class="w-[180px] px-5 py-4 text-right">
                                    <div class="flex flex-nowrap justify-end gap-2 whitespace-nowrap">
                                        <a href="{{ route($routePrefix . '.movements', $product) }}"
                                            class="inline-flex items-center justify-center rounded-xl bg-[#F4B044] px-3 py-2 text-xs font-black text-[#2B1A10] shadow-sm shadow-[#F4B044]/25 transition hover:-translate-y-0.5 hover:bg-[#E7A33D] hover:shadow-md active:scale-95">
                                            Riwayat
                                        </a>

                                        @if ($canManage)
                                            <button type="button" @click="editOpen = true"
                                                class="inline-flex items-center justify-center rounded-xl bg-[#1F444C] px-3 py-2 text-xs font-black text-white shadow-sm shadow-[#1F444C]/25 transition hover:-translate-y-0.5 hover:bg-[#183941] hover:shadow-md active:scale-95">
                                                Edit
                                            </button>

                                            <template x-teleport="body">
                                                <div x-show="editOpen" x-cloak x-transition.opacity
                                                    @keydown.escape.window="editOpen = false"
                                                    class="fixed inset-0 z-[9999] flex min-h-screen items-center justify-center overflow-y-auto bg-[#2B1A10]/45 p-4 backdrop-blur-sm">

                                                    <div @click.self="editOpen = false" class="absolute inset-0"></div>

                                                    <form method="POST"
                                                        action="{{ route($routePrefix . '.adjust', $product) }}"
                                                        class="relative z-10 w-full max-w-md rounded-3xl bg-white p-6 text-left shadow-2xl">
                                                        @csrf

                                                        <h3 class="text-lg font-black text-[#2B1A10]">
                                                            Edit Stok
                                                        </h3>

                                                        <p class="mt-1 text-sm font-semibold text-[#6B3E12]">
                                                            {{ $product->nama_produk }}
                                                        </p>

                                                        <div
                                                            class="mt-4 rounded-2xl border border-[#F4D3B0]/70 bg-[#F7F6F4] p-4">
                                                            <p
                                                                class="text-xs font-black uppercase tracking-wider text-[#6B3E12]">
                                                                Stok Saat Ini
                                                            </p>

                                                            <p class="mt-1 text-2xl font-black text-[#1F444C]">
                                                                {{ number_format($stock, 0, ',', '.') }}
                                                            </p>
                                                        </div>

                                                        <div class="mt-5">
                                                            <label class="mb-2 block text-sm font-black text-[#2B1A10]">
                                                                Stok Aktual
                                                            </label>

                                                            <input type="number" name="stock" min="0" required
                                                                value="{{ $stock }}"
                                                                class="h-12 w-full rounded-2xl border border-[#F4D3B0] bg-[#F7F6F4] px-4 text-sm font-bold text-[#2B1A10] focus:border-[#F4B044] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#F4B044]/20">

                                                            <p
                                                                class="mt-2 text-xs font-semibold leading-relaxed text-[#6B3E12]">
                                                                Isi jumlah stok fisik yang benar saat ini. Jika lebih besar,
                                                                stok bertambah. Jika lebih kecil, stok berkurang.
                                                            </p>
                                                        </div>

                                                        <div class="mt-4">
                                                            <label class="mb-2 block text-sm font-black text-[#2B1A10]">
                                                                Catatan
                                                            </label>

                                                            <textarea name="note" rows="3" placeholder="Contoh: Koreksi stok opname / stok masuk hari ini"
                                                                class="w-full rounded-2xl border border-[#F4D3B0] bg-[#F7F6F4] px-4 py-3 text-sm font-bold text-[#2B1A10] focus:border-[#F4B044] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#F4B044]/20"></textarea>
                                                        </div>

                                                        <div class="mt-6 flex justify-end gap-3">
                                                            <button type="button" @click="editOpen = false"
                                                                class="rounded-2xl border border-[#F4D3B0] px-5 py-3 text-sm font-black text-[#6B3E12] transition hover:bg-[#F7F6F4]">
                                                                Batal
                                                            </button>

                                                            <button type="submit"
                                                                class="rounded-2xl bg-[#F4B044] px-5 py-3 text-sm font-black text-[#2B1A10] shadow-lg shadow-[#F4B044]/20 transition hover:bg-[#f7bd5f]">
                                                                Simpan Stok
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </template>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-10 text-center text-sm font-bold text-[#6B3E12]">
                                    Data stok belum tersedia.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="border-t border-[#F4D3B0]/70 px-5 py-4">
                {{ $products->links() }}
            </div>
        </div>
    </div>
@endsection

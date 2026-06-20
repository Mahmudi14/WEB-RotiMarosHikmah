@extends('layouts.master')

@section('page_title', 'Produk')

@section('content')
    <div class="space-y-6" x-data="{
        deleteModalOpen: false,
        deleteAction: '',
        deleteProductName: '',
    
        openDeleteModal(action, name) {
            this.deleteAction = action;
            this.deleteProductName = name;
            this.deleteModalOpen = true;
        },
    
        closeDeleteModal() {
            this.deleteModalOpen = false;
            this.deleteAction = '';
            this.deleteProductName = '';
        }
    }" @keydown.escape.window="closeDeleteModal()">
        {{-- Header --}}
        <div class="overflow-hidden rounded-3xl bg-[#1F444C] p-6 text-white shadow-lg shadow-[#1F444C]/10">
            <div class="relative">
                <div class="absolute -right-10 -top-12 h-32 w-32 rounded-full bg-[#F4B044]/20"></div>
                <div class="absolute -bottom-16 -left-12 h-36 w-36 rounded-full bg-white/10"></div>

                <div class="relative flex flex-col gap-5 md:flex-row md:items-center md:justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.28em] text-[#F4D3B0]">
                            Admin
                        </p>
                        <h2 class="mt-2 text-2xl font-black tracking-tight text-white">
                            Produk
                        </h2>
                        <p class="mt-2 max-w-2xl text-sm leading-relaxed text-[#F4D3B0]">
                            Kelola produk roti, harga jual, gambar, kategori, ketersediaan, dan status produk.
                        </p>
                    </div>

                    <a href="{{ route('admin.products.create') }}"
                        class="inline-flex items-center justify-center gap-2 rounded-2xl bg-[#F4B044] px-5 py-3 text-sm font-black text-[#2B1A10] shadow-lg shadow-[#F4B044]/20 transition hover:-translate-y-0.5 hover:shadow-xl focus:outline-none focus:ring-4 focus:ring-[#F4B044]/25">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Tambah Produk
                    </a>
                </div>
            </div>
        </div>

        {{-- Filter --}}
        <div class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-5 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]"
            x-data="{
                categoryOpen: false,
                selectedCategory: @js((string) request('category_id', '')),
                categories: @js($categories->mapWithKeys(fn($category) => [(string) $category->id => $category->nama_kategori])->toArray()),
                get selectedCategoryLabel() {
                    return this.selectedCategory ? this.categories[this.selectedCategory] : 'Semua Kategori'
                }
            }">
            <form method="GET" action="{{ route('admin.products.index') }}"
                class="grid gap-3 lg:grid-cols-[minmax(0,1fr)_230px_auto] lg:items-center xl:grid-cols-[minmax(0,1fr)_310px_auto]">

                {{-- Search --}}
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-[#6B3E12]/60">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21 21l-4.35-4.35m1.35-5.65a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>

                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari nama, kode, atau kategori produk..."
                        class="block h-12 w-full rounded-2xl border border-[#F4D3B0] bg-[#F7F6F4] py-0 pl-12 pr-4 text-sm font-medium text-[#2B1A10] shadow-sm transition placeholder:text-[#6B3E12]/45 focus:border-[#F4B044] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#F4B044]/20">
                </div>

                {{-- Custom Category Dropdown --}}
                <div>
                    <input type="hidden" name="category_id" x-model="selectedCategory">

                    <div class="relative">
                        <button type="button" @click="categoryOpen = !categoryOpen"
                            class="flex h-12 w-full items-center justify-between rounded-2xl border border-[#F4D3B0] bg-[#F7F6F4] px-4 py-0 text-left text-sm font-bold text-[#2B1A10] shadow-sm transition hover:bg-white focus:border-[#F4B044] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#F4B044]/20">

                            <div class="flex items-center gap-3">
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

                            <svg class="h-5 w-5 text-[#6B3E12] transition duration-200"
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
                            class="absolute z-40 mt-3 max-h-72 w-full overflow-y-auto rounded-2xl border border-[#F4D3B0]/80 bg-white shadow-[0_25px_70px_-35px_rgba(31,68,76,0.55)]">

                            <div class="p-2">
                                <button type="button" @click="selectedCategory = ''; categoryOpen = false"
                                    class="group flex w-full items-center justify-between rounded-xl px-4 py-3 text-left text-sm font-bold transition"
                                    :class="selectedCategory === ''
                                        ?
                                        'bg-[#F4B044] text-[#2B1A10]' :
                                        'text-[#2B1A10] hover:bg-[#F4B044]/15'">

                                    <div>
                                        <p>Semua Kategori</p>
                                    </div>

                                    <svg x-show="selectedCategory === ''" x-cloak class="h-5 w-5" fill="none"
                                        stroke="currentColor" stroke-width="2.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                </button>

                                @foreach ($categories as $category)
                                    <button type="button"
                                        @click="selectedCategory = '{{ $category->id }}'; categoryOpen = false"
                                        class="group flex w-full items-center justify-between rounded-xl px-4 py-3 text-left text-sm font-bold transition"
                                        :class="selectedCategory === '{{ $category->id }}'
                                            ?
                                            'bg-[#F4B044] text-[#2B1A10]' :
                                            'text-[#2B1A10] hover:bg-[#F4B044]/15'">

                                        <div>
                                            <p>{{ $category->nama_kategori }}</p>

                                        </div>

                                        <svg x-show="selectedCategory === '{{ $category->id }}'" x-cloak class="h-5 w-5"
                                            fill="none" stroke="currentColor" stroke-width="2.8" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </button>
                                @endforeach
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

                    <a href="{{ route('admin.products.index') }}"
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

        {{-- Table --}}
        <div
            class="overflow-hidden rounded-3xl border border-[#F4D3B0]/70 bg-white shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-[#F4D3B0]/70">
                    <thead class="bg-[#F7F6F4]">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-[0.2em] text-[#6B3E12]">
                                No
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-[0.2em] text-[#6B3E12]">
                                Produk
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-[0.2em] text-[#6B3E12]">
                                Kategori
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-[0.2em] text-[#6B3E12]">
                                Harga
                            </th>
                            <th class="px-2 py-4 text-center text-xs font-black uppercase tracking-[0.2em] text-[#6B3E12]">
                                Status
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-black uppercase tracking-[0.2em] text-[#6B3E12]">
                                Aksi
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-[#F4D3B0]/60 bg-white">
                        @forelse ($products as $product)
                            @php
                                $availabilityLabel =
                                    $product->status_ketersediaan === 'tersedia' ? 'Tersedia' : 'Habis';
                                $availabilityClass =
                                    $product->status_ketersediaan === 'tersedia'
                                        ? 'bg-[#1F444C]/10 text-[#1F444C]'
                                        : 'bg-[#A92A35]/10 text-[#A92A35]';

                                $statusLabel = $product->status === 'aktif' ? 'Aktif' : 'Nonaktif';
                                $statusClass =
                                    $product->status === 'aktif'
                                        ? 'bg-[#F4B044]/20 text-[#6B3E12]'
                                        : 'bg-[#A92A35]/10 text-[#A92A35]';
                            @endphp

                            <tr class="transition hover:bg-[#F7F6F4]/70">
                                <td class="px-6 py-4 text-sm font-black text-[#6B3E12]">
                                    {{ $products->firstItem() + $loop->index }}
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        @if ($product->gambar)
                                            <img src="{{ asset('storage/' . $product->gambar) }}"
                                                alt="{{ $product->nama_produk }}"
                                                class="h-14 w-14 shrink-0 rounded-2xl object-cover shadow-sm">
                                        @else
                                            <div
                                                class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-[#F4B044] text-sm font-black text-[#2B1A10] shadow-sm">
                                                {{ strtoupper(substr($product->nama_produk, 0, 1)) }}
                                            </div>
                                        @endif

                                        <div>
                                            <p class="font-black text-[#2B1A10]">
                                                {{ $product->nama_produk }}
                                            </p>

                                            <p class="mt-0.5 text-sm font-medium text-[#6B3E12]">
                                                {{ $product->kode_produk ?: $product->slug }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-sm font-bold text-[#6B3E12]">
                                    {{ $product->category?->nama_kategori ?? '-' }}
                                </td>

                                <td class="px-6 py-4 text-sm font-black text-[#2B1A10]">
                                    {{ $product->harga_jual_formatted }}
                                </td>

                                <td class="px-2 py-4">
                                    <div class="flex flex-col items-center gap-2">
                                        <span
                                            class="inline-flex w-fit justify-center rounded-full px-3 py-1 text-center text-xs font-black {{ $availabilityClass }}">
                                            {{ $availabilityLabel }}
                                        </span>

                                        <span
                                            class="inline-flex w-fit justify-center rounded-full px-3 py-1 text-center text-xs font-black {{ $statusClass }}">
                                            {{ $statusLabel }}
                                        </span>
                                    </div>
                                </td>

                                <td class="px-2 py-4">
                                    <div class="flex flex-wrap items-center justify-end gap-2">
                                        <a href="{{ route('admin.products.show', $product) }}"
                                            class="inline-flex items-center justify-center rounded-xl bg-[#F4B044]/20 px-4 py-2 text-xs font-black text-[#6B3E12] transition hover:bg-[#F4B044] hover:text-[#2B1A10]">
                                            Detail
                                        </a>

                                        <a href="{{ route('admin.products.edit', $product) }}"
                                            class="inline-flex items-center justify-center rounded-xl bg-[#1F444C]/10 px-4 py-2 text-xs font-black text-[#1F444C] transition hover:bg-[#1F444C] hover:text-white">
                                            Edit
                                        </a>

                                        <button type="button"
                                            @click="openDeleteModal(@js(route('admin.products.destroy', $product)), @js($product->nama_produk))"
                                            class="inline-flex items-center justify-center rounded-xl bg-[#A92A35]/10 px-4 py-2 text-xs font-black text-[#A92A35] transition hover:bg-[#A92A35] hover:text-white">
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-14 text-center">
                                    <div class="mx-auto flex max-w-sm flex-col items-center">
                                        <div
                                            class="flex h-16 w-16 items-center justify-center rounded-3xl bg-[#F4B044]/20 text-[#6B3E12]">
                                            <svg class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="2"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M20 7.5L12 3 4 7.5m16 0L12 12m8-4.5v9L12 21m0-9L4 7.5m8 4.5v9M4 7.5v9L12 21" />
                                            </svg>
                                        </div>

                                        <h3 class="mt-4 text-lg font-black text-[#2B1A10]">
                                            Belum ada produk
                                        </h3>

                                        <p class="mt-1 text-sm text-[#6B3E12]">
                                            Tambahkan produk pertama untuk mulai mengelola daftar menu roti.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($products->hasPages())
                <div class="border-t border-[#F4D3B0]/70 bg-[#F7F6F4] px-6 py-4">
                    {{ $products->links() }}
                </div>
            @endif
        </div>

        {{-- Delete Confirmation Modal --}}
        <template x-teleport="body">
            <div x-show="deleteModalOpen" x-cloak
                class="fixed inset-0 z-[9999] flex items-center justify-center p-4 sm:p-6" aria-modal="true"
                role="dialog">

                <div x-show="deleteModalOpen" x-transition.opacity
                    class="absolute inset-0 bg-[#1F444C]/55 backdrop-blur-md" @click="closeDeleteModal()">
                </div>

                <div x-show="deleteModalOpen" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                    class="relative z-10 w-full max-w-md overflow-hidden rounded-[1.75rem] border border-[#F4D3B0]/70 bg-white shadow-[0_35px_90px_-35px_rgba(31,68,76,0.65)]">

                    <div class="relative overflow-hidden bg-[#A92A35] px-6 py-6 text-white">
                        <div class="absolute -right-10 -top-12 h-32 w-32 rounded-full bg-white/10"></div>
                        <div class="absolute -bottom-16 -left-12 h-36 w-36 rounded-full bg-black/10"></div>

                        <div class="relative flex items-start gap-4">
                            <div
                                class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-white/15 text-white ring-1 ring-white/20">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2.4"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 9v3.75m0 3.75h.008v.008H12V16.5z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M10.29 3.86L1.82 18a1.5 1.5 0 001.29 2.25h17.78A1.5 1.5 0 0022.18 18L13.71 3.86a1.5 1.5 0 00-2.42 0z" />
                                </svg>
                            </div>

                            <div>
                                <p class="text-xs font-bold uppercase tracking-[0.24em] text-white/75">
                                    Konfirmasi Hapus
                                </p>
                                <h3 class="mt-1 text-xl font-black text-white">
                                    Hapus Produk?
                                </h3>
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-5">
                        <p class="text-sm leading-relaxed text-[#6B3E12]">
                            Kamu akan menghapus produk
                            <span class="font-black text-[#2B1A10]" x-text="deleteProductName"></span>.
                            Tindakan ini tidak dapat dibatalkan.
                        </p>

                        <div class="mt-5 rounded-2xl border border-[#A92A35]/20 bg-[#A92A35]/5 px-4 py-3">
                            <p class="text-sm font-semibold text-[#A92A35]">
                                Pastikan produk ini memang sudah tidak dibutuhkan.
                            </p>
                        </div>
                    </div>

                    <div
                        class="flex flex-col-reverse gap-3 border-t border-[#F4D3B0]/70 bg-[#F7F6F4] px-6 py-5 sm:flex-row sm:justify-end">
                        <button type="button" @click="closeDeleteModal()"
                            class="inline-flex items-center justify-center rounded-2xl border border-[#F4D3B0] bg-white px-5 py-3 text-sm font-black text-[#6B3E12] transition hover:bg-[#F7F6F4] focus:outline-none focus:ring-4 focus:ring-[#F4B044]/20">
                            Batal
                        </button>

                        <form method="POST" :action="deleteAction">
                            @csrf
                            @method('DELETE')

                            <button type="submit"
                                class="inline-flex w-full items-center justify-center rounded-2xl bg-[#A92A35] px-5 py-3 text-sm font-black text-white shadow-lg shadow-[#A92A35]/20 transition hover:-translate-y-0.5 hover:shadow-xl focus:outline-none focus:ring-4 focus:ring-[#A92A35]/20">
                                Ya, Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </template>
    </div>
@endsection

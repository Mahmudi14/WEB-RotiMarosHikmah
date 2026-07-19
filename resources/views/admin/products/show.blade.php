@extends('layouts.master')

@section('page_title', 'Detail Produk')

@section('content')
    @php
        $stock = (int) $product->stock;

        $availabilityLabel = $stock > 0 ? 'Tersedia' : 'Habis';

        $availabilityClass = $stock > 0 ? 'bg-[#1F444C]/10 text-[#1F444C]' : 'bg-[#A92A35]/10 text-[#A92A35]';
    @endphp
    <div class="space-y-6" x-data="{
        confirmModalOpen: false,
        confirmAction: '',
        confirmTitle: '',
        confirmMessage: '',
        confirmButtonText: '',
        confirmButtonClass: '',
    
        openConfirmModal(action, type, currentValue) {
            this.confirmAction = action;
    
            if (type === 'status') {
                if (currentValue === 'aktif') {
                    this.confirmTitle = 'Nonaktifkan Produk?';
                    this.confirmMessage = 'Produk ini akan dinonaktifkan dan tidak ditampilkan pada transaksi kasir.';
                    this.confirmButtonText = 'Ya, Nonaktifkan';
                    this.confirmButtonClass = 'bg-[#A92A35] text-white shadow-[#A92A35]/20 focus:ring-[#A92A35]/20';
                } else {
                    this.confirmTitle = 'Aktifkan Produk?';
                    this.confirmMessage = 'Produk ini akan diaktifkan kembali dan dapat digunakan pada sistem.';
                    this.confirmButtonText = 'Ya, Aktifkan';
                    this.confirmButtonClass = 'bg-[#F4B044] text-[#2B1A10] shadow-[#F4B044]/20 focus:ring-[#F4B044]/20';
                }
            }
    
            this.confirmModalOpen = true;
        },
    
        closeConfirmModal() {
            this.confirmModalOpen = false;
            this.confirmAction = '';
            this.confirmTitle = '';
            this.confirmMessage = '';
            this.confirmButtonText = '';
            this.confirmButtonClass = '';
        }
    }" @keydown.escape.window="closeConfirmModal()">

        {{-- Header --}}
        <div class="overflow-hidden rounded-3xl bg-[#1F444C] p-6 text-white shadow-lg shadow-[#1F444C]/10">
            <div class="relative">
                <div class="absolute -right-10 -top-12 h-32 w-32 rounded-full bg-[#F4B044]/20"></div>
                <div class="absolute -bottom-16 -left-12 h-36 w-36 rounded-full bg-white/10"></div>

                <div
                    class="relative flex flex-col gap-5 min-[835px]:flex-row min-[835px]:items-center min-[835px]:justify-between">
                    <div class="min-w-0">
                        <p class="text-xs font-bold uppercase tracking-[0.28em] text-[#F4D3B0]">
                            Detail Produk
                        </p>

                        <h2 class="mt-2 text-2xl font-black tracking-tight text-white">
                            {{ $product->nama_produk }}
                        </h2>

                        <p class="mt-2 max-w-2xl text-sm leading-relaxed text-[#F4D3B0]">
                            Informasi lengkap produk, kategori, harga jual, gambar, ketersediaan, dan status produk.
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-3 min-[835px]:shrink-0">
                        <a href="{{ route('admin.products.edit', $product) }}"
                            class="inline-flex items-center justify-center rounded-2xl bg-[#F4B044] px-5 py-3 text-sm font-black text-[#2B1A10] shadow-lg shadow-[#F4B044]/20 transition hover:-translate-y-0.5 hover:shadow-xl">
                            Edit
                        </a>

                        <a href="{{ route('admin.products.index') }}"
                            class="inline-flex items-center justify-center rounded-2xl border border-white/20 bg-white/10 px-5 py-3 text-sm font-black text-white transition hover:bg-white/15">
                            Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Ringkasan Produk --}}
        <div
            class="grid gap-6
           min-[835px]:grid-cols-[320px_minmax(0,1fr)]
           min-[1024px]:grid-cols-[360px_minmax(0,1fr)]">
            {{-- Gambar --}}
            <div
                class="overflow-hidden rounded-3xl border border-[#F4D3B0]/70 bg-white p-5 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                <div
                    class="flex aspect-square items-center justify-center overflow-hidden rounded-3xl border border-[#F4D3B0]/70 bg-[#F7F6F4]">
                    @if ($product->gambar)
                        <img src="{{ asset('storage/' . $product->gambar) }}" alt="{{ $product->nama_produk }}"
                            class="h-full w-full object-cover">
                    @else
                        <div class="flex flex-col items-center justify-center px-6 text-center">
                            <div
                                class="flex h-20 w-20 items-center justify-center rounded-3xl bg-[#F4B044]/20 text-[#6B3E12]">
                                <svg class="h-10 w-10" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3 16.5V6.75A2.25 2.25 0 015.25 4.5h13.5A2.25 2.25 0 0121 6.75v10.5A2.25 2.25 0 0118.75 19.5H5.25A2.25 2.25 0 013 17.25v-.75z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3 16.5l4.72-4.72a2.25 2.25 0 013.18 0l1.35 1.35 2.1-2.1a2.25 2.25 0 013.18 0L21 14.5" />
                                </svg>
                            </div>

                            <p class="mt-4 text-sm font-bold text-[#6B3E12]">
                                Belum ada gambar produk
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Info --}}
            <div class="grid gap-4 md:grid-cols-2">
                <div
                    class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-5 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                    <p class="text-sm font-bold text-[#6B3E12]">Kategori</p>
                    <p class="mt-2 text-xl font-black text-[#2B1A10]">
                        {{ $product->category?->nama_kategori ?? '-' }}
                    </p>
                </div>

                <div
                    class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-5 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                    <p class="text-sm font-bold text-[#6B3E12]">Harga Jual</p>
                    <p class="mt-2 text-xl font-black text-[#2B1A10]">
                        {{ $product->harga_jual_formatted }}
                    </p>
                </div>

                <div
                    class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-5 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                    <p class="text-sm font-bold text-[#6B3E12]">Kode Produk</p>
                    <p class="mt-2 text-xl font-black text-[#2B1A10]">
                        {{ $product->kode_produk ?: '-' }}
                    </p>
                </div>

                <div
                    class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-5 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                    <p class="text-sm font-bold text-[#6B3E12]">Slug</p>
                    <p class="mt-2 break-words text-xl font-black text-[#2B1A10]">
                        {{ $product->slug }}
                    </p>
                </div>

                <div
                    class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-5 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                    <p class="text-sm font-bold text-[#6B3E12]">Stok</p>

                    <div class="mt-3 flex flex-wrap items-center gap-2">
                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-black {{ $availabilityClass }}">
                            {{ $availabilityLabel }}
                        </span>

                        <span class="inline-flex rounded-full bg-[#F4B044]/20 px-3 py-1 text-xs font-black text-[#6B3E12]">
                            {{ number_format($stock, 0, ',', '.') }} stok
                        </span>
                    </div>
                </div>

                <div
                    class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-5 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                    <p class="text-sm font-bold text-[#6B3E12]">Status Produk</p>

                    @if ($product->status === 'aktif')
                        <span
                            class="mt-3 inline-flex rounded-full bg-[#F4B044]/20 px-3 py-1 text-xs font-black text-[#6B3E12]">
                            Aktif
                        </span>
                    @else
                        <span
                            class="mt-3 inline-flex rounded-full bg-[#A92A35]/10 px-3 py-1 text-xs font-black text-[#A92A35]">
                            Nonaktif
                        </span>
                    @endif
                </div>

                <div
                    class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-5 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                    <p class="text-sm font-bold text-[#6B3E12]">Dibuat</p>
                    <p class="mt-2 text-xl font-black text-[#2B1A10]">
                        {{ $product->created_at?->format('d M Y') }}
                    </p>
                </div>

                <div
                    class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-5 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                    <p class="text-sm font-bold text-[#6B3E12]">Diperbarui</p>
                    <p class="mt-2 text-xl font-black text-[#2B1A10]">
                        {{ $product->updated_at?->format('d M Y') }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Deskripsi --}}
        <div class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-6 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
            <p class="text-sm font-black uppercase tracking-[0.2em] text-[#6B3E12]">
                Deskripsi
            </p>

            <p class="mt-4 text-sm leading-relaxed text-[#2B1A10]">
                {{ $product->deskripsi ?: 'Belum ada deskripsi untuk produk ini.' }}
            </p>
        </div>

        {{-- Pengaturan Status --}}
        <div class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-6 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-sm font-black uppercase tracking-[0.2em] text-[#6B3E12]">
                        Pengaturan Status Produk
                    </p>

                    <h3 class="mt-2 text-xl font-black text-[#2B1A10]">
                        {{ $product->status === 'aktif' ? 'Nonaktifkan Produk' : 'Aktifkan Produk' }}
                    </h3>

                    <p class="mt-2 max-w-2xl text-sm leading-relaxed text-[#6B3E12]">
                        {{ $product->status === 'aktif'
                            ? 'Produk nonaktif tidak akan digunakan pada transaksi kasir.'
                            : 'Produk aktif dapat digunakan kembali pada sistem.' }}
                    </p>
                </div>

                <button type="button"
                    @click="openConfirmModal(@js(route('admin.products.update-status', $product)), 'status', @js($product->status))"
                    class="{{ $product->status === 'aktif'
                        ? 'bg-[#A92A35] text-white shadow-[#A92A35]/20'
                        : 'bg-[#F4B044] text-[#2B1A10] shadow-[#F4B044]/20' }}
                        inline-flex w-full items-center justify-center rounded-2xl px-5 py-3 text-sm font-black shadow-lg transition hover:-translate-y-0.5 hover:shadow-xl md:w-auto">
                    {{ $product->status === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' }}
                </button>
            </div>
        </div>

        {{-- Confirmation Modal --}}
        <template x-teleport="body">
            <div x-show="confirmModalOpen" x-cloak
                class="fixed inset-0 z-[9999] flex items-center justify-center p-4 sm:p-6" aria-modal="true" role="dialog">

                <div x-show="confirmModalOpen" x-transition.opacity
                    class="absolute inset-0 bg-[#1F444C]/55 backdrop-blur-md" @click="closeConfirmModal()">
                </div>

                <div x-show="confirmModalOpen" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                    class="relative z-10 w-full max-w-md overflow-hidden rounded-[1.75rem] border border-[#F4D3B0]/70 bg-white shadow-[0_35px_90px_-35px_rgba(31,68,76,0.65)]">

                    <div class="relative overflow-hidden bg-[#1F444C] px-6 py-6 text-white">
                        <div class="absolute -right-10 -top-12 h-32 w-32 rounded-full bg-[#F4B044]/20"></div>
                        <div class="absolute -bottom-16 -left-12 h-36 w-36 rounded-full bg-white/10"></div>

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
                                    Konfirmasi Perubahan
                                </p>
                                <h3 class="mt-1 text-xl font-black text-white" x-text="confirmTitle"></h3>
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-5">
                        <p class="text-sm leading-relaxed text-[#6B3E12]" x-text="confirmMessage"></p>

                        <div class="mt-5 rounded-2xl border border-[#F4B044]/30 bg-[#F4B044]/10 px-4 py-3">
                            <p class="text-sm font-semibold text-[#6B3E12]">
                                Perubahan ini akan memengaruhi penggunaan produk pada sistem.
                            </p>
                        </div>
                    </div>

                    <div
                        class="flex flex-col-reverse gap-3 border-t border-[#F4D3B0]/70 bg-[#F7F6F4] px-6 py-5 sm:flex-row sm:justify-end">
                        <button type="button" @click="closeConfirmModal()"
                            class="inline-flex items-center justify-center rounded-2xl border border-[#F4D3B0] bg-white px-5 py-3 text-sm font-black text-[#6B3E12] transition hover:bg-[#F7F6F4] focus:outline-none focus:ring-4 focus:ring-[#F4B044]/20">
                            Batal
                        </button>

                        <form method="POST" :action="confirmAction">
                            @csrf
                            @method('PATCH')

                            <button type="submit" :class="confirmButtonClass"
                                class="inline-flex w-full items-center justify-center rounded-2xl px-5 py-3 text-sm font-black shadow-lg transition hover:-translate-y-0.5 hover:shadow-xl focus:outline-none focus:ring-4">
                                <span x-text="confirmButtonText"></span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </template>
    </div>
@endsection

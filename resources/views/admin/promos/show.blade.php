@extends('layouts.master')

@section('page_title', 'Detail Promo')

@section('content')
    @php
        $statusClass = $promo->status === 'aktif' ? 'bg-[#1F444C]/10 text-[#1F444C]' : 'bg-[#A92A35]/10 text-[#A92A35]';

        $runningClass = $promo->is_berjalan ? 'bg-[#F4B044]/20 text-[#6B3E12]' : 'bg-[#6B3E12]/10 text-[#6B3E12]';
    @endphp

    <div class="space-y-6" x-data="{
        confirmModalOpen: false,
        confirmAction: '',
        confirmTitle: '',
        confirmMessage: '',
        confirmButtonText: '',
        confirmButtonClass: '',
    
        openStatusModal(action, currentStatus) {
            this.confirmAction = action;
    
            if (currentStatus === 'aktif') {
                this.confirmTitle = 'Nonaktifkan Promo?';
                this.confirmMessage = 'Promo ini tidak akan digunakan pada transaksi POS sampai diaktifkan kembali.';
                this.confirmButtonText = 'Ya, Nonaktifkan';
                this.confirmButtonClass = 'bg-[#A92A35] text-white shadow-[#A92A35]/20 focus:ring-[#A92A35]/20';
            } else {
                this.confirmTitle = 'Aktifkan Promo?';
                this.confirmMessage = 'Promo ini akan tersedia untuk perhitungan otomatis pada transaksi POS.';
                this.confirmButtonText = 'Ya, Aktifkan';
                this.confirmButtonClass = 'bg-[#F4B044] text-[#2B1A10] shadow-[#F4B044]/20 focus:ring-[#F4B044]/20';
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

                <div class="relative flex flex-col gap-5 md:flex-row md:items-center md:justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.28em] text-[#F4D3B0]">
                            Admin / Promo / Detail
                        </p>
                        <h1 class="mt-2 text-2xl font-black tracking-tight">
                            {{ $promo->nama_promo }}
                        </h1>
                        <p class="mt-2 max-w-2xl text-sm font-medium leading-relaxed text-[#F7F6F4]/80">
                            Detail promo dan cakupan produk yang mendapatkan diskon.
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('admin.promos.edit', $promo) }}"
                            class="inline-flex items-center justify-center gap-2 rounded-2xl bg-[#F4B044] px-5 py-3 text-sm font-black text-[#2B1A10] shadow-lg shadow-[#F4B044]/20 transition hover:-translate-y-0.5 hover:shadow-xl">
                            Edit Promo
                        </a>

                        <a href="{{ route('admin.promos.index') }}"
                            class="inline-flex items-center justify-center gap-2 rounded-2xl border border-white/15 bg-white/10 px-5 py-3 text-sm font-black text-white transition hover:bg-white/15">
                            Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Info Cards --}}
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-5 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                <p class="text-xs font-black uppercase tracking-[0.2em] text-[#6B3E12]">
                    Kode Promo
                </p>
                <p class="mt-3 text-lg font-black text-[#2B1A10]">
                    {{ $promo->kode_promo ?: '-' }}
                </p>
            </div>

            <div class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-5 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                <p class="text-xs font-black uppercase tracking-[0.2em] text-[#6B3E12]">
                    Diskon
                </p>
                <p class="mt-3 text-lg font-black text-[#2B1A10]">
                    {{ $promo->nilai_diskon_formatted }}
                </p>
                <p class="mt-1 text-sm font-bold text-[#6B3E12]">
                    {{ $promo->tipe_diskon_label }}
                </p>
            </div>

            <div class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-5 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                <p class="text-xs font-black uppercase tracking-[0.2em] text-[#6B3E12]">
                    Cakupan
                </p>
                <p class="mt-3 text-lg font-black text-[#2B1A10]">
                    {{ $promo->cakupan_promo_label }}
                </p>
                <p class="mt-1 text-sm font-bold text-[#6B3E12]">
                    {{ $promo->cakupan_promo === 'menu_tertentu' ? $promo->products->count() . ' produk' : 'Semua produk' }}
                </p>
            </div>

            <div class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-5 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                <p class="text-xs font-black uppercase tracking-[0.2em] text-[#6B3E12]">
                    Status
                </p>

                <div class="mt-3 flex flex-wrap gap-2">
                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-black {{ $statusClass }}">
                        {{ ucfirst($promo->status) }}
                    </span>

                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-black {{ $runningClass }}">
                        {{ $promo->is_berjalan ? 'Berjalan' : 'Tidak Berjalan' }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Detail --}}
        <div class="grid gap-6 xl:grid-cols-[1fr_360px]">
            <div class="space-y-6">
                {{-- Deskripsi --}}
                <div
                    class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-6 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                    <h2 class="text-lg font-black text-[#2B1A10]">
                        Deskripsi Promo
                    </h2>

                    <p class="mt-4 text-sm font-semibold leading-relaxed text-[#6B3E12]">
                        {{ $promo->deskripsi ?: 'Belum ada deskripsi untuk promo ini.' }}
                    </p>
                </div>

                {{-- Produk --}}
                <div
                    class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-6 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-lg font-black text-[#2B1A10]">
                                Produk yang Mendapat Promo
                            </h2>
                            <p class="mt-1 text-sm font-semibold text-[#6B3E12]">
                                {{ $promo->cakupan_promo === 'semua_menu' ? 'Promo berlaku untuk semua menu.' : 'Promo hanya berlaku untuk produk berikut.' }}
                            </p>
                        </div>
                    </div>

                    @if ($promo->cakupan_promo === 'semua_menu')
                        <div class="mt-5 rounded-2xl bg-[#F7F6F4] p-5">
                            <p class="text-sm font-bold leading-relaxed text-[#6B3E12]">
                                Promo ini akan dihitung untuk semua produk yang masuk ke transaksi.
                            </p>
                        </div>
                    @else
                        <div class="mt-5 grid gap-3 sm:grid-cols-2">
                            @forelse ($promo->products as $product)
                                <div class="rounded-2xl border border-[#F4D3B0] bg-[#F7F6F4] p-4">
                                    <p class="text-sm font-black text-[#2B1A10]">
                                        {{ $product->nama_produk }}
                                    </p>
                                    <p class="mt-1 text-xs font-bold text-[#6B3E12]">
                                        {{ $product->harga_jual_formatted }}
                                    </p>
                                </div>
                            @empty
                                <div
                                    class="col-span-full rounded-2xl border border-dashed border-[#F4D3B0] p-5 text-center">
                                    <p class="text-sm font-bold text-[#6B3E12]">
                                        Belum ada produk yang dipilih.
                                    </p>
                                </div>
                            @endforelse
                        </div>
                    @endif
                </div>
            </div>

            {{-- Side Info --}}
            <div class="space-y-6">
                <div
                    class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-6 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                    <h2 class="text-lg font-black text-[#2B1A10]">
                        Periode Promo
                    </h2>

                    <div class="mt-4 space-y-3">
                        <div class="rounded-2xl bg-[#F7F6F4] p-4">
                            <p class="text-xs font-black uppercase tracking-[0.2em] text-[#6B3E12]">
                                Mulai
                            </p>
                            <p class="mt-1 text-sm font-black text-[#2B1A10]">
                                {{ $promo->tanggal_mulai ? $promo->tanggal_mulai->format('d M Y') : 'Tidak dibatasi' }}
                            </p>
                        </div>

                        <div class="rounded-2xl bg-[#F7F6F4] p-4">
                            <p class="text-xs font-black uppercase tracking-[0.2em] text-[#6B3E12]">
                                Selesai
                            </p>
                            <p class="mt-1 text-sm font-black text-[#2B1A10]">
                                {{ $promo->tanggal_selesai ? $promo->tanggal_selesai->format('d M Y') : 'Tidak dibatasi' }}
                            </p>
                        </div>
                    </div>
                </div>

                <div
                    class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-6 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                    <h2 class="text-lg font-black text-[#2B1A10]">
                        Pengaturan Status
                    </h2>

                    <p class="mt-2 text-sm font-semibold leading-relaxed text-[#6B3E12]">
                        {{ $promo->status === 'aktif'
                            ? 'Promo sedang aktif dan dapat digunakan jika periode tanggal masih berlaku.'
                            : 'Promo sedang nonaktif dan tidak akan digunakan pada transaksi.' }}
                    </p>

                    <button type="button"
                        @click="openStatusModal(@js(route('admin.promos.update-status', $promo)), @js($promo->status))"
                        class="{{ $promo->status === 'aktif'
                            ? 'bg-[#A92A35] text-white shadow-[#A92A35]/20'
                            : 'bg-[#F4B044] text-[#2B1A10] shadow-[#F4B044]/20' }}
                        mt-5 inline-flex h-12 w-full items-center justify-center rounded-2xl px-5 text-sm font-black shadow-lg transition hover:-translate-y-0.5 hover:shadow-xl">
                        {{ $promo->status === 'aktif' ? 'Nonaktifkan Promo' : 'Aktifkan Promo' }}
                    </button>
                </div>
            </div>
        </div>

        {{-- Confirm Modal --}}
        <template x-teleport="body">
            <div x-show="confirmModalOpen" x-cloak x-transition.opacity
                class="fixed inset-0 z-[9998] flex items-center justify-center bg-[#1F444C]/55 px-4 backdrop-blur-md">
                <div @click.outside="closeConfirmModal()" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                    class="w-full max-w-md overflow-hidden rounded-3xl border border-[#F4D3B0]/70 bg-white shadow-[0_30px_90px_-35px_rgba(31,68,76,0.8)]">

                    <div class="bg-[#1F444C] px-6 py-5 text-white">
                        <h2 class="text-lg font-black" x-text="confirmTitle"></h2>
                        <p class="mt-1 text-sm font-medium text-white/80" x-text="confirmMessage"></p>
                    </div>

                    <div class="p-6">
                        <div class="flex justify-end gap-3">
                            <button type="button" @click="closeConfirmModal()"
                                class="inline-flex h-11 items-center justify-center rounded-2xl border border-[#F4D3B0] bg-white px-5 text-sm font-black text-[#6B3E12] transition hover:bg-[#F7F6F4]">
                                Batal
                            </button>

                            <form method="POST" :action="confirmAction">
                                @csrf
                                @method('PATCH')

                                <button type="submit" :class="confirmButtonClass"
                                    class="inline-flex h-11 items-center justify-center rounded-2xl px-5 text-sm font-black shadow-lg transition hover:-translate-y-0.5 hover:shadow-xl focus:outline-none focus:ring-4"
                                    x-text="confirmButtonText">
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
@endsection

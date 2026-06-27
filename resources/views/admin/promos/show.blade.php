@extends('layouts.master')

@section('page_title', 'Detail Promo')

@section('content')
    @php
        $statusClass =
            $promo->status_efektif === 'aktif' ? 'bg-[#1F444C]/10 text-[#1F444C]' : 'bg-[#A92A35]/10 text-[#A92A35]';

        $runningClass = $promo->is_berjalan ? 'bg-[#F4B044]/20 text-[#6B3E12]' : 'bg-[#6B3E12]/10 text-[#6B3E12]';
    @endphp

    <div class="space-y-6">
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
                        {{ $promo->status_efektif_label }}
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
            </div>
        </div>

    </div>
@endsection

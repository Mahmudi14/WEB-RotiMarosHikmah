@extends('layouts.master')

@section('page_title', 'Detail Shift')

@section('content')
    @php
        $totalCashSales = (float) $totals['total_cash_sales'];
        $totalNonCashSales = (float) $totals['total_non_cash_sales'];
        $totalExpenses = (float) $totals['total_expenses'];

        $totalIncome = (float) ($totals['total_income'] ?? $totalCashSales + $totalNonCashSales);
        $netIncome = (float) ($totals['net_income'] ?? $totalIncome - $totalExpenses);

        // Acuan uang laci saat tutup shift
        $cashInSystem = (float) ($totals['cash_in_system'] ?? $totals['expected_cash']);
    @endphp

    <div class="space-y-6" x-data="{
        openedAt: @js($shift->opened_at?->toIso8601String()),
        durationText: '-',
    
        updateDuration() {
            if (!this.openedAt) {
                this.durationText = '-';
                return;
            }
    
            const start = new Date(this.openedAt);
            const now = new Date();
            const diff = Math.max(0, now - start);
    
            const hours = Math.floor(diff / 1000 / 60 / 60);
            const minutes = Math.floor((diff / 1000 / 60) % 60);
    
            this.durationText = `${hours} jam ${minutes} menit`;
        }
    }" x-init="updateDuration();
    setInterval(() => updateDuration(), 60000)">
        {{-- Header --}}
        <div class="overflow-hidden rounded-3xl bg-[#1F444C] p-6 text-white shadow-lg shadow-[#1F444C]/10">
            <div class="relative">
                <div class="absolute -right-10 -top-12 h-32 w-32 rounded-full bg-[#F4B044]/20"></div>
                <div class="absolute -bottom-16 -left-12 h-36 w-36 rounded-full bg-white/10"></div>

                <div
                    class="relative flex flex-col gap-5 min-[835px]:flex-row min-[835px]:items-center min-[835px]:justify-between">
                    <div class="min-w-0">
                        <p class="text-xs font-bold uppercase tracking-[0.28em] text-[#F4D3B0]">
                            Kasir / Shift / Detail
                        </p>

                        <h1 class="mt-2 text-2xl font-black tracking-tight">
                            Detail Shift
                        </h1>

                        <p class="mt-2 max-w-2xl text-sm font-medium leading-relaxed text-[#F7F6F4]/80">
                            {{ $shift->terminal?->kode_terminal }} - {{ $shift->terminal?->nama_terminal }}
                        </p>
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row min-[835px]:shrink-0">
                        @if ($shift->status === 'aktif')
                            <a href="{{ route('cashier.shifts.close-form', $shift) }}"
                                class="inline-flex h-12 items-center justify-center rounded-2xl bg-[#A92A35] px-5 text-sm font-black text-white shadow-lg shadow-[#A92A35]/20 transition hover:-translate-y-0.5 hover:shadow-xl">
                                Tutup Shift
                            </a>
                        @endif

                        <a href="{{ route('cashier.shifts.index') }}"
                            class="inline-flex h-12 items-center justify-center rounded-2xl border border-white/15 bg-white/10 px-5 text-sm font-black text-white transition hover:bg-white/15">
                            Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Info Cards --}}
        <div class="grid gap-4 md:grid-cols-2 min-[1280px]:grid-cols-4">
            <div class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-5 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                <p class="text-xs font-black uppercase tracking-[0.2em] text-[#6B3E12]">
                    Status
                </p>

                <span class="mt-3 inline-flex rounded-full bg-[#1F444C]/10 px-3 py-1 text-xs font-black text-[#1F444C]">
                    {{ $shift->status_label }}
                </span>
            </div>

            <div class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-5 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                <p class="text-xs font-black uppercase tracking-[0.2em] text-[#6B3E12]">
                    Terminal
                </p>

                <p class="mt-3 truncate text-sm font-black text-[#2B1A10]">
                    {{ $shift->terminal?->nama_terminal }}
                </p>

                <p class="mt-1 truncate text-xs font-bold text-[#6B3E12]">
                    {{ $shift->terminal?->kode_terminal }}
                </p>
            </div>

            <div class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-5 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                <p class="text-xs font-black uppercase tracking-[0.2em] text-[#6B3E12]">
                    Jam Buka
                </p>

                <p class="mt-3 text-sm font-black text-[#2B1A10]">
                    {{ $shift->opened_at->format('d M Y, H:i') }}
                </p>
            </div>

            <div class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-5 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                <p class="text-xs font-black uppercase tracking-[0.2em] text-[#6B3E12]">
                    Total Jam
                </p>

                <p class="mt-3 text-sm font-black text-[#2B1A10]" x-text="durationText">
                    -
                </p>
            </div>
        </div>

        {{-- Content --}}
        <div class="grid gap-5 min-[835px]:grid-cols-[300px_minmax(0,1fr)] min-[1280px]:grid-cols-[360px_minmax(0,1fr)]">
            {{-- Informasi Shift --}}
            <div
                class="overflow-hidden rounded-3xl border border-[#F4D3B0]/70 bg-white shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                <div class="border-b border-[#F4D3B0]/70 bg-[#F7F6F4] px-5 py-4">
                    <p class="text-xs font-black uppercase tracking-[0.22em] text-[#6B3E12]">
                        Informasi Shift
                    </p>

                    <h2 class="mt-1 text-lg font-black text-[#2B1A10]">
                        Shift Berjalan
                    </h2>
                </div>

                <div class="p-5">
                    <div class="rounded-2xl bg-[#F7F6F4] p-4">
                        <p class="text-xs font-black uppercase tracking-[0.18em] text-[#6B3E12]">
                            Catatan Buka
                        </p>

                        <p class="mt-2 text-sm font-semibold leading-relaxed text-[#6B3E12]">
                            {{ $shift->opening_note ?: 'Tidak ada catatan buka shift.' }}
                        </p>
                    </div>

                    <div class="mt-4 rounded-2xl bg-[#1F444C] p-4 text-white">
                        <p class="text-xs font-black uppercase tracking-[0.18em] text-[#F4D3B0]">
                            Panduan
                        </p>

                        <p class="mt-2 text-sm font-semibold leading-relaxed text-white/75">
                            Ringkasan pemasukan menghitung cash dan non cash. Untuk perhitungan laci saat tutup shift,
                            sistem hanya memakai kas awal, cash, dan pengeluaran.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Ringkasan --}}
            <div class="space-y-6">
                <div
                    class="overflow-hidden rounded-3xl border border-[#F4D3B0]/70 bg-white shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                    <div class="border-b border-[#F4D3B0]/70 bg-[#F7F6F4] px-5 py-4">
                        <p class="text-xs font-black uppercase tracking-[0.22em] text-[#6B3E12]">
                            Ringkasan Shift
                        </p>

                        <h2 class="mt-1 text-lg font-black text-[#2B1A10]">
                            Pemasukan & Pengeluaran
                        </h2>
                    </div>

                    <div class="p-5">
                        <div class="grid gap-4 md:grid-cols-2 min-[1280px]:grid-cols-2">
                            <div class="rounded-3xl bg-[#F7F6F4] p-5">
                                <p class="text-xs font-black uppercase tracking-[0.18em] text-[#6B3E12]">
                                    Cash
                                </p>

                                <p class="mt-3 text-xl font-black text-[#1F444C]">
                                    Rp {{ number_format($totalCashSales, 0, ',', '.') }}
                                </p>

                                <p class="mt-2 text-xs font-semibold text-[#6B3E12]">
                                    Tunai
                                </p>
                            </div>

                            <div class="rounded-3xl bg-[#F7F6F4] p-5">
                                <p class="text-xs font-black uppercase tracking-[0.18em] text-[#6B3E12]">
                                    Non Cash
                                </p>

                                <p class="mt-3 text-xl font-black text-[#1F444C]">
                                    Rp {{ number_format($totalNonCashSales, 0, ',', '.') }}
                                </p>

                                <p class="mt-2 text-xs font-semibold text-[#6B3E12]">
                                    QRIS / transfer
                                </p>
                            </div>

                            <div class="rounded-3xl bg-[#F7F6F4] p-5">
                                <p class="text-xs font-black uppercase tracking-[0.18em] text-[#6B3E12]">
                                    Total Pemasukan
                                </p>

                                <p class="mt-3 text-xl font-black text-[#2B1A10]">
                                    Rp {{ number_format($totalIncome, 0, ',', '.') }}
                                </p>

                                <p class="mt-2 text-xs font-semibold text-[#6B3E12]">
                                    Cash + non cash
                                </p>
                            </div>

                            <div class="rounded-3xl bg-[#F7F6F4] p-5">
                                <p class="text-xs font-black uppercase tracking-[0.18em] text-[#6B3E12]">
                                    Pengeluaran
                                </p>

                                <p class="mt-3 text-xl font-black text-[#A92A35]">
                                    Rp {{ number_format($totalExpenses, 0, ',', '.') }}
                                </p>

                                <p class="mt-2 text-xs font-semibold text-[#6B3E12]">
                                    Mengurangi pemasukan
                                </p>
                            </div>
                        </div>

                        <div class="mt-4 grid gap-4 min-[1280px]:grid-cols-[minmax(0,1fr)_280px]">
                            <div class="rounded-3xl bg-[#1F444C] p-5 text-white">
                                <div
                                    class="flex flex-col gap-2 min-[1280px]:flex-row min-[1280px]:items-center min-[1280px]:justify-between">
                                    <div>
                                        <p class="text-xs font-black uppercase tracking-[0.22em] text-[#F4D3B0]">
                                            Pemasukan Bersih
                                        </p>

                                        <p class="mt-2 text-sm font-semibold text-white/75">
                                            Cash + non cash - pengeluaran
                                        </p>
                                    </div>

                                    <p class="text-2xl font-black text-[#F4B044]">
                                        Rp {{ number_format($netIncome, 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>

                            <div class="rounded-3xl border border-[#F4D3B0]/70 bg-[#F7F6F4] p-5">
                                <p class="text-xs font-black uppercase tracking-[0.18em] text-[#6B3E12]">
                                    Kas di Sistem
                                </p>

                                <p class="mt-3 text-xl font-black text-[#2B1A10]">
                                    Rp {{ number_format($cashInSystem, 0, ',', '.') }}
                                </p>

                                <p class="mt-2 text-xs font-semibold text-[#6B3E12]">
                                    Kas awal + cash - pengeluaran
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Ringkasan Produk Terjual --}}
                <div
                    class="overflow-hidden rounded-3xl border border-[#F4D3B0]/70 bg-white shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                    <div class="border-b border-[#F4D3B0]/70 bg-[#F7F6F4] px-5 py-4">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="text-xs font-black uppercase tracking-[0.22em] text-[#6B3E12]">
                                    Produk Terjual
                                </p>

                                <h2 class="mt-1 text-lg font-black text-[#2B1A10]">
                                    Ringkasan Produk
                                </h2>
                            </div>

                            <span
                                class="inline-flex w-fit rounded-2xl bg-[#F4B044]/20 px-4 py-2 text-sm font-black text-[#6B3E12]">
                                {{ $totalProductsSold }} item
                            </span>
                        </div>
                    </div>

                    <div class="divide-y divide-[#F4D3B0]/60">
                        @forelse ($productSales as $product)
                            <div
                                class="flex flex-col gap-3 px-5 py-4 min-[835px]:flex-row min-[835px]:items-center min-[835px]:justify-between">
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-black text-[#2B1A10]">
                                        {{ $product->nama_produk }}
                                    </p>

                                </div>

                                <div class="shrink-0">
                                    <span
                                        class="inline-flex w-full justify-center rounded-2xl bg-[#1F444C]/10 px-4 py-2 text-sm font-black text-[#1F444C] min-[835px]:w-auto min-[835px]:min-w-24">
                                        {{ (int) $product->total_qty }} terjual
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="px-5 py-10 text-center">
                                <h3 class="text-base font-black text-[#2B1A10]">
                                    Belum Ada Produk Terjual
                                </h3>

                                <p class="mt-2 text-sm font-semibold text-[#6B3E12]">
                                    Produk yang terjual akan muncul setelah ada transaksi selesai.
                                </p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

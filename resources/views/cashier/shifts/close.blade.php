@extends('layouts.master')

@section('page_title', 'Tutup Shift')

@section('content')
    @php
        $totalCashSales = (float) $totals['total_cash_sales'];
        $totalNonCashSales = (float) $totals['total_non_cash_sales'];

        $totalIncome = (float) ($totals['total_income'] ?? $totalCashSales + $totalNonCashSales);

        $totalExpenses = (float) $totals['total_expenses'];

        $netIncome = (float) ($totals['net_income'] ?? $totalIncome - $totalExpenses);

        // Untuk selisih laci, tetap menggunakan uang kas di sistem.
        $cashInSystem = (float) ($totals['cash_in_system'] ?? $totals['expected_cash']);
    @endphp

    <div class="space-y-6">
        {{-- Header --}}
        <div class="overflow-hidden rounded-3xl bg-[#1F444C] p-6 text-white shadow-lg shadow-[#1F444C]/10">
            <div class="relative">
                <div class="absolute -right-10 -top-12 h-32 w-32 rounded-full bg-[#F4B044]/20">
                </div>

                <div class="absolute -bottom-16 -left-12 h-36 w-36 rounded-full bg-white/10">
                </div>

                <div class="relative flex flex-col gap-5 md:flex-row md:items-center md:justify-between">

                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.28em] text-[#F4D3B0]">
                            Kasir / Shift / Tutup
                        </p>

                        <h1 class="mt-2 text-2xl font-black tracking-tight">
                            Tutup Shift
                        </h1>

                        <p class="mt-2 max-w-2xl text-sm font-medium leading-relaxed text-[#F7F6F4]/80">
                            Masukkan uang fisik di laci untuk menghitung selisih kas.
                        </p>
                    </div>

                    <a href="{{ route('cashier.shifts.show', $shift) }}"
                        class="inline-flex items-center justify-center rounded-2xl border border-white/15 bg-white/10 px-5 py-3 text-sm font-black text-white transition hover:bg-white/15">
                        Kembali
                    </a>
                </div>
            </div>
        </div>

        <div class="grid gap-6 min-[1024px]:grid-cols-[minmax(0,1fr)_440px]">

            {{-- Ringkasan --}}
            <div class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-6 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">

                <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-xs font-black uppercase tracking-[0.22em] text-[#6B3E12]">
                            Ringkasan Shift
                        </p>

                        <h2 class="mt-1 text-lg font-black text-[#2B1A10]">
                            Informasi Keuangan
                        </h2>
                    </div>

                    <span class="inline-flex rounded-2xl bg-[#F4B044]/20 px-4 py-2 text-sm font-black text-[#6B3E12]">
                        {{ $shift->terminal?->kode_terminal }}
                    </span>
                </div>

                <div class="mt-5 grid gap-4 md:grid-cols-2">
                    <div class="rounded-3xl bg-[#F7F6F4] p-5">
                        <p class="text-xs font-black uppercase tracking-[0.18em] text-[#6B3E12]">
                            Penjualan Tunai
                        </p>

                        <p class="mt-3 text-2xl font-black text-[#1F444C]">
                            Rp {{ number_format($totalCashSales, 0, ',', '.') }}
                        </p>
                    </div>

                    <div class="rounded-3xl bg-[#F7F6F4] p-5">
                        <p class="text-xs font-black uppercase tracking-[0.18em] text-[#6B3E12]">
                            Penjualan Non Tunai
                        </p>

                        <p class="mt-3 text-2xl font-black text-[#1F444C]">
                            Rp {{ number_format($totalNonCashSales, 0, ',', '.') }}
                        </p>
                    </div>

                    <div class="rounded-3xl bg-[#F7F6F4] p-5">
                        <p class="text-xs font-black uppercase tracking-[0.18em] text-[#6B3E12]">
                            Total Pemasukan
                        </p>

                        <p class="mt-3 text-2xl font-black text-[#2B1A10]">
                            Rp {{ number_format($totalIncome, 0, ',', '.') }}
                        </p>
                    </div>

                    <div class="rounded-3xl bg-[#F7F6F4] p-5">
                        <p class="text-xs font-black uppercase tracking-[0.18em] text-[#6B3E12]">
                            Pengeluaran
                        </p>

                        <p class="mt-3 text-2xl font-black text-[#A92A35]">
                            Rp {{ number_format($totalExpenses, 0, ',', '.') }}
                        </p>
                    </div>
                </div>

                <div class="mt-5 rounded-3xl bg-[#1F444C] p-5 text-white">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-xs font-black uppercase tracking-[0.22em] text-[#F4D3B0]">
                            Pemasukan Bersih
                        </p>

                        <p class="text-2xl font-black text-[#F4B044]">
                            Rp {{ number_format($netIncome, 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Form --}}
            <div class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-6 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]"
                x-data="{
                    confirmClose: false,
                    isSubmitting: false,
                    submitConfirmed: false,
                
                    cashInSystem: @js($cashInSystem),
                    closingCash: @js((string) old('closing_cash', '')),
                
                    openConfirm() {
                        if (this.isSubmitting) {
                            return;
                        }
                
                        if (!this.$refs.closeForm.reportValidity()) {
                            return;
                        }
                
                        this.submitConfirmed = false;
                        this.confirmClose = true;
                    },
                
                    cancelConfirm() {
                        if (this.isSubmitting) {
                            return;
                        }
                
                        this.confirmClose = false;
                        this.submitConfirmed = false;
                    },
                
                    submitClose() {
                        if (this.isSubmitting) {
                            return;
                        }
                
                        if (!this.$refs.closeForm.reportValidity()) {
                            this.confirmClose = false;
                            this.submitConfirmed = false;
                            return;
                        }
                
                        this.submitConfirmed = true;
                        this.$refs.closeForm.requestSubmit();
                    },
                
                    handleSubmit(event) {
                        if (this.isSubmitting) {
                            event.preventDefault();
                            return;
                        }
                
                        /*
                         * Submit melalui tombol Enter tetap harus
                         * melewati modal konfirmasi.
                         */
                        if (!this.submitConfirmed) {
                            event.preventDefault();
                            this.openConfirm();
                            return;
                        }
                
                        this.isSubmitting = true;
                    },
                
                    parseCurrency(value) {
                        return Number(
                            String(value).replace(/[^0-9]/g, '')
                        ) || 0;
                    },
                
                    formatCurrencyNumber(value) {
                        return new Intl.NumberFormat('id-ID').format(
                            Math.abs(Number(value) || 0)
                        );
                    },
                
                    formatRupiah(value) {
                        const number = Number(value) || 0;
                
                        return number < 0 ?
                            '-Rp ' + this.formatCurrencyNumber(number) :
                            'Rp ' + this.formatCurrencyNumber(number);
                    },
                
                    formatCurrency(event) {
                        const value = String(event.target.value)
                            .replace(/[^0-9]/g, '');
                
                        this.closingCash = value ?
                            this.formatCurrencyNumber(value) :
                            '';
                
                        event.target.value = this.closingCash;
                    },
                
                    get closingCashNumber() {
                        return this.parseCurrency(this.closingCash);
                    },
                
                    get difference() {
                        return this.closingCashNumber - this.cashInSystem;
                    },
                
                    get differenceFormatted() {
                        return this.formatRupiah(this.difference);
                    },
                
                    get differenceStatus() {
                        if (this.difference === 0) {
                            return 'Sesuai';
                        }
                
                        return this.difference > 0 ?
                            'Kelebihan' :
                            'Kekurangan';
                    },
                
                }">

                <h2 class="text-lg font-black text-[#2B1A10]">
                    Form Tutup Shift
                </h2>

                <form x-ref="closeForm" method="POST" action="{{ route('cashier.shifts.close', $shift) }}"
                    class="mt-6 space-y-6" @submit="handleSubmit($event)">

                    @csrf
                    @method('PATCH')

                    {{-- Uang Fisik --}}
                    <div>
                        <label class="mb-2 block text-sm font-black text-[#2B1A10]">
                            Uang Fisik di Laci
                            <span class="text-[#A92A35]">*</span>
                        </label>

                        <div class="relative">
                            <span
                                class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-sm font-black text-[#6B3E12]">
                                Rp
                            </span>

                            <input type="text" name="closing_cash" inputmode="numeric" autocomplete="off"
                                x-init="if (closingCash) {
                                    closingCash = formatCurrencyNumber(
                                        parseCurrency(closingCash)
                                    );
                                
                                    $el.value = closingCash;
                                }" @input="formatCurrency($event)" placeholder="0" required
                                :disabled="isSubmitting"
                                class="block h-12 w-full rounded-2xl border border-[#F4D3B0] bg-[#F7F6F4] py-0 pl-12 pr-4 text-sm font-medium text-[#2B1A10] shadow-sm transition placeholder:text-[#6B3E12]/45 focus:border-[#F4B044] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#F4B044]/20 disabled:cursor-not-allowed disabled:opacity-60">
                        </div>

                        @error('closing_cash')
                            <p class="mt-2 text-sm font-bold text-[#A92A35]">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Informasi Kas --}}
                    <div class="space-y-3 rounded-3xl bg-[#F7F6F4] p-5">
                        <div class="flex items-center justify-between gap-4">
                            <p class="text-xs font-black uppercase tracking-[0.18em] text-[#6B3E12]">
                                Kas Menurut Sistem
                            </p>

                            <p class="text-sm font-black text-[#2B1A10]">
                                Rp {{ number_format($cashInSystem, 0, ',', '.') }}
                            </p>
                        </div>

                        <div class="h-px bg-[#F4D3B0]"></div>

                        <div class="flex items-center justify-between gap-4">
                            <p class="text-xs font-black uppercase tracking-[0.18em] text-[#6B3E12]">
                                Uang Fisik di Laci
                            </p>

                            <p class="text-sm font-black text-[#2B1A10]">
                                Rp <span x-text="closingCash || '0'"></span>
                            </p>
                        </div>

                        <div class="rounded-2xl bg-white p-4">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <p class="text-xs font-black uppercase tracking-[0.18em] text-[#6B3E12]">
                                        Selisih Kas
                                    </p>

                                    <p class="mt-1 text-sm font-black"
                                        :class="difference < 0 ?
                                            'text-[#A92A35]' :
                                            'text-[#1F444C]'"
                                        x-text="differenceStatus">
                                    </p>
                                </div>

                                <p class="text-xl font-black"
                                    :class="difference < 0 ?
                                        'text-[#A92A35]' :
                                        'text-[#1F444C]'"
                                    x-text="differenceFormatted">
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Catatan --}}
                    <div>
                        <label class="mb-2 block text-sm font-black text-[#2B1A10]">
                            Catatan Tutup Shift

                            <span class="font-semibold text-[#6B3E12]/60">
                                (Opsional)
                            </span>
                        </label>

                        <textarea name="closing_note" rows="4" placeholder="Contoh: Shift ditutup normal." :disabled="isSubmitting"
                            class="block w-full rounded-2xl border border-[#F4D3B0] bg-[#F7F6F4] px-4 py-3 text-sm font-medium text-[#2B1A10] shadow-sm transition placeholder:text-[#6B3E12]/45 focus:border-[#F4B044] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#F4B044]/20 disabled:cursor-not-allowed disabled:opacity-60">{{ old('closing_note') }}</textarea>

                        @error('closing_note')
                            <p class="mt-2 text-sm font-bold text-[#A92A35]">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Action --}}
                    <div class="flex flex-col-reverse gap-3 border-t border-[#F4D3B0]/70 pt-6">

                        <a href="{{ route('cashier.shifts.show', $shift) }}"
                            @click="if (isSubmitting) $event.preventDefault()"
                            class="inline-flex h-12 items-center justify-center rounded-2xl border border-[#F4D3B0] bg-white px-6 text-sm font-black text-[#6B3E12] transition hover:bg-[#F7F6F4]">
                            Batal
                        </a>

                        <button type="button" @click="openConfirm()" :disabled="isSubmitting"
                            class="inline-flex h-12 items-center justify-center gap-2 rounded-2xl bg-[#A92A35] px-6 text-sm font-black text-white shadow-lg shadow-[#A92A35]/20 transition hover:-translate-y-0.5 hover:shadow-xl disabled:cursor-not-allowed disabled:opacity-60 disabled:hover:translate-y-0">

                            <svg x-show="isSubmitting" x-cloak class="h-5 w-5 animate-spin" fill="none"
                                viewBox="0 0 24 24">

                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4">
                                </circle>

                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z">
                                </path>
                            </svg>

                            <span x-show="!isSubmitting">
                                Tutup Shift
                            </span>

                            <span x-show="isSubmitting" x-cloak>
                                Menutup Shift...
                            </span>
                        </button>
                    </div>
                </form>

                {{-- Modal Konfirmasi --}}
                <template x-teleport="body">
                    <div x-show="confirmClose" x-cloak @keydown.escape.window="cancelConfirm()"
                        class="fixed inset-0 z-[9998] flex items-center justify-center bg-[#1F444C]/55 px-4 backdrop-blur-md"
                        x-transition.opacity>

                        <div @click.outside="cancelConfirm()" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95 translate-y-3"
                            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                            x-transition:leave-end="opacity-0 scale-95 translate-y-3"
                            class="w-full max-w-md overflow-hidden rounded-3xl bg-white shadow-2xl shadow-[#1F444C]/25">

                            <div class="bg-[#A92A35] px-6 py-5 text-white">
                                <div class="flex items-start gap-4">
                                    <div
                                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-white/15">

                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2.4"
                                            viewBox="0 0 24 24">

                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                                        </svg>
                                    </div>

                                    <div>
                                        <h2 class="text-lg font-black">
                                            Konfirmasi Tutup Shift
                                        </h2>

                                        <p class="mt-1 text-sm font-semibold text-white/80">
                                            Pastikan uang fisik di laci sudah benar.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-4 p-6">
                                <div class="rounded-2xl bg-[#F7F6F4] p-4">
                                    <p class="text-xs font-black uppercase tracking-[0.18em] text-[#6B3E12]">
                                        Uang Kas di Sistem
                                    </p>

                                    <p class="mt-2 text-xl font-black text-[#2B1A10]">
                                        Rp {{ number_format($cashInSystem, 0, ',', '.') }}
                                    </p>
                                </div>

                                <div class="rounded-2xl bg-[#F7F6F4] p-4">
                                    <p class="text-xs font-black uppercase tracking-[0.18em] text-[#6B3E12]">
                                        Uang Fisik di Laci
                                    </p>

                                    <p class="mt-2 text-xl font-black text-[#2B1A10]">
                                        Rp <span x-text="closingCash || '0'"></span>
                                    </p>
                                </div>

                                <div class="rounded-2xl bg-white p-4 ring-1 ring-[#F4D3B0]">

                                    <p class="text-xs font-black uppercase tracking-[0.18em] text-[#6B3E12]">
                                        Selisih Kas
                                    </p>

                                    <div class="mt-2 flex items-center justify-between gap-4">

                                        <span class="text-sm font-black"
                                            :class="difference < 0 ?
                                                'text-[#A92A35]' :
                                                'text-[#1F444C]'"
                                            x-text="differenceStatus">
                                        </span>

                                        <span class="text-xl font-black"
                                            :class="difference < 0 ?
                                                'text-[#A92A35]' :
                                                'text-[#1F444C]'"
                                            x-text="differenceFormatted">
                                        </span>
                                    </div>
                                </div>

                                <div class="grid gap-3 pt-2 sm:grid-cols-2">
                                    <button type="button" @click="cancelConfirm()" :disabled="isSubmitting"
                                        class="inline-flex h-12 items-center justify-center rounded-2xl border border-[#F4D3B0] bg-white px-5 text-sm font-black text-[#6B3E12] transition hover:bg-[#F7F6F4] disabled:cursor-not-allowed disabled:opacity-50">
                                        Batal
                                    </button>

                                    <button type="button" @click="submitClose()" :disabled="isSubmitting"
                                        class="inline-flex h-12 items-center justify-center gap-2 rounded-2xl bg-[#A92A35] px-5 text-sm font-black text-white shadow-lg shadow-[#A92A35]/20 transition hover:-translate-y-0.5 hover:shadow-xl disabled:cursor-not-allowed disabled:opacity-60 disabled:hover:translate-y-0">

                                        <svg x-show="isSubmitting" x-cloak class="h-5 w-5 animate-spin" fill="none"
                                            viewBox="0 0 24 24">

                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="4">
                                            </circle>

                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z">
                                            </path>
                                        </svg>

                                        <span x-show="!isSubmitting">
                                            Ya, Tutup Shift
                                        </span>

                                        <span x-show="isSubmitting" x-cloak>
                                            Menutup Shift...
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
@endsection

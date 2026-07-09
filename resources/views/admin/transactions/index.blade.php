@extends('layouts.master')

@section('page_title', 'Riwayat Transaksi')

@section('content')
    <div class="space-y-6">
        {{-- Header --}}
        <div class="overflow-hidden rounded-3xl bg-[#1F444C] p-6 text-white shadow-lg shadow-[#1F444C]/10">
            <div class="relative">
                <div class="absolute -right-10 -top-12 h-32 w-32 rounded-full bg-[#F4B044]/20"></div>
                <div class="absolute -bottom-16 -left-12 h-36 w-36 rounded-full bg-white/10"></div>

                <div
                    class="relative flex flex-col gap-5 min-[1024px]:flex-row min-[1024px]:items-center min-[1024px]:justify-between">
                    <div class="min-w-0">
                        <p class="text-xs font-bold uppercase tracking-[0.28em] text-[#F4D3B0]">
                            Admin / Transaksi
                        </p>

                        <h1 class="mt-2 text-2xl font-black tracking-tight">
                            Riwayat Transaksi
                        </h1>

                        <p class="mt-2 max-w-2xl text-sm font-medium leading-relaxed text-[#F7F6F4]/80">
                            Pantau seluruh transaksi dari semua kasir, terminal, dan metode pembayaran.
                        </p>
                    </div>

                    <a href="{{ route('admin.income-analysis.index') }}"
                        class="inline-flex h-12 items-center justify-center rounded-2xl bg-[#F4B044] px-5 text-sm font-black text-[#2B1A10] shadow-lg shadow-[#F4B044]/20 transition hover:-translate-y-0.5 hover:shadow-xl min-[1024px]:shrink-0">
                        Analisis Pendapatan
                    </a>
                </div>
            </div>
        </div>

        {{-- Summary --}}
        <div class="grid gap-4 md:grid-cols-2 min-[1280px]:grid-cols-4">
            <div class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-5 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                <p class="text-xs font-black uppercase tracking-[0.2em] text-[#6B3E12]">
                    Total Transaksi
                </p>

                <p class="mt-3 text-2xl font-black text-[#2B1A10]">
                    {{ number_format($summary['total_transactions'], 0, ',', '.') }}
                </p>

                <p class="mt-1 text-xs font-semibold text-[#6B3E12]">
                    Semua status transaksi
                </p>
            </div>

            <div class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-5 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                <p class="text-xs font-black uppercase tracking-[0.2em] text-[#6B3E12]">
                    Transaksi Selesai
                </p>

                <p class="mt-3 text-2xl font-black text-[#1F444C]">
                    {{ number_format($summary['completed_transactions'], 0, ',', '.') }}
                </p>

                <p class="mt-1 text-xs font-semibold text-[#6B3E12]">
                    Dibatalkan: {{ number_format($summary['cancelled_transactions'], 0, ',', '.') }}
                </p>
            </div>

            <div class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-5 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                <p class="text-xs font-black uppercase tracking-[0.2em] text-[#6B3E12]">
                    Total Penjualan
                </p>

                <p class="mt-3 text-xl font-black text-[#1F444C]">
                    Rp {{ number_format($summary['grand_total'], 0, ',', '.') }}
                </p>

                <p class="mt-1 text-xs font-semibold text-[#6B3E12]">
                    Dari transaksi selesai
                </p>
            </div>

            <div class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-5 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                <p class="text-xs font-black uppercase tracking-[0.2em] text-[#6B3E12]">
                    Non Tunai
                </p>

                <p class="mt-3 text-xl font-black text-[#1F444C]">
                    Rp {{ number_format($summary['non_cash_total'], 0, ',', '.') }}
                </p>

                <p class="mt-1 text-xs font-semibold text-[#6B3E12]">
                    Tunai: Rp {{ number_format($summary['cash_total'], 0, ',', '.') }}
                </p>
            </div>
        </div>

        {{-- Filter --}}
        <div class="relative z-0 overflow-visible rounded-3xl border border-[#F4D3B0]/70 bg-white p-5 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]"
            x-data="{
                cashierOpen: false,
                terminalOpen: false,
                paymentOpen: false,
                statusOpen: false,
            
                selectedCashier: @js((string) request('cashier_id', '')),
                selectedTerminal: @js((string) request('pos_terminal_id', '')),
                selectedPayment: @js((string) request('payment_method', '')),
                selectedStatus: @js((string) request('status', '')),
            
                cashiers: @js(
    $cashiers
        ->mapWithKeys(
            fn($cashier) => [
                (string) $cashier->id => [
                    'name' => $cashier->name,
                    'status' => $cashier->status,
                    'label' => $cashier->name . ($cashier->status === 'nonaktif' ? ' (Nonaktif)' : ''),
                ],
            ],
        )
        ->toArray(),
),
            
                terminals: @js($terminals->mapWithKeys(fn($terminal) => [(string) $terminal->id => $terminal->kode_terminal . ' - ' . $terminal->nama_terminal])->toArray()),
                paymentMethods: @js($paymentMethods),
                statuses: @js($statuses),
            
                get selectedCashierLabel() {
                    return this.selectedCashier && this.cashiers[this.selectedCashier] ?
                        this.cashiers[this.selectedCashier].label :
                        'Semua Kasir';
                },
            
                get selectedTerminalLabel() {
                    return this.selectedTerminal ? this.terminals[this.selectedTerminal] : 'Semua Terminal';
                },
            
                get selectedPaymentLabel() {
                    return this.selectedPayment ? this.paymentMethods[this.selectedPayment] : 'Semua Bayar';
                },
            
                get selectedStatusLabel() {
                    return this.selectedStatus ? this.statuses[this.selectedStatus] : 'Semua Status';
                },
            
                closeDropdowns(except = '') {
                    if (except !== 'cashier') this.cashierOpen = false;
                    if (except !== 'terminal') this.terminalOpen = false;
                    if (except !== 'payment') this.paymentOpen = false;
                    if (except !== 'status') this.statusOpen = false;
                }
            }">
            <form method="GET" action="{{ route('admin.transactions.index') }}" class="space-y-3">
                {{-- Main Filter Row --}}
                <div
                    class="grid gap-3 min-[1024px]:grid-cols-[minmax(0,1fr)_190px_190px_auto] min-[1024px]:items-center min-[1280px]:grid-cols-[minmax(0,1fr)_210px_210px_auto]">
                    {{-- Search --}}
                    <div class="relative min-w-0">
                        <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-[#6B3E12]/60">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M21 21l-4.35-4.35m1.35-5.65a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span>

                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Cari kode transaksi, kasir, atau terminal..."
                            class="block h-12 w-full rounded-2xl border border-[#F4D3B0] bg-[#F7F6F4] py-0 pl-12 pr-4 text-sm font-medium text-[#2B1A10] shadow-sm transition placeholder:text-[#6B3E12]/45 focus:border-[#F4B044] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#F4B044]/20">
                    </div>

                    {{-- Tanggal Mulai --}}
                    <div class="relative min-w-0">
                        <input type="date" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}"
                            class="block h-12 w-full min-w-[190px] rounded-2xl border border-[#F4D3B0] bg-[#F7F6F4] px-3 text-sm font-medium text-[#2B1A10] shadow-sm transition [color-scheme:light] focus:border-[#F4B044] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#F4B044]/20">
                    </div>

                    {{-- Tanggal Selesai --}}
                    <div class="relative min-w-0">
                        <input type="date" name="tanggal_selesai" value="{{ request('tanggal_selesai') }}"
                            class="block h-12 w-full min-w-[190px] rounded-2xl border border-[#F4D3B0] bg-[#F7F6F4] px-3 text-sm font-medium text-[#2B1A10] shadow-sm transition [color-scheme:light] focus:border-[#F4B044] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#F4B044]/20">
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

                        <a href="{{ route('admin.transactions.index') }}"
                            class="inline-flex h-12 shrink-0 items-center justify-center gap-2 rounded-2xl border border-[#F4D3B0] bg-white px-5 py-0 text-sm font-black text-[#6B3E12] transition hover:bg-[#F7F6F4]">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.3"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0013.803-3.7M7.977 14.652H2.985m18.03-5.304-3.181-3.183a8.25 8.25 0 00-13.803 3.7" />
                            </svg>
                            Reset
                        </a>
                    </div>
                </div>

                {{-- Dropdown Row --}}
                <div class="grid gap-3 md:grid-cols-2 min-[1024px]:grid-cols-4">
                    {{-- Kasir --}}
                    <div class="min-w-0">
                        <input type="hidden" name="cashier_id" x-model="selectedCashier">

                        <div class="relative">
                            <button type="button" @click="cashierOpen = !cashierOpen; closeDropdowns('cashier')"
                                class="flex h-12 w-full items-center justify-between rounded-2xl border border-[#F4D3B0] bg-[#F7F6F4] px-4 py-0 text-left text-sm font-bold text-[#2B1A10] shadow-sm transition hover:bg-white focus:border-[#F4B044] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#F4B044]/20">
                                <div class="flex min-w-0 items-center gap-3">
                                    <span
                                        class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-[#F4B044]/20 text-[#6B3E12]">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.2"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.25a8.25 8.25 0 1115 0" />
                                        </svg>
                                    </span>

                                    <span x-text="selectedCashierLabel" class="truncate"
                                        :class="selectedCashier ? 'text-[#2B1A10]' : 'text-[#6B3E12]/60'"></span>
                                </div>

                                <svg class="h-5 w-5 shrink-0 text-[#6B3E12] transition duration-200"
                                    :class="cashierOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                                    stroke-width="2.4" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <div x-show="cashierOpen" x-cloak @click.outside="cashierOpen = false"
                                x-transition:enter="transition ease-out duration-150"
                                x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-100"
                                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                x-transition:leave-end="opacity-0 scale-95 translate-y-1"
                                class="absolute left-0 top-full z-10 mt-3 max-h-72 w-full overflow-y-auto rounded-2xl border border-[#F4D3B0]/80 bg-white shadow-[0_25px_70px_-35px_rgba(31,68,76,0.55)]">
                                <div class="p-2">
                                    <button type="button" @click="selectedCashier = ''; cashierOpen = false"
                                        class="group flex w-full items-center justify-between rounded-xl px-4 py-3 text-left text-sm font-bold transition"
                                        :class="selectedCashier === ''
                                            ?
                                            'bg-[#F4B044] text-[#2B1A10]' :
                                            'text-[#2B1A10] hover:bg-[#F4B044]/15'">
                                        <span>Semua Kasir</span>

                                        <svg x-show="selectedCashier === ''" x-cloak class="h-5 w-5 shrink-0"
                                            fill="none" stroke="currentColor" stroke-width="2.8" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </button>

                                    @foreach ($cashiers as $cashier)
                                        <button type="button"
                                            @click="selectedCashier = @js((string) $cashier->id); cashierOpen = false"
                                            class="group flex w-full items-center justify-between rounded-xl px-4 py-3 text-left text-sm font-bold transition"
                                            :class="selectedCashier === @js((string) $cashier->id) ?
                                                'bg-[#F4B044] text-[#2B1A10]' :
                                                'text-[#2B1A10] hover:bg-[#F4B044]/15'">
                                            <div class="min-w-0">
                                                <div class="flex items-center gap-2">
                                                    <span class="truncate">{{ $cashier->name }}</span>

                                                    @if ($cashier->status === 'nonaktif')
                                                        <span
                                                            class="shrink-0 rounded-full bg-[#A92A35]/10 px-2 py-0.5 text-[10px] font-black uppercase tracking-wide text-[#A92A35]">
                                                            Nonaktif
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <svg x-show="selectedCashier === @js((string) $cashier->id)" x-cloak
                                                class="h-5 w-5 shrink-0" fill="none" stroke="currentColor"
                                                stroke-width="2.8" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Terminal --}}
                    <div class="min-w-0">
                        <input type="hidden" name="pos_terminal_id" x-model="selectedTerminal">

                        <div class="relative">
                            <button type="button" @click="terminalOpen = !terminalOpen; closeDropdowns('terminal')"
                                class="flex h-12 w-full items-center justify-between rounded-2xl border border-[#F4D3B0] bg-[#F7F6F4] px-4 py-0 text-left text-sm font-bold text-[#2B1A10] shadow-sm transition hover:bg-white focus:border-[#F4B044] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#F4B044]/20">
                                <div class="flex min-w-0 items-center gap-3">
                                    <span
                                        class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-[#F4B044]/20 text-[#6B3E12]">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.2"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M3 4.5h18v10.5H3zM8.25 19.5h7.5M12 15v4.5" />
                                        </svg>
                                    </span>

                                    <span x-text="selectedTerminalLabel" class="truncate"
                                        :class="selectedTerminal ? 'text-[#2B1A10]' : 'text-[#6B3E12]/60'"></span>
                                </div>

                                <svg class="h-5 w-5 shrink-0 text-[#6B3E12] transition duration-200"
                                    :class="terminalOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                                    stroke-width="2.4" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <div x-show="terminalOpen" x-cloak @click.outside="terminalOpen = false"
                                x-transition:enter="transition ease-out duration-150"
                                x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-100"
                                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                x-transition:leave-end="opacity-0 scale-95 translate-y-1"
                                class="absolute left-0 top-full z-10 mt-3 max-h-72 w-full overflow-y-auto rounded-2xl border border-[#F4D3B0]/80 bg-white shadow-[0_25px_70px_-35px_rgba(31,68,76,0.55)]">
                                <div class="p-2">
                                    <button type="button" @click="selectedTerminal = ''; terminalOpen = false"
                                        class="group flex w-full items-center justify-between rounded-xl px-4 py-3 text-left text-sm font-bold transition"
                                        :class="selectedTerminal === ''
                                            ?
                                            'bg-[#F4B044] text-[#2B1A10]' :
                                            'text-[#2B1A10] hover:bg-[#F4B044]/15'">
                                        <span>Semua Terminal</span>

                                        <svg x-show="selectedTerminal === ''" x-cloak class="h-5 w-5 shrink-0"
                                            fill="none" stroke="currentColor" stroke-width="2.8" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </button>

                                    @foreach ($terminals as $terminal)
                                        <button type="button"
                                            @click="selectedTerminal = @js((string) $terminal->id); terminalOpen = false"
                                            class="group flex w-full items-center justify-between rounded-xl px-4 py-3 text-left text-sm font-bold transition"
                                            :class="selectedTerminal === @js((string) $terminal->id) ?
                                                'bg-[#F4B044] text-[#2B1A10]' :
                                                'text-[#2B1A10] hover:bg-[#F4B044]/15'">
                                            <div class="min-w-0">
                                                <p class="truncate">{{ $terminal->kode_terminal }}</p>
                                                <p class="truncate text-xs font-semibold opacity-75">
                                                    {{ $terminal->nama_terminal }}
                                                </p>
                                            </div>

                                            <svg x-show="selectedTerminal === @js((string) $terminal->id)" x-cloak
                                                class="h-5 w-5 shrink-0" fill="none" stroke="currentColor"
                                                stroke-width="2.8" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Bayar --}}
                    <div class="min-w-0">
                        <input type="hidden" name="payment_method" x-model="selectedPayment">

                        <div class="relative">
                            <button type="button" @click="paymentOpen = !paymentOpen; closeDropdowns('payment')"
                                class="flex h-12 w-full items-center justify-between rounded-2xl border border-[#F4D3B0] bg-[#F7F6F4] px-4 py-0 text-left text-sm font-bold text-[#2B1A10] shadow-sm transition hover:bg-white focus:border-[#F4B044] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#F4B044]/20">
                                <div class="flex min-w-0 items-center gap-3">
                                    <span
                                        class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-[#F4B044]/20 text-[#6B3E12]">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.2"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M2.25 8.25h19.5m-18 3.75h16.5m-15 3.75h6.75M4.5 6h15A2.25 2.25 0 0121.75 8.25v7.5A2.25 2.25 0 0119.5 18h-15a2.25 2.25 0 01-2.25-2.25v-7.5A2.25 2.25 0 014.5 6z" />
                                        </svg>
                                    </span>

                                    <span x-text="selectedPaymentLabel" class="truncate"
                                        :class="selectedPayment ? 'text-[#2B1A10]' : 'text-[#6B3E12]/60'"></span>
                                </div>

                                <svg class="h-5 w-5 shrink-0 text-[#6B3E12] transition duration-200"
                                    :class="paymentOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                                    stroke-width="2.4" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <div x-show="paymentOpen" x-cloak @click.outside="paymentOpen = false"
                                x-transition:enter="transition ease-out duration-150"
                                x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-100"
                                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                x-transition:leave-end="opacity-0 scale-95 translate-y-1"
                                class="absolute left-0 top-full z-10 mt-3 max-h-72 w-full overflow-y-auto rounded-2xl border border-[#F4D3B0]/80 bg-white shadow-[0_25px_70px_-35px_rgba(31,68,76,0.55)]">
                                <div class="p-2">
                                    <button type="button" @click="selectedPayment = ''; paymentOpen = false"
                                        class="group flex w-full items-center justify-between rounded-xl px-4 py-3 text-left text-sm font-bold transition"
                                        :class="selectedPayment === ''
                                            ?
                                            'bg-[#F4B044] text-[#2B1A10]' :
                                            'text-[#2B1A10] hover:bg-[#F4B044]/15'">
                                        <span>Semua Bayar</span>

                                        <svg x-show="selectedPayment === ''" x-cloak class="h-5 w-5 shrink-0"
                                            fill="none" stroke="currentColor" stroke-width="2.8" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </button>

                                    @foreach ($paymentMethods as $value => $label)
                                        <button type="button"
                                            @click="selectedPayment = @js((string) $value); paymentOpen = false"
                                            class="group flex w-full items-center justify-between rounded-xl px-4 py-3 text-left text-sm font-bold transition"
                                            :class="selectedPayment === @js((string) $value) ?
                                                'bg-[#F4B044] text-[#2B1A10]' :
                                                'text-[#2B1A10] hover:bg-[#F4B044]/15'">
                                            <span>{{ $label }}</span>

                                            <svg x-show="selectedPayment === @js((string) $value)" x-cloak
                                                class="h-5 w-5 shrink-0" fill="none" stroke="currentColor"
                                                stroke-width="2.8" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Status --}}
                    <div class="min-w-0">
                        <input type="hidden" name="status" x-model="selectedStatus">

                        <div class="relative">
                            <button type="button" @click="statusOpen = !statusOpen; closeDropdowns('status')"
                                class="flex h-12 w-full items-center justify-between rounded-2xl border border-[#F4D3B0] bg-[#F7F6F4] px-4 py-0 text-left text-sm font-bold text-[#2B1A10] shadow-sm transition hover:bg-white focus:border-[#F4B044] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#F4B044]/20">
                                <div class="flex min-w-0 items-center gap-3">
                                    <span
                                        class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-[#F4B044]/20 text-[#6B3E12]">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.2"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </span>

                                    <span x-text="selectedStatusLabel" class="truncate"
                                        :class="selectedStatus ? 'text-[#2B1A10]' : 'text-[#6B3E12]/60'"></span>
                                </div>

                                <svg class="h-5 w-5 shrink-0 text-[#6B3E12] transition duration-200"
                                    :class="statusOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                                    stroke-width="2.4" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <div x-show="statusOpen" x-cloak @click.outside="statusOpen = false"
                                x-transition:enter="transition ease-out duration-150"
                                x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-100"
                                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                x-transition:leave-end="opacity-0 scale-95 translate-y-1"
                                class="absolute left-0 top-full z-10 mt-3 max-h-72 w-full overflow-y-auto rounded-2xl border border-[#F4D3B0]/80 bg-white shadow-[0_25px_70px_-35px_rgba(31,68,76,0.55)]">
                                <div class="p-2">
                                    <button type="button" @click="selectedStatus = ''; statusOpen = false"
                                        class="group flex w-full items-center justify-between rounded-xl px-4 py-3 text-left text-sm font-bold transition"
                                        :class="selectedStatus === ''
                                            ?
                                            'bg-[#F4B044] text-[#2B1A10]' :
                                            'text-[#2B1A10] hover:bg-[#F4B044]/15'">
                                        <span>Semua Status</span>

                                        <svg x-show="selectedStatus === ''" x-cloak class="h-5 w-5 shrink-0"
                                            fill="none" stroke="currentColor" stroke-width="2.8" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </button>

                                    @foreach ($statuses as $value => $label)
                                        <button type="button"
                                            @click="selectedStatus = @js((string) $value); statusOpen = false"
                                            class="group flex w-full items-center justify-between rounded-xl px-4 py-3 text-left text-sm font-bold transition"
                                            :class="selectedStatus === @js((string) $value) ?
                                                'bg-[#F4B044] text-[#2B1A10]' :
                                                'text-[#2B1A10] hover:bg-[#F4B044]/15'">
                                            <span>{{ $label }}</span>

                                            <svg x-show="selectedStatus === @js((string) $value)" x-cloak
                                                class="h-5 w-5 shrink-0" fill="none" stroke="currentColor"
                                                stroke-width="2.8" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        {{-- Table --}}
        <div
            class="overflow-hidden rounded-3xl border border-[#F4D3B0]/70 bg-white shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
            <div class="border-b border-[#F4D3B0]/70 bg-[#F7F6F4] px-5 py-4">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-xs font-black uppercase tracking-[0.22em] text-[#6B3E12]">
                            Daftar Transaksi
                        </p>

                        <h2 class="mt-1 text-lg font-black text-[#2B1A10]">
                            Seluruh Riwayat Transaksi
                        </h2>
                    </div>

                    <span
                        class="inline-flex w-fit rounded-2xl bg-[#F4B044]/20 px-4 py-2 text-sm font-black text-[#6B3E12]">
                        {{ $sales->total() }} data
                    </span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-[#F4D3B0]/60">
                    <thead class="bg-white">
                        <tr>
                            <th
                                class="whitespace-nowrap px-5 py-4 text-left text-xs font-black uppercase tracking-[0.18em] text-[#6B3E12]">
                                Transaksi
                            </th>
                            <th
                                class="whitespace-nowrap px-5 py-4 text-left text-xs font-black uppercase tracking-[0.18em] text-[#6B3E12]">
                                Kasir
                            </th>
                            <th
                                class="whitespace-nowrap px-5 py-4 text-left text-xs font-black uppercase tracking-[0.18em] text-[#6B3E12]">
                                Terminal
                            </th>
                            <th
                                class="whitespace-nowrap px-5 py-4 text-left text-xs font-black uppercase tracking-[0.18em] text-[#6B3E12]">
                                Bayar
                            </th>
                            <th
                                class="whitespace-nowrap px-5 py-4 text-right text-xs font-black uppercase tracking-[0.18em] text-[#6B3E12]">
                                Total
                            </th>
                            <th
                                class="whitespace-nowrap px-5 py-4 text-center text-xs font-black uppercase tracking-[0.18em] text-[#6B3E12]">
                                Status
                            </th>
                            <th
                                class="whitespace-nowrap px-5 py-4 text-right text-xs font-black uppercase tracking-[0.18em] text-[#6B3E12]">
                                Aksi
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-[#F4D3B0]/60 bg-white">
                        @forelse ($sales as $sale)
                            <tr class="transition hover:bg-[#F7F6F4]">
                                <td class="whitespace-nowrap px-5 py-4">
                                    <p class="text-sm font-black text-[#2B1A10]">
                                        {{ $sale->kode_transaksi }}
                                    </p>

                                    <p class="mt-1 text-xs font-semibold text-[#6B3E12]">
                                        {{ $sale->created_at?->format('d M Y, H:i') }}
                                    </p>
                                </td>

                                <td class="whitespace-nowrap px-5 py-4">
                                    <p class="text-sm font-black text-[#2B1A10]">
                                        {{ $sale->cashier?->name ?? '-' }}
                                    </p>
                                </td>

                                <td class="whitespace-nowrap px-5 py-4">
                                    <p class="text-sm font-black text-[#2B1A10]">
                                        {{ $sale->terminal?->kode_terminal ?? '-' }}
                                    </p>

                                    <p class="mt-1 text-xs font-semibold text-[#6B3E12]">
                                        {{ $sale->terminal?->nama_terminal ?? '-' }}
                                    </p>
                                </td>

                                <td class="whitespace-nowrap px-5 py-4">
                                    <span
                                        class="inline-flex rounded-full bg-[#1F444C]/10 px-3 py-1 text-xs font-black uppercase text-[#1F444C]">
                                        {{ $sale->payment_method }}
                                    </span>
                                </td>

                                <td class="whitespace-nowrap px-5 py-4 text-right text-sm font-black text-[#2B1A10]">
                                    Rp {{ number_format((float) $sale->grand_total, 0, ',', '.') }}
                                </td>

                                <td class="whitespace-nowrap px-5 py-4 text-center">
                                    @if ($sale->status === 'selesai')
                                        <span
                                            class="inline-flex rounded-full bg-[#1F444C]/10 px-3 py-1 text-xs font-black text-[#1F444C]">
                                            Selesai
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex rounded-full bg-[#A92A35]/10 px-3 py-1 text-xs font-black text-[#A92A35]">
                                            Dibatalkan
                                        </span>
                                    @endif
                                </td>

                                <td class="whitespace-nowrap px-5 py-4 text-right">
                                    <a href="{{ route('admin.transactions.show', $sale) }}"
                                        class="inline-flex h-9 items-center justify-center rounded-xl bg-[#F4B044] px-3 text-xs font-black text-[#2B1A10] shadow-sm shadow-[#F4B044]/20 transition hover:-translate-y-0.5 hover:shadow-md focus:outline-none focus:ring-4 focus:ring-[#F4B044]/25">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-12 text-center">
                                    <h3 class="text-base font-black text-[#2B1A10]">
                                        Belum Ada Transaksi
                                    </h3>

                                    <p class="mt-2 text-sm font-semibold text-[#6B3E12]">
                                        Transaksi dari kasir akan muncul di sini.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($sales->hasPages())
                <div class="border-t border-[#F4D3B0]/70 px-5 py-4">
                    {{ $sales->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

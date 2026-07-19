@extends('layouts.master')

@section('page_title', 'Riwayat Transaksi Saya')

@section('content')
    <div class="space-y-6">

        {{-- Summary --}}
        <div class="grid gap-4 md:grid-cols-2 min-[1280px]:grid-cols-4">
            <div class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-5 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                <p class="text-xs font-black uppercase tracking-[0.2em] text-[#6B3E12]">
                    Total Transaksi
                </p>

                <p class="mt-3 text-2xl font-black text-[#2B1A10]">
                    {{ number_format($summary['total_transactions'], 0, ',', '.') }}
                </p>
            </div>

            <div class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-5 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                <p class="text-xs font-black uppercase tracking-[0.2em] text-[#6B3E12]">
                    Transaksi Selesai
                </p>

                <p class="mt-3 text-2xl font-black text-[#1F444C]">
                    {{ number_format($summary['completed_transactions'], 0, ',', '.') }}
                </p>
            </div>

            <div class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-5 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                <p class="text-xs font-black uppercase tracking-[0.2em] text-[#6B3E12]">
                    Tunai
                </p>

                <p class="mt-3 text-xl font-black text-[#1F444C]">
                    Rp {{ number_format($summary['total_cash'], 0, ',', '.') }}
                </p>
            </div>

            <div class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-5 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                <p class="text-xs font-black uppercase tracking-[0.2em] text-[#6B3E12]">
                    Non Tunai
                </p>

                <p class="mt-3 text-xl font-black text-[#1F444C]">
                    Rp {{ number_format($summary['total_non_cash'], 0, ',', '.') }}
                </p>
            </div>
        </div>

        {{-- Filter --}}
        <div class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-5 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]"
            x-data="{
                paymentOpen: false,
                statusOpen: false,
            
                selectedPayment: @js((string) request('payment_method', '')),
                selectedStatus: @js((string) request('status', '')),
            
                paymentMethods: @js($paymentMethods),
                statuses: @js($statuses),
            
                get selectedPaymentLabel() {
                    return this.selectedPayment ? this.paymentMethods[this.selectedPayment] : 'Semua Metode';
                },
            
                get selectedStatusLabel() {
                    return this.selectedStatus ? this.statuses[this.selectedStatus] : 'Semua Status';
                }
            }">
            <form method="GET" action="{{ route('cashier.transactions.index') }}"
                class="grid gap-3
           min-[835px]:grid-cols-[minmax(0,1fr)_160px_160px_auto]
           min-[835px]:items-center
           min-[1024px]:grid-cols-[minmax(0,1fr)_190px_190px_auto]
           min-[1280px]:grid-cols-[minmax(0,1fr)_230px_230px_auto]">

                {{-- Search --}}
                <div class="relative min-w-0">
                    <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-[#6B3E12]/60">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21 21l-4.35-4.35m1.35-5.65a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>

                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari kode transaksi..."
                        class="block h-12 w-full rounded-2xl border border-[#F4D3B0] bg-[#F7F6F4] py-0 pl-12 pr-4 text-sm font-medium text-[#2B1A10] shadow-sm transition placeholder:text-[#6B3E12]/45 focus:border-[#F4B044] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#F4B044]/20">
                </div>

                {{-- Custom Payment Dropdown --}}
                <div class="min-w-0">
                    <input type="hidden" name="payment_method" x-model="selectedPayment">

                    <div class="relative">
                        <button type="button" @click="paymentOpen = !paymentOpen; statusOpen = false"
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
                            class="absolute z-40 mt-3 max-h-72 w-full overflow-y-auto rounded-2xl border border-[#F4D3B0]/80 bg-white shadow-[0_25px_70px_-35px_rgba(31,68,76,0.55)]">

                            <div class="p-2">
                                <button type="button" @click="selectedPayment = ''; paymentOpen = false"
                                    class="group flex w-full items-center justify-between rounded-xl px-4 py-3 text-left text-sm font-bold transition"
                                    :class="selectedPayment === ''
                                        ?
                                        'bg-[#F4B044] text-[#2B1A10]' :
                                        'text-[#2B1A10] hover:bg-[#F4B044]/15'">
                                    <span>Semua Metode</span>

                                    <svg x-show="selectedPayment === ''" x-cloak class="h-5 w-5" fill="none"
                                        stroke="currentColor" stroke-width="2.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                </button>

                                @foreach ($paymentMethods as $value => $label)
                                    <button type="button"
                                        @click="selectedPayment = @js($value); paymentOpen = false"
                                        class="group flex w-full items-center justify-between rounded-xl px-4 py-3 text-left text-sm font-bold transition"
                                        :class="selectedPayment === @js($value) ?
                                            'bg-[#F4B044] text-[#2B1A10]' :
                                            'text-[#2B1A10] hover:bg-[#F4B044]/15'">
                                        <span>{{ $label }}</span>

                                        <svg x-show="selectedPayment === @js($value)" x-cloak
                                            class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.8"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Custom Status Dropdown --}}
                <div class="min-w-0">
                    <input type="hidden" name="status" x-model="selectedStatus">

                    <div class="relative">
                        <button type="button" @click="statusOpen = !statusOpen; paymentOpen = false"
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
                            class="absolute z-40 mt-3 max-h-72 w-full overflow-y-auto rounded-2xl border border-[#F4D3B0]/80 bg-white shadow-[0_25px_70px_-35px_rgba(31,68,76,0.55)]">

                            <div class="p-2">
                                <button type="button" @click="selectedStatus = ''; statusOpen = false"
                                    class="group flex w-full items-center justify-between rounded-xl px-4 py-3 text-left text-sm font-bold transition"
                                    :class="selectedStatus === ''
                                        ?
                                        'bg-[#F4B044] text-[#2B1A10]' :
                                        'text-[#2B1A10] hover:bg-[#F4B044]/15'">
                                    <span>Semua Status</span>

                                    <svg x-show="selectedStatus === ''" x-cloak class="h-5 w-5" fill="none"
                                        stroke="currentColor" stroke-width="2.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                </button>

                                @foreach ($statuses as $value => $label)
                                    <button type="button"
                                        @click="selectedStatus = @js($value); statusOpen = false"
                                        class="group flex w-full items-center justify-between rounded-xl px-4 py-3 text-left text-sm font-bold transition"
                                        :class="selectedStatus === @js($value) ?
                                            'bg-[#F4B044] text-[#2B1A10]' :
                                            'text-[#2B1A10] hover:bg-[#F4B044]/15'">
                                        <span>{{ $label }}</span>

                                        <svg x-show="selectedStatus === @js($value)" x-cloak
                                            class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.8"
                                            viewBox="0 0 24 24">
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

                    <a href="{{ route('cashier.transactions.index') }}"
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
            <div class="border-b border-[#F4D3B0]/70 bg-[#F7F6F4] px-5 py-4">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-xs font-black uppercase tracking-[0.22em] text-[#6B3E12]">
                            Daftar Transaksi
                        </p>

                        <h2 class="mt-1 text-lg font-black text-[#2B1A10]">
                            Riwayat Saya
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
                                        {{ $sale->terminal?->kode_terminal }}
                                    </p>

                                    <p class="mt-1 text-xs font-semibold text-[#6B3E12]">
                                        {{ $sale->terminal?->nama_terminal }}
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
                                    <a href="{{ route('cashier.transactions.show', $sale) }}"
                                        class="inline-flex h-9 items-center justify-center rounded-xl border border-[#F4B044] bg-[#F4B044] px-3 text-xs font-black text-[#2B1A10] shadow-md shadow-[#F4B044]/25 transition duration-150 hover:-translate-y-0.5 hover:bg-[#f7bd5f] hover:shadow-lg active:translate-y-0 active:scale-95 active:bg-[#d99a32]">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-12 text-center">
                                    <h3 class="text-base font-black text-[#2B1A10]">
                                        Belum Ada Transaksi
                                    </h3>

                                    <p class="mt-2 text-sm font-semibold text-[#6B3E12]">
                                        Transaksi yang kamu buat di POS akan muncul di sini.
                                    </p>

                                    <a href="{{ route('cashier.pos.index') }}"
                                        class="mt-5 inline-flex h-12 items-center justify-center rounded-2xl bg-[#1F444C] px-5 text-sm font-black text-white shadow-lg shadow-[#1F444C]/10 transition hover:-translate-y-0.5 hover:shadow-xl">
                                        Buka POS
                                    </a>
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

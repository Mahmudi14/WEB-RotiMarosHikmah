@extends('layouts.master')

@section('page_title', 'Dashboard Admin')

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
                            Admin / Dashboard
                        </p>

                        <h1 class="mt-2 text-2xl font-black tracking-tight">
                            Dashboard Admin
                        </h1>

                        <p class="mt-2 max-w-2xl text-sm font-medium leading-relaxed text-[#F7F6F4]/80">
                            Pantau penjualan, shift aktif, transaksi terbaru, produk terlaris, dan antrean cetak hari ini.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Summary Hari Ini --}}
        <div class="grid gap-4 md:grid-cols-2 min-[1280px]:grid-cols-4">
            <div class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-5 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                <p class="text-xs font-black uppercase tracking-[0.2em] text-[#6B3E12]">
                    Penjualan Hari Ini
                </p>

                <p class="mt-3 text-xl font-black text-[#1F444C]">
                    Rp {{ number_format($todaySummary['gross_sales'], 0, ',', '.') }}
                </p>

                <p class="mt-1 text-xs font-semibold text-[#6B3E12]">
                    {{ number_format($todaySummary['transactions'], 0, ',', '.') }} transaksi selesai
                </p>
            </div>

            <div class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-5 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                <p class="text-xs font-black uppercase tracking-[0.2em] text-[#6B3E12]">
                    Tunai
                </p>

                <p class="mt-3 text-xl font-black text-[#1F444C]">
                    Rp {{ number_format($todaySummary['cash_sales'], 0, ',', '.') }}
                </p>

                <p class="mt-1 text-xs font-semibold text-[#6B3E12]">
                    Pembayaran masuk laci kasir
                </p>
            </div>

            <div class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-5 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                <p class="text-xs font-black uppercase tracking-[0.2em] text-[#6B3E12]">
                    Non Tunai
                </p>

                <p class="mt-3 text-xl font-black text-[#1F444C]">
                    Rp {{ number_format($todaySummary['non_cash_sales'], 0, ',', '.') }}
                </p>

                <p class="mt-1 text-xs font-semibold text-[#6B3E12]">
                    QRIS dan transfer
                </p>
            </div>

            <div class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-5 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                <p class="text-xs font-black uppercase tracking-[0.2em] text-[#6B3E12]">
                    Pendapatan Bersih
                </p>

                <p class="mt-3 text-xl font-black text-[#1F444C]">
                    Rp {{ number_format($todaySummary['net_income'], 0, ',', '.') }}
                </p>

                <p class="mt-1 text-xs font-semibold text-[#6B3E12]">
                    Penjualan - pengeluaran
                </p>
            </div>
        </div>

        {{-- Secondary Summary --}}
        <div class="grid gap-4 md:grid-cols-2 min-[1280px]:grid-cols-4">
            <div class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-5 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                <p class="text-xs font-black uppercase tracking-[0.2em] text-[#6B3E12]">
                    Pengeluaran Hari Ini
                </p>

                <p class="mt-3 text-xl font-black text-[#A92A35]">
                    Rp {{ number_format($todaySummary['expenses'], 0, ',', '.') }}
                </p>
            </div>

            <div class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-5 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                <p class="text-xs font-black uppercase tracking-[0.2em] text-[#6B3E12]">
                    Total Diskon
                </p>

                <p class="mt-3 text-xl font-black text-[#A92A35]">
                    Rp {{ number_format($todaySummary['discounts'], 0, ',', '.') }}
                </p>
            </div>

            <div class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-5 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                <p class="text-xs font-black uppercase tracking-[0.2em] text-[#6B3E12]">
                    Total Pajak
                </p>

                <p class="mt-3 text-xl font-black text-[#1F444C]">
                    Rp {{ number_format($todaySummary['taxes'], 0, ',', '.') }}
                </p>
            </div>

            <div class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-5 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                <p class="text-xs font-black uppercase tracking-[0.2em] text-[#6B3E12]">
                    Shift Aktif
                </p>

                <p class="mt-3 text-xl font-black text-[#1F444C]">
                    {{ number_format($shiftSummary['active_shifts'], 0, ',', '.') }}
                </p>

                <p class="mt-1 text-xs font-semibold text-[#6B3E12]">
                    {{ number_format($shiftSummary['active_cashiers'], 0, ',', '.') }} kasir sedang aktif
                </p>
            </div>
        </div>

        {{-- Main Content --}}
        <div class="grid gap-6 min-[1024px]:grid-cols-[minmax(0,1fr)_360px] min-[1280px]:grid-cols-[minmax(0,1fr)_400px]">
            {{-- Recent Transactions --}}
            <div
                class="overflow-hidden rounded-3xl border border-[#F4D3B0]/70 bg-white shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                <div class="border-b border-[#F4D3B0]/70 bg-[#F7F6F4] px-5 py-4">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="text-xs font-black uppercase tracking-[0.22em] text-[#6B3E12]">
                                Transaksi
                            </p>

                            <h2 class="mt-1 text-lg font-black text-[#2B1A10]">
                                Transaksi Terbaru
                            </h2>
                        </div>

                        <a href="{{ route('admin.transactions.index') }}"
                            class="inline-flex w-fit rounded-2xl bg-[#F4B044]/20 px-4 py-2 text-sm font-black text-[#6B3E12] transition hover:bg-[#F4B044] hover:text-[#2B1A10]">
                            Lihat Semua
                        </a>
                    </div>
                </div>

                <div class="divide-y divide-[#F4D3B0]/60">
                    @forelse ($recentSales as $sale)
                        <div class="flex flex-col gap-3 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-black text-[#2B1A10]">
                                    {{ $sale->kode_transaksi }}
                                </p>

                                <p class="mt-1 text-xs font-semibold text-[#6B3E12]">
                                    {{ $sale->created_at?->format('d M Y, H:i') }} •
                                    {{ $sale->cashier?->name ?? '-' }} •
                                    {{ strtoupper($sale->payment_method) }}
                                </p>
                            </div>

                            <div class="flex shrink-0 items-center gap-3">
                                <div class="text-right">
                                    <p class="whitespace-nowrap text-sm font-black text-[#1F444C]">
                                        Rp {{ number_format((float) $sale->grand_total, 0, ',', '.') }}
                                    </p>

                                    @if ($sale->status === 'selesai')
                                        <p class="mt-1 text-xs font-black text-[#1F444C]">
                                            Selesai
                                        </p>
                                    @else
                                        <p class="mt-1 text-xs font-black text-[#A92A35]">
                                            Dibatalkan
                                        </p>
                                    @endif
                                </div>

                                <a href="{{ route('admin.transactions.show', $sale) }}"
                                    class="inline-flex h-9 items-center justify-center rounded-xl bg-[#F4B044]/20 px-3 text-xs font-black text-[#6B3E12] transition hover:bg-[#F4B044] hover:text-[#2B1A10]">
                                    Detail
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="px-5 py-12 text-center">
                            <h3 class="text-base font-black text-[#2B1A10]">
                                Belum Ada Transaksi
                            </h3>

                            <p class="mt-2 text-sm font-semibold text-[#6B3E12]">
                                Transaksi dari kasir akan muncul di sini.
                            </p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Side Content --}}
            <div class="space-y-6">
                {{-- Active Shifts --}}
                <div
                    class="overflow-hidden rounded-3xl border border-[#F4D3B0]/70 bg-white shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                    <div class="border-b border-[#F4D3B0]/70 bg-[#F7F6F4] px-5 py-4">
                        <p class="text-xs font-black uppercase tracking-[0.22em] text-[#6B3E12]">
                            Shift
                        </p>

                        <h2 class="mt-1 text-lg font-black text-[#2B1A10]">
                            Shift Aktif
                        </h2>
                    </div>

                    <div class="divide-y divide-[#F4D3B0]/60">
                        @forelse ($activeShifts as $shift)
                            <div class="px-5 py-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-black text-[#2B1A10]">
                                            {{ $shift->cashier?->name ?? '-' }}
                                        </p>

                                        <p class="mt-1 truncate text-xs font-semibold text-[#6B3E12]">
                                            {{ $shift->terminal?->nama_terminal ?? '-' }}
                                        </p>
                                    </div>

                                    <span
                                        class="shrink-0 rounded-full bg-[#1F444C]/10 px-3 py-1 text-xs font-black text-[#1F444C]">
                                        Aktif
                                    </span>
                                </div>

                                <p class="mt-3 text-xs font-semibold text-[#6B3E12]">
                                    Dibuka {{ $shift->opened_at?->format('d M Y, H:i') }}
                                </p>
                            </div>
                        @empty
                            <div class="px-5 py-10 text-center">
                                <p class="text-sm font-semibold text-[#6B3E12]">
                                    Belum ada shift aktif.
                                </p>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Print Status --}}
                <div
                    class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-5 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                    <p class="text-xs font-black uppercase tracking-[0.22em] text-[#6B3E12]">
                        Printer
                    </p>

                    <h2 class="mt-1 text-lg font-black text-[#2B1A10]">
                        Antrean Cetak Hari Ini
                    </h2>

                    <div class="mt-4 grid grid-cols-2 gap-3">
                        <div class="rounded-2xl bg-[#F7F6F4] p-4">
                            <p class="text-xs font-black uppercase tracking-[0.16em] text-[#6B3E12]">
                                Pending
                            </p>

                            <p class="mt-2 text-xl font-black text-[#F4B044]">
                                {{ number_format($printJobSummary['pending'], 0, ',', '.') }}
                            </p>
                        </div>

                        <div class="rounded-2xl bg-[#F7F6F4] p-4">
                            <p class="text-xs font-black uppercase tracking-[0.16em] text-[#6B3E12]">
                                Printing
                            </p>

                            <p class="mt-2 text-xl font-black text-[#1F444C]">
                                {{ number_format($printJobSummary['printing'], 0, ',', '.') }}
                            </p>
                        </div>

                        <div class="rounded-2xl bg-[#F7F6F4] p-4">
                            <p class="text-xs font-black uppercase tracking-[0.16em] text-[#6B3E12]">
                                Berhasil
                            </p>

                            <p class="mt-2 text-xl font-black text-[#1F444C]">
                                {{ number_format($printJobSummary['printed'], 0, ',', '.') }}
                            </p>
                        </div>

                        <div class="rounded-2xl bg-[#A92A35]/10 p-4">
                            <p class="text-xs font-black uppercase tracking-[0.16em] text-[#A92A35]">
                                Gagal
                            </p>

                            <p class="mt-2 text-xl font-black text-[#A92A35]">
                                {{ number_format($printJobSummary['failed'], 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Top Products --}}
        <div
            class="overflow-hidden rounded-3xl border border-[#F4D3B0]/70 bg-white shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
            <div class="border-b border-[#F4D3B0]/70 bg-[#F7F6F4] px-5 py-4">
                <p class="text-xs font-black uppercase tracking-[0.22em] text-[#6B3E12]">
                    Produk
                </p>

                <h2 class="mt-1 text-lg font-black text-[#2B1A10]">
                    Produk Terlaris Hari Ini
                </h2>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-[#F4D3B0]/60">
                    <thead class="bg-white">
                        <tr>
                            <th
                                class="whitespace-nowrap px-5 py-4 text-left text-xs font-black uppercase tracking-[0.18em] text-[#6B3E12]">
                                Produk
                            </th>
                            <th
                                class="whitespace-nowrap px-5 py-4 text-left text-xs font-black uppercase tracking-[0.18em] text-[#6B3E12]">
                                Kategori
                            </th>
                            <th
                                class="whitespace-nowrap px-5 py-4 text-right text-xs font-black uppercase tracking-[0.18em] text-[#6B3E12]">
                                Terjual
                            </th>
                            <th
                                class="whitespace-nowrap px-5 py-4 text-right text-xs font-black uppercase tracking-[0.18em] text-[#6B3E12]">
                                Subtotal
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-[#F4D3B0]/60 bg-white">
                        @forelse ($topProducts as $product)
                            <tr class="transition hover:bg-[#F7F6F4]">
                                <td class="whitespace-nowrap px-5 py-4 text-sm font-black text-[#2B1A10]">
                                    {{ $product->nama_produk }}
                                </td>

                                <td class="whitespace-nowrap px-5 py-4 text-sm font-semibold text-[#6B3E12]">
                                    {{ $product->nama_kategori ?: '-' }}
                                </td>

                                <td class="whitespace-nowrap px-5 py-4 text-right text-sm font-black text-[#1F444C]">
                                    {{ number_format((int) $product->total_qty, 0, ',', '.') }}x
                                </td>

                                <td class="whitespace-nowrap px-5 py-4 text-right text-sm font-black text-[#2B1A10]">
                                    Rp {{ number_format((float) $product->total_subtotal, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-5 py-10 text-center">
                                    <p class="text-sm font-semibold text-[#6B3E12]">
                                        Belum ada produk terjual hari ini.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

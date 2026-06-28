@extends('layouts.master')

@section('page_title', 'Dashboard Kasir')

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
                            Kasir / Dashboard
                        </p>

                        <h1 class="mt-2 text-2xl font-black tracking-tight">
                            Dashboard Kasir
                        </h1>

                        <p class="mt-2 max-w-2xl text-sm font-medium leading-relaxed text-[#F7F6F4]/80">
                            Pantau shift, transaksi, pengeluaran, dan status cetak struk kamu hari ini.
                        </p>
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row min-[1024px]:shrink-0">
                        @if ($activeShift)
                            <a href="{{ route('cashier.pos.index') }}"
                                class="inline-flex h-12 items-center justify-center rounded-2xl bg-[#F4B044] px-5 text-sm font-black text-[#2B1A10] shadow-lg shadow-[#F4B044]/20 transition hover:-translate-y-0.5 hover:shadow-xl">
                                Buka POS
                            </a>

                            <a href="{{ route('cashier.shifts.close-form', $activeShift) }}"
                                class="inline-flex h-12 items-center justify-center rounded-2xl border border-white/15 bg-white/10 px-5 text-sm font-black text-white transition hover:bg-white/15">
                                Tutup Shift
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Shift Warning --}}
        @unless ($activeShift)
            <div
                class="rounded-3xl border border-[#F4B044]/50 bg-[#F4B044]/15 p-5 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm font-black text-[#2B1A10]">
                            Shift belum dibuka
                        </p>

                        <p class="mt-1 text-sm font-semibold text-[#6B3E12]">
                            Buka shift terlebih dahulu agar bisa mulai mencatat transaksi dan pengeluaran.
                        </p>
                    </div>

                    <a href="{{ route('cashier.shifts.create') }}"
                        class="inline-flex h-12 items-center justify-center rounded-2xl bg-[#1F444C] px-5 text-sm font-black text-white shadow-lg shadow-[#1F444C]/10 transition hover:-translate-y-0.5 hover:shadow-xl">
                        Buka Shift Sekarang
                    </a>
                </div>
            </div>
        @endunless

        {{-- Shift Info --}}
        <div class="grid gap-4 md:grid-cols-2 min-[1280px]:grid-cols-4">
            <div class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-5 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                <p class="text-xs font-black uppercase tracking-[0.2em] text-[#6B3E12]">
                    Status Shift
                </p>

                @if ($activeShift)
                    <div class="mt-3">
                        <span class="inline-flex rounded-full bg-[#1F444C]/10 px-3 py-1 text-xs font-black text-[#1F444C]">
                            Aktif
                        </span>

                        <p class="mt-3 text-sm font-black text-[#2B1A10]">
                            {{ $activeShift->terminal?->nama_terminal }}
                        </p>

                        <p class="mt-1 text-xs font-semibold text-[#6B3E12]">
                            Dibuka {{ $activeShift->opened_at?->format('d M Y, H:i') }}
                        </p>
                    </div>
                @else
                    <p class="mt-3 text-sm font-black text-[#A92A35]">
                        Belum Aktif
                    </p>
                @endif
            </div>

            <div class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-5 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                <p class="text-xs font-black uppercase tracking-[0.2em] text-[#6B3E12]">
                    Penjualan Shift
                </p>

                <p class="mt-3 text-xl font-black text-[#1F444C]">
                    Rp {{ number_format($shiftSummary['total_sales'], 0, ',', '.') }}
                </p>

                <p class="mt-1 text-xs font-semibold text-[#6B3E12]">
                    {{ number_format($shiftSummary['sales_count'], 0, ',', '.') }} transaksi
                </p>
            </div>

            <div class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-5 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                <p class="text-xs font-black uppercase tracking-[0.2em] text-[#6B3E12]">
                    Pengeluaran Shift
                </p>

                <p class="mt-3 text-xl font-black text-[#A92A35]">
                    Rp {{ number_format($shiftSummary['total_expenses'], 0, ',', '.') }}
                </p>

                <p class="mt-1 text-xs font-semibold text-[#6B3E12]">
                    Dicatat pada shift aktif
                </p>
            </div>

            <div class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-5 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                <p class="text-xs font-black uppercase tracking-[0.2em] text-[#6B3E12]">
                    Kas di Sistem
                </p>

                <p class="mt-3 text-xl font-black text-[#1F444C]">
                    Rp {{ number_format($shiftSummary['cash_in_system'], 0, ',', '.') }}
                </p>

                <p class="mt-1 text-xs font-semibold text-[#6B3E12]">
                    Kas awal + tunai - pengeluaran
                </p>
            </div>
        </div>

        {{-- Payment Summary --}}
        <div class="grid gap-4 md:grid-cols-2 min-[1280px]:grid-cols-4">
            <div class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-5 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                <p class="text-xs font-black uppercase tracking-[0.2em] text-[#6B3E12]">
                    Tunai Shift
                </p>

                <p class="mt-3 text-xl font-black text-[#1F444C]">
                    Rp {{ number_format($shiftSummary['total_cash_sales'], 0, ',', '.') }}
                </p>
            </div>

            <div class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-5 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                <p class="text-xs font-black uppercase tracking-[0.2em] text-[#6B3E12]">
                    Non Tunai Shift
                </p>

                <p class="mt-3 text-xl font-black text-[#1F444C]">
                    Rp {{ number_format($shiftSummary['total_non_cash_sales'], 0, ',', '.') }}
                </p>
            </div>

            <div class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-5 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                <p class="text-xs font-black uppercase tracking-[0.2em] text-[#6B3E12]">
                    Pemasukan Bersih
                </p>

                <p class="mt-3 text-xl font-black text-[#1F444C]">
                    Rp {{ number_format($shiftSummary['net_income'], 0, ',', '.') }}
                </p>

                <p class="mt-1 text-xs font-semibold text-[#6B3E12]">
                    Tunai + non tunai - pengeluaran
                </p>
            </div>

            <div class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-5 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                <p class="text-xs font-black uppercase tracking-[0.2em] text-[#6B3E12]">
                    Total Hari Ini
                </p>

                <p class="mt-3 text-xl font-black text-[#1F444C]">
                    Rp {{ number_format($todaySummary['grand_total'], 0, ',', '.') }}
                </p>

                <p class="mt-1 text-xs font-semibold text-[#6B3E12]">
                    {{ number_format($todaySummary['transactions'], 0, ',', '.') }} transaksi selesai
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

                        <a href="{{ route('cashier.transactions.index') }}"
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
                                    {{ strtoupper($sale->payment_method) }}
                                </p>
                            </div>

                            <div class="flex shrink-0 items-center gap-3">
                                <p class="whitespace-nowrap text-sm font-black text-[#1F444C]">
                                    Rp {{ number_format((float) $sale->grand_total, 0, ',', '.') }}
                                </p>

                                <a href="{{ route('cashier.transactions.show', $sale) }}"
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
                                Transaksi terbaru akan muncul setelah kasir menyimpan transaksi dari POS.
                            </p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Side Content --}}
            <div class="space-y-6">
                {{-- Top Products --}}
                <div
                    class="overflow-hidden rounded-3xl border border-[#F4D3B0]/70 bg-white shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                    <div class="border-b border-[#F4D3B0]/70 bg-[#F7F6F4] px-5 py-4">
                        <p class="text-xs font-black uppercase tracking-[0.22em] text-[#6B3E12]">
                            Produk
                        </p>

                        <h2 class="mt-1 text-lg font-black text-[#2B1A10]">
                            Terlaris Hari Ini
                        </h2>
                    </div>

                    <div class="divide-y divide-[#F4D3B0]/60">
                        @forelse ($topProducts as $product)
                            <div class="flex items-center justify-between gap-3 px-5 py-4">
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-black text-[#2B1A10]">
                                        {{ $product->nama_produk }}
                                    </p>

                                    <p class="mt-1 text-xs font-semibold text-[#6B3E12]">
                                        {{ $product->nama_kategori ?: '-' }}
                                    </p>
                                </div>

                                <div class="shrink-0 text-right">
                                    <p class="text-sm font-black text-[#1F444C]">
                                        {{ number_format((int) $product->total_qty, 0, ',', '.') }}x
                                    </p>

                                    <p class="mt-1 text-xs font-semibold text-[#6B3E12]">
                                        Rp {{ number_format((float) $product->total_subtotal, 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                        @empty
                            <div class="px-5 py-10 text-center">
                                <p class="text-sm font-semibold text-[#6B3E12]">
                                    Belum ada produk terjual hari ini.
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
                        Status Antrean Struk
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

                    <p class="mt-4 text-xs font-semibold leading-relaxed text-[#6B3E12]">
                        Data ini mengikuti antrean struk pada shift aktif.
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection

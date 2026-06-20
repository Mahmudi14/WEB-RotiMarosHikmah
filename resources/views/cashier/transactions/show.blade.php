@extends('layouts.master')

@section('page_title', 'Detail Transaksi')

@section('content')
    @php
        $latestReceiptJob = $sale->printJobs->where('type', 'receipt')->sortByDesc('created_at')->first();

        $isPrintWaiting = $latestReceiptJob && in_array($latestReceiptJob->status, ['pending', 'printing']);

        $canReprint = $sale->status === 'selesai' && !$isPrintWaiting;

        $statusClass =
            $sale->status === 'selesai' ? 'bg-[#1F444C]/10 text-[#1F444C]' : 'bg-[#A92A35]/10 text-[#A92A35]';
    @endphp

    <div class="space-y-6" x-data="{
        reprintOpen: false
    }">
        {{-- Header --}}
        <div class="overflow-hidden rounded-3xl bg-[#1F444C] p-6 text-white shadow-lg shadow-[#1F444C]/10">
            <div class="relative">
                <div class="absolute -right-10 -top-12 h-32 w-32 rounded-full bg-[#F4B044]/20"></div>
                <div class="absolute -bottom-16 -left-12 h-36 w-36 rounded-full bg-white/10"></div>

                <div
                    class="relative flex flex-col gap-5 min-[1024px]:flex-row min-[1024px]:items-center min-[1024px]:justify-between">
                    <div class="min-w-0">
                        <p class="text-xs font-bold uppercase tracking-[0.28em] text-[#F4D3B0]">
                            Kasir / Transaksi / Detail
                        </p>

                        <h1 class="mt-2 text-2xl font-black tracking-tight">
                            Detail Transaksi
                        </h1>

                        <p class="mt-2 max-w-2xl text-sm font-medium leading-relaxed text-[#F7F6F4]/80">
                            {{ $sale->kode_transaksi }}
                        </p>
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row min-[1024px]:shrink-0">
                        @if ($canReprint)
                            <button type="button" @click="reprintOpen = true"
                                class="inline-flex h-12 items-center justify-center rounded-2xl bg-[#F4B044] px-5 text-sm font-black text-[#2B1A10] shadow-lg shadow-[#F4B044]/20 transition hover:-translate-y-0.5 hover:shadow-xl">
                                Cetak Ulang Struk
                            </button>
                        @endif

                        <a href="{{ route('cashier.transactions.index') }}"
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

                <span class="mt-3 inline-flex rounded-full px-3 py-1 text-xs font-black {{ $statusClass }}">
                    {{ ucfirst($sale->status) }}
                </span>
            </div>

            <div class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-5 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                <p class="text-xs font-black uppercase tracking-[0.2em] text-[#6B3E12]">
                    Waktu
                </p>

                <p class="mt-3 text-sm font-black text-[#2B1A10]">
                    {{ $sale->created_at?->format('d M Y, H:i') }}
                </p>
            </div>

            <div class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-5 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                <p class="text-xs font-black uppercase tracking-[0.2em] text-[#6B3E12]">
                    Terminal
                </p>

                <p class="mt-3 truncate text-sm font-black text-[#2B1A10]">
                    {{ $sale->terminal?->nama_terminal }}
                </p>

                <p class="mt-1 truncate text-xs font-bold text-[#6B3E12]">
                    {{ $sale->terminal?->kode_terminal }}
                </p>
            </div>

            <div class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-5 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                <p class="text-xs font-black uppercase tracking-[0.2em] text-[#6B3E12]">
                    Total Bayar
                </p>

                <p class="mt-3 text-xl font-black text-[#1F444C]">
                    Rp {{ number_format((float) $sale->grand_total, 0, ',', '.') }}
                </p>
            </div>
        </div>

        {{-- Content --}}
        <div class="grid gap-6 min-[1024px]:grid-cols-[minmax(0,1fr)_380px]">
            {{-- Items --}}
            <div
                class="overflow-hidden rounded-3xl border border-[#F4D3B0]/70 bg-white shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                <div class="border-b border-[#F4D3B0]/70 bg-[#F7F6F4] px-5 py-4">
                    <p class="text-xs font-black uppercase tracking-[0.22em] text-[#6B3E12]">
                        Produk
                    </p>

                    <h2 class="mt-1 text-lg font-black text-[#2B1A10]">
                        Item Transaksi
                    </h2>
                </div>

                <div class="divide-y divide-[#F4D3B0]/60">
                    @foreach ($sale->items as $item)
                        <div class="flex flex-col gap-3 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-black text-[#2B1A10]">
                                    {{ $item->nama_produk }}
                                </p>

                                <p class="mt-1 text-xs font-semibold text-[#6B3E12]">
                                    {{ $item->nama_kategori ?: '-' }} •
                                    {{ (int) $item->qty }} x Rp
                                    {{ number_format((float) $item->harga_satuan, 0, ',', '.') }}
                                </p>
                            </div>

                            <p class="shrink-0 whitespace-nowrap text-sm font-black text-[#1F444C]">
                                Rp {{ number_format((float) $item->subtotal, 0, ',', '.') }}
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Summary --}}
            <div class="space-y-6">
                <div
                    class="overflow-hidden rounded-3xl border border-[#F4D3B0]/70 bg-white shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                    <div class="border-b border-[#F4D3B0]/70 bg-[#F7F6F4] px-5 py-4">
                        <p class="text-xs font-black uppercase tracking-[0.22em] text-[#6B3E12]">
                            Ringkasan
                        </p>

                        <h2 class="mt-1 text-lg font-black text-[#2B1A10]">
                            Pembayaran
                        </h2>
                    </div>

                    <div class="space-y-3 p-5">
                        <div class="flex items-center justify-between rounded-2xl bg-[#F7F6F4] p-4">
                            <span class="text-sm font-bold text-[#6B3E12]">Subtotal</span>
                            <span class="text-sm font-black text-[#2B1A10]">
                                Rp {{ number_format((float) $sale->subtotal, 0, ',', '.') }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between rounded-2xl bg-[#F7F6F4] p-4">
                            <div>
                                <span class="text-sm font-bold text-[#6B3E12]">Diskon</span>
                                <p class="mt-1 text-xs font-semibold text-[#6B3E12]/70">
                                    {{ $sale->nama_promo ?: 'Tidak ada promo' }}
                                </p>
                            </div>

                            <span class="text-sm font-black text-[#A92A35]">
                                - Rp {{ number_format((float) $sale->total_diskon, 0, ',', '.') }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between rounded-2xl bg-[#F7F6F4] p-4">
                            <div>
                                <span class="text-sm font-bold text-[#6B3E12]">Pajak</span>
                                <p class="mt-1 text-xs font-semibold text-[#6B3E12]/70">
                                    {{ $sale->nama_pajak ? $sale->nama_pajak . ' (' . rtrim(rtrim(number_format((float) $sale->persentase_pajak, 2, ',', '.'), '0'), ',') . '%)' : 'Tidak ada pajak' }}
                                </p>
                            </div>

                            <span class="text-sm font-black text-[#2B1A10]">
                                Rp {{ number_format((float) $sale->total_pajak, 0, ',', '.') }}
                            </span>
                        </div>

                        <div class="rounded-2xl bg-[#1F444C] p-4 text-white">
                            <div class="flex items-center justify-between gap-4">
                                <span class="text-sm font-black">Grand Total</span>
                                <span class="text-xl font-black text-[#F4B044]">
                                    Rp {{ number_format((float) $sale->grand_total, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>

                        <div class="grid gap-3 sm:grid-cols-2">
                            <div class="rounded-2xl bg-[#F7F6F4] p-4">
                                <p class="text-xs font-black uppercase tracking-[0.18em] text-[#6B3E12]">
                                    Metode
                                </p>

                                <p class="mt-2 text-sm font-black uppercase text-[#2B1A10]">
                                    {{ $sale->payment_method }}
                                </p>
                            </div>

                            <div class="rounded-2xl bg-[#F7F6F4] p-4">
                                <p class="text-xs font-black uppercase tracking-[0.18em] text-[#6B3E12]">
                                    Kembalian
                                </p>

                                <p class="mt-2 text-sm font-black text-[#1F444C]">
                                    Rp {{ number_format((float) $sale->change_amount, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Print Status --}}
                <div
                    class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-6 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                    <p class="text-xs font-black uppercase tracking-[0.22em] text-[#6B3E12]">
                        Status Cetak
                    </p>

                    <h2 class="mt-1 text-lg font-black text-[#2B1A10]">
                        Struk
                    </h2>

                    @if ($latestReceiptJob)
                        <div class="mt-4 rounded-2xl bg-[#F7F6F4] p-4">
                            <p class="text-sm font-black text-[#2B1A10]">
                                {{ $latestReceiptJob->status_label }}
                            </p>

                            <p class="mt-1 text-xs font-semibold text-[#6B3E12]">
                                Dibuat {{ $latestReceiptJob->created_at?->format('d M Y, H:i') }}
                            </p>
                        </div>
                    @else
                        <p class="mt-4 text-sm font-semibold text-[#6B3E12]">
                            Belum ada antrean cetak struk.
                        </p>
                    @endif

                    @if ($canReprint)
                        <button type="button" @click="reprintOpen = true"
                            class="mt-4 inline-flex h-12 w-full items-center justify-center rounded-2xl bg-[#F4B044] px-5 text-sm font-black text-[#2B1A10] shadow-lg shadow-[#F4B044]/20 transition hover:-translate-y-0.5 hover:shadow-xl">
                            Cetak Ulang Struk
                        </button>
                    @elseif ($isPrintWaiting)
                        <div class="mt-4 rounded-2xl bg-[#F4B044]/20 p-4">
                            <p class="text-sm font-black text-[#6B3E12]">
                                Struk sedang menunggu proses cetak.
                            </p>

                            <p class="mt-1 text-xs font-semibold text-[#6B3E12]/75">
                                Tombol cetak ulang akan tersedia setelah status cetak menjadi berhasil atau gagal.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Reprint Modal --}}
        @if ($canReprint)
            <template x-teleport="body">
                <div x-show="reprintOpen" x-cloak
                    class="fixed inset-0 z-[9998] flex items-center justify-center bg-[#1F444C]/55 px-4 backdrop-blur-md"
                    x-transition.opacity>
                    <div @click.outside="reprintOpen = false" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95 translate-y-3"
                        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                        x-transition:leave-end="opacity-0 scale-95 translate-y-3"
                        class="w-full max-w-md overflow-hidden rounded-3xl bg-white shadow-2xl shadow-[#1F444C]/25">
                        <div class="bg-[#1F444C] px-6 py-5 text-white">
                            <h2 class="text-lg font-black">
                                Cetak Ulang Struk?
                            </h2>

                            <p class="mt-1 text-sm font-semibold text-white/80">
                                Struk akan dimasukkan ulang ke antrean printer terminal.
                            </p>
                        </div>

                        <div class="space-y-4 p-6">
                            <div class="rounded-2xl bg-[#F7F6F4] p-4">
                                <p class="text-xs font-black uppercase tracking-[0.18em] text-[#6B3E12]">
                                    Transaksi
                                </p>

                                <p class="mt-2 text-base font-black text-[#2B1A10]">
                                    {{ $sale->kode_transaksi }}
                                </p>

                                <p class="mt-1 text-sm font-black text-[#1F444C]">
                                    Rp {{ number_format((float) $sale->grand_total, 0, ',', '.') }}
                                </p>
                            </div>

                            <p class="text-sm font-semibold leading-relaxed text-[#6B3E12]">
                                Pastikan Flutter Printer Bridge pada terminal ini aktif agar struk langsung tercetak.
                            </p>

                            <div class="grid gap-3 pt-2 sm:grid-cols-2">
                                <button type="button" @click="reprintOpen = false"
                                    class="inline-flex h-12 items-center justify-center rounded-2xl border border-[#F4D3B0] bg-white px-5 text-sm font-black text-[#6B3E12] transition hover:bg-[#F7F6F4]">
                                    Batal
                                </button>

                                <form method="POST" action="{{ route('cashier.transactions.reprint', $sale) }}">
                                    @csrf

                                    <button type="submit"
                                        class="inline-flex h-12 w-full items-center justify-center rounded-2xl bg-[#F4B044] px-5 text-sm font-black text-[#2B1A10] shadow-lg shadow-[#F4B044]/20 transition hover:-translate-y-0.5 hover:shadow-xl">
                                        Ya, Cetak
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        @endif
    </div>
@endsection

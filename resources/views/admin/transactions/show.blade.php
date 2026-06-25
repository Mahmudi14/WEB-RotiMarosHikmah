@extends('layouts.master')

@section('page_title', 'Detail Transaksi')

@section('content')
    @php
        $latestReceiptJob = $sale->printJobs->where('type', 'receipt')->sortByDesc('created_at')->first();

        $statusClass =
            $sale->status === 'selesai' ? 'bg-[#1F444C]/10 text-[#1F444C]' : 'bg-[#A92A35]/10 text-[#A92A35]';
    @endphp

    <div class="space-y-6" x-data="{ cancelOpen: false }">
        {{-- Header --}}
        <div class="overflow-hidden rounded-3xl bg-[#1F444C] p-6 text-white shadow-lg shadow-[#1F444C]/10">
            <div class="relative">
                <div class="absolute -right-10 -top-12 h-32 w-32 rounded-full bg-[#F4B044]/20"></div>
                <div class="absolute -bottom-16 -left-12 h-36 w-36 rounded-full bg-white/10"></div>

                <div
                    class="relative flex flex-col gap-5 min-[1024px]:flex-row min-[1024px]:items-center min-[1024px]:justify-between">
                    <div class="min-w-0">
                        <p class="text-xs font-bold uppercase tracking-[0.28em] text-[#F4D3B0]">
                            Admin / Transaksi / Detail
                        </p>

                        <h1 class="mt-2 text-2xl font-black tracking-tight">
                            Detail Transaksi
                        </h1>

                        <p class="mt-2 max-w-2xl text-sm font-medium leading-relaxed text-[#F7F6F4]/80">
                            {{ $sale->kode_transaksi }}
                        </p>
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row min-[1024px]:shrink-0">
                        <div class="flex flex-col gap-3 sm:flex-row min-[1024px]:shrink-0">
                            @if ($sale->status === 'selesai')
                                <button type="button" @click="cancelOpen = true"
                                    class="inline-flex h-12 items-center justify-center rounded-2xl bg-[#A92A35] px-5 text-sm font-black text-white shadow-lg shadow-[#A92A35]/20 transition hover:-translate-y-0.5 hover:shadow-xl">
                                    Batalkan
                                </button>
                            @endif

                            <a href="{{ route('admin.transactions.index') }}"
                                class="inline-flex h-12 items-center justify-center rounded-2xl bg-[#F4B044] px-5 text-sm font-black text-[#2B1A10] shadow-lg shadow-[#F4B044]/20 transition hover:-translate-y-0.5 hover:shadow-xl">
                                Kembali
                            </a>

                        </div>
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
                    Waktu Transaksi
                </p>

                <p class="mt-3 text-sm font-black text-[#2B1A10]">
                    {{ $sale->created_at?->format('d M Y, H:i') }}
                </p>
            </div>

            <div class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-5 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                <p class="text-xs font-black uppercase tracking-[0.2em] text-[#6B3E12]">
                    Kasir
                </p>

                <p class="mt-3 truncate text-sm font-black text-[#2B1A10]">
                    {{ $sale->cashier?->name ?? '-' }}
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
        <div class="space-y-6">
            {{-- Top Detail --}}
            <div
                class="grid gap-6 min-[1024px]:grid-cols-[minmax(0,1fr)_390px] min-[1280px]:grid-cols-[minmax(0,1fr)_420px]">
                {{-- Payment Summary --}}
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
                            <div class="min-w-0">
                                <span class="text-sm font-bold text-[#6B3E12]">Diskon</span>
                                <p class="mt-1 truncate text-xs font-semibold text-[#6B3E12]/70">
                                    {{ $sale->nama_promo ?: 'Tidak ada promo' }}
                                </p>
                            </div>

                            <span class="shrink-0 whitespace-nowrap text-sm font-black text-[#A92A35]">
                                - Rp {{ number_format((float) $sale->total_diskon, 0, ',', '.') }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between rounded-2xl bg-[#F7F6F4] p-4">
                            <div class="min-w-0">
                                <span class="text-sm font-bold text-[#6B3E12]">Pajak</span>
                                <p class="mt-1 truncate text-xs font-semibold text-[#6B3E12]/70">
                                    {{ $sale->nama_pajak ? $sale->nama_pajak . ' (' . rtrim(rtrim(number_format((float) $sale->persentase_pajak, 2, ',', '.'), '0'), ',') . '%)' : 'Tidak ada pajak' }}
                                </p>
                            </div>

                            <span class="shrink-0 whitespace-nowrap text-sm font-black text-[#2B1A10]">
                                Rp {{ number_format((float) $sale->total_pajak, 0, ',', '.') }}
                            </span>
                        </div>

                        <div class="rounded-2xl bg-[#1F444C] p-4 text-white">
                            <div class="flex items-center justify-between gap-4">
                                <span class="text-sm font-black">Grand Total</span>
                                <span class="whitespace-nowrap text-xl font-black text-[#F4B044]">
                                    Rp {{ number_format((float) $sale->grand_total, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>

                        <div class="grid gap-3 sm:grid-cols-3">
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
                                    Diterima
                                </p>

                                <p class="mt-2 whitespace-nowrap text-sm font-black text-[#2B1A10]">
                                    Rp {{ number_format((float) $sale->paid_amount, 0, ',', '.') }}
                                </p>
                            </div>

                            <div class="rounded-2xl bg-[#F7F6F4] p-4">
                                <p class="text-xs font-black uppercase tracking-[0.18em] text-[#6B3E12]">
                                    Kembalian
                                </p>

                                <p class="mt-2 whitespace-nowrap text-sm font-black text-[#1F444C]">
                                    Rp {{ number_format((float) $sale->change_amount, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right Info --}}
                <div class="space-y-6">
                    {{-- Transaction Info --}}
                    <div
                        class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-6 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                        <p class="text-xs font-black uppercase tracking-[0.22em] text-[#6B3E12]">
                            Informasi
                        </p>

                        <h2 class="mt-1 text-lg font-black text-[#2B1A10]">
                            Kasir & Terminal
                        </h2>

                        <div class="mt-5 space-y-3">
                            <div class="rounded-2xl bg-[#F7F6F4] p-4">
                                <p class="text-xs font-black uppercase tracking-[0.16em] text-[#6B3E12]">
                                    Kasir
                                </p>

                                <p class="mt-2 text-sm font-black text-[#2B1A10]">
                                    {{ $sale->cashier?->name ?? '-' }}
                                </p>
                            </div>

                            <div class="rounded-2xl bg-[#F7F6F4] p-4">
                                <p class="text-xs font-black uppercase tracking-[0.16em] text-[#6B3E12]">
                                    Terminal
                                </p>

                                <p class="mt-2 text-sm font-black text-[#2B1A10]">
                                    {{ $sale->terminal?->nama_terminal ?? '-' }}
                                </p>

                                <p class="mt-1 text-xs font-semibold text-[#6B3E12]">
                                    {{ $sale->terminal?->kode_terminal ?? '-' }}
                                </p>
                            </div>

                            <div class="rounded-2xl bg-[#F7F6F4] p-4">
                                <p class="text-xs font-black uppercase tracking-[0.16em] text-[#6B3E12]">
                                    Shift
                                </p>

                                <p class="mt-2 text-sm font-black text-[#2B1A10]">
                                    {{ $sale->shift?->opened_at?->format('d M Y, H:i') ?? '-' }}
                                </p>

                                <p class="mt-1 text-xs font-semibold text-[#6B3E12]">
                                    Status: {{ ucfirst($sale->shift?->status ?? '-') }}
                                </p>
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

                                @if ($latestReceiptJob->error_message)
                                    <p
                                        class="mt-3 rounded-xl bg-[#A92A35]/10 p-3 text-xs font-semibold leading-relaxed text-[#A92A35]">
                                        {{ $latestReceiptJob->error_message }}
                                    </p>
                                @endif
                            </div>
                        @else
                            <p class="mt-4 text-sm font-semibold text-[#6B3E12]">
                                Belum ada antrean cetak struk.
                            </p>
                        @endif
                    </div>
                </div>
            </div>

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
                                    class="whitespace-nowrap px-5 py-4 text-center text-xs font-black uppercase tracking-[0.18em] text-[#6B3E12]">
                                    Qty
                                </th>
                                <th
                                    class="whitespace-nowrap px-5 py-4 text-right text-xs font-black uppercase tracking-[0.18em] text-[#6B3E12]">
                                    Harga
                                </th>
                                <th
                                    class="whitespace-nowrap px-5 py-4 text-right text-xs font-black uppercase tracking-[0.18em] text-[#6B3E12]">
                                    Subtotal
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-[#F4D3B0]/60 bg-white">
                            @foreach ($sale->items as $item)
                                <tr class="transition hover:bg-[#F7F6F4]">
                                    <td class="whitespace-nowrap px-5 py-4 text-sm font-black text-[#2B1A10]">
                                        {{ $item->nama_produk }}
                                    </td>

                                    <td class="whitespace-nowrap px-5 py-4 text-sm font-semibold text-[#6B3E12]">
                                        {{ $item->nama_kategori ?: '-' }}
                                    </td>

                                    <td class="whitespace-nowrap px-5 py-4 text-center text-sm font-black text-[#1F444C]">
                                        {{ number_format((int) $item->qty, 0, ',', '.') }}x
                                    </td>

                                    <td
                                        class="whitespace-nowrap px-5 py-4 text-right text-sm font-semibold text-[#6B3E12]">
                                        Rp {{ number_format((float) $item->harga_satuan, 0, ',', '.') }}
                                    </td>

                                    <td class="whitespace-nowrap px-5 py-4 text-right text-sm font-black text-[#2B1A10]">
                                        Rp {{ number_format((float) $item->subtotal, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Cancel Info --}}
        @if ($sale->status === 'dibatalkan')
            <div
                class="rounded-3xl border border-[#A92A35]/20 bg-[#A92A35]/10 p-6 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                <p class="text-xs font-black uppercase tracking-[0.22em] text-[#A92A35]">
                    Pembatalan
                </p>

                <h2 class="mt-1 text-lg font-black text-[#2B1A10]">
                    Informasi Transaksi Dibatalkan
                </h2>

                <div class="mt-4 grid gap-4 md:grid-cols-3">
                    <div class="rounded-2xl bg-white/70 p-4">
                        <p class="text-xs font-black uppercase tracking-[0.16em] text-[#A92A35]">
                            Dibatalkan Oleh
                        </p>

                        <p class="mt-2 text-sm font-black text-[#2B1A10]">
                            {{ $sale->cancelledBy?->name ?? '-' }}
                        </p>
                    </div>

                    <div class="rounded-2xl bg-white/70 p-4">
                        <p class="text-xs font-black uppercase tracking-[0.16em] text-[#A92A35]">
                            Waktu
                        </p>

                        <p class="mt-2 text-sm font-black text-[#2B1A10]">
                            {{ $sale->cancelled_at?->format('d M Y, H:i') ?? '-' }}
                        </p>
                    </div>

                    <div class="rounded-2xl bg-white/70 p-4">
                        <p class="text-xs font-black uppercase tracking-[0.16em] text-[#A92A35]">
                            Alasan
                        </p>

                        <p class="mt-2 text-sm font-semibold text-[#2B1A10]">
                            {{ $sale->cancel_reason ?: '-' }}
                        </p>
                    </div>
                </div>
            </div>
        @endif
        @if ($sale->status === 'selesai')
            <template x-teleport="body">
                <div x-show="cancelOpen" x-cloak x-transition.opacity @keydown.escape.window="cancelOpen = false"
                    @click.self="cancelOpen = false"
                    class="fixed inset-0 z-[9999] flex min-h-screen items-center justify-center overflow-y-auto bg-[#2B1A10]/45 p-4 backdrop-blur-sm">

                    <form method="POST" action="{{ route('admin.transactions.cancel', $sale) }}"
                        class="relative w-full max-w-lg rounded-3xl bg-white p-6 shadow-2xl">
                        @csrf
                        @method('PATCH')

                        <div class="flex items-start gap-4">
                            <div
                                class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-[#A92A35]/10 text-[#A92A35]">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2.4"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 9v4m0 4h.01M10.29 3.86 1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                                </svg>
                            </div>

                            <div class="min-w-0">
                                <h3 class="text-lg font-black text-[#2B1A10]">
                                    Batalkan Transaksi?
                                </h3>

                                <p class="mt-1 text-sm font-semibold leading-relaxed text-[#6B3E12]">
                                    Transaksi {{ $sale->kode_transaksi }} akan dibatalkan dan stok produk akan
                                    dikembalikan.
                                </p>
                            </div>
                        </div>

                        <div class="mt-5 rounded-2xl border border-[#A92A35]/20 bg-[#A92A35]/10 p-4">
                            <p class="text-sm font-bold leading-relaxed text-[#A92A35]">
                                Tindakan ini tidak menghapus transaksi. Status transaksi akan berubah menjadi dibatalkan.
                            </p>
                        </div>

                        <div class="mt-5">
                            <label class="mb-2 block text-sm font-black text-[#2B1A10]">
                                Alasan Pembatalan
                            </label>

                            <textarea name="cancel_reason" rows="4" required
                                placeholder="Contoh: Kasir salah input produk / jumlah transaksi salah"
                                class="block w-full rounded-2xl border border-[#F4D3B0] bg-[#F7F6F4] px-4 py-3 text-sm font-bold text-[#2B1A10] shadow-sm transition placeholder:text-[#6B3E12]/45 focus:border-[#F4B044] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#F4B044]/20"></textarea>

                            @error('cancel_reason')
                                <p class="mt-2 text-sm font-bold text-[#A92A35]">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                            <button type="button" @click="cancelOpen = false"
                                class="inline-flex h-12 items-center justify-center rounded-2xl border border-[#F4D3B0] bg-white px-5 text-sm font-black text-[#6B3E12] transition hover:bg-[#F7F6F4]">
                                Batal
                            </button>

                            <button type="submit"
                                class="inline-flex h-12 items-center justify-center rounded-2xl bg-[#A92A35] px-5 text-sm font-black text-white shadow-lg shadow-[#A92A35]/20 transition hover:-translate-y-0.5 hover:shadow-xl">
                                Ya, Batalkan
                            </button>
                        </div>
                    </form>
                </div>
            </template>
        @endif
    </div>

@endsection

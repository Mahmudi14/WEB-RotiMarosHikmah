@extends('layouts.master')

@section('page_title', $pageTitle)

@section('content')
    <div class="space-y-6">
        <div class="overflow-hidden rounded-3xl bg-[#1F444C] p-6 text-white shadow-lg shadow-[#1F444C]/10">
            <div class="relative">
                <div class="absolute -right-10 -top-12 h-32 w-32 rounded-full bg-[#F4B044]/20"></div>
                <div class="absolute -bottom-16 -left-12 h-36 w-36 rounded-full bg-white/10"></div>

                <div class="relative flex flex-col gap-5 md:flex-row md:items-center md:justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.28em] text-[#F4D3B0]">
                            {{ $canManage ? 'Admin' : 'Keuangan' }}
                        </p>
                        <h2 class="mt-2 text-2xl font-black tracking-tight text-white">
                            Riwayat Stok
                        </h2>
                        <p class="mt-2 text-sm leading-relaxed text-[#F4D3B0]">
                            {{ $product->nama_produk }} · {{ $product->category?->nama_kategori ?? '-' }}
                        </p>
                    </div>

                    <a href="{{ $backRoute }}"
                        class="inline-flex items-center justify-center rounded-2xl border border-white/20 bg-white/10 px-5 py-3 text-sm font-black text-white transition hover:bg-white/15">
                        Kembali
                    </a>
                </div>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-5 shadow-sm">
                <p class="text-xs font-black uppercase tracking-wider text-[#6B3E12]">Stok Saat Ini</p>
                <p class="mt-2 text-3xl font-black text-[#1F444C]">{{ number_format((int) $product->stock, 0, ',', '.') }}
                </p>
            </div>


            <div class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-5 shadow-sm">
                <p class="text-xs font-black uppercase tracking-wider text-[#6B3E12]">Status</p>
                <p class="mt-2 text-3xl font-black text-[#2B1A10]">{{ ucfirst($product->status_ketersediaan) }}</p>
            </div>
        </div>

        <div class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-5 shadow-sm">
            <form method="GET" action="{{ route($routePrefix . '.movements', $product) }}"
                class="grid gap-4 md:grid-cols-[220px_220px_auto]">
                <select name="type"
                    class="h-12 rounded-2xl border border-[#F4D3B0] bg-[#F7F6F4] px-4 text-sm font-bold text-[#2B1A10]">
                    <option value="">Semua Tipe</option>
                    <option value="in" @selected(request('type') === 'in')>Masuk</option>
                    <option value="out" @selected(request('type') === 'out')>Keluar</option>
                    <option value="adjustment" @selected(request('type') === 'adjustment')>Koreksi</option>
                </select>

                <select name="source"
                    class="h-12 rounded-2xl border border-[#F4D3B0] bg-[#F7F6F4] px-4 text-sm font-bold text-[#2B1A10]">
                    <option value="">Semua Sumber</option>
                    <option value="initial" @selected(request('source') === 'initial')>Stok Awal</option>
                    <option value="manual" @selected(request('source') === 'manual')>Manual</option>
                    <option value="sale" @selected(request('source') === 'sale')>Penjualan</option>
                    <option value="correction" @selected(request('source') === 'correction')>Koreksi</option>
                </select>

                <button type="submit"
                    class="inline-flex h-12 items-center justify-center rounded-2xl bg-[#1F444C] px-5 text-sm font-black text-white">
                    Filter
                </button>
            </form>
        </div>

        <div
            class="overflow-hidden rounded-3xl border border-[#F4D3B0]/70 bg-white shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-[#F4D3B0]/70">
                    <thead class="bg-[#F7F6F4]">
                        <tr>
                            <th class="px-5 py-4 text-left text-xs font-black uppercase tracking-wider text-[#6B3E12]">
                                Tanggal</th>
                            <th class="px-5 py-4 text-left text-xs font-black uppercase tracking-wider text-[#6B3E12]">Tipe
                            </th>
                            <th class="px-5 py-4 text-left text-xs font-black uppercase tracking-wider text-[#6B3E12]">
                                Jumlah</th>
                            <th class="px-5 py-4 text-left text-xs font-black uppercase tracking-wider text-[#6B3E12]">
                                Sebelum</th>
                            <th class="px-5 py-4 text-left text-xs font-black uppercase tracking-wider text-[#6B3E12]">
                                Sesudah</th>
                            <th class="px-5 py-4 text-left text-xs font-black uppercase tracking-wider text-[#6B3E12]">
                                Sumber</th>
                            <th class="px-5 py-4 text-left text-xs font-black uppercase tracking-wider text-[#6B3E12]">
                                Catatan</th>
                            <th class="px-5 py-4 text-left text-xs font-black uppercase tracking-wider text-[#6B3E12]">User
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-[#F4D3B0]/70 bg-white">
                        @forelse ($movements as $movement)
                            @php
                                $typeLabel = match ($movement->type) {
                                    'in' => 'Masuk',
                                    'out' => 'Keluar',
                                    'adjustment' => 'Koreksi',
                                    default => '-',
                                };

                                $sourceLabel = match ($movement->source) {
                                    'initial' => 'Stok Awal',
                                    'manual' => 'Manual',
                                    'sale' => 'Penjualan',
                                    'correction' => 'Koreksi',
                                    default => ucfirst($movement->source),
                                };

                                $quantity = (int) $movement->quantity;
                            @endphp

                            <tr>
                                <td class="px-5 py-4 text-sm font-bold text-[#2B1A10]">
                                    {{ $movement->created_at?->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-5 py-4 text-sm font-black text-[#2B1A10]">
                                    {{ $typeLabel }}
                                </td>
                                <td
                                    class="px-5 py-4 text-sm font-black {{ $quantity < 0 ? 'text-[#A92A35]' : 'text-[#1F444C]' }}">
                                    {{ $quantity > 0 ? '+' : '' }}{{ number_format($quantity, 0, ',', '.') }}
                                </td>
                                <td class="px-5 py-4 text-sm font-bold text-[#6B3E12]">
                                    {{ number_format((int) $movement->stock_before, 0, ',', '.') }}
                                </td>
                                <td class="px-5 py-4 text-sm font-bold text-[#6B3E12]">
                                    {{ number_format((int) $movement->stock_after, 0, ',', '.') }}
                                </td>
                                <td class="px-5 py-4 text-sm font-bold text-[#2B1A10]">
                                    {{ $sourceLabel }}
                                </td>
                                <td class="px-5 py-4 text-sm font-semibold text-[#6B3E12]">
                                    {{ $movement->note ?? '-' }}
                                </td>
                                <td class="px-5 py-4 text-sm font-semibold text-[#6B3E12]">
                                    {{ $movement->creator?->name ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-5 py-10 text-center text-sm font-bold text-[#6B3E12]">
                                    Riwayat stok belum tersedia.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="border-t border-[#F4D3B0]/70 px-5 py-4">
                {{ $movements->links() }}
            </div>
        </div>
    </div>
@endsection

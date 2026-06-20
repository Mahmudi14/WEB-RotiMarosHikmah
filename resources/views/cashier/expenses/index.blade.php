@extends('layouts.master')

@section('page_title', 'Pengeluaran Shift')

@section('content')
    @php
        $shiftTotal = $activeShift ? (float) $activeShift->expenses()->sum('nominal') : 0;
    @endphp

    <div class="space-y-6" x-data="{
        deleteOpen: false,
        deleteUrl: '',
        deleteTitle: '',
        deleteNominal: '',
    
        openDelete(url, title, nominal) {
            this.deleteUrl = url;
            this.deleteTitle = title;
            this.deleteNominal = nominal;
            this.deleteOpen = true;
        }
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
                            Kasir / Pengeluaran
                        </p>

                        <h1 class="mt-2 text-2xl font-black tracking-tight">
                            Pengeluaran Shift
                        </h1>

                        <p class="mt-2 max-w-2xl text-sm font-medium leading-relaxed text-[#F7F6F4]/80">
                            Catat barang atau kebutuhan yang dibeli menggunakan uang laci kasir pada shift aktif.
                        </p>
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row min-[1024px]:shrink-0">
                        @if ($activeShift)
                            <a href="{{ route('cashier.expenses.create') }}"
                                class="inline-flex h-12 items-center justify-center rounded-2xl bg-[#F4B044] px-5 text-sm font-black text-[#2B1A10] shadow-lg shadow-[#F4B044]/20 transition hover:-translate-y-0.5 hover:shadow-xl">
                                Tambah Pengeluaran
                            </a>
                        @else
                            <a href="{{ route('cashier.shifts.index') }}"
                                class="inline-flex h-12 items-center justify-center rounded-2xl bg-[#F4B044] px-5 text-sm font-black text-[#2B1A10] shadow-lg shadow-[#F4B044]/20 transition hover:-translate-y-0.5 hover:shadow-xl">
                                Buka Shift
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @if (!$activeShift)
            {{-- Empty Shift State --}}
            <div class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-6 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                <div
                    class="flex flex-col gap-4 min-[1024px]:flex-row min-[1024px]:items-center min-[1024px]:justify-between">
                    <div>
                        <p class="text-xs font-black uppercase tracking-[0.22em] text-[#A92A35]">
                            Shift Belum Dibuka
                        </p>

                        <h2 class="mt-1 text-lg font-black text-[#2B1A10]">
                            Pengeluaran belum bisa dicatat.
                        </h2>

                        <p class="mt-2 text-sm font-semibold leading-relaxed text-[#6B3E12]">
                            Buka shift terlebih dahulu agar pengeluaran bisa terhubung ke shift kasir.
                        </p>
                    </div>

                    <a href="{{ route('cashier.shifts.index') }}"
                        class="inline-flex h-12 items-center justify-center rounded-2xl bg-[#1F444C] px-5 text-sm font-black text-white shadow-lg shadow-[#1F444C]/15 transition hover:-translate-y-0.5 hover:shadow-xl">
                        Ke Halaman Shift
                    </a>
                </div>
            </div>
        @else
            {{-- Filter --}}
            <div class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-5 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                <form method="GET" action="{{ route('cashier.expenses.index') }}"
                    class="grid gap-3 min-[1024px]:grid-cols-[minmax(0,1fr)_auto] min-[1024px]:items-center">
                    <div>
                        <label class="sr-only" for="search">Cari</label>

                        <input id="search" type="text" name="search" value="{{ request('search') }}"
                            placeholder="Cari barang yang dibeli atau deskripsi..."
                            class="block h-12 w-full rounded-2xl border border-[#F4D3B0] bg-[#F7F6F4] px-4 text-sm font-medium text-[#2B1A10] shadow-sm transition placeholder:text-[#6B3E12]/45 focus:border-[#F4B044] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#F4B044]/20">
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2">
                        <button type="submit"
                            class="inline-flex h-12 items-center justify-center rounded-2xl bg-[#1F444C] px-5 text-sm font-black text-white shadow-lg shadow-[#1F444C]/10 transition hover:-translate-y-0.5 hover:shadow-xl">
                            Cari
                        </button>

                        <a href="{{ route('cashier.expenses.index') }}"
                            class="inline-flex h-12 items-center justify-center rounded-2xl border border-[#F4D3B0] bg-white px-5 text-sm font-black text-[#6B3E12] transition hover:bg-[#F7F6F4]">
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
                                Daftar Pengeluaran
                            </p>

                            <h2 class="mt-1 text-lg font-black text-[#2B1A10]">
                                Pengeluaran Shift Aktif
                            </h2>
                        </div>

                        <span
                            class="inline-flex w-fit rounded-2xl bg-[#F4B044]/20 px-4 py-2 text-sm font-black text-[#6B3E12]">
                            {{ $expenses->total() }} data
                        </span>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-[#F4D3B0]/60">
                        <thead class="bg-white">
                            <tr>
                                <th
                                    class="whitespace-nowrap px-5 py-4 text-left text-xs font-black uppercase tracking-[0.18em] text-[#6B3E12]">
                                    Waktu
                                </th>

                                <th
                                    class="whitespace-nowrap px-5 py-4 text-left text-xs font-black uppercase tracking-[0.18em] text-[#6B3E12]">
                                    Apa yang Dibeli
                                </th>

                                <th
                                    class="whitespace-nowrap px-5 py-4 text-left text-xs font-black uppercase tracking-[0.18em] text-[#6B3E12]">
                                    Deskripsi
                                </th>

                                <th
                                    class="whitespace-nowrap px-5 py-4 text-right text-xs font-black uppercase tracking-[0.18em] text-[#6B3E12]">
                                    Harga
                                </th>

                                <th
                                    class="whitespace-nowrap px-5 py-4 text-right text-xs font-black uppercase tracking-[0.18em] text-[#6B3E12]">
                                    Aksi
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-[#F4D3B0]/60 bg-white">
                            @forelse ($expenses as $expense)
                                <tr class="transition hover:bg-[#F7F6F4]">
                                    <td class="whitespace-nowrap px-5 py-4">
                                        <p class="text-sm font-black text-[#2B1A10]">
                                            {{ $expense->created_at?->format('H:i') }}
                                        </p>

                                        <p class="mt-1 text-xs font-semibold text-[#6B3E12]">
                                            {{ $expense->tanggal_pengeluaran?->format('d M Y') }}
                                        </p>
                                    </td>

                                    <td class="whitespace-nowrap px-5 py-4">
                                        <p class="text-sm font-black text-[#2B1A10]">
                                            {{ $expense->kategori_pengeluaran }}
                                        </p>
                                    </td>

                                    <td class="px-5 py-4">
                                        <p class="max-w-md truncate text-sm font-semibold text-[#6B3E12]">
                                            {{ $expense->keterangan ?: 'Tidak ada deskripsi.' }}
                                        </p>
                                    </td>

                                    <td class="whitespace-nowrap px-5 py-4 text-right text-sm font-black text-[#A92A35]">
                                        Rp {{ number_format((float) $expense->nominal, 0, ',', '.') }}
                                    </td>

                                    <td class="whitespace-nowrap px-5 py-4 text-right">
                                        <button type="button"
                                            @click="openDelete(
                                    @js(route('cashier.expenses.destroy', $expense)),
                                    @js($expense->kategori_pengeluaran),
                                    @js('Rp ' . number_format((float) $expense->nominal, 0, ',', '.'))
                                )"
                                            class="inline-flex h-9 items-center justify-center rounded-xl bg-[#A92A35]/10 px-3 text-xs font-black text-[#A92A35] transition hover:bg-[#A92A35] hover:text-white">
                                            Hapus
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-12 text-center">
                                        <h3 class="text-base font-black text-[#2B1A10]">
                                            Belum Ada Pengeluaran
                                        </h3>

                                        <p class="mt-2 text-sm font-semibold text-[#6B3E12]">
                                            Pengeluaran untuk shift aktif akan muncul di sini.
                                        </p>

                                        <a href="{{ route('cashier.expenses.create') }}"
                                            class="mt-5 inline-flex h-12 items-center justify-center rounded-2xl bg-[#1F444C] px-5 text-sm font-black text-white shadow-lg shadow-[#1F444C]/10 transition hover:-translate-y-0.5 hover:shadow-xl">
                                            Tambah Pengeluaran
                                        </a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Total Footer --}}
                <div class="border-t border-[#F4D3B0]/70 bg-[#F7F6F4] px-5 py-4">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="text-xs font-black uppercase tracking-[0.2em] text-[#6B3E12]">
                                Total Pengeluaran Shift
                            </p>

                            <p class="mt-1 text-sm font-semibold text-[#6B3E12]">
                                Total seluruh pengeluaran yang dicatat pada shift aktif.
                            </p>
                        </div>

                        <div class="rounded-2xl bg-[#A92A35]/10 px-5 py-3 text-right">
                            <p class="text-xs font-black uppercase tracking-[0.18em] text-[#A92A35]">
                                Total
                            </p>

                            <p class="mt-1 text-xl font-black text-[#A92A35]">
                                Rp {{ number_format($shiftTotal, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                </div>

                @if ($expenses->hasPages())
                    <div class="border-t border-[#F4D3B0]/70 px-5 py-4">
                        {{ $expenses->links() }}
                    </div>
                @endif
            </div>
        @endif

        {{-- Delete Modal --}}
        <template x-teleport="body">
            <div x-show="deleteOpen" x-cloak
                class="fixed inset-0 z-[9998] flex items-center justify-center bg-[#1F444C]/55 px-4 backdrop-blur-md"
                x-transition.opacity>
                <div @click.outside="deleteOpen = false" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-3"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 translate-y-3"
                    class="w-full max-w-md overflow-hidden rounded-3xl bg-white shadow-2xl shadow-[#1F444C]/25">
                    <div class="bg-[#A92A35] px-6 py-5 text-white">
                        <h2 class="text-lg font-black">
                            Hapus Pengeluaran?
                        </h2>

                        <p class="mt-1 text-sm font-semibold text-white/80">
                            Data pengeluaran akan dihapus dari shift aktif.
                        </p>
                    </div>

                    <div class="space-y-4 p-6">
                        <div class="rounded-2xl bg-[#F7F6F4] p-4">
                            <p class="text-xs font-black uppercase tracking-[0.18em] text-[#6B3E12]">
                                Apa yang Dibeli
                            </p>

                            <p class="mt-2 text-base font-black text-[#2B1A10]" x-text="deleteTitle"></p>

                            <p class="mt-1 text-sm font-black text-[#A92A35]" x-text="deleteNominal"></p>
                        </div>

                        <p class="text-sm font-semibold leading-relaxed text-[#6B3E12]">
                            Pengeluaran yang dihapus akan mengubah total pengeluaran shift dan kas di sistem.
                        </p>

                        <div class="grid gap-3 pt-2 sm:grid-cols-2">
                            <button type="button" @click="deleteOpen = false"
                                class="inline-flex h-12 items-center justify-center rounded-2xl border border-[#F4D3B0] bg-white px-5 text-sm font-black text-[#6B3E12] transition hover:bg-[#F7F6F4]">
                                Batal
                            </button>

                            <form method="POST" :action="deleteUrl">
                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                    class="inline-flex h-12 w-full items-center justify-center rounded-2xl bg-[#A92A35] px-5 text-sm font-black text-white shadow-lg shadow-[#A92A35]/20 transition hover:-translate-y-0.5 hover:shadow-xl">
                                    Ya, Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
@endsection

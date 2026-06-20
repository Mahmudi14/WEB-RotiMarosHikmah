@extends('layouts.master')

@section('page_title', 'Promo')

@section('content')
    <div class="space-y-6" x-data="{
        deleteModalOpen: false,
        deleteAction: '',
        deletePromoName: '',
    
        openDeleteModal(action, name) {
            this.deleteAction = action;
            this.deletePromoName = name;
            this.deleteModalOpen = true;
        },
    
        closeDeleteModal() {
            this.deleteModalOpen = false;
            this.deleteAction = '';
            this.deletePromoName = '';
        }
    }" @keydown.escape.window="closeDeleteModal()">
        {{-- Header --}}
        <div class="overflow-hidden rounded-3xl bg-[#1F444C] p-6 text-white shadow-lg shadow-[#1F444C]/10">
            <div class="relative">
                <div class="absolute -right-10 -top-12 h-32 w-32 rounded-full bg-[#F4B044]/20"></div>
                <div class="absolute -bottom-16 -left-12 h-36 w-36 rounded-full bg-white/10"></div>

                <div class="relative flex flex-col gap-5 md:flex-row md:items-center md:justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.28em] text-[#F4D3B0]">
                            Admin / Promo
                        </p>
                        <h1 class="mt-2 text-2xl font-black tracking-tight">
                            Manajemen Promo
                        </h1>
                        <p class="mt-2 max-w-2xl text-sm font-medium leading-relaxed text-[#F7F6F4]/80">
                            Kelola promo yang akan diterapkan otomatis pada transaksi POS berdasarkan diskon terbesar.
                        </p>
                    </div>

                    <a href="{{ route('admin.promos.create') }}"
                        class="inline-flex items-center justify-center gap-2 rounded-2xl bg-[#F4B044] px-5 py-3 text-sm font-black text-[#2B1A10] shadow-lg shadow-[#F4B044]/20 transition hover:-translate-y-0.5 hover:shadow-xl">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m7-7H5" />
                        </svg>
                        Tambah Promo
                    </a>
                </div>
            </div>
        </div>

        {{-- Filter --}}
        <div class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-5 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]"
            x-data="{
                statusOpen: false,
                selectedStatus: @js((string) request('status', '')),
                statuses: @js($statuses),
                get selectedStatusLabel() {
                    return this.selectedStatus ? this.statuses[this.selectedStatus] : 'Semua Status'
                }
            }">
            <form method="GET" action="{{ route('admin.promos.index') }}"
                class="grid gap-3 lg:grid-cols-[minmax(0,1fr)_230px_auto] lg:items-center xl:grid-cols-[minmax(0,1fr)_310px_auto]">
                {{-- Search --}}
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-[#6B3E12]/60">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21 21l-4.35-4.35m1.35-5.65a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>

                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari nama promo atau kode promo..."
                        class="block h-12 w-full rounded-2xl border border-[#F4D3B0] bg-[#F7F6F4] py-0 pl-12 pr-4 text-sm font-medium text-[#2B1A10] shadow-sm transition placeholder:text-[#6B3E12]/45 focus:border-[#F4B044] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#F4B044]/20">
                </div>

                {{-- Status --}}
                <div>
                    <input type="hidden" name="status" x-model="selectedStatus">

                    <div class="relative">
                        <button type="button" @click="statusOpen = !statusOpen"
                            class="flex h-12 w-full items-center justify-between rounded-2xl border border-[#F4D3B0] bg-[#F7F6F4] px-4 py-0 text-left text-sm font-bold text-[#2B1A10] shadow-sm transition hover:bg-white focus:border-[#F4B044] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#F4B044]/20">

                            <div class="flex items-center gap-3">
                                <span
                                    class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-[#F4B044]/20 text-[#6B3E12]">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.2"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9 12.75L11.25 15 15 9.75" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </span>

                                <span x-text="selectedStatusLabel" class="truncate"
                                    :class="selectedStatus ? 'text-[#2B1A10]' : 'text-[#6B3E12]/60'"></span>
                            </div>

                            <svg class="h-5 w-5 text-[#6B3E12] transition duration-200"
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
                            class="absolute z-40 mt-3 w-full overflow-hidden rounded-2xl border border-[#F4D3B0]/80 bg-white shadow-[0_25px_70px_-35px_rgba(31,68,76,0.55)]">

                            <div class="p-2">
                                <button type="button" @click="selectedStatus = ''; statusOpen = false"
                                    class="flex w-full items-center justify-between rounded-xl px-4 py-3 text-left text-sm font-bold transition"
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
                                        @click="selectedStatus = '{{ $value }}'; statusOpen = false"
                                        class="flex w-full items-center justify-between rounded-xl px-4 py-3 text-left text-sm font-bold transition"
                                        :class="selectedStatus === '{{ $value }}'
                                            ?
                                            'bg-[#F4B044] text-[#2B1A10]' :
                                            'text-[#2B1A10] hover:bg-[#F4B044]/15'">
                                        <span>{{ $label }}</span>

                                        <svg x-show="selectedStatus === '{{ $value }}'" x-cloak class="h-5 w-5"
                                            fill="none" stroke="currentColor" stroke-width="2.8" viewBox="0 0 24 24">
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

                    <a href="{{ route('admin.promos.index') }}"
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
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-[#F4D3B0]/70">
                    <thead class="bg-[#F7F6F4]">
                        <tr>
                            <th
                                class="w-16 px-5 py-4 text-left text-xs font-black uppercase tracking-[0.2em] text-[#6B3E12]">
                                No
                            </th>
                            <th class="px-5 py-4 text-left text-xs font-black uppercase tracking-[0.2em] text-[#6B3E12]">
                                Promo
                            </th>
                            <th class="px-5 py-4 text-left text-xs font-black uppercase tracking-[0.2em] text-[#6B3E12]">
                                Diskon
                            </th>
                            <th class="px-5 py-4 text-left text-xs font-black uppercase tracking-[0.2em] text-[#6B3E12]">
                                Cakupan
                            </th>
                            <th class="px-5 py-4 text-center text-xs font-black uppercase tracking-[0.2em] text-[#6B3E12]">
                                Status
                            </th>
                            <th class="px-5 py-4 text-right text-xs font-black uppercase tracking-[0.2em] text-[#6B3E12]">
                                Aksi
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-[#F4D3B0]/60 bg-white">
                        @forelse ($promos as $promo)
                            @php
                                $statusClass =
                                    $promo->status_efektif === 'aktif'
                                        ? 'bg-[#1F444C]/10 text-[#1F444C]'
                                        : 'bg-[#A92A35]/10 text-[#A92A35]';
                            @endphp

                            <tr class="transition hover:bg-[#F7F6F4]/80">
                                <td class="px-5 py-4 text-sm font-bold text-[#6B3E12]">
                                    {{ $promos->firstItem() + $loop->index }}
                                </td>

                                <td class="px-5 py-4">
                                    <div>
                                        <p class="text-sm font-black text-[#2B1A10]">
                                            {{ $promo->nama_promo }}
                                        </p>

                                        <p class="mt-1 text-xs font-bold text-[#6B3E12]">
                                            {{ $promo->kode_promo ?: 'Tanpa kode promo' }}
                                        </p>
                                    </div>
                                </td>

                                <td class="px-5 py-4">
                                    <p class="text-sm font-black text-[#2B1A10]">
                                        {{ $promo->nilai_diskon_formatted }}
                                    </p>
                                    <p class="mt-1 text-xs font-semibold text-[#6B3E12]">
                                        {{ $promo->tipe_diskon_label }}
                                    </p>
                                </td>

                                <td class="px-5 py-4">
                                    <p class="text-sm font-black text-[#2B1A10]">
                                        {{ $promo->cakupan_promo_label }}
                                    </p>

                                    @if ($promo->cakupan_promo === 'menu_tertentu')
                                        <p class="mt-1 text-xs font-semibold text-[#6B3E12]">
                                            {{ $promo->products_count }} produk dipilih
                                        </p>
                                    @else
                                        <p class="mt-1 text-xs font-semibold text-[#6B3E12]">
                                            Berlaku untuk semua produk
                                        </p>
                                    @endif
                                </td>

                                <td class="px-5 py-4">
                                    <div class="flex flex-col items-center gap-1.5">
                                        <span
                                            class="inline-flex rounded-full px-3 py-1 text-xs font-black {{ $statusClass }}">
                                            {{ $promo->status_efektif_label }}
                                        </span>

                                    </div>
                                </td>

                                <td class="px-5 py-4">
                                    <div class="flex flex-wrap items-center justify-end gap-2">
                                        <a href="{{ route('admin.promos.show', $promo) }}"
                                            class="inline-flex items-center justify-center rounded-xl bg-[#F4B044]/20 px-4 py-2 text-xs font-black text-[#6B3E12] transition hover:bg-[#F4B044] hover:text-[#2B1A10]">
                                            Detail
                                        </a>

                                        <a href="{{ route('admin.promos.edit', $promo) }}"
                                            class="inline-flex items-center justify-center rounded-xl bg-[#1F444C]/10 px-4 py-2 text-xs font-black text-[#1F444C] transition hover:bg-[#1F444C] hover:text-white">
                                            Edit
                                        </a>

                                        <button type="button"
                                            @click="openDeleteModal(@js(route('admin.promos.destroy', $promo)), @js($promo->nama_promo))"
                                            class="inline-flex items-center justify-center rounded-xl bg-[#A92A35]/10 px-4 py-2 text-xs font-black text-[#A92A35] transition hover:bg-[#A92A35] hover:text-white">
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-14 text-center">
                                    <div class="mx-auto flex max-w-sm flex-col items-center">
                                        <div
                                            class="flex h-16 w-16 items-center justify-center rounded-3xl bg-[#F4B044]/20 text-[#6B3E12]">
                                            <svg class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="2"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.169.659 1.591l8.182 8.182a2.25 2.25 0 003.182 0l4.318-4.318a2.25 2.25 0 000-3.182L11.159 3.659A2.25 2.25 0 009.568 3z" />
                                            </svg>
                                        </div>

                                        <h3 class="mt-4 text-base font-black text-[#2B1A10]">
                                            Belum ada promo
                                        </h3>

                                        <p class="mt-2 text-sm font-medium leading-relaxed text-[#6B3E12]">
                                            Tambahkan promo pertama untuk digunakan pada transaksi POS.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($promos->hasPages())
                <div class="border-t border-[#F4D3B0]/70 px-5 py-4">
                    {{ $promos->links() }}
                </div>
            @endif
        </div>

        {{-- Delete Modal --}}
        <template x-teleport="body">
            <div x-show="deleteModalOpen" x-cloak x-transition.opacity
                class="fixed inset-0 z-[9998] flex items-center justify-center bg-[#1F444C]/55 px-4 backdrop-blur-md">
                <div @click.outside="closeDeleteModal()" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                    class="w-full max-w-md overflow-hidden rounded-3xl border border-[#F4D3B0]/70 bg-white shadow-[0_30px_90px_-35px_rgba(31,68,76,0.8)]">

                    <div class="bg-[#A92A35] px-6 py-5 text-white">
                        <h2 class="text-lg font-black">
                            Hapus Promo?
                        </h2>
                        <p class="mt-1 text-sm font-medium text-white/80">
                            Tindakan ini tidak dapat dibatalkan.
                        </p>
                    </div>

                    <div class="p-6">
                        <p class="text-sm font-semibold leading-relaxed text-[#6B3E12]">
                            Promo <span class="font-black text-[#2B1A10]" x-text="deletePromoName"></span>
                            akan dihapus dari sistem.
                        </p>

                        <div class="mt-6 flex justify-end gap-3">
                            <button type="button" @click="closeDeleteModal()"
                                class="inline-flex h-11 items-center justify-center rounded-2xl border border-[#F4D3B0] bg-white px-5 text-sm font-black text-[#6B3E12] transition hover:bg-[#F7F6F4]">
                                Batal
                            </button>

                            <form method="POST" :action="deleteAction">
                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                    class="inline-flex h-11 items-center justify-center rounded-2xl bg-[#A92A35] px-5 text-sm font-black text-white shadow-lg shadow-[#A92A35]/20 transition hover:-translate-y-0.5 hover:shadow-xl">
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

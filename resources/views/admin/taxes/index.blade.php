@extends('layouts.master')

@section('page_title', 'Pajak')

@section('content')
    <div class="space-y-6" x-data="{
        deleteModalOpen: false,
        deleteAction: '',
        deleteTaxName: '',
    
        openDeleteModal(action, name) {
            this.deleteAction = action;
            this.deleteTaxName = name;
            this.deleteModalOpen = true;
        },
    
        closeDeleteModal() {
            this.deleteModalOpen = false;
            this.deleteAction = '';
            this.deleteTaxName = '';
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
                            Admin / Pajak
                        </p>

                        <h1 class="mt-2 text-2xl font-black tracking-tight">
                            Manajemen Pajak
                        </h1>

                        <p class="mt-2 max-w-2xl text-sm font-medium leading-relaxed text-[#F7F6F4]/80">
                            Kelola pajak transaksi POS. Hanya satu pajak yang dapat aktif dalam satu waktu.
                        </p>
                    </div>

                    <a href="{{ route('admin.taxes.create') }}"
                        class="inline-flex items-center justify-center gap-2 rounded-2xl bg-[#F4B044] px-5 py-3 text-sm font-black text-[#2B1A10] shadow-lg shadow-[#F4B044]/20 transition hover:-translate-y-0.5 hover:shadow-xl">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m7-7H5" />
                        </svg>
                        Tambah Pajak
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
            <form method="GET" action="{{ route('admin.taxes.index') }}"
                class="grid gap-3 lg:grid-cols-[minmax(0,1fr)_230px_auto] lg:items-center xl:grid-cols-[minmax(0,1fr)_310px_auto]">
                {{-- Search --}}
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-[#6B3E12]/60">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21 21l-4.35-4.35m1.35-5.65a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>

                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama pajak..."
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

                    <a href="{{ route('admin.taxes.index') }}"
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
                                Nama Pajak
                            </th>
                            <th class="px-5 py-4 text-left text-xs font-black uppercase tracking-[0.2em] text-[#6B3E12]">
                                Persentase
                            </th>
                            <th class="px-5 py-4 text-center text-xs font-black uppercase tracking-[0.2em] text-[#6B3E12]">
                                Status
                            </th>
                            <th class="px-5 py-4 text-left text-xs font-black uppercase tracking-[0.2em] text-[#6B3E12]">
                                Dibuat
                            </th>
                            <th class="px-5 py-4 text-right text-xs font-black uppercase tracking-[0.2em] text-[#6B3E12]">
                                Aksi
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-[#F4D3B0]/60 bg-white">
                        @forelse ($taxes as $tax)
                            @php
                                $statusClass =
                                    $tax->status === 'aktif'
                                        ? 'bg-[#1F444C]/10 text-[#1F444C]'
                                        : 'bg-[#A92A35]/10 text-[#A92A35]';
                            @endphp

                            <tr class="transition hover:bg-[#F7F6F4]/80">
                                <td class="px-5 py-4 text-sm font-bold text-[#6B3E12]">
                                    {{ $taxes->firstItem() + $loop->index }}
                                </td>

                                <td class="px-5 py-4">
                                    <div>
                                        <p class="text-sm font-black text-[#2B1A10]">
                                            {{ $tax->nama_pajak }}
                                        </p>

                                        <p class="mt-1 line-clamp-1 text-xs font-semibold text-[#6B3E12]">
                                            {{ $tax->deskripsi ?: 'Tidak ada deskripsi' }}
                                        </p>
                                    </div>
                                </td>

                                <td class="px-5 py-4">
                                    <p class="text-sm font-black text-[#2B1A10]">
                                        {{ $tax->persentase_formatted }}
                                    </p>
                                    <p class="mt-1 text-xs font-semibold text-[#6B3E12]">
                                        Dihitung setelah promo
                                    </p>
                                </td>

                                <td class="px-5 py-4 text-center">
                                    <span
                                        class="inline-flex rounded-full px-3 py-1 text-xs font-black {{ $statusClass }}">
                                        {{ $tax->status_label }}
                                    </span>
                                </td>

                                <td class="px-5 py-4">
                                    <p class="text-sm font-bold text-[#2B1A10]">
                                        {{ $tax->created_at->format('d M Y') }}
                                    </p>
                                    <p class="mt-1 text-xs font-semibold text-[#6B3E12]">
                                        {{ $tax->created_at->format('H:i') }}
                                    </p>
                                </td>

                                <td class="px-5 py-4">
                                    <div class="flex flex-wrap items-center justify-end gap-2">
                                        <a href="{{ route('admin.taxes.show', $tax) }}"
                                            class="inline-flex items-center justify-center rounded-xl bg-[#F4B044]/20 px-4 py-2 text-xs font-black text-[#6B3E12] transition hover:bg-[#F4B044] hover:text-[#2B1A10]">
                                            Detail
                                        </a>

                                        <a href="{{ route('admin.taxes.edit', $tax) }}"
                                            class="inline-flex items-center justify-center rounded-xl bg-[#1F444C]/10 px-4 py-2 text-xs font-black text-[#1F444C] transition hover:bg-[#1F444C] hover:text-white">
                                            Edit
                                        </a>

                                        <button type="button"
                                            @click="openDeleteModal(@js(route('admin.taxes.destroy', $tax)), @js($tax->nama_pajak))"
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
                                                    d="M9 7.5h6m-6 4.5h6m-6 4.5h3M6.75 3.75h10.5A1.75 1.75 0 0119 5.5v15.25l-3-1.5-3 1.5-3-1.5-3 1.5V5.5A1.75 1.75 0 016.75 3.75z" />
                                            </svg>
                                        </div>

                                        <h3 class="mt-4 text-base font-black text-[#2B1A10]">
                                            Belum ada pajak
                                        </h3>

                                        <p class="mt-2 text-sm font-medium leading-relaxed text-[#6B3E12]">
                                            Tambahkan pajak pertama untuk digunakan pada transaksi POS.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($taxes->hasPages())
                <div class="border-t border-[#F4D3B0]/70 px-5 py-4">
                    {{ $taxes->links() }}
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
                            Hapus Pajak?
                        </h2>
                    </div>

                    <div class="p-6">
                        <p class="text-sm font-semibold leading-relaxed text-[#6B3E12]">
                            Pajak <span class="font-black text-[#2B1A10]" x-text="deleteTaxName"></span>
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

@extends('layouts.master')

@section('page_title', 'Kategori Produk')

@section('content')
    <div class="space-y-6" x-data="{
        deleteModalOpen: false,
        deleteAction: '',
        deleteCategoryName: '',
    
        openDeleteModal(action, name) {
            this.deleteAction = action;
            this.deleteCategoryName = name;
            this.deleteModalOpen = true;
        },
    
        closeDeleteModal() {
            this.deleteModalOpen = false;
            this.deleteAction = '';
            this.deleteCategoryName = '';
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
                            Admin
                        </p>
                        <h2 class="mt-2 text-2xl font-black tracking-tight text-white">
                            Kategori Produk
                        </h2>
                        <p class="mt-2 max-w-2xl text-sm leading-relaxed text-[#F4D3B0]">
                            Kelola kategori produk Roti Maros Hikmah.
                        </p>
                    </div>

                    <a href="{{ route('admin.categories.create') }}"
                        class="inline-flex items-center justify-center gap-2 rounded-2xl bg-[#F4B044] px-5 py-3 text-sm font-black text-[#2B1A10] shadow-lg shadow-[#F4B044]/20 transition hover:-translate-y-0.5 hover:shadow-xl focus:outline-none focus:ring-4 focus:ring-[#F4B044]/25">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Tambah Kategori
                    </a>
                </div>
            </div>
        </div>
        {{-- Filter --}}
        <div class="relative overflow-visible rounded-3xl border border-[#F4D3B0]/70 bg-white p-5 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]"
            x-data="{
                statusOpen: false,
                selectedStatus: @js((string) request('status', '')),
                statuses: @js($statuses),
            
                get selectedStatusLabel() {
                    return this.selectedStatus ? this.statuses[this.selectedStatus] : 'Semua Status'
                }
            }">
            <form method="GET" action="{{ route('admin.categories.index') }}"
                class="grid gap-3 min-[835px]:grid-cols-[minmax(0,1fr)_230px_auto] min-[835px]:items-center xl:grid-cols-[minmax(0,1fr)_310px_auto]">

                {{-- Search --}}
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-[#6B3E12]/60">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21 21l-4.35-4.35m1.35-5.65a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>

                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama kategori..."
                        class="block h-12 w-full rounded-2xl border border-[#F4D3B0] bg-[#F7F6F4] py-0 pl-12 pr-4 text-sm font-medium text-[#2B1A10] shadow-sm transition placeholder:text-[#6B3E12]/45 focus:border-[#F4B044] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#F4B044]/20">
                </div>

                {{-- Custom Status Dropdown --}}
                <div>
                    <input type="hidden" name="status" x-model="selectedStatus">

                    <div class="relative">
                        <button type="button" @click="statusOpen = !statusOpen"
                            class="flex h-12 w-full items-center justify-between rounded-2xl border border-[#F4D3B0] bg-[#F7F6F4] px-4 py-0 text-left text-sm font-bold text-[#2B1A10] shadow-sm transition hover:bg-white focus:border-[#F4B044] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#F4B044]/20">

                            <div class="flex min-w-0 items-center gap-3">
                                <span
                                    class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-[#F4B044]/20 text-[#6B3E12]">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.2"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5" />
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
                            class="absolute left-0 top-full z-20 mt-3 w-full overflow-hidden rounded-2xl border border-[#F4D3B0]/80 bg-white shadow-[0_25px_70px_-35px_rgba(31,68,76,0.55)]">

                            <div class="p-2">
                                <button type="button" @click="selectedStatus = ''; statusOpen = false"
                                    class="group flex w-full items-center justify-between rounded-xl px-4 py-3 text-left text-sm font-bold transition"
                                    :class="selectedStatus === ''
                                        ?
                                        'bg-[#F4B044] text-[#2B1A10]' :
                                        'text-[#2B1A10] hover:bg-[#F4B044]/15'">
                                    <div class="min-w-0">
                                        <p class="truncate">Semua Status</p>
                                        <p class="mt-0.5 text-xs font-medium opacity-70">
                                            Tampilkan semua kategori
                                        </p>
                                    </div>

                                    <svg x-show="selectedStatus === ''" x-cloak class="h-5 w-5 shrink-0" fill="none"
                                        stroke="currentColor" stroke-width="2.8" viewBox="0 0 24 24">
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
                                        <div class="min-w-0">
                                            <p class="truncate">{{ $label }}</p>
                                            <p class="mt-0.5 text-xs font-medium opacity-70">
                                                Filter kategori {{ strtolower($label) }}
                                            </p>
                                        </div>

                                        <svg x-show="selectedStatus === @js((string) $value)" x-cloak
                                            class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2.8"
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

                    <a href="{{ route('admin.categories.index') }}"
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
            <div class="overflow-x-auto overscroll-x-contain">
                <table class="w-full min-w-[900px] divide-y divide-[#F4D3B0]/70">
                    <thead class="bg-[#F7F6F4]">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-[0.2em] text-[#6B3E12]">
                                No
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-[0.2em] text-[#6B3E12]">
                                Kategori
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-[0.2em] text-[#6B3E12]">
                                Total Menu
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-[0.2em] text-[#6B3E12]">
                                Status
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-[0.2em] text-[#6B3E12]">
                                Dibuat
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-black uppercase tracking-[0.2em] text-[#6B3E12]">
                                Aksi
                            </th>
                        </tr>
                    </thead>

                    <tbody x-data="categorySortable({
                        url: '{{ route('admin.categories.reorder') }}',
                        csrf: '{{ csrf_token() }}',
                        startNumber: {{ $categories->firstItem() ?? 1 }}
                    })" x-init="setup($el)" class="divide-y divide-[#F4D3B0]/60 bg-white">

                        @forelse ($categories as $category)
                            @php
                                $statusLabel = $category->status === 'aktif' ? 'Aktif' : 'Nonaktif';
                                $statusClass =
                                    $category->status === 'aktif'
                                        ? 'bg-[#1F444C]/10 text-[#1F444C]'
                                        : 'bg-[#A92A35]/10 text-[#A92A35]';
                            @endphp

                            <tr data-category-id="{{ $category->id }}" class="transition hover:bg-[#F7F6F4]/70">

                                <td class="px-6 py-4 text-sm font-black text-[#6B3E12]">
                                    <div class="flex items-center gap-3">
                                        <button type="button"
                                            class="drag-handle inline-flex h-9 w-9 touch-none select-none items-center justify-center rounded-xl bg-[#F4B044] text-[#2B1A10] shadow-md shadow-[#F4B044]/25 transition active:scale-95"
                                            title="Geser untuk mengubah urutan">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M8 6h.01M8 12h.01M8 18h.01M16 6h.01M16 12h.01M16 18h.01" />
                                            </svg>
                                        </button>

                                        <span data-row-number>
                                            {{ $categories->firstItem() + $loop->index }}
                                        </span>
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div>
                                            <p class="font-black text-[#2B1A10]">
                                                {{ $category->nama_kategori }}
                                            </p>
                                            <p class="mt-0.5 text-sm font-medium text-[#6B3E12]">
                                                {{ $category->slug }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <span
                                        class="inline-flex rounded-full bg-[#F4B044]/20 px-3 py-1 text-xs font-black text-[#6B3E12]">
                                        {{ $category->products_count }} Menu
                                    </span>
                                </td>

                                <td class="px-6 py-4">
                                    <span
                                        class="inline-flex rounded-full px-3 py-1 text-xs font-black {{ $statusClass }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-sm font-semibold text-[#6B3E12]">
                                    {{ $category->created_at?->format('d M Y') }}
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap items-center justify-end gap-2">
                                        <a href="{{ route('admin.categories.show', $category) }}"
                                            class="inline-flex items-center justify-center rounded-xl bg-[#F4B044] px-4 py-2 text-xs font-black text-[#2B1A10] shadow-sm shadow-[#F4B044]/20 transition hover:-translate-y-0.5 hover:shadow-md focus:outline-none focus:ring-4 focus:ring-[#F4B044]/25">
                                            Detail
                                        </a>

                                        <a href="{{ route('admin.categories.edit', $category) }}"
                                            class="inline-flex items-center justify-center rounded-xl bg-[#1F444C] px-4 py-2 text-xs font-black text-white shadow-sm shadow-[#1F444C]/20 transition hover:-translate-y-0.5 hover:shadow-md focus:outline-none focus:ring-4 focus:ring-[#1F444C]/20">
                                            Edit
                                        </a>

                                        <button type="button"
                                            @click="openDeleteModal(@js(route('admin.categories.destroy', $category)), @js($category->nama_kategori))"
                                            class="inline-flex items-center justify-center rounded-xl bg-[#A92A35] px-4 py-2 text-xs font-black text-white shadow-sm shadow-[#A92A35]/20 transition hover:-translate-y-0.5 hover:shadow-md focus:outline-none focus:ring-4 focus:ring-[#A92A35]/20">
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-14 text-center">
                                    <div class="mx-auto flex max-w-sm flex-col items-center">
                                        <div
                                            class="flex h-16 w-16 items-center justify-center rounded-3xl bg-[#F4B044]/20 text-[#6B3E12]">
                                            <svg class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="2"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M20 7.5L12 3 4 7.5m16 0L12 12m8-4.5v9L12 21m0-9L4 7.5m8 4.5v9M4 7.5v9L12 21" />
                                            </svg>
                                        </div>
                                        <h3 class="mt-4 text-lg font-black text-[#2B1A10]">
                                            Belum ada kategori
                                        </h3>
                                        <p class="mt-1 text-sm text-[#6B3E12]">
                                            Tambahkan kategori untuk mulai mengelompokkan menu produk.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($categories->hasPages())
                <div class="border-t border-[#F4D3B0]/70 bg-[#F7F6F4] px-6 py-4">
                    {{ $categories->links() }}
                </div>
            @endif
        </div>

        {{-- Delete Confirmation Modal --}}
        <template x-teleport="body">
            <div x-show="deleteModalOpen" x-cloak
                class="fixed inset-0 z-[9999] flex items-center justify-center p-4 sm:p-6" aria-modal="true"
                role="dialog">

                <div x-show="deleteModalOpen" x-transition.opacity
                    class="absolute inset-0 bg-[#1F444C]/55 backdrop-blur-md" @click="closeDeleteModal()">
                </div>

                <div x-show="deleteModalOpen" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                    class="relative z-10 w-full max-w-md overflow-hidden rounded-[1.75rem] border border-[#F4D3B0]/70 bg-white shadow-[0_35px_90px_-35px_rgba(31,68,76,0.65)]">

                    <div class="relative overflow-hidden bg-[#A92A35] px-6 py-6 text-white">
                        <div class="absolute -right-10 -top-12 h-32 w-32 rounded-full bg-white/10"></div>
                        <div class="absolute -bottom-16 -left-12 h-36 w-36 rounded-full bg-black/10"></div>

                        <div class="relative flex items-start gap-4">
                            <div
                                class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-white/15 text-white ring-1 ring-white/20">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2.4"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 9v3.75m0 3.75h.008v.008H12V16.5z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M10.29 3.86L1.82 18a1.5 1.5 0 001.29 2.25h17.78A1.5 1.5 0 0022.18 18L13.71 3.86a1.5 1.5 0 00-2.42 0z" />
                                </svg>
                            </div>

                            <div>
                                <p class="text-xs font-bold uppercase tracking-[0.24em] text-white/75">
                                    Konfirmasi Hapus
                                </p>
                                <h3 class="mt-1 text-xl font-black text-white">
                                    Hapus Kategori?
                                </h3>
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-5">
                        <p class="text-sm leading-relaxed text-[#6B3E12]">
                            Kamu akan menghapus kategori
                            <span class="font-black text-[#2B1A10]" x-text="deleteCategoryName"></span>.
                            Tindakan ini tidak dapat dibatalkan.
                        </p>

                        <div class="mt-5 rounded-2xl border border-[#A92A35]/20 bg-[#A92A35]/5 px-4 py-3">
                            <p class="text-sm font-semibold text-[#A92A35]">
                                Kategori tidak dapat dihapus jika masih memiliki menu/produk.
                            </p>
                        </div>
                    </div>

                    <div
                        class="flex flex-col-reverse gap-3 border-t border-[#F4D3B0]/70 bg-[#F7F6F4] px-6 py-5 sm:flex-row sm:justify-end">
                        <button type="button" @click="closeDeleteModal()"
                            class="inline-flex items-center justify-center rounded-2xl border border-[#F4D3B0] bg-white px-5 py-3 text-sm font-black text-[#6B3E12] transition hover:bg-[#F7F6F4] focus:outline-none focus:ring-4 focus:ring-[#F4B044]/20">
                            Batal
                        </button>

                        <form method="POST" :action="deleteAction">
                            @csrf
                            @method('DELETE')

                            <button type="submit"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-[#A92A35] px-5 py-3 text-sm font-black text-white shadow-lg shadow-[#A92A35]/20 transition hover:-translate-y-0.5 hover:shadow-xl focus:outline-none focus:ring-4 focus:ring-[#A92A35]/20">
                                Ya, Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </template>
    </div>
    <style>
        .sortable-ghost {
            opacity: 0.2;
            background: rgba(244, 176, 68, 0.12);
        }

        .sortable-chosen {
            background: rgba(244, 176, 68, 0.1);
        }

        .sortable-drag,
        .sortable-fallback {
            display: table !important;
            width: 100% !important;
            background: white !important;
            opacity: 0.95 !important;
            box-shadow: 0 20px 45px -20px rgba(31, 68, 76, 0.45);
            z-index: 9999 !important;
        }

        .sortable-fallback td {
            background: white;
        }
    </style>
    <script>
        function categorySortable({
            url,
            csrf,
            startNumber
        }) {
            return {
                tableBody: null,
                sortable: null,
                isSaving: false,

                setup(element) {
                    if (!(element instanceof HTMLElement)) {
                        console.error('Elemen tabel kategori tidak ditemukan.');
                        return;
                    }

                    if (!window.Sortable) {
                        console.error('SortableJS belum dimuat.');
                        return;
                    }

                    this.tableBody = element;

                    this.sortable = window.Sortable.create(this.tableBody, {
                        animation: 180,
                        easing: 'cubic-bezier(0.2, 0, 0, 1)',
                        direction: 'vertical',

                        handle: '.drag-handle',
                        draggable: 'tr[data-category-id]',

                        ghostClass: 'sortable-ghost',
                        chosenClass: 'sortable-chosen',
                        dragClass: 'sortable-drag',
                        fallbackClass: 'sortable-fallback',

                        forceFallback: true,
                        fallbackOnBody: false,
                        fallbackTolerance: 4,

                        swapThreshold: 0.5,
                        invertSwap: false,

                        delay: 120,
                        delayOnTouchOnly: true,
                        touchStartThreshold: 5,

                        onEnd: () => {
                            this.updateNumbers();
                            this.saveOrder();
                        },
                    });
                },

                updateNumbers() {
                    const rows = this.tableBody.querySelectorAll('tr[data-category-id]');

                    rows.forEach((row, index) => {
                        const numberElement = row.querySelector('[data-row-number]');

                        if (numberElement) {
                            numberElement.textContent = Number(startNumber) + index;
                        }
                    });
                },

                saveOrder() {
                    if (this.isSaving) {
                        return;
                    }

                    this.isSaving = true;

                    const rows = [...this.tableBody.querySelectorAll('tr[data-category-id]')];

                    const orders = rows.map((row, index) => {
                        return {
                            id: Number(row.dataset.categoryId),
                            sort_order: Number(startNumber) + index,
                        };
                    });

                    fetch(url, {
                            method: 'PATCH',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrf,
                            },
                            body: JSON.stringify({
                                orders
                            }),
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Gagal menyimpan urutan kategori.');
                            }

                            return response.json();
                        })
                        .catch(error => {
                            alert(error.message);
                            window.location.reload();
                        })
                        .finally(() => {
                            this.isSaving = false;
                        });
                },
            };
        }
    </script>
@endsection

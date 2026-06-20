@extends('layouts.master')

@section('page_title', 'Detail Kategori')

@section('content')
    <div class="space-y-6" x-data="{
        statusModalOpen: false,
        statusAction: '',
        statusTitle: '',
        statusMessage: '',
        statusButtonText: '',
        statusButtonClass: '',
    
        openStatusModal(action, currentStatus) {
            this.statusAction = action;
    
            if (currentStatus === 'aktif') {
                this.statusTitle = 'Nonaktifkan Kategori?';
                this.statusMessage = 'Kategori ini akan dinonaktifkan dan tidak digunakan untuk pengelompokan produk baru.';
                this.statusButtonText = 'Ya, Nonaktifkan';
                this.statusButtonClass = 'bg-[#A92A35] text-white shadow-[#A92A35]/20 focus:ring-[#A92A35]/20';
            } else {
                this.statusTitle = 'Aktifkan Kategori?';
                this.statusMessage = 'Kategori ini akan diaktifkan kembali dan dapat digunakan untuk pengelompokan produk.';
                this.statusButtonText = 'Ya, Aktifkan';
                this.statusButtonClass = 'bg-[#F4B044] text-[#2B1A10] shadow-[#F4B044]/20 focus:ring-[#F4B044]/20';
            }
    
            this.statusModalOpen = true;
        },
    
        closeStatusModal() {
            this.statusModalOpen = false;
            this.statusAction = '';
            this.statusTitle = '';
            this.statusMessage = '';
            this.statusButtonText = '';
            this.statusButtonClass = '';
        }
    }" @keydown.escape.window="closeStatusModal()">
        <div class="overflow-hidden rounded-3xl bg-[#1F444C] p-6 text-white shadow-lg shadow-[#1F444C]/10">
            <div class="relative">
                <div class="absolute -right-10 -top-12 h-32 w-32 rounded-full bg-[#F4B044]/20"></div>
                <div class="absolute -bottom-16 -left-12 h-36 w-36 rounded-full bg-white/10"></div>

                <div class="relative flex flex-col gap-5 md:flex-row md:items-center md:justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.28em] text-[#F4D3B0]">
                            Detail Kategori
                        </p>
                        <h2 class="mt-2 text-2xl font-black tracking-tight text-white">
                            {{ $category->nama_kategori }}
                        </h2>
                        <p class="mt-2 max-w-2xl text-sm leading-relaxed text-[#F4D3B0]">
                            Informasi lengkap kategori produk.
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('admin.categories.edit', $category) }}"
                            class="inline-flex items-center justify-center rounded-2xl bg-[#F4B044] px-5 py-3 text-sm font-black text-[#2B1A10] shadow-lg shadow-[#F4B044]/20 transition hover:-translate-y-0.5 hover:shadow-xl">
                            Edit
                        </a>

                        <a href="{{ route('admin.categories.index') }}"
                            class="inline-flex items-center justify-center rounded-2xl border border-white/20 bg-white/10 px-5 py-3 text-sm font-black text-white transition hover:bg-white/15">
                            Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="rounded-2xl border border-[#1F444C]/20 bg-[#1F444C]/10 px-5 py-4 text-sm font-bold text-[#1F444C]">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid gap-4 md:grid-cols-3">
            <div class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-5 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                <p class="text-sm font-bold text-[#6B3E12]">Status</p>
                <p class="mt-2 text-2xl font-black text-[#2B1A10]">
                    {{ $category->status === 'aktif' ? 'Aktif' : 'Nonaktif' }}
                </p>
            </div>

            <div class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-5 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                <p class="text-sm font-bold text-[#6B3E12]">Total Menu</p>
                <p class="mt-2 text-2xl font-black text-[#2B1A10]">
                    {{ $category->products_count }} Menu
                </p>
            </div>

            <div class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-5 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                <p class="text-sm font-bold text-[#6B3E12]">Dibuat</p>
                <p class="mt-2 text-2xl font-black text-[#2B1A10]">
                    {{ $category->created_at?->format('d M Y') }}
                </p>
            </div>
        </div>

        <div class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-6 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
            <p class="text-sm font-black uppercase tracking-[0.2em] text-[#6B3E12]">
                Deskripsi
            </p>

            <p class="mt-4 text-sm leading-relaxed text-[#2B1A10]">
                {{ $category->deskripsi ?: 'Belum ada deskripsi untuk kategori ini.' }}
            </p>
        </div>

        <div class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-6 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-sm font-black uppercase tracking-[0.2em] text-[#6B3E12]">
                        Pengaturan Status
                    </p>

                    <h3 class="mt-2 text-xl font-black text-[#2B1A10]">
                        {{ $category->status === 'aktif' ? 'Nonaktifkan Kategori' : 'Aktifkan Kategori' }}
                    </h3>

                    <p class="mt-2 max-w-2xl text-sm leading-relaxed text-[#6B3E12]">
                        {{ $category->status === 'aktif'
                            ? 'Kategori yang dinonaktifkan tidak akan digunakan untuk pengelompokan produk baru.'
                            : 'Kategori yang diaktifkan dapat digunakan kembali untuk pengelompokan produk.' }}
                    </p>
                </div>

                <button type="button" @click="openStatusModal(@js(route('admin.categories.update-status', $category)), @js($category->status))"
                    class="{{ $category->status === 'aktif'
                        ? 'bg-[#A92A35] text-white shadow-[#A92A35]/20'
                        : 'bg-[#F4B044] text-[#2B1A10] shadow-[#F4B044]/20' }}
        inline-flex w-full items-center justify-center rounded-2xl px-5 py-3 text-sm font-black shadow-lg transition hover:-translate-y-0.5 hover:shadow-xl md:w-auto">
                    {{ $category->status === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' }}
                </button>
            </div>
        </div>
        <template x-teleport="body">
            <div x-show="statusModalOpen" x-cloak class="fixed inset-0 z-[9999] flex items-center justify-center p-4 sm:p-6"
                aria-modal="true" role="dialog">

                {{-- Overlay --}}
                <div x-show="statusModalOpen" x-transition.opacity class="absolute inset-0 bg-[#1F444C]/55 backdrop-blur-md"
                    @click="closeStatusModal()">
                </div>

                {{-- Modal Card --}}
                <div x-show="statusModalOpen" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                    class="relative z-10 w-full max-w-md overflow-hidden rounded-[1.75rem] border border-[#F4D3B0]/70 bg-white shadow-[0_35px_90px_-35px_rgba(31,68,76,0.65)]">

                    {{-- Header --}}
                    <div class="relative overflow-hidden bg-[#1F444C] px-6 py-6 text-white">
                        <div class="absolute -right-10 -top-12 h-32 w-32 rounded-full bg-[#F4B044]/20"></div>
                        <div class="absolute -bottom-16 -left-12 h-36 w-36 rounded-full bg-white/10"></div>

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
                                    Konfirmasi Status
                                </p>
                                <h3 class="mt-1 text-xl font-black text-white" x-text="statusTitle"></h3>
                            </div>
                        </div>
                    </div>

                    {{-- Body --}}
                    <div class="px-6 py-5">
                        <p class="text-sm leading-relaxed text-[#6B3E12]" x-text="statusMessage"></p>

                        <div class="mt-5 rounded-2xl border border-[#F4B044]/30 bg-[#F4B044]/10 px-4 py-3">
                            <p class="text-sm font-semibold text-[#6B3E12]">
                                Perubahan status ini akan memengaruhi penggunaan kategori pada data produk.
                            </p>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div
                        class="flex flex-col-reverse gap-3 border-t border-[#F4D3B0]/70 bg-[#F7F6F4] px-6 py-5 sm:flex-row sm:justify-end">
                        <button type="button" @click="closeStatusModal()"
                            class="inline-flex items-center justify-center rounded-2xl border border-[#F4D3B0] bg-white px-5 py-3 text-sm font-black text-[#6B3E12] transition hover:bg-[#F7F6F4] focus:outline-none focus:ring-4 focus:ring-[#F4B044]/20">
                            Batal
                        </button>

                        <form method="POST" :action="statusAction">
                            @csrf
                            @method('PATCH')

                            <button type="submit" :class="statusButtonClass"
                                class="inline-flex w-full items-center justify-center rounded-2xl px-5 py-3 text-sm font-black shadow-lg transition hover:-translate-y-0.5 hover:shadow-xl focus:outline-none focus:ring-4">
                                <span x-text="statusButtonText"></span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </template>
    </div>
@endsection

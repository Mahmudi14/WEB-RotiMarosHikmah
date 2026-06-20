@extends('layouts.master')

@section('page_title', 'Detail Terminal Kasir')

@section('content')
    @php
        $statusClass =
            $terminal->status === 'aktif' ? 'bg-[#1F444C]/10 text-[#1F444C]' : 'bg-[#A92A35]/10 text-[#A92A35]';

        $bridgeClass = $terminal->is_bridge_online
            ? 'bg-[#1F444C]/10 text-[#1F444C]'
            : 'bg-[#A92A35]/10 text-[#A92A35]';
    @endphp

    <div class="space-y-6" x-data="{
        copied: false,
    
        async copyToken(token) {
            await navigator.clipboard.writeText(token);
            this.copied = true;
    
            setTimeout(() => {
                this.copied = false;
            }, 1800);
        },
    
        confirmModalOpen: false,
        confirmAction: '',
        confirmTitle: '',
        confirmMessage: '',
        confirmButtonText: '',
        confirmButtonClass: '',
        confirmMethod: 'PATCH',
    
        openStatusModal(action, currentStatus) {
            this.confirmAction = action;
            this.confirmMethod = 'PATCH';
    
            if (currentStatus === 'aktif') {
                this.confirmTitle = 'Nonaktifkan Terminal?';
                this.confirmMessage = 'Terminal ini tidak dapat digunakan untuk buka shift dan Flutter Bridge tidak bisa mengambil print job.';
                this.confirmButtonText = 'Ya, Nonaktifkan';
                this.confirmButtonClass = 'bg-[#A92A35] text-white shadow-[#A92A35]/20 focus:ring-[#A92A35]/20';
            } else {
                this.confirmTitle = 'Aktifkan Terminal?';
                this.confirmMessage = 'Terminal ini akan dapat digunakan kembali untuk operasional kasir.';
                this.confirmButtonText = 'Ya, Aktifkan';
                this.confirmButtonClass = 'bg-[#F4B044] text-[#2B1A10] shadow-[#F4B044]/20 focus:ring-[#F4B044]/20';
            }
    
            this.confirmModalOpen = true;
        },
    
        openRegenerateModal(action) {
            this.confirmAction = action;
            this.confirmMethod = 'PATCH';
            this.confirmTitle = 'Regenerate Token Bridge?';
            this.confirmMessage = 'Token lama tidak akan bisa digunakan lagi. Flutter Bridge harus diisi ulang dengan token baru.';
            this.confirmButtonText = 'Ya, Buat Token Baru';
            this.confirmButtonClass = 'bg-[#A92A35] text-white shadow-[#A92A35]/20 focus:ring-[#A92A35]/20';
            this.confirmModalOpen = true;
        },
    
        closeConfirmModal() {
            this.confirmModalOpen = false;
            this.confirmAction = '';
            this.confirmTitle = '';
            this.confirmMessage = '';
            this.confirmButtonText = '';
            this.confirmButtonClass = '';
            this.confirmMethod = 'PATCH';
        }
    }" @keydown.escape.window="closeConfirmModal()">
        {{-- Header --}}
        <div class="overflow-hidden rounded-3xl bg-[#1F444C] p-6 text-white shadow-lg shadow-[#1F444C]/10">
            <div class="relative">
                <div class="absolute -right-10 -top-12 h-32 w-32 rounded-full bg-[#F4B044]/20"></div>
                <div class="absolute -bottom-16 -left-12 h-36 w-36 rounded-full bg-white/10"></div>

                <div class="relative flex flex-col gap-5 md:flex-row md:items-center md:justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.28em] text-[#F4D3B0]">
                            Admin / Terminal Kasir / Detail
                        </p>

                        <h1 class="mt-2 text-2xl font-black tracking-tight">
                            {{ $terminal->nama_terminal }}
                        </h1>

                        <p class="mt-2 max-w-2xl text-sm font-medium leading-relaxed text-[#F7F6F4]/80">
                            {{ $terminal->kode_terminal }} — detail terminal POS dan token Flutter Printer Bridge.
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('admin.pos-terminals.edit', $terminal) }}"
                            class="inline-flex items-center justify-center gap-2 rounded-2xl bg-[#F4B044] px-5 py-3 text-sm font-black text-[#2B1A10] shadow-lg shadow-[#F4B044]/20 transition hover:-translate-y-0.5 hover:shadow-xl">
                            Edit Terminal
                        </a>

                        <a href="{{ route('admin.pos-terminals.index') }}"
                            class="inline-flex items-center justify-center gap-2 rounded-2xl border border-white/15 bg-white/10 px-5 py-3 text-sm font-black text-white transition hover:bg-white/15">
                            Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Info Cards --}}
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-5 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                <p class="text-xs font-black uppercase tracking-[0.2em] text-[#6B3E12]">
                    Kode Terminal
                </p>
                <p class="mt-3 text-lg font-black text-[#2B1A10]">
                    {{ $terminal->kode_terminal }}
                </p>
            </div>

            <div class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-5 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                <p class="text-xs font-black uppercase tracking-[0.2em] text-[#6B3E12]">
                    Status Terminal
                </p>
                <div class="mt-3">
                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-black {{ $statusClass }}">
                        {{ $terminal->status_label }}
                    </span>
                </div>
            </div>

            <div class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-5 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                <p class="text-xs font-black uppercase tracking-[0.2em] text-[#6B3E12]">
                    Status Bridge
                </p>
                <div class="mt-3">
                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-black {{ $bridgeClass }}">
                        {{ $terminal->bridge_status_label }}
                    </span>
                </div>
                <p class="mt-2 text-xs font-bold text-[#6B3E12]">
                    {{ $terminal->bridge_status_description }}
                </p>
            </div>

            <div class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-5 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                <p class="text-xs font-black uppercase tracking-[0.2em] text-[#6B3E12]">
                    Dibuat
                </p>
                <p class="mt-3 text-sm font-black text-[#2B1A10]">
                    {{ $terminal->created_at->format('d M Y') }}
                </p>
                <p class="mt-1 text-xs font-bold text-[#6B3E12]">
                    {{ $terminal->created_at->format('H:i') }}
                </p>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-[1fr_380px]">
            {{-- Token --}}
            <div class="space-y-6">
                <div
                    class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-6 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                    <h2 class="text-lg font-black text-[#2B1A10]">
                        Token Flutter Bridge
                    </h2>

                    <p class="mt-2 text-sm font-semibold leading-relaxed text-[#6B3E12]">
                        Masukkan token ini ke aplikasi Flutter Printer Bridge pada perangkat terminal ini.
                    </p>

                    <div class="mt-5 rounded-3xl border border-[#F4D3B0] bg-[#F7F6F4] p-4">
                        <code class="block break-all text-sm font-black leading-relaxed text-[#2B1A10]">
                            {{ $terminal->bridge_token }}
                        </code>
                    </div>

                    <div class="mt-5 flex flex-wrap gap-3">
                        <button type="button" @click="copyToken(@js($terminal->bridge_token))"
                            class="inline-flex h-12 items-center justify-center rounded-2xl bg-[#1F444C] px-5 text-sm font-black text-white shadow-lg shadow-[#1F444C]/20 transition hover:-translate-y-0.5 hover:shadow-xl">
                            <span x-show="!copied">Copy Token</span>
                            <span x-show="copied" x-cloak>Token Disalin</span>
                        </button>

                        <button type="button" @click="openRegenerateModal(@js(route('admin.pos-terminals.regenerate-token', $terminal)))"
                            class="inline-flex h-12 items-center justify-center rounded-2xl bg-[#A92A35] px-5 text-sm font-black text-white shadow-lg shadow-[#A92A35]/20 transition hover:-translate-y-0.5 hover:shadow-xl">
                            Regenerate Token
                        </button>
                    </div>
                </div>

                {{-- Deskripsi --}}
                <div
                    class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-6 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                    <h2 class="text-lg font-black text-[#2B1A10]">
                        Deskripsi Terminal
                    </h2>

                    <p class="mt-4 text-sm font-semibold leading-relaxed text-[#6B3E12]">
                        {{ $terminal->deskripsi ?: 'Belum ada deskripsi untuk terminal ini.' }}
                    </p>
                </div>
            </div>

            {{-- Pengaturan --}}
            <div class="space-y-6">
                <div
                    class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-6 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                    <h2 class="text-lg font-black text-[#2B1A10]">
                        Pengaturan Status
                    </h2>

                    <p class="mt-2 text-sm font-semibold leading-relaxed text-[#6B3E12]">
                        {{ $terminal->status === 'aktif'
                            ? 'Terminal sedang aktif dan dapat digunakan untuk operasional POS.'
                            : 'Terminal sedang nonaktif dan tidak dapat digunakan untuk operasional POS.' }}
                    </p>

                    <button type="button"
                        @click="openStatusModal(@js(route('admin.pos-terminals.update-status', $terminal)), @js($terminal->status))"
                        class="{{ $terminal->status === 'aktif'
                            ? 'bg-[#A92A35] text-white shadow-[#A92A35]/20'
                            : 'bg-[#F4B044] text-[#2B1A10] shadow-[#F4B044]/20' }}
                        mt-5 inline-flex h-12 w-full items-center justify-center rounded-2xl px-5 text-sm font-black shadow-lg transition hover:-translate-y-0.5 hover:shadow-xl">
                        {{ $terminal->status === 'aktif' ? 'Nonaktifkan Terminal' : 'Aktifkan Terminal' }}
                    </button>
                </div>

                <div
                    class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-6 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                    <h2 class="text-lg font-black text-[#2B1A10]">
                        Informasi Bridge
                    </h2>

                    <div class="mt-4 space-y-3">
                        <div class="rounded-2xl bg-[#F7F6F4] p-4">
                            <p class="text-xs font-black uppercase tracking-[0.2em] text-[#6B3E12]">
                                Last Seen
                            </p>

                            <p class="mt-1 text-sm font-black text-[#2B1A10]">
                                {{ $terminal->last_seen_at ? $terminal->last_seen_at->format('d M Y, H:i') : 'Belum pernah terhubung' }}
                            </p>
                        </div>

                        <div class="rounded-2xl bg-[#F7F6F4] p-4">
                            <p class="text-xs font-black uppercase tracking-[0.2em] text-[#6B3E12]">
                                Terakhir Diperbarui
                            </p>

                            <p class="mt-1 text-sm font-black text-[#2B1A10]">
                                {{ $terminal->updated_at->format('d M Y, H:i') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Confirm Modal --}}
        <template x-teleport="body">
            <div x-show="confirmModalOpen" x-cloak x-transition.opacity
                class="fixed inset-0 z-[9998] flex items-center justify-center bg-[#1F444C]/55 px-4 backdrop-blur-md">
                <div @click.outside="closeConfirmModal()" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                    class="w-full max-w-md overflow-hidden rounded-3xl border border-[#F4D3B0]/70 bg-white shadow-[0_30px_90px_-35px_rgba(31,68,76,0.8)]">

                    <div class="bg-[#1F444C] px-6 py-5 text-white">
                        <h2 class="text-lg font-black" x-text="confirmTitle"></h2>
                        <p class="mt-1 text-sm font-medium text-white/80" x-text="confirmMessage"></p>
                    </div>

                    <div class="p-6">
                        <div class="flex justify-end gap-3">
                            <button type="button" @click="closeConfirmModal()"
                                class="inline-flex h-11 items-center justify-center rounded-2xl border border-[#F4D3B0] bg-white px-5 text-sm font-black text-[#6B3E12] transition hover:bg-[#F7F6F4]">
                                Batal
                            </button>

                            <form method="POST" :action="confirmAction">
                                @csrf
                                @method('PATCH')

                                <button type="submit" :class="confirmButtonClass"
                                    class="inline-flex h-11 items-center justify-center rounded-2xl px-5 text-sm font-black shadow-lg transition hover:-translate-y-0.5 hover:shadow-xl focus:outline-none focus:ring-4"
                                    x-text="confirmButtonText">
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
@endsection

@extends('layouts.master')

@section('page_title', 'Detail Kasir')

@section('content')
    @php
        $statusClass =
            $cashier->status === 'aktif' ? 'bg-[#1F444C]/10 text-[#1F444C]' : 'bg-[#A92A35]/10 text-[#A92A35]';
    @endphp

    <div class="space-y-6" x-data="{
        statusModalOpen: false,
        resetModalOpen: false,
    
        openStatusModal() {
            this.statusModalOpen = true;
        },
    
        closeStatusModal() {
            this.statusModalOpen = false;
        },
    
        openResetModal() {
            this.resetModalOpen = true;
        },
    
        closeResetModal() {
            this.resetModalOpen = false;
        }
    }" @keydown.escape.window="closeStatusModal(); closeResetModal();">
        {{-- Header --}}
        <div class="overflow-hidden rounded-3xl bg-[#1F444C] p-6 text-white shadow-lg shadow-[#1F444C]/10">
            <div class="relative">
                <div class="absolute -right-10 -top-12 h-32 w-32 rounded-full bg-[#F4B044]/20"></div>
                <div class="absolute -bottom-16 -left-12 h-36 w-36 rounded-full bg-white/10"></div>

                <div class="relative flex flex-col gap-5 md:flex-row md:items-center md:justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.28em] text-[#F4D3B0]">
                            Admin / Manajemen Kasir / Detail
                        </p>

                        <h1 class="mt-2 text-2xl font-black tracking-tight">
                            {{ $cashier->name }}
                        </h1>

                        <p class="mt-2 max-w-2xl text-sm font-medium leading-relaxed text-[#F7F6F4]/80">
                            Detail akun kasir dan pengaturan status login.
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('admin.cashiers.edit', $cashier) }}"
                            class="inline-flex items-center justify-center gap-2 rounded-2xl bg-[#F4B044] px-5 py-3 text-sm font-black text-[#2B1A10] shadow-lg shadow-[#F4B044]/20 transition hover:-translate-y-0.5 hover:shadow-xl">
                            Edit Kasir
                        </a>

                        <a href="{{ route('admin.cashiers.index') }}"
                            class="inline-flex items-center justify-center gap-2 rounded-2xl border border-white/15 bg-white/10 px-5 py-3 text-sm font-black text-white transition hover:bg-white/15">
                            Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Info Cards --}}
        <div class="grid gap-4 md:grid-cols-3">
            <div class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-5 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                <p class="text-xs font-black uppercase tracking-[0.2em] text-[#6B3E12]">
                    Nama Kasir
                </p>
                <p class="mt-3 text-lg font-black text-[#2B1A10]">
                    {{ $cashier->name }}
                </p>
            </div>

            <div class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-5 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                <p class="text-xs font-black uppercase tracking-[0.2em] text-[#6B3E12]">
                    Email
                </p>
                <p class="mt-3 break-all text-sm font-black text-[#2B1A10]">
                    {{ $cashier->email }}
                </p>
            </div>

            <div class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-5 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                <p class="text-xs font-black uppercase tracking-[0.2em] text-[#6B3E12]">
                    Status
                </p>

                <div class="mt-3">
                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-black {{ $statusClass }}">
                        {{ ucfirst($cashier->status) }}
                    </span>
                </div>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-[1fr_380px]">
            {{-- Detail --}}
            <div class="space-y-6">
                <div
                    class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-6 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                    <h2 class="text-lg font-black text-[#2B1A10]">
                        Informasi Akun
                    </h2>

                    <div class="mt-5 grid gap-4 md:grid-cols-2">
                        <div class="rounded-2xl bg-[#F7F6F4] p-4">
                            <p class="text-xs font-black uppercase tracking-[0.2em] text-[#6B3E12]">
                                Role
                            </p>
                            <p class="mt-1 text-sm font-black text-[#2B1A10]">
                                Kasir
                            </p>
                        </div>

                        <div class="rounded-2xl bg-[#F7F6F4] p-4">
                            <p class="text-xs font-black uppercase tracking-[0.2em] text-[#6B3E12]">
                                Dibuat
                            </p>
                            <p class="mt-1 text-sm font-black text-[#2B1A10]">
                                {{ $cashier->created_at->format('d M Y, H:i') }}
                            </p>
                        </div>

                        <div class="rounded-2xl bg-[#F7F6F4] p-4">
                            <p class="text-xs font-black uppercase tracking-[0.2em] text-[#6B3E12]">
                                Terakhir Diperbarui
                            </p>
                            <p class="mt-1 text-sm font-black text-[#2B1A10]">
                                {{ $cashier->updated_at->format('d M Y, H:i') }}
                            </p>
                        </div>
                    </div>
                </div>

                <div
                    class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-6 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                    <h2 class="text-lg font-black text-[#2B1A10]">
                        Password Default
                    </h2>

                    <p class="mt-2 text-sm font-semibold leading-relaxed text-[#6B3E12]">
                        Gunakan reset password jika kasir lupa password. Password akan dikembalikan ke:
                    </p>

                    <div class="mt-4 rounded-2xl bg-[#F7F6F4] px-4 py-3">
                        <code class="text-sm font-black text-[#2B1A10]">rotimaroshikmah111</code>
                    </div>
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
                        {{ $cashier->status === 'aktif'
                            ? 'Kasir sedang aktif dan dapat login ke sistem.'
                            : 'Kasir sedang nonaktif dan tidak dapat login ke sistem.' }}
                    </p>

                    <button type="button" @click="openStatusModal()"
                        class="{{ $cashier->status === 'aktif'
                            ? 'bg-[#A92A35] text-white shadow-[#A92A35]/20'
                            : 'bg-[#F4B044] text-[#2B1A10] shadow-[#F4B044]/20' }}
                        mt-5 inline-flex h-12 w-full items-center justify-center rounded-2xl px-5 text-sm font-black shadow-lg transition hover:-translate-y-0.5 hover:shadow-xl">
                        {{ $cashier->status === 'aktif' ? 'Nonaktifkan Kasir' : 'Aktifkan Kasir' }}
                    </button>
                </div>

                <div
                    class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-6 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                    <h2 class="text-lg font-black text-[#2B1A10]">
                        Reset Password
                    </h2>

                    <p class="mt-2 text-sm font-semibold leading-relaxed text-[#6B3E12]">
                        Reset password kasir ke password default.
                    </p>

                    <button type="button" @click="openResetModal()"
                        class="mt-5 inline-flex h-12 w-full items-center justify-center rounded-2xl bg-[#6B3E12] px-5 text-sm font-black text-white shadow-lg shadow-[#6B3E12]/20 transition hover:-translate-y-0.5 hover:shadow-xl">
                        Reset Password
                    </button>
                </div>
            </div>
        </div>

        {{-- Status Modal --}}
        <template x-teleport="body">
            <div x-show="statusModalOpen" x-cloak x-transition.opacity
                class="fixed inset-0 z-[9998] flex items-center justify-center bg-[#1F444C]/55 px-4 backdrop-blur-md">
                <div @click.outside="closeStatusModal()"
                    class="w-full max-w-md overflow-hidden rounded-3xl border border-[#F4D3B0]/70 bg-white shadow-[0_30px_90px_-35px_rgba(31,68,76,0.8)]">

                    <div class="bg-[#1F444C] px-6 py-5 text-white">
                        <h2 class="text-lg font-black">
                            {{ $cashier->status === 'aktif' ? 'Nonaktifkan Kasir?' : 'Aktifkan Kasir?' }}
                        </h2>
                        <p class="mt-1 text-sm font-medium text-white/80">
                            {{ $cashier->status === 'aktif'
                                ? 'Kasir tidak bisa login sampai diaktifkan kembali.'
                                : 'Kasir akan dapat login kembali ke sistem.' }}
                        </p>
                    </div>

                    <div class="p-6">
                        <div class="flex justify-end gap-3">
                            <button type="button" @click="closeStatusModal()"
                                class="inline-flex h-11 items-center justify-center rounded-2xl border border-[#F4D3B0] bg-white px-5 text-sm font-black text-[#6B3E12] transition hover:bg-[#F7F6F4]">
                                Batal
                            </button>

                            <form method="POST" action="{{ route('admin.cashiers.update-status', $cashier) }}">
                                @csrf
                                @method('PATCH')

                                <button type="submit"
                                    class="{{ $cashier->status === 'aktif'
                                        ? 'bg-[#A92A35] text-white shadow-[#A92A35]/20'
                                        : 'bg-[#F4B044] text-[#2B1A10] shadow-[#F4B044]/20' }}
                                    inline-flex h-11 items-center justify-center rounded-2xl px-5 text-sm font-black shadow-lg transition hover:-translate-y-0.5 hover:shadow-xl">
                                    {{ $cashier->status === 'aktif' ? 'Ya, Nonaktifkan' : 'Ya, Aktifkan' }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        {{-- Reset Modal --}}
        <template x-teleport="body">
            <div x-show="resetModalOpen" x-cloak x-transition.opacity
                class="fixed inset-0 z-[9998] flex items-center justify-center bg-[#1F444C]/55 px-4 backdrop-blur-md">
                <div @click.outside="closeResetModal()"
                    class="w-full max-w-md overflow-hidden rounded-3xl border border-[#F4D3B0]/70 bg-white shadow-[0_30px_90px_-35px_rgba(31,68,76,0.8)]">

                    <div class="bg-[#6B3E12] px-6 py-5 text-white">
                        <h2 class="text-lg font-black">
                            Reset Password?
                        </h2>
                        <p class="mt-1 text-sm font-medium text-white/80">
                            Password kasir akan dikembalikan ke default.
                        </p>
                    </div>

                    <div class="p-6">
                        <div class="rounded-2xl bg-[#F7F6F4] px-4 py-3">
                            <code class="text-sm font-black text-[#2B1A10]">rotimaroshikmah111</code>
                        </div>

                        <div class="mt-6 flex justify-end gap-3">
                            <button type="button" @click="closeResetModal()"
                                class="inline-flex h-11 items-center justify-center rounded-2xl border border-[#F4D3B0] bg-white px-5 text-sm font-black text-[#6B3E12] transition hover:bg-[#F7F6F4]">
                                Batal
                            </button>

                            <form method="POST" action="{{ route('admin.cashiers.reset-password', $cashier) }}">
                                @csrf
                                @method('PATCH')

                                <button type="submit"
                                    class="inline-flex h-11 items-center justify-center rounded-2xl bg-[#6B3E12] px-5 text-sm font-black text-white shadow-lg shadow-[#6B3E12]/20 transition hover:-translate-y-0.5 hover:shadow-xl">
                                    Ya, Reset
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
@endsection

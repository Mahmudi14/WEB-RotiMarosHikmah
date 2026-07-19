@extends('layouts.master')

@section('page_title', 'Shift Kasir')

@section('content')
    <div class="flex flex-col gap-4" x-data="{
        copied: false,
        isReprinting: false,
    
        openedAt: @js($activeShift?->opened_at?->toIso8601String()),
        durationText: '-',
    
        async copyToken(token) {
            await navigator.clipboard.writeText(token);
            this.copied = true;
    
            setTimeout(() => {
                this.copied = false;
            }, 1800);
        },
    
        updateDuration() {
            if (!this.openedAt) {
                this.durationText = '-';
                return;
            }
    
            const start = new Date(this.openedAt);
            const now = new Date();
            const diff = Math.max(0, now - start);
    
            const hours = Math.floor(diff / 1000 / 60 / 60);
            const minutes = Math.floor((diff / 1000 / 60) % 60);
    
            this.durationText = `${hours} jam ${minutes} menit`;
        }
    }" x-init="updateDuration();
    setInterval(() => updateDuration(), 60000);">
        {{-- Header --}}
        <div class="shrink-0 overflow-hidden rounded-3xl bg-[#1F444C] p-6 text-white shadow-lg shadow-[#1F444C]/10">
            <div class="relative">
                <div class="absolute -right-10 -top-12 h-32 w-32 rounded-full bg-[#F4B044]/20"></div>
                <div class="absolute -bottom-16 -left-12 h-36 w-36 rounded-full bg-white/10"></div>

                <div
                    class="relative flex flex-col gap-5 min-[835px]:flex-row min-[835px]:items-center min-[835px]:justify-between">

                    <div class="min-w-0">
                        <p class="text-xs font-bold uppercase tracking-[0.28em] text-[#F4D3B0]">
                            Kasir / Shift
                        </p>

                        <h1 class="mt-2 text-2xl font-black tracking-tight text-white">
                            Shift Kasir
                        </h1>

                        <p class="mt-2 max-w-2xl text-sm font-medium leading-relaxed text-[#F7F6F4]/80">
                            Buka shift dan lihat terminal aktif.
                        </p>
                    </div>

                    @if ($activeShift)
                        <div
                            class="w-full rounded-2xl border border-white/10 bg-white/10 p-3 backdrop-blur-sm min-[835px]:w-[360px] min-[835px]:shrink-0">
                            <div class="flex items-center justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-xs font-black uppercase tracking-[0.18em] text-[#F4D3B0]">
                                        Token Terminal
                                    </p>

                                    <p class="mt-1 truncate font-mono text-sm font-black text-white">
                                        {{ $activeShift->terminal?->masked_bridge_token }}
                                    </p>

                                    <p class="mt-1 text-xs font-semibold text-white/65">
                                        Untuk Aplikasi Printer Bridge
                                    </p>
                                </div>

                                <button type="button" @click="copyToken(@js($activeShift->terminal?->bridge_token))"
                                    class="inline-flex h-11 shrink-0 items-center justify-center rounded-xl bg-[#F4B044] px-4 text-xs font-black text-[#2B1A10] shadow-lg shadow-[#F4B044]/20 transition hover:-translate-y-0.5 hover:shadow-xl">
                                    <span x-show="!copied">Salin</span>
                                    <span x-show="copied" x-cloak>Disalin</span>
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        @if (!$activeShift)
            {{-- Belum buka shift --}}
            <div class="grid gap-4 min-[835px]:grid-cols-[minmax(0,1fr)_360px] xl:grid-cols-[minmax(0,1fr)_380px]">
                <div
                    class="flex min-h-[320px] items-center justify-center rounded-3xl border border-[#F4D3B0]/70 bg-white p-6 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                    <div class="mx-auto max-w-md text-center">
                        <div
                            class="mx-auto flex h-20 w-20 items-center justify-center rounded-3xl bg-[#F4B044]/20 text-[#6B3E12]">
                            <svg class="h-10 w-10" fill="none" stroke="currentColor" stroke-width="2.2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 6v6l4 2M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>

                        <h2 class="mt-5 text-2xl font-black text-[#2B1A10]">
                            Kamu Belum Membuka Shift
                        </h2>

                        <p class="mt-3 text-sm font-semibold leading-relaxed text-[#6B3E12]">
                            Buka shift terlebih dahulu dengan memilih terminal dan mengisi kas awal.
                        </p>

                        <a href="{{ route('cashier.shifts.create') }}"
                            class="mt-6 inline-flex h-12 items-center justify-center rounded-2xl bg-[#1F444C] px-6 text-sm font-black text-white shadow-lg shadow-[#1F444C]/20 transition hover:-translate-y-0.5 hover:shadow-xl">
                            Buka Shift Sekarang
                        </a>
                    </div>
                </div>

                {{-- Kasir Aktif --}}
                <div
                    class="flex flex-col overflow-hidden rounded-3xl border border-[#F4D3B0]/70 bg-white shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                    <div class="shrink-0 border-b border-[#F4D3B0]/70 bg-[#F7F6F4] px-5 py-4">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <p class="text-xs font-black uppercase tracking-[0.22em] text-[#6B3E12]">
                                    Kasir Aktif
                                </p>

                                <h2 class="mt-1 text-lg font-black text-[#2B1A10]">
                                    Sedang Shift
                                </h2>
                            </div>

                            <span
                                class="inline-flex h-10 min-w-10 items-center justify-center rounded-2xl bg-[#F4B044]/20 px-3 text-sm font-black text-[#6B3E12]">
                                {{ $activeShifts->count() }}
                            </span>
                        </div>
                    </div>

                    <div class="max-h-[420px] overflow-y-auto">
                        @forelse ($activeShifts as $shift)
                            <div
                                class="border-b border-[#F4D3B0]/60 px-5 py-4 transition last:border-b-0 hover:bg-[#F7F6F4]">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-[#1F444C] text-sm font-black text-[#F4B044]">
                                        {{ strtoupper(substr($shift->cashier?->name ?? '-', 0, 1)) }}
                                    </div>

                                    <div class="min-w-0">
                                        <h3 class="truncate text-sm font-black text-[#2B1A10]">
                                            {{ $shift->cashier?->name ?? '-' }}
                                        </h3>

                                        <p class="mt-1 truncate text-xs font-bold text-[#6B3E12]">
                                            {{ $shift->terminal?->kode_terminal }} -
                                            {{ $shift->terminal?->nama_terminal }}
                                        </p>

                                        <p class="mt-1 text-xs font-semibold text-[#6B3E12]/75">
                                            Dibuka {{ $shift->opened_at->format('H:i') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div
                                class="flex min-h-[160px] items-center justify-center px-6 py-8 text-center xl:min-h-[220px]">
                                <div>
                                    <h3 class="text-base font-black text-[#2B1A10]">
                                        Belum Ada Kasir Aktif
                                    </h3>

                                    <p class="mt-2 text-sm font-semibold text-[#6B3E12]">
                                        Daftar akan muncul saat ada kasir membuka shift.
                                    </p>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        @else
            {{-- Shift aktif --}}
            <div class="grid gap-4 min-[835px]:grid-cols-[360px_minmax(0,1fr)] xl:grid-cols-[400px_minmax(0,1fr)]">
                {{-- Terminal & Token --}}
                <div
                    class="flex flex-col overflow-hidden rounded-3xl border border-[#F4D3B0]/70 bg-white shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                    <div class="shrink-0 border-b border-[#F4D3B0]/70 bg-[#F7F6F4] px-5 py-4">
                        <p class="text-xs font-black uppercase tracking-[0.22em] text-[#6B3E12]">
                            Terminal Aktif
                        </p>

                        <h2 class="mt-1 truncate text-lg font-black text-[#2B1A10]">
                            {{ $activeShift->terminal?->nama_terminal }}
                        </h2>
                    </div>

                    <div class="p-4">
                        <span class="inline-flex rounded-full bg-[#1F444C]/10 px-3 py-1 text-xs font-black text-[#1F444C]">
                            Shift Berjalan
                        </span>

                        <div class="mt-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-2">

                            <div class="rounded-2xl bg-[#F7F6F4] p-4">
                                <p class="text-xs font-black uppercase tracking-[0.18em] text-[#6B3E12]">
                                    Total Jam
                                </p>
                                <p class="mt-1 text-sm font-black text-[#2B1A10]" x-text="durationText">
                                    -
                                </p>
                            </div>
                        </div>

                        <div class="mt-3 rounded-2xl bg-[#F7F6F4] p-4">
                            <p class="text-xs font-black uppercase tracking-[0.18em] text-[#6B3E12]">
                                Jam Buka
                            </p>
                            <p class="mt-1 text-sm font-black text-[#2B1A10]">
                                {{ $activeShift->opened_at->format('d M Y, H:i') }}
                            </p>
                        </div>


                    </div>
                </div>

                {{-- Aksi & Kasir Aktif --}}
                <div
                    class="flex flex-col overflow-hidden rounded-3xl border border-[#F4D3B0]/70 bg-white shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                    <div class="shrink-0 border-b border-[#F4D3B0]/70 bg-[#F7F6F4] px-5 py-4">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="text-xs font-black uppercase tracking-[0.22em] text-[#6B3E12]">
                                    Shift Saya
                                </p>

                                <h2 class="mt-1 text-lg font-black text-[#2B1A10]">
                                    Aksi Shift
                                </h2>
                            </div>

                            <div class="flex flex-wrap gap-2">

                                <a href="{{ route('cashier.shifts.show', $activeShift) }}"
                                    class="inline-flex h-10 items-center justify-center rounded-xl bg-[#1F444C] px-4 text-xs font-black text-white shadow-sm transition active:scale-95">
                                    Detail Shift
                                </a>

                                <a href="{{ route('cashier.shifts.close-form', $activeShift) }}"
                                    class="inline-flex h-10 items-center justify-center rounded-xl bg-[#A92A35] px-4 text-xs font-black text-white shadow-sm transition active:scale-95">
                                    Tutup Shift
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="p-4">
                        <div class="mt-4 flex flex-col overflow-hidden rounded-3xl border border-[#F4D3B0]/70">
                            <div class="flex shrink-0 items-center justify-between bg-[#F7F6F4] px-5 py-3">
                                <p class="text-sm font-black text-[#2B1A10]">
                                    Kasir Sedang Aktif
                                </p>

                                <span class="text-xs font-black text-[#6B3E12]">
                                    {{ $activeShifts->count() }} aktif
                                </span>
                            </div>

                            <div class="max-h-[360px] overflow-y-auto">
                                @forelse ($activeShifts as $shift)
                                    @php
                                        $isMine = $activeShift && $activeShift->id === $shift->id;
                                    @endphp

                                    <div
                                        class="{{ $isMine ? 'bg-[#F4B044]/10' : 'bg-white' }} flex items-center justify-between gap-3 border-t border-[#F4D3B0]/60 px-5 py-3">
                                        <div class="min-w-0">
                                            <p class="truncate text-sm font-black text-[#2B1A10]">
                                                {{ $shift->cashier?->name ?? '-' }}

                                                @if ($isMine)
                                                    <span class="text-xs text-[#6B3E12]">(Shift Saya)</span>
                                                @endif
                                            </p>

                                            <p class="mt-1 truncate text-xs font-bold text-[#6B3E12]">
                                                {{ $shift->terminal?->kode_terminal }} -
                                                {{ $shift->terminal?->nama_terminal }}
                                            </p>
                                        </div>

                                        <p class="shrink-0 text-xs font-black text-[#6B3E12]">
                                            {{ $shift->opened_at->format('H:i') }}
                                        </p>
                                    </div>
                                @empty
                                    <div class="px-5 py-10 text-center">
                                        <p class="text-sm font-black text-[#2B1A10]">
                                            Belum ada kasir aktif
                                        </p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        @if (!$activeShift && isset($recentClosedShift) && $recentClosedShift)
            @php
                $latestPrintJob = $recentClosedShift->printJobs->first();
            @endphp

            <div
                class="overflow-hidden rounded-3xl border border-[#F4B044]/50 bg-[#F4B044]/10 p-5 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                <div class="flex flex-col gap-4 min-[835px]:flex-row min-[835px]:items-center min-[835px]:justify-between">
                    <div>
                        <p class="text-xs font-black uppercase tracking-[0.22em] text-[#6B3E12]">
                            Struk Tutup Shift
                        </p>

                        <h2 class="mt-1 text-lg font-black text-[#2B1A10]">
                            Shift berhasil ditutup
                        </h2>

                        <p class="mt-2 text-sm font-semibold text-[#6B3E12]">
                            Laporan shift untuk terminal
                            <span class="font-black text-[#2B1A10]">
                                {{ $recentClosedShift->terminal?->kode_terminal }}
                            </span>
                            sudah masuk antrean print.

                            @if ($latestPrintJob)
                                Status:
                                <span class="font-black text-[#1F444C]">
                                    {{ $latestPrintJob->status_label }}
                                </span>
                            @endif
                        </p>
                    </div>

                    <form method="POST" action="{{ route('cashier.shifts.reprint-report', $recentClosedShift) }}"
                        @submit="
        if (isReprinting) {
            $event.preventDefault();
            return;
        }

        isReprinting = true;
    ">
                        @csrf

                        <button type="submit" :disabled="isReprinting"
                            class="inline-flex h-11 min-w-[210px] items-center justify-center gap-2 rounded-2xl bg-[#1F444C] px-5 text-sm font-black text-white shadow-lg shadow-[#1F444C]/20 transition hover:-translate-y-0.5 hover:shadow-xl disabled:cursor-not-allowed disabled:opacity-60 disabled:hover:translate-y-0">

                            <svg x-show="isReprinting" x-cloak class="h-5 w-5 animate-spin" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4">
                                </circle>

                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z">
                                </path>
                            </svg>

                            <span x-show="!isReprinting">
                                Cetak Ulang Struk Shift
                            </span>

                            <span x-show="isReprinting" x-cloak>
                                Mengirim ke Printer...
                            </span>
                        </button>
                    </form>
                </div>
            </div>
        @endif
    </div>
@endsection

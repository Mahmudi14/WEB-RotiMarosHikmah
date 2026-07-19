@extends('layouts.master')

@section('page_title', 'Buka Shift')

@section('content')
    <div class="space-y-6">
        {{-- Header --}}
        <div class="overflow-hidden rounded-3xl bg-[#1F444C] p-6 text-white shadow-lg shadow-[#1F444C]/10">
            <div class="relative">
                <div class="absolute -right-10 -top-12 h-32 w-32 rounded-full bg-[#F4B044]/20"></div>
                <div class="absolute -bottom-16 -left-12 h-36 w-36 rounded-full bg-white/10"></div>

                <div class="relative flex flex-col gap-5 md:flex-row md:items-center md:justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.28em] text-[#F4D3B0]">
                            Kasir / Shift / Buka
                        </p>

                        <h1 class="mt-2 text-2xl font-black tracking-tight">
                            Buka Shift
                        </h1>

                        <p class="mt-2 max-w-2xl text-sm font-medium leading-relaxed text-[#F7F6F4]/80">
                            Pilih terminal kasir dan masukkan kas awal sebelum mulai transaksi.
                        </p>
                    </div>

                    <a href="{{ route('cashier.shifts.index') }}"
                        class="inline-flex items-center justify-center gap-2 rounded-2xl border border-white/15 bg-white/10 px-5 py-3 text-sm font-black text-white transition hover:bg-white/15">
                        Kembali
                    </a>
                </div>
            </div>
        </div>

        <div class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-6 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]"
            x-data="{
                terminalOpen: false,
                isSubmitting: false,
            
                selectedTerminal: @js((string) old('pos_terminal_id', '')),
                terminals: @js($terminals->mapWithKeys(fn($terminal) => [(string) $terminal->id => $terminal->kode_terminal . ' - ' . $terminal->nama_terminal])->toArray()),
                openingCash: @js((string) old('opening_cash', '')),
            
                get selectedTerminalLabel() {
                    return this.selectedTerminal ?
                        this.terminals[this.selectedTerminal] :
                        'Pilih Terminal Kasir'
                },
            
                formatCurrency(event) {
                    let value = event.target.value.replace(/[^0-9]/g, '');
                    this.openingCash = value ?
                        new Intl.NumberFormat('id-ID').format(value) :
                        '';
            
                    event.target.value = this.openingCash;
                }
            }">
            @if ($terminals->isEmpty())
                <div class="rounded-3xl border border-[#A92A35]/20 bg-[#A92A35]/5 p-5">
                    <h2 class="text-base font-black text-[#A92A35]">
                        Tidak ada terminal tersedia
                    </h2>

                    <p class="mt-2 text-sm font-semibold leading-relaxed text-[#6B3E12]">
                        Semua terminal sedang digunakan atau belum ada terminal aktif. Hubungi admin untuk mengaktifkan
                        terminal kasir.
                    </p>
                </div>
            @endif

            <form method="POST" action="{{ route('cashier.shifts.store') }}" class="mt-6 space-y-6"
                @submit="
                    if (isSubmitting) {
                        $event.preventDefault();
                        return;
                    }

        isSubmitting = true;
    ">
                @csrf

                <div class="grid gap-5 lg:grid-cols-2">
                    {{-- Terminal --}}
                    <div>
                        <label class="mb-2 block text-sm font-black text-[#2B1A10]">
                            Terminal Kasir
                        </label>

                        <input type="hidden" name="pos_terminal_id" x-model="selectedTerminal">

                        <div class="relative">
                            <button type="button" @click="terminalOpen = !terminalOpen"
                                class="flex h-12 w-full items-center justify-between rounded-2xl border border-[#F4D3B0] bg-[#F7F6F4] px-4 py-0 text-left text-sm font-bold text-[#2B1A10] shadow-sm transition hover:bg-white focus:border-[#F4B044] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#F4B044]/20">

                                <div class="flex min-w-0 items-center gap-3">
                                    <span
                                        class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-[#F4B044]/20 text-[#6B3E12]">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.2"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M6 20h12M8 20v-3.5A2.5 2.5 0 0110.5 14h3A2.5 2.5 0 0116 16.5V20M6.75 4h10.5A1.75 1.75 0 0119 5.75v5.5A1.75 1.75 0 0117.25 13H6.75A1.75 1.75 0 015 11.25v-5.5A1.75 1.75 0 016.75 4z" />
                                        </svg>
                                    </span>

                                    <span x-text="selectedTerminalLabel" class="truncate"
                                        :class="selectedTerminal ? 'text-[#2B1A10]' : 'text-[#6B3E12]/60'"></span>
                                </div>

                                <svg class="h-5 w-5 shrink-0 text-[#6B3E12] transition duration-200"
                                    :class="terminalOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                                    stroke-width="2.4" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <div x-show="terminalOpen" x-cloak @click.outside="terminalOpen = false"
                                x-transition:enter="transition ease-out duration-150"
                                x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-100"
                                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                x-transition:leave-end="opacity-0 scale-95 translate-y-1"
                                class="absolute z-40 mt-3 max-h-72 w-full overflow-y-auto rounded-2xl border border-[#F4D3B0]/80 bg-white shadow-[0_25px_70px_-35px_rgba(31,68,76,0.55)]">

                                <div class="p-2">
                                    @forelse ($terminals as $terminal)
                                        <button type="button"
                                            @click="selectedTerminal = '{{ $terminal->id }}'; terminalOpen = false"
                                            class="flex w-full items-center justify-between rounded-xl px-4 py-3 text-left text-sm font-bold transition"
                                            :class="selectedTerminal === '{{ $terminal->id }}'
                                                ?
                                                'bg-[#F4B044] text-[#2B1A10]' :
                                                'text-[#2B1A10] hover:bg-[#F4B044]/15'">

                                            <div>
                                                <p>{{ $terminal->kode_terminal }} - {{ $terminal->nama_terminal }}</p>
                                                <p class="mt-1 text-xs font-semibold opacity-70">
                                                    Tersedia
                                                </p>
                                            </div>

                                            <svg x-show="selectedTerminal === '{{ $terminal->id }}'" x-cloak class="h-5 w-5"
                                                fill="none" stroke="currentColor" stroke-width="2.8" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </button>
                                    @empty
                                        <div class="px-4 py-3 text-sm font-bold text-[#6B3E12]">
                                            Tidak ada terminal tersedia
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        @error('pos_terminal_id')
                            <p class="mt-2 text-sm font-bold text-[#A92A35]">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Kas Awal --}}
                    <div>
                        <label class="mb-2 block text-sm font-black text-[#2B1A10]">
                            Kas Awal
                        </label>

                        <div class="relative">
                            <span
                                class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-sm font-black text-[#6B3E12]">
                                Rp
                            </span>

                            <input type="text" name="opening_cash" x-init="$el.value = openingCash"
                                @input="formatCurrency($event)" placeholder="0"
                                class="block h-12 w-full rounded-2xl border border-[#F4D3B0] bg-[#F7F6F4] py-0 pl-12 pr-4 text-sm font-medium text-[#2B1A10] shadow-sm transition placeholder:text-[#6B3E12]/45 focus:border-[#F4B044] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#F4B044]/20">
                        </div>

                        @error('opening_cash')
                            <p class="mt-2 text-sm font-bold text-[#A92A35]">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Catatan --}}
                <div>
                    <label class="mb-2 block text-sm font-black text-[#2B1A10]">
                        Catatan Buka Shift
                        <span class="font-semibold text-[#6B3E12]/60">(Opsional)</span>
                    </label>

                    <textarea name="opening_note" rows="4"
                        class="block w-full rounded-2xl border border-[#F4D3B0] bg-[#F7F6F4] px-4 py-3 text-sm font-medium text-[#2B1A10] shadow-sm transition placeholder:text-[#6B3E12]/45 focus:border-[#F4B044] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#F4B044]/20">{{ old('opening_note') }}</textarea>

                    @error('opening_note')
                        <p class="mt-2 text-sm font-bold text-[#A92A35]">{{ $message }}</p>
                    @enderror
                </div>


                {{-- Action --}}
                <div class="flex flex-col-reverse gap-3 border-t border-[#F4D3B0]/70 pt-6 sm:flex-row sm:justify-end">
                    <a href="{{ route('cashier.shifts.index') }}"
                        class="inline-flex h-12 items-center justify-center rounded-2xl border border-[#F4D3B0] bg-white px-6 text-sm font-black text-[#6B3E12] transition hover:bg-[#F7F6F4]">
                        Batal
                    </a>

                    <button type="submit" :disabled="isSubmitting || @js($terminals->isEmpty())"
                        class="inline-flex h-12 min-w-[150px] items-center justify-center gap-2 rounded-2xl bg-[#1F444C] px-6 text-sm font-black text-white shadow-lg shadow-[#1F444C]/20 transition hover:-translate-y-0.5 hover:shadow-xl disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:translate-y-0">

                        <svg x-show="isSubmitting" x-cloak class="h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4">
                            </circle>

                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z">
                            </path>
                        </svg>

                        <span x-show="!isSubmitting">
                            Buka Shift
                        </span>

                        <span x-show="isSubmitting" x-cloak>
                            Membuka Shift...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@extends('layouts.master')

@section('page_title', 'Tambah Pengeluaran')

@section('content')
    <div class="space-y-6" x-data="{
        harga: @js((string) old('harga', '')),
    
        parseCurrency(value) {
            return Number(String(value).replace(/[^0-9]/g, '')) || 0;
        },
    
        formatCurrencyNumber(value) {
            return new Intl.NumberFormat('id-ID').format(Number(value) || 0);
        },
    
        formatCurrency(event) {
            let value = event.target.value.replace(/[^0-9]/g, '');
            this.harga = value ? this.formatCurrencyNumber(value) : '';
            event.target.value = this.harga;
        }
    }">
        {{-- Header --}}
        <div class="overflow-hidden rounded-3xl bg-[#1F444C] p-6 text-white shadow-lg shadow-[#1F444C]/10">
            <div class="relative">
                <div class="absolute -right-10 -top-12 h-32 w-32 rounded-full bg-[#F4B044]/20"></div>
                <div class="absolute -bottom-16 -left-12 h-36 w-36 rounded-full bg-white/10"></div>

                <div
                    class="relative flex flex-col gap-5 min-[835px]:flex-row min-[835px]:items-center min-[835px]:justify-between">
                    <div class="min-w-0">
                        <p class="text-xs font-bold uppercase tracking-[0.28em] text-[#F4D3B0]">
                            Kasir / Pengeluaran / Tambah
                        </p>

                        <h1 class="mt-2 text-2xl font-black tracking-tight">
                            Tambah Pengeluaran
                        </h1>

                        <p class="mt-2 max-w-2xl text-sm font-medium leading-relaxed text-[#F7F6F4]/80">
                            Catat barang atau kebutuhan yang dibeli menggunakan uang laci kasir.
                        </p>
                    </div>

                    <a href="{{ route('cashier.expenses.index') }}"
                        class="inline-flex h-12 items-center justify-center rounded-2xl border border-white/15 bg-white/10 px-5 text-sm font-black text-white transition hover:bg-white/15">
                        Kembali
                    </a>
                </div>
            </div>
        </div>

        <div
            class="grid gap-6
           min-[835px]:grid-cols-[minmax(0,1fr)_320px]
           min-[1024px]:grid-cols-[minmax(0,1fr)_360px]
           min-[1280px]:grid-cols-[minmax(0,1fr)_420px]">
            {{-- Form --}}
            <div class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-6 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                <div>
                    <p class="text-xs font-black uppercase tracking-[0.22em] text-[#6B3E12]">
                        Form Pengeluaran
                    </p>

                    <h2 class="mt-1 text-lg font-black text-[#2B1A10]">
                        Data Barang Dibeli
                    </h2>
                </div>

                <form method="POST" action="{{ route('cashier.expenses.store') }}" class="mt-6 space-y-6">
                    @csrf

                    {{-- Apa yang dibeli --}}
                    <div>
                        <label class="mb-2 block text-sm font-black text-[#2B1A10]">
                            Apa yang Dibeli
                            <span class="text-[#A92A35]">*</span>
                        </label>

                        <input type="text" name="nama_pengeluaran" value="{{ old('nama_pengeluaran') }}"
                            placeholder="Contoh: Plastik roti, bensin, tisu, galon air" required
                            class="block h-12 w-full rounded-2xl border border-[#F4D3B0] bg-[#F7F6F4] px-4 text-sm font-medium text-[#2B1A10] shadow-sm transition placeholder:text-[#6B3E12]/45 focus:border-[#F4B044] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#F4B044]/20">

                        @error('nama_pengeluaran')
                            <p class="mt-2 text-sm font-bold text-[#A92A35]">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Harga --}}
                    <div>
                        <label class="mb-2 block text-sm font-black text-[#2B1A10]">
                            Harga
                            <span class="text-[#A92A35]">*</span>
                        </label>

                        <div class="relative">
                            <span
                                class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-sm font-black text-[#6B3E12]">
                                Rp
                            </span>

                            <input type="text" name="harga" x-init="if (harga) {
                                harga = formatCurrencyNumber(parseCurrency(harga));
                                $el.value = harga;
                            }" @input="formatCurrency($event)"
                                placeholder="0" required
                                class="block h-12 w-full rounded-2xl border border-[#F4D3B0] bg-[#F7F6F4] py-0 pl-12 pr-4 text-sm font-medium text-[#2B1A10] shadow-sm transition placeholder:text-[#6B3E12]/45 focus:border-[#F4B044] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#F4B044]/20">
                        </div>

                        @error('harga')
                            <p class="mt-2 text-sm font-bold text-[#A92A35]">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Deskripsi --}}
                    <div>
                        <label class="mb-2 block text-sm font-black text-[#2B1A10]">
                            Deskripsi
                            <span class="font-semibold text-[#6B3E12]/60">(Opsional)</span>
                        </label>

                        <textarea name="deskripsi" rows="5" placeholder="Contoh: Dibeli untuk kebutuhan operasional shift hari ini."
                            class="block w-full rounded-2xl border border-[#F4D3B0] bg-[#F7F6F4] px-4 py-3 text-sm font-medium text-[#2B1A10] shadow-sm transition placeholder:text-[#6B3E12]/45 focus:border-[#F4B044] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#F4B044]/20">{{ old('deskripsi') }}</textarea>

                        @error('deskripsi')
                            <p class="mt-2 text-sm font-bold text-[#A92A35]">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Action --}}
                    <div class="grid gap-3 border-t border-[#F4D3B0]/70 pt-6 sm:grid-cols-2">
                        <a href="{{ route('cashier.expenses.index') }}"
                            class="inline-flex h-12 items-center justify-center rounded-2xl border border-[#F4D3B0] bg-white px-6 text-sm font-black text-[#6B3E12] transition hover:bg-[#F7F6F4]">
                            Batal
                        </a>

                        <button type="submit"
                            class="inline-flex h-12 items-center justify-center rounded-2xl bg-[#1F444C] px-6 text-sm font-black text-white shadow-lg shadow-[#1F444C]/15 transition hover:-translate-y-0.5 hover:shadow-xl">
                            Simpan Pengeluaran
                        </button>
                    </div>
                </form>
            </div>

            {{-- Shift Info --}}
            <div class="space-y-6">
                <div
                    class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-6 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                    <p class="text-xs font-black uppercase tracking-[0.22em] text-[#6B3E12]">
                        Shift Aktif
                    </p>

                    <h2 class="mt-1 truncate text-lg font-black text-[#2B1A10]">
                        {{ $activeShift->terminal?->nama_terminal }}
                    </h2>

                    <div class="mt-5 space-y-3">
                        <div class="rounded-2xl bg-[#F7F6F4] p-4">
                            <p class="text-xs font-black uppercase tracking-[0.18em] text-[#6B3E12]">
                                Kode Terminal
                            </p>

                            <p class="mt-1 text-sm font-black text-[#2B1A10]">
                                {{ $activeShift->terminal?->kode_terminal }}
                            </p>
                        </div>

                        <div class="rounded-2xl bg-[#F7F6F4] p-4">
                            <p class="text-xs font-black uppercase tracking-[0.18em] text-[#6B3E12]">
                                Jam Buka
                            </p>

                            <p class="mt-1 text-sm font-black text-[#2B1A10]">
                                {{ $activeShift->opened_at?->format('d M Y, H:i') }}
                            </p>
                        </div>

                        <div class="rounded-2xl bg-[#1F444C] p-4 text-white">
                            <p class="text-xs font-black uppercase tracking-[0.18em] text-[#F4D3B0]">
                                Dampak Pengeluaran
                            </p>

                            <p class="mt-2 text-sm font-semibold leading-relaxed text-white/75">
                                Harga yang dicatat akan menambah total pengeluaran shift dan mengurangi kas di sistem.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="rounded-3xl border border-[#F4D3B0]/70 bg-[#F7F6F4] p-6">
                    <p class="text-xs font-black uppercase tracking-[0.22em] text-[#6B3E12]">
                        Catatan
                    </p>

                    <p class="mt-2 text-sm font-semibold leading-relaxed text-[#6B3E12]">
                        Pengeluaran otomatis dicatat untuk shift aktif. Kasir tidak perlu memilih tanggal, terminal,
                        atau kategori.
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection

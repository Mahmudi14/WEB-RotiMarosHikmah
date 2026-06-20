@php
    $isEdit = isset($terminal);
    $showStatus = $showStatus ?? $isEdit;

    $statuses = $statuses ?? [
        'aktif' => 'Aktif',
        'nonaktif' => 'Nonaktif',
    ];

    $initialStatus = old('status', $isEdit ? $terminal->status : 'aktif');
@endphp

<div class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-6 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]"
    x-data="{
        statusOpen: false,
        selectedStatus: @js($initialStatus),
        statuses: @js($statuses),
    
        get selectedStatusLabel() {
            return this.statuses[this.selectedStatus] ?? 'Pilih Status'
        }
    }">
    <form method="POST" action="{{ $action }}" class="space-y-6">
        @csrf

        @if ($method !== 'POST')
            @method($method)
        @endif

        <div class="grid gap-5 lg:grid-cols-2">
            {{-- Kode Terminal --}}
            <div>
                <label class="mb-2 block text-sm font-black text-[#2B1A10]">
                    Kode Terminal
                    @if (!$isEdit)
                        <span class="font-semibold text-[#6B3E12]/60">(Opsional)</span>
                    @endif
                </label>

                <input type="text" name="kode_terminal"
                    value="{{ old('kode_terminal', $terminal->kode_terminal ?? '') }}"
                    placeholder="{{ $isEdit ? 'Contoh: TRM-001' : 'Kosongkan untuk dibuat otomatis' }}"
                    class="block h-12 w-full rounded-2xl border border-[#F4D3B0] bg-[#F7F6F4] px-4 py-0 text-sm font-medium uppercase text-[#2B1A10] shadow-sm transition placeholder:normal-case placeholder:text-[#6B3E12]/45 focus:border-[#F4B044] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#F4B044]/20">

                @error('kode_terminal')
                    <p class="mt-2 text-sm font-bold text-[#A92A35]">{{ $message }}</p>
                @enderror
            </div>

            {{-- Nama Terminal --}}
            <div>
                <label class="mb-2 block text-sm font-black text-[#2B1A10]">
                    Nama Terminal
                </label>

                <input type="text" name="nama_terminal"
                    value="{{ old('nama_terminal', $terminal->nama_terminal ?? '') }}" placeholder="Contoh: Kasir Depan"
                    class="block h-12 w-full rounded-2xl border border-[#F4D3B0] bg-[#F7F6F4] px-4 py-0 text-sm font-medium text-[#2B1A10] shadow-sm transition placeholder:text-[#6B3E12]/45 focus:border-[#F4B044] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#F4B044]/20">

                @error('nama_terminal')
                    <p class="mt-2 text-sm font-bold text-[#A92A35]">{{ $message }}</p>
                @enderror
            </div>


        </div>

        {{-- Deskripsi --}}
        <div>
            <label class="mb-2 block text-sm font-black text-[#2B1A10]">
                Deskripsi
                <span class="font-semibold text-[#6B3E12]/60">(Opsional)</span>
            </label>

            <textarea name="deskripsi" rows="4" placeholder="Contoh: Terminal kasir utama di area depan."
                class="block w-full rounded-2xl border border-[#F4D3B0] bg-[#F7F6F4] px-4 py-3 text-sm font-medium text-[#2B1A10] shadow-sm transition placeholder:text-[#6B3E12]/45 focus:border-[#F4B044] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#F4B044]/20">{{ old('deskripsi', $terminal->deskripsi ?? '') }}</textarea>

            @error('deskripsi')
                <p class="mt-2 text-sm font-bold text-[#A92A35]">{{ $message }}</p>
            @enderror
        </div>
        {{-- Status --}}
        @if ($showStatus)
            <div>
                <label class="mb-2 block text-sm font-black text-[#2B1A10]">
                    Status
                </label>

                <input type="hidden" name="status" x-model="selectedStatus">

                <div class="relative">
                    <button type="button" @click="statusOpen = !statusOpen"
                        class="flex h-12 w-full items-center justify-between rounded-2xl border border-[#F4D3B0] bg-[#F7F6F4] px-4 py-0 text-left text-sm font-bold text-[#2B1A10] shadow-sm transition hover:bg-white focus:border-[#F4B044] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#F4B044]/20">
                        <span x-text="selectedStatusLabel"></span>

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

                @error('status')
                    <p class="mt-2 text-sm font-bold text-[#A92A35]">{{ $message }}</p>
                @enderror
            </div>
        @endif
        {{-- Info --}}
        <div class="rounded-3xl border border-[#F4D3B0]/70 bg-[#F7F6F4] p-5">
            <div class="flex gap-4">
                <div
                    class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-[#F4B044]/20 text-[#6B3E12]">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2.3" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8.25h.008v.008H12V8.25z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>

                <div>
                    <h3 class="text-sm font-black text-[#2B1A10]">
                        Informasi Terminal
                    </h3>
                    <p class="mt-1 text-sm font-semibold leading-relaxed text-[#6B3E12]">
                        Token bridge dibuat otomatis oleh sistem. Token digunakan pada aplikasi Flutter Printer Bridge
                        agar terminal hanya mengambil print job miliknya.
                    </p>
                </div>
            </div>
        </div>

        {{-- Action --}}
        <div class="flex flex-col-reverse gap-3 border-t border-[#F4D3B0]/70 pt-6 sm:flex-row sm:justify-end">
            <a href="{{ route('admin.pos-terminals.index') }}"
                class="inline-flex h-12 items-center justify-center rounded-2xl border border-[#F4D3B0] bg-white px-6 text-sm font-black text-[#6B3E12] transition hover:bg-[#F7F6F4]">
                Batal
            </a>

            <button type="submit"
                class="inline-flex h-12 items-center justify-center rounded-2xl bg-[#1F444C] px-6 text-sm font-black text-white shadow-lg shadow-[#1F444C]/20 transition hover:-translate-y-0.5 hover:shadow-xl">
                {{ $submitLabel ?? 'Simpan' }}
            </button>
        </div>
    </form>
</div>

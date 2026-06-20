@php
    $isEdit = isset($cashier);
    $showStatus = $showStatus ?? $isEdit;

    $statuses = $statuses ?? [
        'aktif' => 'Aktif',
        'nonaktif' => 'Nonaktif',
    ];

    $initialStatus = old('status', $isEdit ? $cashier->status : 'aktif');
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
            {{-- Nama Kasir --}}
            <div>
                <label class="mb-2 block text-sm font-black text-[#2B1A10]">
                    Nama Kasir
                </label>

                <input type="text" name="name" value="{{ old('name', $cashier->name ?? '') }}"
                    placeholder="Contoh: Andi Kasir"
                    class="block h-12 w-full rounded-2xl border border-[#F4D3B0] bg-[#F7F6F4] px-4 py-0 text-sm font-medium text-[#2B1A10] shadow-sm transition placeholder:text-[#6B3E12]/45 focus:border-[#F4B044] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#F4B044]/20">

                @error('name')
                    <p class="mt-2 text-sm font-bold text-[#A92A35]">{{ $message }}</p>
                @enderror
            </div>

            {{-- Email --}}
            <div>
                <label class="mb-2 block text-sm font-black text-[#2B1A10]">
                    Email
                </label>

                <input type="email" name="email" value="{{ old('email', $cashier->email ?? '') }}"
                    placeholder="contoh@email.com"
                    class="block h-12 w-full rounded-2xl border border-[#F4D3B0] bg-[#F7F6F4] px-4 py-0 text-sm font-medium text-[#2B1A10] shadow-sm transition placeholder:text-[#6B3E12]/45 focus:border-[#F4B044] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#F4B044]/20">

                @error('email')
                    <p class="mt-2 text-sm font-bold text-[#A92A35]">{{ $message }}</p>
                @enderror
            </div>
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

        {{-- Action --}}
        <div class="flex flex-col-reverse gap-3 border-t border-[#F4D3B0]/70 pt-6 sm:flex-row sm:justify-end">
            <a href="{{ route('admin.cashiers.index') }}"
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

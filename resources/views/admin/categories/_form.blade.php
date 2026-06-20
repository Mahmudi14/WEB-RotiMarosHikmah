<form method="POST" action="{{ $action }}" class="space-y-5" x-data="{
    statusOpen: false,
    selectedStatus: @js(old('status', $category->status ?? 'aktif')),
    statuses: @js($statuses ?? ['aktif' => 'Aktif', 'nonaktif' => 'Nonaktif']),
    get selectedStatusLabel() {
        return this.statuses[this.selectedStatus] ?? 'Pilih Status'
    }
}">
    @csrf

    @if (($method ?? 'POST') !== 'POST')
        @method($method)
    @endif

    <div>
        <label class="mb-2 block text-sm font-black text-[#2B1A10]">
            Nama Kategori
        </label>

        <input type="text" name="nama_kategori" value="{{ old('nama_kategori', $category->nama_kategori ?? '') }}"
            placeholder="Contoh: Roti Manis"
            class="block h-12 w-full rounded-2xl border border-[#F4D3B0] bg-[#F7F6F4] px-4 py-0 text-sm font-medium text-[#2B1A10] shadow-sm transition placeholder:text-[#6B3E12]/45 focus:border-[#F4B044] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#F4B044]/20">

        @error('nama_kategori')
            <p class="mt-2 text-sm font-bold text-[#A92A35]">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-2 block text-sm font-black text-[#2B1A10]">
            Deskripsi
        </label>

        <textarea name="deskripsi" rows="5" placeholder="Tulis deskripsi kategori..."
            class="block w-full rounded-2xl border border-[#F4D3B0] bg-[#F7F6F4] px-4 py-3 text-sm font-medium text-[#2B1A10] shadow-sm transition placeholder:text-[#6B3E12]/45 focus:border-[#F4B044] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#F4B044]/20">{{ old('deskripsi', $category->deskripsi ?? '') }}</textarea>

        @error('deskripsi')
            <p class="mt-2 text-sm font-bold text-[#A92A35]">{{ $message }}</p>
        @enderror
    </div>

    @if ($showStatus ?? false)
        <div>
            <label class="mb-2 block text-sm font-black text-[#2B1A10]">
                Status
            </label>

            <input type="hidden" name="status" x-model="selectedStatus">

            <div class="relative">
                <button type="button" @click="statusOpen = !statusOpen"
                    class="flex h-12 w-full items-center justify-between rounded-2xl border border-[#F4D3B0] bg-[#F7F6F4] px-4 py-0 text-left text-sm font-bold text-[#2B1A10] shadow-sm transition hover:bg-white focus:border-[#F4B044] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#F4B044]/20">
                    <span x-text="selectedStatusLabel"></span>

                    <svg class="h-5 w-5 text-[#6B3E12] transition duration-200" :class="statusOpen ? 'rotate-180' : ''"
                        fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24">
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
                            <button type="button" @click="selectedStatus = '{{ $value }}'; statusOpen = false"
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

    <div class="flex flex-col-reverse gap-3 border-t border-[#F4D3B0]/70 pt-5 sm:flex-row sm:justify-end">
        <a href="{{ route('admin.categories.index') }}"
            class="inline-flex items-center justify-center rounded-2xl border border-[#F4D3B0] bg-white px-5 py-3 text-sm font-black text-[#6B3E12] transition hover:bg-[#F7F6F4]">
            Batal
        </a>

        <button type="submit"
            class="inline-flex items-center justify-center rounded-2xl bg-[#1F444C] px-5 py-3 text-sm font-black text-white shadow-lg shadow-[#1F444C]/20 transition hover:-translate-y-0.5 hover:shadow-xl">
            {{ $buttonText }}
        </button>
    </div>
</form>

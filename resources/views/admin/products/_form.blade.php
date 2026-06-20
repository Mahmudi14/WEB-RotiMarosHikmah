<form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="space-y-5" x-data="{
    categoryOpen: false,
    availabilityOpen: false,
    statusOpen: false,

    selectedCategory: @js((string) old('category_id', $product->category_id ?? '')),
    selectedAvailability: @js(old('status_ketersediaan', $product->status_ketersediaan ?? 'tersedia')),
    selectedStatus: @js(old('status', $product->status ?? 'aktif')),

    categories: @js(
    $categories
        ->mapWithKeys(
            fn($category) => [
                (string) $category->id => $category->nama_kategori . ($category->status === 'nonaktif' ? ' (Nonaktif)' : ''),
            ],
        )
        ->toArray(),
),

    availabilityStatuses: @js($availabilityStatuses ?? ['tersedia' => 'Tersedia', 'habis' => 'Habis']),
    productStatuses: @js($productStatuses ?? ['aktif' => 'Aktif', 'nonaktif' => 'Nonaktif']),

    imagePreview: @js(isset($product) && $product?->gambar ? asset('storage/' . $product->gambar) : null),

    get selectedCategoryLabel() {
        return this.selectedCategory ? this.categories[this.selectedCategory] : 'Pilih Kategori'
    },

    get selectedAvailabilityLabel() {
        return this.availabilityStatuses[this.selectedAvailability] ?? 'Pilih Ketersediaan'
    },

    get selectedStatusLabel() {
        return this.productStatuses[this.selectedStatus] ?? 'Pilih Status'
    },

    previewImage(event) {
        const file = event.target.files[0];

        if (!file) {
            return;
        }

        this.imagePreview = URL.createObjectURL(file);
    }
}">
    @csrf

    @if (($method ?? 'POST') !== 'POST')
        @method($method)
    @endif

    <div class="grid gap-5 lg:grid-cols-[minmax(0,1fr)_320px]">
        <div class="space-y-5">
            {{-- Nama Produk --}}
            <div>
                <label class="mb-2 block text-sm font-black text-[#2B1A10]">
                    Nama Produk
                </label>

                <input type="text" name="nama_produk" value="{{ old('nama_produk', $product->nama_produk ?? '') }}"
                    placeholder="Contoh: Roti Coklat"
                    class="block h-12 w-full rounded-2xl border border-[#F4D3B0] bg-[#F7F6F4] px-4 py-0 text-sm font-medium text-[#2B1A10] shadow-sm transition placeholder:text-[#6B3E12]/45 focus:border-[#F4B044] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#F4B044]/20">

                @error('nama_produk')
                    <p class="mt-2 text-sm font-bold text-[#A92A35]">{{ $message }}</p>
                @enderror
            </div>

            {{-- Kode Produk --}}
            <div>
                <label class="mb-2 block text-sm font-black text-[#2B1A10]">
                    Kode Produk
                </label>

                <input type="text" name="kode_produk" value="{{ old('kode_produk', $product->kode_produk ?? '') }}"
                    placeholder="Contoh: RT-001"
                    class="block h-12 w-full rounded-2xl border border-[#F4D3B0] bg-[#F7F6F4] px-4 py-0 text-sm font-medium text-[#2B1A10] shadow-sm transition placeholder:text-[#6B3E12]/45 focus:border-[#F4B044] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#F4B044]/20">

                @error('kode_produk')
                    <p class="mt-2 text-sm font-bold text-[#A92A35]">{{ $message }}</p>
                @enderror
            </div>

            {{-- Kategori --}}
            <div>
                <label class="mb-2 block text-sm font-black text-[#2B1A10]">
                    Kategori
                </label>

                <input type="hidden" name="category_id" x-model="selectedCategory">

                <div class="relative">
                    <button type="button" @click="categoryOpen = !categoryOpen"
                        class="flex h-12 w-full items-center justify-between rounded-2xl border border-[#F4D3B0] bg-[#F7F6F4] px-4 py-0 text-left text-sm font-bold text-[#2B1A10] shadow-sm transition hover:bg-white focus:border-[#F4B044] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#F4B044]/20">
                        <span x-text="selectedCategoryLabel" class="truncate"
                            :class="selectedCategory ? 'text-[#2B1A10]' : 'text-[#6B3E12]/60'"></span>

                        <svg class="h-5 w-5 text-[#6B3E12] transition duration-200"
                            :class="categoryOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                            stroke-width="2.4" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="categoryOpen" x-cloak @click.outside="categoryOpen = false"
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                        x-transition:leave-end="opacity-0 scale-95 translate-y-1"
                        class="absolute z-40 mt-3 max-h-72 w-full overflow-y-auto rounded-2xl border border-[#F4D3B0]/80 bg-white shadow-[0_25px_70px_-35px_rgba(31,68,76,0.55)]">

                        <div class="p-2">
                            @foreach ($categories as $category)
                                <button type="button"
                                    @click="selectedCategory = '{{ $category->id }}'; categoryOpen = false"
                                    class="flex w-full items-center justify-between rounded-xl px-4 py-3 text-left text-sm font-bold transition"
                                    :class="selectedCategory === '{{ $category->id }}'
                                        ?
                                        'bg-[#F4B044] text-[#2B1A10]' :
                                        'text-[#2B1A10] hover:bg-[#F4B044]/15'">

                                    <div>
                                        <p>{{ $category->nama_kategori }}</p>

                                        @if ($category->status === 'nonaktif')
                                            <p class="mt-0.5 text-xs font-medium opacity-70">
                                                Kategori nonaktif
                                            </p>
                                        @endif
                                    </div>

                                    <svg x-show="selectedCategory === '{{ $category->id }}'" x-cloak class="h-5 w-5"
                                        fill="none" stroke="currentColor" stroke-width="2.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>

                @error('category_id')
                    <p class="mt-2 text-sm font-bold text-[#A92A35]">{{ $message }}</p>
                @enderror
            </div>

            {{-- Harga Jual --}}
            {{-- Harga Jual --}}
            <div x-data="{
                rawPrice: @js((string) old('harga_jual', isset($product) && $product?->harga_jual !== null ? (int) $product->harga_jual : '')),
            
                formatRupiah(value) {
                    value = String(value ?? '').replace(/\D/g, '');
            
                    if (!value) {
                        return '';
                    }
            
                    return new Intl.NumberFormat('id-ID').format(Number(value));
                },
            
                updatePrice(event) {
                    this.rawPrice = event.target.value.replace(/\D/g, '');
                    event.target.value = this.formatRupiah(this.rawPrice);
                }
            }">
                <label class="mb-2 block text-sm font-black text-[#2B1A10]">
                    Harga Jual
                </label>

                <input type="hidden" name="harga_jual" x-model="rawPrice">

                <div class="relative">
                    <span
                        class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-sm font-black text-[#6B3E12]/70">
                        Rp
                    </span>

                    <input type="text" inputmode="numeric" x-init="$el.value = formatRupiah(rawPrice)" @input="updatePrice($event)"
                        placeholder="Contoh: 15.000"
                        class="block h-12 w-full rounded-2xl border border-[#F4D3B0] bg-[#F7F6F4] py-0 pl-12 pr-4 text-sm font-medium text-[#2B1A10] shadow-sm transition placeholder:text-[#6B3E12]/45 focus:border-[#F4B044] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#F4B044]/20">
                </div>

                @error('harga_jual')
                    <p class="mt-2 text-sm font-bold text-[#A92A35]">{{ $message }}</p>
                @enderror
            </div>

            {{-- Deskripsi --}}
            <div>
                <label class="mb-2 block text-sm font-black text-[#2B1A10]">
                    Deskripsi
                </label>

                <textarea name="deskripsi" rows="5" placeholder="Tulis deskripsi produk..."
                    class="block w-full rounded-2xl border border-[#F4D3B0] bg-[#F7F6F4] px-4 py-3 text-sm font-medium text-[#2B1A10] shadow-sm transition placeholder:text-[#6B3E12]/45 focus:border-[#F4B044] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#F4B044]/20">{{ old('deskripsi', $product->deskripsi ?? '') }}</textarea>

                @error('deskripsi')
                    <p class="mt-2 text-sm font-bold text-[#A92A35]">{{ $message }}</p>
                @enderror
            </div>

            @if ($showStatuses ?? false)
                <div class="grid gap-5 md:grid-cols-2">
                    {{-- Status Ketersediaan --}}
                    <div>
                        <label class="mb-2 block text-sm font-black text-[#2B1A10]">
                            Status Ketersediaan
                        </label>

                        <input type="hidden" name="status_ketersediaan" x-model="selectedAvailability">

                        <div class="relative">
                            <button type="button" @click="availabilityOpen = !availabilityOpen"
                                class="flex h-12 w-full items-center justify-between rounded-2xl border border-[#F4D3B0] bg-[#F7F6F4] px-4 py-0 text-left text-sm font-bold text-[#2B1A10] shadow-sm transition hover:bg-white focus:border-[#F4B044] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#F4B044]/20">
                                <span x-text="selectedAvailabilityLabel"></span>

                                <svg class="h-5 w-5 text-[#6B3E12] transition duration-200"
                                    :class="availabilityOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                                    stroke-width="2.4" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <div x-show="availabilityOpen" x-cloak @click.outside="availabilityOpen = false"
                                x-transition
                                class="absolute z-40 mt-3 w-full overflow-hidden rounded-2xl border border-[#F4D3B0]/80 bg-white shadow-[0_25px_70px_-35px_rgba(31,68,76,0.55)]">
                                <div class="p-2">
                                    @foreach ($availabilityStatuses as $value => $label)
                                        <button type="button"
                                            @click="selectedAvailability = '{{ $value }}'; availabilityOpen = false"
                                            class="flex w-full items-center justify-between rounded-xl px-4 py-3 text-left text-sm font-bold transition"
                                            :class="selectedAvailability === '{{ $value }}'
                                                ?
                                                'bg-[#F4B044] text-[#2B1A10]' :
                                                'text-[#2B1A10] hover:bg-[#F4B044]/15'">
                                            <span>{{ $label }}</span>

                                            <svg x-show="selectedAvailability === '{{ $value }}'" x-cloak
                                                class="h-5 w-5" fill="none" stroke="currentColor"
                                                stroke-width="2.8" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        @error('status_ketersediaan')
                            <p class="mt-2 text-sm font-bold text-[#A92A35]">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Status Produk --}}
                    <div>
                        <label class="mb-2 block text-sm font-black text-[#2B1A10]">
                            Status Produk
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

                            <div x-show="statusOpen" x-cloak @click.outside="statusOpen = false" x-transition
                                class="absolute z-40 mt-3 w-full overflow-hidden rounded-2xl border border-[#F4D3B0]/80 bg-white shadow-[0_25px_70px_-35px_rgba(31,68,76,0.55)]">
                                <div class="p-2">
                                    @foreach ($productStatuses as $value => $label)
                                        <button type="button"
                                            @click="selectedStatus = '{{ $value }}'; statusOpen = false"
                                            class="flex w-full items-center justify-between rounded-xl px-4 py-3 text-left text-sm font-bold transition"
                                            :class="selectedStatus === '{{ $value }}'
                                                ?
                                                'bg-[#F4B044] text-[#2B1A10]' :
                                                'text-[#2B1A10] hover:bg-[#F4B044]/15'">
                                            <span>{{ $label }}</span>

                                            <svg x-show="selectedStatus === '{{ $value }}'" x-cloak
                                                class="h-5 w-5" fill="none" stroke="currentColor"
                                                stroke-width="2.8" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M5 13l4 4L19 7" />
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
                </div>
            @endif
        </div>

        {{-- Gambar --}}
        <div>
            <label class="mb-2 block text-sm font-black text-[#2B1A10]">
                Gambar Produk
            </label>

            <div class="overflow-hidden rounded-3xl border border-[#F4D3B0]/70 bg-[#F7F6F4] p-4 shadow-sm">
                <div
                    class="flex aspect-square w-full items-center justify-center overflow-hidden rounded-2xl border border-[#F4D3B0]/70 bg-white">
                    <template x-if="imagePreview">
                        <img :src="imagePreview" alt="Preview produk" class="h-full w-full object-cover">
                    </template>

                    <template x-if="!imagePreview">
                        <div class="flex flex-col items-center justify-center px-6 text-center">
                            <div
                                class="flex h-16 w-16 items-center justify-center rounded-3xl bg-[#F4B044]/20 text-[#6B3E12]">
                                <svg class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3 16.5V6.75A2.25 2.25 0 015.25 4.5h13.5A2.25 2.25 0 0121 6.75v10.5A2.25 2.25 0 0118.75 19.5H5.25A2.25 2.25 0 013 17.25v-.75z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3 16.5l4.72-4.72a2.25 2.25 0 013.18 0l1.35 1.35 2.1-2.1a2.25 2.25 0 013.18 0L21 14.5" />
                                </svg>
                            </div>

                            <p class="mt-4 text-sm font-bold text-[#6B3E12]">
                                Belum ada gambar
                            </p>
                        </div>
                    </template>
                </div>

                <input type="file" name="gambar" accept="image/png,image/jpeg,image/jpg,image/webp"
                    @change="previewImage"
                    class="mt-4 block w-full rounded-2xl border border-[#F4D3B0] bg-white px-4 py-3 text-sm font-bold text-[#2B1A10] file:mr-4 file:rounded-xl file:border-0 file:bg-[#F4B044] file:px-4 file:py-2 file:text-sm file:font-black file:text-[#2B1A10] hover:file:bg-[#f7bd5f] focus:outline-none focus:ring-4 focus:ring-[#F4B044]/20">

                <p class="mt-3 text-xs font-semibold leading-relaxed text-[#6B3E12]">
                    Format: JPG, JPEG, PNG, atau WEBP. Maksimal 2MB.
                </p>

                @error('gambar')
                    <p class="mt-2 text-sm font-bold text-[#A92A35]">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <div class="flex flex-col-reverse gap-3 border-t border-[#F4D3B0]/70 pt-5 sm:flex-row sm:justify-end">
        <a href="{{ route('admin.products.index') }}"
            class="inline-flex items-center justify-center rounded-2xl border border-[#F4D3B0] bg-white px-5 py-3 text-sm font-black text-[#6B3E12] transition hover:bg-[#F7F6F4]">
            Batal
        </a>

        <button type="submit"
            class="inline-flex items-center justify-center rounded-2xl bg-[#1F444C] px-5 py-3 text-sm font-black text-white shadow-lg shadow-[#1F444C]/20 transition hover:-translate-y-0.5 hover:shadow-xl">
            {{ $buttonText }}
        </button>
    </div>
</form>

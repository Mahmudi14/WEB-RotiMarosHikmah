@php
    $isEdit = isset($promo);
    $showStatus = $showStatus ?? $isEdit;

    $selectedProductIds = collect(old('product_ids', $isEdit ? $promo->products->pluck('id')->toArray() : []))
        ->map(fn($id) => (string) $id)
        ->toArray();

    $initialDiscountType = old('tipe_diskon', $isEdit ? $promo->tipe_diskon : 'persentase');

    $initialDiscountValue = old(
        'nilai_diskon',
        $isEdit
            ? ($promo->tipe_diskon === 'nominal'
                ? (string) (int) $promo->nilai_diskon
                : rtrim(rtrim(number_format((float) $promo->nilai_diskon, 2, ',', '.'), '0'), ','))
            : '',
    );

    $initialScope = old('cakupan_promo', $isEdit ? $promo->cakupan_promo : 'semua_menu');
    $initialStatus = old('status', $isEdit ? $promo->status : 'aktif');
@endphp

<div class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-6 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]"
    x-data="{
        discountTypeOpen: false,
        scopeOpen: false,
        statusOpen: false,
    
        selectedDiscountType: @js($initialDiscountType),
        selectedScope: @js($initialScope),
        selectedStatus: @js($initialStatus),
    
        discountTypes: @js($discountTypes),
        scopes: @js($scopes),
        statuses: @js($statuses ?? []),
    
        discountValue: @js((string) $initialDiscountValue),
    
        get selectedDiscountTypeLabel() {
            return this.discountTypes[this.selectedDiscountType] ?? 'Pilih Tipe Diskon'
        },
    
        get selectedScopeLabel() {
            return this.scopes[this.selectedScope] ?? 'Pilih Cakupan Promo'
        },
    
        get selectedStatusLabel() {
            return this.statuses[this.selectedStatus] ?? 'Pilih Status'
        },
    
        formatNominal(value) {
            value = String(value ?? '').replace(/\D/g, '');
    
            if (!value) {
                return '';
            }
    
            return new Intl.NumberFormat('id-ID').format(Number(value));
        },
    
        updateDiscountValue(event) {
            if (this.selectedDiscountType === 'nominal') {
                this.discountValue = event.target.value.replace(/\D/g, '');
                event.target.value = this.formatNominal(this.discountValue);
                return;
            }
    
            let value = event.target.value.replace(/[^0-9,]/g, '');
    
            const parts = value.split(',');
            if (parts.length > 2) {
                value = parts[0] + ',' + parts.slice(1).join('');
            }
    
            this.discountValue = value;
            event.target.value = value;
        },
    
        setDiscountType(type) {
            this.selectedDiscountType = type;
            this.discountTypeOpen = false;
    
            this.$nextTick(() => {
                if (!this.$refs.discountInput) return;
    
                if (this.selectedDiscountType === 'nominal') {
                    this.$refs.discountInput.value = this.formatNominal(this.discountValue);
                } else {
                    this.$refs.discountInput.value = String(this.discountValue ?? '').replace(/\D/g, '');
                }
            });
        }
    }">
    <form method="POST" action="{{ $action }}" class="space-y-6">
        @csrf

        @if ($method !== 'POST')
            @method($method)
        @endif

        <div class="grid gap-5 lg:grid-cols-2">
            {{-- Nama Promo --}}
            <div>
                <label class="mb-2 block text-sm font-black text-[#2B1A10]">
                    Nama Promo
                </label>

                <input type="text" name="nama_promo" value="{{ old('nama_promo', $promo->nama_promo ?? '') }}"
                    placeholder="Contoh: Promo Jumat Berkah"
                    class="block h-12 w-full rounded-2xl border border-[#F4D3B0] bg-[#F7F6F4] px-4 py-0 text-sm font-medium text-[#2B1A10] shadow-sm transition placeholder:text-[#6B3E12]/45 focus:border-[#F4B044] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#F4B044]/20">

                @error('nama_promo')
                    <p class="mt-2 text-sm font-bold text-[#A92A35]">{{ $message }}</p>
                @enderror
            </div>

            {{-- Kode Promo --}}
            <div>
                <label class="mb-2 block text-sm font-black text-[#2B1A10]">
                    Kode Promo
                    <span class="font-semibold text-[#6B3E12]/60">(Opsional)</span>
                </label>

                <input type="text" name="kode_promo" value="{{ old('kode_promo', $promo->kode_promo ?? '') }}"
                    placeholder="Contoh: JUMAT10"
                    class="block h-12 w-full rounded-2xl border border-[#F4D3B0] bg-[#F7F6F4] px-4 py-0 text-sm font-medium uppercase text-[#2B1A10] shadow-sm transition placeholder:normal-case placeholder:text-[#6B3E12]/45 focus:border-[#F4B044] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#F4B044]/20">

                @error('kode_promo')
                    <p class="mt-2 text-sm font-bold text-[#A92A35]">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tipe Diskon --}}
            <div>
                <label class="mb-2 block text-sm font-black text-[#2B1A10]">
                    Tipe Diskon
                </label>

                <input type="hidden" name="tipe_diskon" x-model="selectedDiscountType">

                <div class="relative">
                    <button type="button" @click="discountTypeOpen = !discountTypeOpen"
                        class="flex h-12 w-full items-center justify-between rounded-2xl border border-[#F4D3B0] bg-[#F7F6F4] px-4 py-0 text-left text-sm font-bold text-[#2B1A10] shadow-sm transition hover:bg-white focus:border-[#F4B044] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#F4B044]/20">
                        <span x-text="selectedDiscountTypeLabel"></span>

                        <svg class="h-5 w-5 text-[#6B3E12] transition duration-200"
                            :class="discountTypeOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                            stroke-width="2.4" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="discountTypeOpen" x-cloak @click.outside="discountTypeOpen = false"
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                        x-transition:leave-end="opacity-0 scale-95 translate-y-1"
                        class="absolute z-40 mt-3 w-full overflow-hidden rounded-2xl border border-[#F4D3B0]/80 bg-white shadow-[0_25px_70px_-35px_rgba(31,68,76,0.55)]">
                        <div class="p-2">
                            @foreach ($discountTypes as $value => $label)
                                <button type="button" @click="setDiscountType('{{ $value }}')"
                                    class="flex w-full items-center justify-between rounded-xl px-4 py-3 text-left text-sm font-bold transition"
                                    :class="selectedDiscountType === '{{ $value }}'
                                        ?
                                        'bg-[#F4B044] text-[#2B1A10]' :
                                        'text-[#2B1A10] hover:bg-[#F4B044]/15'">
                                    <span>{{ $label }}</span>

                                    <svg x-show="selectedDiscountType === '{{ $value }}'" x-cloak class="h-5 w-5"
                                        fill="none" stroke="currentColor" stroke-width="2.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>

                @error('tipe_diskon')
                    <p class="mt-2 text-sm font-bold text-[#A92A35]">{{ $message }}</p>
                @enderror
            </div>

            {{-- Nilai Diskon --}}
            <div>
                <label class="mb-2 block text-sm font-black text-[#2B1A10]">
                    Nilai Diskon
                </label>

                <div class="relative">
                    <span x-show="selectedDiscountType === 'nominal'" x-cloak
                        class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-sm font-black text-[#6B3E12]/70">
                        Rp
                    </span>

                    <input type="text" name="nilai_diskon" x-ref="discountInput" x-init="$el.value = selectedDiscountType === 'nominal' ? formatNominal(discountValue) : discountValue"
                        @input="updateDiscountValue($event)"
                        :placeholder="selectedDiscountType === 'nominal' ? 'Contoh: 5.000' : 'Contoh: 10'"
                        class="block h-12 w-full rounded-2xl border border-[#F4D3B0] bg-[#F7F6F4] py-0 pl-12 pr-12 text-sm font-medium text-[#2B1A10] shadow-sm transition placeholder:text-[#6B3E12]/45 focus:border-[#F4B044] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#F4B044]/20">

                    <span x-show="selectedDiscountType === 'persentase'" x-cloak
                        class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-sm font-black text-[#6B3E12]/70">
                        %
                    </span>
                </div>

                <p class="mt-2 text-xs font-semibold text-[#6B3E12]/75">
                    Untuk persentase maksimal 100%. Diskon transaksi nanti tidak boleh melebihi harga produk.
                </p>

                @error('nilai_diskon')
                    <p class="mt-2 text-sm font-bold text-[#A92A35]">{{ $message }}</p>
                @enderror
            </div>



            {{-- Tanggal Mulai --}}
            <div>
                <label class="mb-2 block text-sm font-black text-[#2B1A10]">
                    Tanggal Mulai
                    <span class="font-semibold text-[#6B3E12]/60">(Opsional)</span>
                </label>

                <input type="date" name="tanggal_mulai"
                    value="{{ old('tanggal_mulai', isset($promo) && $promo->tanggal_mulai ? $promo->tanggal_mulai->format('Y-m-d') : '') }}"
                    class="block h-12 w-full rounded-2xl border border-[#F4D3B0] bg-[#F7F6F4] px-4 py-0 text-sm font-medium text-[#2B1A10] shadow-sm transition focus:border-[#F4B044] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#F4B044]/20">

                @error('tanggal_mulai')
                    <p class="mt-2 text-sm font-bold text-[#A92A35]">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tanggal Selesai --}}
            <div>
                <label class="mb-2 block text-sm font-black text-[#2B1A10]">
                    Tanggal Selesai
                    <span class="font-semibold text-[#6B3E12]/60">(Opsional)</span>
                </label>

                <input type="date" name="tanggal_selesai"
                    value="{{ old('tanggal_selesai', isset($promo) && $promo->tanggal_selesai ? $promo->tanggal_selesai->format('Y-m-d') : '') }}"
                    class="block h-12 w-full rounded-2xl border border-[#F4D3B0] bg-[#F7F6F4] px-4 py-0 text-sm font-medium text-[#2B1A10] shadow-sm transition focus:border-[#F4B044] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#F4B044]/20">

                @error('tanggal_selesai')
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

        {{-- Cakupan Promo --}}
        <div>
            <label class="mb-2 block text-sm font-black text-[#2B1A10]">
                Cakupan Promo
            </label>

            <input type="hidden" name="cakupan_promo" x-model="selectedScope">

            <div class="relative">
                <button type="button" @click="scopeOpen = !scopeOpen"
                    class="flex h-12 w-full items-center justify-between rounded-2xl border border-[#F4D3B0] bg-[#F7F6F4] px-4 py-0 text-left text-sm font-bold text-[#2B1A10] shadow-sm transition hover:bg-white focus:border-[#F4B044] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#F4B044]/20">
                    <span x-text="selectedScopeLabel"></span>

                    <svg class="h-5 w-5 text-[#6B3E12] transition duration-200" :class="scopeOpen ? 'rotate-180' : ''"
                        fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div x-show="scopeOpen" x-cloak @click.outside="scopeOpen = false"
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 translate-y-1"
                    class="absolute z-40 mt-3 w-full overflow-hidden rounded-2xl border border-[#F4D3B0]/80 bg-white shadow-[0_25px_70px_-35px_rgba(31,68,76,0.55)]">
                    <div class="p-2">
                        @foreach ($scopes as $value => $label)
                            <button type="button" @click="selectedScope = '{{ $value }}'; scopeOpen = false"
                                class="flex w-full items-center justify-between rounded-xl px-4 py-3 text-left text-sm font-bold transition"
                                :class="selectedScope === '{{ $value }}'
                                    ?
                                    'bg-[#F4B044] text-[#2B1A10]' :
                                    'text-[#2B1A10] hover:bg-[#F4B044]/15'">
                                <span>{{ $label }}</span>

                                <svg x-show="selectedScope === '{{ $value }}'" x-cloak class="h-5 w-5"
                                    fill="none" stroke="currentColor" stroke-width="2.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            @error('cakupan_promo')
                <p class="mt-2 text-sm font-bold text-[#A92A35]">{{ $message }}</p>
            @enderror
        </div>



        {{-- Produk Tertentu --}}
        <div x-show="selectedScope === 'menu_tertentu'" x-cloak
            class="rounded-3xl border border-[#F4D3B0]/70 bg-[#F7F6F4] p-5">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="text-base font-black text-[#2B1A10]">
                        Pilih Produk Promo
                    </h3>
                    <p class="mt-1 text-sm font-semibold text-[#6B3E12]">
                        Promo hanya berlaku untuk produk yang dipilih.
                    </p>
                </div>
            </div>

            @error('product_ids')
                <p class="mt-3 text-sm font-bold text-[#A92A35]">{{ $message }}</p>
            @enderror

            <div class="mt-4 grid max-h-80 gap-3 overflow-y-auto pr-2 sm:grid-cols-2 xl:grid-cols-3">
                @forelse ($products as $product)
                    <label
                        class="group relative flex cursor-pointer items-start gap-3 rounded-2xl border border-[#F4D3B0] bg-white p-4 transition hover:border-[#F4B044] hover:shadow-md">
                        <input type="checkbox" name="product_ids[]" value="{{ $product->id }}"
                            class="peer sr-only" @checked(in_array((string) $product->id, $selectedProductIds, true))>

                        <span
                            class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-lg border-2 border-[#F4D3B0] text-white transition peer-checked:border-[#1F444C] peer-checked:bg-[#1F444C]">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="3"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        </span>

                        <span class="min-w-0">
                            <span class="block truncate text-sm font-black text-[#2B1A10]">
                                {{ $product->nama_produk }}
                            </span>

                            <span class="mt-1 block text-xs font-bold text-[#6B3E12]">
                                {{ $product->harga_jual_formatted }}
                            </span>
                        </span>
                    </label>
                @empty
                    <div
                        class="col-span-full rounded-2xl border border-dashed border-[#F4D3B0] bg-white p-6 text-center">
                        <p class="text-sm font-bold text-[#6B3E12]">
                            Belum ada produk aktif yang bisa dipilih.
                        </p>
                    </div>
                @endforelse
            </div>

            @error('product_ids.*')
                <p class="mt-3 text-sm font-bold text-[#A92A35]">{{ $message }}</p>
            @enderror
        </div>

        {{-- Deskripsi --}}
        <div>
            <label class="mb-2 block text-sm font-black text-[#2B1A10]">
                Deskripsi
                <span class="font-semibold text-[#6B3E12]/60">(Opsional)</span>
            </label>

            <textarea name="deskripsi" rows="4" placeholder="Catatan atau keterangan promo..."
                class="block w-full rounded-2xl border border-[#F4D3B0] bg-[#F7F6F4] px-4 py-3 text-sm font-medium text-[#2B1A10] shadow-sm transition placeholder:text-[#6B3E12]/45 focus:border-[#F4B044] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#F4B044]/20">{{ old('deskripsi', $promo->deskripsi ?? '') }}</textarea>

            @error('deskripsi')
                <p class="mt-2 text-sm font-bold text-[#A92A35]">{{ $message }}</p>
            @enderror
        </div>

        {{-- Action --}}
        <div class="flex flex-col-reverse gap-3 border-t border-[#F4D3B0]/70 pt-6 sm:flex-row sm:justify-end">
            <a href="{{ route('admin.promos.index') }}"
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

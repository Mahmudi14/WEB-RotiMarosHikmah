@extends('layouts.master')

@section('page_title', 'POS Kasir')

@section('content')
    <audio id="cart-add-sound" src="{{ asset('sounds/add-cart.mp3') }}" preload="auto"></audio>
    <div class="relative" x-data="{
        products: @js($products),
        categories: @js($categories),
        promos: @js($promos),
        activeTax: @js($activeTax),
        search: '',
        categoryOpen: false,
        selectedCategory: '',
        cart: [],
        paymentMethod: 'tunai',
        paidAmount: '',
        paymentOpen: false,
        isSubmitting: false,
        cartToastOpen: false,
        cartToastMessage: '',
        cartToastTimeout: null,
        storageKey: @js('pos_cart_shift_' . $activeShift->id),
    
        get cartJson() {
            return JSON.stringify(this.cart.map(item => {
                return {
                    product_id: item.id,
                    qty: item.qty
                };
            }));
        },
        initDraft() {
            try {
                const raw = localStorage.getItem(this.storageKey);
    
                if (!raw) {
                    return;
                }
    
                const draft = JSON.parse(raw);
    
                if (!draft || !Array.isArray(draft.cart)) {
                    return;
                }
    
                this.cart = draft.cart
                    .map(savedItem => {
                        const product = this.products.find(product => Number(product.id) === Number(savedItem.id));
    
                        if (!product) {
                            return null;
                        }
    
                        return {
                            id: product.id,
                            nama_produk: product.nama_produk,
                            kode_produk: product.kode_produk,
                            harga_jual: Number(product.harga_jual),
                            qty: Math.max(1, Number(savedItem.qty) || 1)
                        };
                    })
                    .filter(Boolean);
    
                this.paymentMethod = ['tunai', 'qris', 'transfer'].includes(draft.paymentMethod) ?
                    draft.paymentMethod :
                    'tunai';
    
                this.paidAmount = draft.paidAmount || '';
            } catch (error) {
                localStorage.removeItem(this.storageKey);
            }
        },
    
        showCartToast(product) {
            this.cartToastMessage = `${product.nama_produk} ditambahkan ke keranjang`;
            this.cartToastOpen = true;
    
            if (this.cartToastTimeout) {
                clearTimeout(this.cartToastTimeout);
            }
    
            this.cartToastTimeout = setTimeout(() => {
                this.cartToastOpen = false;
            }, 2000);
        },
    
        persistDraft() {
            localStorage.setItem(this.storageKey, JSON.stringify({
                cart: this.cart,
                paymentMethod: this.paymentMethod,
                paidAmount: this.paidAmount,
            }));
        },
    
        clearDraft() {
            localStorage.removeItem(this.storageKey);
        },
    
        openPayment() {
            if (this.cart.length === 0) {
                return;
            }
    
            this.paymentOpen = true;
        },
    
        closePayment() {
            this.paymentOpen = false;
        },
    
        openPayment() {
            if (this.cart.length === 0) {
                return;
            }
    
            this.paymentOpen = true;
        },
    
        closePayment() {
            this.paymentOpen = false;
        },
    
    
        get selectedCategoryLabel() {
            if (!this.selectedCategory) {
                return 'Semua kategori';
            }
    
            const category = this.categories.find(category => String(category.id) === String(this.selectedCategory));
    
            return category ? category.nama_kategori : 'Semua kategori';
        },
    
        get filteredProducts() {
            const keyword = this.search.toLowerCase().trim();
    
            return this.products
                .filter(product => {
                    const matchSearch =
                        String(product.nama_produk || '').toLowerCase().includes(keyword) ||
                        String(product.kode_produk || '').toLowerCase().includes(keyword);
    
                    const matchCategory = !this.selectedCategory ||
                        String(product.category_id) === String(this.selectedCategory);
    
                    return matchSearch && matchCategory;
                })
                .sort((a, b) => {
                    const categoryOrderA = Number(a.category_order ?? 999999);
                    const categoryOrderB = Number(b.category_order ?? 999999);
    
                    if (categoryOrderA !== categoryOrderB) {
                        return categoryOrderA - categoryOrderB;
                    }
    
                    return String(a.nama_produk || '').localeCompare(
                        String(b.nama_produk || ''),
                        'id-ID'
                    );
                });
        },
    
        get groupedProducts() {
            const groups = this.filteredProducts.reduce((result, product) => {
                const categoryName = product.category_name || 'Tanpa Kategori';
                const categoryOrder = Number(product.category_order ?? 999999);
    
                let group = result.find(item => item.category === categoryName);
    
                if (!group) {
                    group = {
                        category: categoryName,
                        order: categoryOrder,
                        products: [],
                    };
    
                    result.push(group);
                }
    
                group.products.push(product);
    
                return result;
            }, []);
    
            return groups.sort((a, b) => {
                if (a.order !== b.order) {
                    return a.order - b.order;
                }
    
                return String(a.category || '').localeCompare(
                    String(b.category || ''),
                    'id-ID'
                );
            });
        },
    
        chooseCategory(categoryId) {
            this.selectedCategory = categoryId;
            this.categoryOpen = false;
        },
    
        resetFilter() {
            this.search = '';
            this.selectedCategory = '';
        },
    
        addToCart(product) {
            const existing = this.cart.find(item => item.id === product.id);
    
            if (existing) {
                existing.qty++;
            } else {
                this.cart.push({
                    id: product.id,
                    nama_produk: product.nama_produk,
                    kode_produk: product.kode_produk,
                    harga_jual: Number(product.harga_jual),
                    qty: 1
                });
            }
    
            this.persistDraft();
            this.playCartSound();
            this.showCartToast(product);
        },
    
        increment(item) {
            item.qty++;
            this.persistDraft();
        },
    
        decrement(item) {
            if (item.qty <= 1) {
                this.removeItem(item.id);
                return;
            }
    
            item.qty--;
            this.persistDraft();
        },
    
        removeItem(productId) {
            this.cart = this.cart.filter(item => item.id !== productId);
            this.persistDraft();
        },
    
        clearCart() {
            this.cart = [];
            this.paidAmount = '';
            this.clearDraft();
        },
    
        parseCurrency(value) {
            return Number(String(value).replace(/[^0-9]/g, '')) || 0;
        },
    
        formatCurrencyNumber(value) {
            return new Intl.NumberFormat('id-ID', {
                maximumFractionDigits: 0,
            }).format(Math.round(Number(value) || 0));
        },
    
        rupiah(value) {
            return 'Rp ' + this.formatCurrencyNumber(value);
        },
    
        formatPaidAmount(event) {
            let value = event.target.value.replace(/[^0-9]/g, '');
            this.paidAmount = value ? this.formatCurrencyNumber(value) : '';
            event.target.value = this.paidAmount;
            this.persistDraft();
        },
    
        get subtotal() {
            return this.cart.reduce((total, item) => {
                return total + (Number(item.harga_jual) * Number(item.qty));
            }, 0);
        },
    
        get bestPromo() {
            if (!this.promos || this.promos.length === 0 || this.subtotal <= 0) {
                return null;
            }
    
            let best = null;
            let bestDiscount = 0;
    
            this.promos.forEach((promo) => {
                let eligibleSubtotal = this.subtotal;
    
                if (promo.cakupan_promo === 'menu_tertentu') {
                    const productIds = promo.product_ids || [];
    
                    eligibleSubtotal = this.cart.reduce((total, item) => {
                        if (productIds.includes(Number(item.id))) {
                            return total + (Number(item.harga_jual) * Number(item.qty));
                        }
    
                        return total;
                    }, 0);
                }
    
                if (eligibleSubtotal <= 0) {
                    return;
                }
    
                let discount = promo.tipe_diskon === 'persentase' ?
                    eligibleSubtotal * (Number(promo.nilai_diskon) / 100) :
                    Number(promo.nilai_diskon);
    
                discount = Math.min(discount, eligibleSubtotal, this.subtotal);
    
                if (discount > bestDiscount) {
                    best = promo;
                    bestDiscount = discount;
                }
            });
    
            return best ? {
                    ...best,
                    discount: bestDiscount,
                } :
                null;
        },
    
        get totalDiscount() {
            return this.bestPromo ? Number(this.bestPromo.discount) : 0;
        },
    
        get taxableSubtotal() {
            return Math.max(0, this.subtotal - this.totalDiscount);
        },
    
        get taxTotal() {
            if (!this.activeTax || this.taxableSubtotal <= 0) {
                return 0;
            }
    
            return this.taxableSubtotal * (Number(this.activeTax.persentase) / 100);
        },
    
        get grandTotal() {
            return this.taxableSubtotal + this.taxTotal;
        },
    
        get cartCount() {
            return this.cart.reduce((total, item) => total + Number(item.qty), 0);
        },
    
        get paidAmountNumber() {
            return this.parseCurrency(this.paidAmount);
        },
    
        get changeAmount() {
            if (this.paymentMethod !== 'tunai') {
                return 0;
            }
    
            return Math.max(0, this.paidAmountNumber - this.grandTotal);
        },
    
        addPaidAmount(amount) {
            if (this.paymentMethod !== 'tunai') {
                return;
            }
    
            const current = Number(this.paidAmountNumber || 0);
            const next = current + Number(amount);
    
            this.paidAmount = new Intl.NumberFormat('id-ID').format(next);
    
            this.persistDraft();
        },
    
        clearPaidAmount() {
            if (this.paymentMethod !== 'tunai') {
                return;
            }
    
            this.paidAmount = '';
    
            this.persistDraft();
        },
    
        playCartSound() {
            const sound = document.getElementById('cart-add-sound');
    
            if (!sound) {
                return;
            }
    
            sound.currentTime = 0;
            sound.volume = 0.7;
    
            sound.play().catch(() => {
                // Audio bisa gagal jika browser/PWA belum mengizinkan suara.
            });
        },
    
        get canPay() {
            if (this.cart.length === 0) {
                return false;
            }
    
            if (this.paymentMethod === 'tunai') {
                return this.paidAmountNumber >= this.grandTotal;
            }
    
            return true;
        },
    }" x-init="if (@js(session('clear_pos_cart', false))) {
        clearDraft();
    }
    
    initDraft();">

        {{-- Cart Toast Notification --}}
        <div x-show="cartToastOpen" x-cloak x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="translate-y-2 opacity-0 scale-95"
            x-transition:enter-end="translate-y-0 opacity-100 scale-100" x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="translate-y-0 opacity-100 scale-100"
            x-transition:leave-end="translate-y-2 opacity-0 scale-95"
            class="fixed right-4 top-[5.75rem] z-[70] w-[calc(100%-2rem)] max-w-sm rounded-3xl border border-[#F4D3B0]/70 bg-white p-4 shadow-[0_25px_80px_-35px_rgba(31,68,76,0.65)]">
            <div class="flex items-start gap-3">
                <div class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-[#1F444C] text-white">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </div>

                <div class="min-w-0 flex-1">
                    <p class="text-sm font-black text-[#2B1A10]">
                        Berhasil ditambahkan
                    </p>

                    <p class="mt-1 truncate text-sm font-semibold text-[#6B3E12]" x-text="cartToastMessage"></p>
                </div>
            </div>
        </div>
        {{-- Product Area --}}
        <div class="space-y-6">
            {{-- Filter --}}
            <div
                class="relative z-20 overflow-visible rounded-3xl border border-[#F4D3B0]/70 bg-white p-5 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                <div class="grid gap-3 min-[1024px]:grid-cols-[minmax(0,1fr)_240px_auto] min-[1024px]:items-center">
                    {{-- Search --}}
                    <div class="relative">
                        <label class="sr-only" for="search">Cari produk</label>

                        <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-[#6B3E12]/60">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M21 21l-4.35-4.35m1.35-5.65a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span>

                        <input id="search" type="text" x-model="search" placeholder="Cari nama produk atau kode..."
                            class="block h-12 w-full rounded-2xl border border-[#F4D3B0] bg-[#F7F6F4] py-0 pl-12 pr-4 text-sm font-medium text-[#2B1A10] shadow-sm transition placeholder:text-[#6B3E12]/45 focus:border-[#F4B044] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#F4B044]/20">
                    </div>

                    {{-- Category Dropdown --}}
                    <div>
                        <div class="relative">
                            <button type="button" @click="categoryOpen = !categoryOpen"
                                class="flex h-12 w-full items-center justify-between rounded-2xl border border-[#F4D3B0] bg-[#F7F6F4] px-4 py-0 text-left text-sm font-bold text-[#2B1A10] shadow-sm transition hover:bg-white focus:border-[#F4B044] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#F4B044]/20">

                                <div class="flex min-w-0 items-center gap-3">
                                    <span
                                        class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-[#F4B044]/20 text-[#6B3E12]">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.2"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5" />
                                        </svg>
                                    </span>

                                    <span x-text="selectedCategoryLabel" class="truncate"
                                        :class="selectedCategory ? 'text-[#2B1A10]' : 'text-[#6B3E12]/60'"></span>
                                </div>

                                <svg class="h-5 w-5 shrink-0 text-[#6B3E12] transition duration-200"
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
                                class="absolute left-0 top-full z-10 mt-3 max-h-72 w-full overflow-y-auto rounded-2xl border border-[#F4D3B0]/80 bg-white shadow-[0_25px_70px_-35px_rgba(31,68,76,0.55)]">

                                <div class="p-2">
                                    <button type="button" @click="chooseCategory(''); categoryOpen = false"
                                        class="group flex w-full items-center justify-between rounded-xl px-4 py-3 text-left text-sm font-bold transition"
                                        :class="!selectedCategory
                                            ?
                                            'bg-[#F4B044] text-[#2B1A10]' :
                                            'text-[#2B1A10] hover:bg-[#F4B044]/15'">
                                        <span class="truncate">Semua Kategori</span>

                                        <svg x-show="!selectedCategory" x-cloak class="h-5 w-5 shrink-0" fill="none"
                                            stroke="currentColor" stroke-width="2.8" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </button>

                                    <template x-for="category in categories" :key="category.id">
                                        <button type="button" @click="chooseCategory(category.id); categoryOpen = false"
                                            class="group flex w-full items-center justify-between rounded-xl px-4 py-3 text-left text-sm font-bold transition"
                                            :class="selectedCategory == category.id ?
                                                'bg-[#F4B044] text-[#2B1A10]' :
                                                'text-[#2B1A10] hover:bg-[#F4B044]/15'">
                                            <span x-text="category.nama_kategori" class="truncate"></span>

                                            <svg x-show="selectedCategory == category.id" x-cloak class="h-5 w-5 shrink-0"
                                                fill="none" stroke="currentColor" stroke-width="2.8"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Reset --}}
                    <div class="flex shrink-0">
                        <button type="button" @click="resetFilter(); categoryOpen = false"
                            class="inline-flex h-12 w-full shrink-0 items-center justify-center gap-2 rounded-2xl bg-[#F4B044] px-5 py-0 text-sm font-black text-[#2B1A10] shadow-lg shadow-[#F4B044]/25 transition hover:-translate-y-0.5 hover:bg-[#f7bd5f] hover:shadow-xl active:translate-y-0 active:scale-95 focus:outline-none focus:ring-4 focus:ring-[#F4B044]/25 min-[1024px]:w-auto">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.3"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0013.803-3.7M7.977 14.652H2.985m18.03-5.304-3.181-3.183a8.25 8.25 0 00-13.803 3.7" />
                            </svg>
                            Reset
                        </button>
                    </div>
                </div>
            </div>

            <div
                class="grid gap-6 min-[1024px]:grid-cols-[minmax(0,1fr)_360px] min-[1280px]:grid-cols-[minmax(0,1fr)_420px]">
                {{-- Product List --}}
                <div
                    class="overflow-hidden rounded-3xl border border-[#F4D3B0]/70 bg-white shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                    <div class="border-b border-[#F4D3B0]/70 bg-[#F7F6F4] px-5 py-3">
                        <div class="flex flex-col gap-1.5 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="text-[11px] font-black uppercase leading-none tracking-[0.22em] text-[#6B3E12]">
                                    Produk
                                </p>

                                <h2 class="mt-0.5 text-base font-black leading-tight text-[#2B1A10]">
                                    Pilih Produk
                                </h2>
                            </div>

                            <span
                                class="inline-flex w-fit rounded-xl bg-[#F4B044]/20 px-3 py-1.5 text-xs font-black leading-none text-[#6B3E12]">
                                <span x-text="filteredProducts.length"></span>&nbsp;produk
                            </span>
                        </div>
                    </div>

                    <div class="px-5">
                        <template x-if="filteredProducts.length > 0">
                            <div class="space-y-7">
                                <template x-for="group in groupedProducts" :key="group.category">
                                    <section>
                                        {{-- Category Header --}}
                                        <div
                                            class="mb-3 flex items-center justify-between gap-3 border-b border-[#F4D3B0]/70 pb-2">
                                            <div class="min-w-0">
                                                <p class="truncate text-sm font-black uppercase tracking-[0.16em] text-[#1F444C]"
                                                    x-text="group.category">
                                                </p>
                                            </div>

                                            <span
                                                class="inline-flex shrink-0 rounded-xl bg-[#F4B044]/20 px-3 py-1 text-xs font-black text-[#6B3E12]">
                                                <span x-text="group.products.length"></span>&nbsp;produk
                                            </span>
                                        </div>

                                        {{-- Product Grid --}}
                                        <div
                                            class="grid grid-cols-2 gap-3 min-[1024px]:grid-cols-3 min-[1280px]:grid-cols-4">
                                            <template x-for="product in group.products" :key="product.id">
                                                <button type="button" @click="addToCart(product)"
                                                    class="group overflow-hidden rounded-2xl border border-[#F4D3B0]/70 bg-white text-left shadow-[0_18px_45px_-35px_rgba(31,68,76,0.55)] transition hover:-translate-y-0.5 hover:border-[#F4B044] hover:shadow-[0_24px_60px_-35px_rgba(31,68,76,0.7)]">
                                                    <div class="aspect-[5/3] overflow-hidden bg-[#F7F6F4]">
                                                        <template x-if="product.gambar_url">
                                                            <img :src="product.gambar_url" :alt="product.nama_produk"
                                                                class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
                                                        </template>

                                                        <template x-if="!product.gambar_url">
                                                            <div
                                                                class="flex h-full w-full items-center justify-center bg-[#1F444C]/10">
                                                                <div
                                                                    class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#1F444C] text-sm font-black text-[#F4B044]">
                                                                    RM
                                                                </div>
                                                            </div>
                                                        </template>
                                                    </div>

                                                    <div class="p-3">
                                                        <p class="truncate text-xs font-black text-[#2B1A10]"
                                                            x-text="product.nama_produk">
                                                        </p>

                                                        <p class="mt-1 truncate text-[11px] font-semibold text-[#6B3E12]"
                                                            x-text="product.category_name || '-'">
                                                        </p>

                                                        <div class="mt-3 space-y-2">
                                                            <p class="whitespace-nowrap text-sm font-black leading-none text-[#1F444C]"
                                                                x-text="rupiah(product.harga_jual)">
                                                            </p>

                                                            <span
                                                                class="inline-flex h-8 w-full items-center justify-center rounded-xl bg-[#F4B044] px-3 text-[11px] font-black text-[#2B1A10] shadow-md shadow-[#F4B044]/20 transition group-hover:-translate-y-0.5 group-hover:shadow-lg">
                                                                Tambah
                                                            </span>
                                                        </div>
                                                    </div>
                                                </button>
                                            </template>
                                        </div>
                                    </section>
                                </template>
                            </div>
                        </template>

                        <template x-if="filteredProducts.length === 0">
                            <div class="px-5 py-14 text-center">
                                <h3 class="text-base font-black text-[#2B1A10]">
                                    Produk Tidak Ditemukan
                                </h3>

                                <p class="mt-2 text-sm font-semibold text-[#6B3E12]">
                                    Coba ubah kata kunci pencarian atau kategori.
                                </p>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Cart Area --}}
                <div
                    class="relative z-0 space-y-6 pb-4 min-[1024px]:sticky min-[1024px]:top-[5.5rem] min-[1024px]:self-start min-[1280px]:top-4">
                    <div
                        class="relative z-0 flex h-[calc(100svh-6.5rem)] max-h-[calc(100svh-6.5rem)] min-h-[360px] flex-col overflow-hidden rounded-3xl border border-[#F4D3B0]/70 bg-white shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)] min-[1280px]:h-[calc(100dvh-8rem)] min-[1280px]:max-h-[calc(100dvh-8rem)]">

                        {{-- Cart Header --}}
                        <div class="shrink-0 border-b border-[#F4D3B0]/70 bg-[#F7F6F4] px-5 py-3">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <p
                                        class="text-[11px] font-black uppercase leading-none tracking-[0.22em] text-[#6B3E12]">
                                        Keranjang
                                    </p>

                                    <h2 class="mt-0.5 text-base font-black leading-tight text-[#2B1A10]">
                                        Pesanan
                                    </h2>
                                </div>

                                <button type="button" x-show="cart.length > 0" x-cloak @click="clearCart()"
                                    class="inline-flex h-9 shrink-0 items-center justify-center rounded-xl bg-[#A92A35]/10 px-3 text-xs font-black text-[#A92A35] transition hover:bg-[#A92A35] hover:text-white focus:outline-none focus:ring-4 focus:ring-[#A92A35]/20">
                                    Kosongkan
                                </button>
                            </div>
                        </div>

                        {{-- Cart Items Scroll Area --}}
                        <div class="min-h-0 flex-1 overflow-y-auto overscroll-contain">
                            <template x-if="cart.length > 0">
                                <div class="divide-y divide-[#F4D3B0]/60">
                                    <template x-for="item in cart" :key="item.id">
                                        <div class="px-4 py-2.5">
                                            <div class="flex items-start justify-between gap-3">
                                                <div class="min-w-0 flex-1">
                                                    <p class="truncate text-sm font-black leading-tight text-[#2B1A10]"
                                                        x-text="item.nama_produk"></p>

                                                    <p class="mt-0.5 text-[11px] font-bold leading-none text-[#6B3E12]"
                                                        x-text="rupiah(item.harga_jual)"></p>
                                                </div>

                                                <button type="button" @click="removeItem(item.id)"
                                                    class="inline-flex h-6 w-6 shrink-0 items-center justify-center rounded-lg bg-[#A92A35]/10 text-xs font-black leading-none text-[#A92A35] transition hover:bg-[#A92A35] hover:text-white">
                                                    ×
                                                </button>
                                            </div>

                                            <div class="mt-2 flex items-center justify-between gap-3">
                                                <div class="inline-flex items-center rounded-2xl bg-[#F7F6F4] p-1">
                                                    <button type="button" @click="decrement(item)"
                                                        class="inline-flex h-7 w-7 items-center justify-center rounded-xl bg-white text-sm font-black text-[#6B3E12] shadow-sm transition hover:bg-[#F4D3B0]/40">
                                                        -
                                                    </button>

                                                    <span
                                                        class="inline-flex h-7 min-w-8 items-center justify-center px-2 text-sm font-black text-[#2B1A10]"
                                                        x-text="item.qty"></span>

                                                    <button type="button" @click="increment(item)"
                                                        class="inline-flex h-7 w-7 items-center justify-center rounded-xl bg-white text-sm font-black text-[#6B3E12] shadow-sm transition hover:bg-[#F4D3B0]/40">
                                                        +
                                                    </button>
                                                </div>

                                                <p class="shrink-0 whitespace-nowrap text-sm font-black text-[#1F444C]"
                                                    x-text="rupiah(item.harga_jual * item.qty)"></p>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </template>

                            <template x-if="cart.length === 0">
                                <div class="flex min-h-[260px] items-center justify-center px-5 py-12 text-center">
                                    <div>
                                        <h3 class="text-base font-black text-[#2B1A10]">
                                            Keranjang Kosong
                                        </h3>

                                        <p class="mt-2 text-sm font-semibold text-[#6B3E12]">
                                            Pilih produk untuk mulai transaksi.
                                        </p>
                                    </div>
                                </div>
                            </template>
                        </div>

                        {{-- Cart Footer --}}
                        <div
                            class="shrink-0 border-t border-[#F4D3B0]/70 bg-[#F7F6F4] px-4 pb-[calc(1rem+env(safe-area-inset-bottom))] pt-4">
                            <div class="rounded-2xl bg-[#1F444C] px-4 py-2.5 text-white">
                                <div class="grid grid-cols-[minmax(0,1fr)_auto] items-center gap-3">
                                    <div class="min-w-0">
                                        <p
                                            class="text-[9px] font-black uppercase leading-none tracking-[0.2em] text-[#F4D3B0]">
                                            Total Bayar
                                        </p>

                                        <p class="mt-1 text-xs font-semibold leading-none text-white/70">
                                            <span x-text="cartCount"></span> item
                                        </p>
                                    </div>

                                    <p class="shrink-0 whitespace-nowrap text-right text-lg font-black leading-none text-[#F4B044] min-[1280px]:text-xl"
                                        x-text="rupiah(grandTotal)">
                                    </p>
                                </div>
                            </div>

                            <button type="button" @click="openPayment()" :disabled="cart.length === 0"
                                class="mt-3 inline-flex h-11 w-full items-center justify-center rounded-2xl px-6 text-sm font-black shadow-lg transition"
                                :class="cart.length > 0 ?
                                    'bg-[#F4B044] text-[#2B1A10] shadow-[#F4B044]/20 hover:-translate-y-0.5 hover:shadow-xl active:scale-95' :
                                    'cursor-not-allowed bg-[#F4D3B0]/50 text-[#6B3E12]/50 shadow-none'">
                                Bayar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- Payment Modal --}}
        <template x-teleport="body">
            <div x-show="paymentOpen" x-cloak
                class="fixed inset-0 z-[9998] flex items-center justify-center bg-[#1F444C]/55 px-4 py-4 backdrop-blur-md"
                x-transition.opacity @keydown.escape.window="closePayment()">

                <div @click.outside="closePayment()" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-3"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 translate-y-3"
                    class="w-full overflow-hidden rounded-3xl bg-white shadow-2xl shadow-[#1F444C]/25"
                    style="max-width: min(900px, calc(100vw - 32px)); max-height: calc(100vh - 32px);">

                    {{-- Header --}}
                    <div class="bg-[#1F444C] px-5 py-3 text-white">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-[11px] font-black uppercase tracking-[0.22em] text-[#F4D3B0]">
                                    Pembayaran
                                </p>

                                <h2 class="text-lg font-black leading-tight">
                                    Ringkasan Bayar
                                </h2>
                            </div>

                            <button type="button" @click="closePayment()"
                                class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-2xl bg-white/10 text-xl font-black text-white transition hover:bg-white/15">
                                ×
                            </button>
                        </div>
                    </div>

                    {{-- Body --}}
                    <div class="p-4">
                        <div class="grid grid-cols-[minmax(0,1fr)_minmax(280px,0.85fr)] gap-4">
                            {{-- Kolom Kiri --}}
                            <div class="min-w-0">
                                {{-- Summary --}}
                                <div class="grid grid-cols-2 gap-3">
                                    <div class="rounded-2xl bg-[#F7F6F4] p-3">
                                        <div class="flex items-center justify-between gap-3">
                                            <p class="text-sm font-bold text-[#6B3E12]">
                                                Subtotal
                                            </p>

                                            <p class="shrink-0 whitespace-nowrap text-sm font-black text-[#2B1A10]"
                                                x-text="rupiah(subtotal)">
                                            </p>
                                        </div>
                                    </div>

                                    <div class="rounded-2xl bg-[#F7F6F4] p-3">
                                        <div class="flex items-center justify-between gap-3">
                                            <div class="min-w-0">
                                                <p class="text-sm font-bold text-[#6B3E12]">
                                                    Diskon Promo
                                                </p>

                                                <p class="mt-0.5 truncate text-xs font-semibold text-[#6B3E12]/70"
                                                    x-text="bestPromo ? bestPromo.nama_promo : 'Tidak ada promo'">
                                                </p>
                                            </div>

                                            <p class="shrink-0 whitespace-nowrap text-sm font-black text-[#A92A35]">
                                                - <span x-text="rupiah(totalDiscount)"></span>
                                            </p>
                                        </div>
                                    </div>

                                    <div class="rounded-2xl bg-[#F7F6F4] p-3">
                                        <div class="flex items-center justify-between gap-3">
                                            <div class="min-w-0">
                                                <p class="text-sm font-bold text-[#6B3E12]">
                                                    Pajak
                                                </p>

                                                <p class="mt-0.5 truncate text-xs font-semibold text-[#6B3E12]/70"
                                                    x-text="activeTax ? `${activeTax.nama_pajak} (${activeTax.persentase}%)` : 'Tidak ada pajak aktif'">
                                                </p>
                                            </div>

                                            <p class="shrink-0 whitespace-nowrap text-sm font-black text-[#2B1A10]"
                                                x-text="rupiah(taxTotal)">
                                            </p>
                                        </div>
                                    </div>

                                    <div class="rounded-2xl bg-[#1F444C] p-3 text-white">
                                        <div class="flex h-full items-center justify-between gap-3">
                                            <p class="text-sm font-black leading-tight">
                                                Total Bayar
                                            </p>

                                            <p class="shrink-0 whitespace-nowrap text-lg font-black text-[#F4B044]"
                                                x-text="rupiah(grandTotal)">
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Payment Method --}}
                                <div class="mt-3">
                                    <label class="mb-2 block text-sm font-black text-[#2B1A10]">
                                        Metode Pembayaran
                                    </label>

                                    <div class="grid grid-cols-3 gap-2">
                                        <button type="button" @click="paymentMethod = 'tunai'; persistDraft()"
                                            class="h-12 rounded-2xl text-sm font-black transition"
                                            :class="paymentMethod === 'tunai'
                                                ?
                                                'bg-[#1F444C] text-white' :
                                                'bg-[#F7F6F4] text-[#6B3E12]'">
                                            Tunai
                                        </button>

                                        <button type="button"
                                            @click="paymentMethod = 'qris'; paidAmount = ''; persistDraft()"
                                            class="h-12 rounded-2xl text-sm font-black transition"
                                            :class="paymentMethod === 'qris'
                                                ?
                                                'bg-[#1F444C] text-white' :
                                                'bg-[#F7F6F4] text-[#6B3E12]'">
                                            QRIS
                                        </button>

                                        <button type="button"
                                            @click="paymentMethod = 'transfer'; paidAmount = ''; persistDraft()"
                                            class="h-12 rounded-2xl text-sm font-black transition"
                                            :class="paymentMethod === 'transfer'
                                                ?
                                                'bg-[#1F444C] text-white' :
                                                'bg-[#F7F6F4] text-[#6B3E12]'">
                                            Transfer
                                        </button>
                                    </div>
                                </div>

                                {{-- Cash Summary --}}
                                <div class="mt-3 grid grid-cols-2 gap-3">
                                    <div class="rounded-2xl bg-[#F7F6F4] p-3">
                                        <p class="text-[11px] font-black uppercase tracking-[0.18em] text-[#6B3E12]">
                                            Uang Diterima
                                        </p>

                                        <p class="mt-1.5 whitespace-nowrap text-base font-black text-[#2B1A10]">
                                            <span
                                                x-text="paymentMethod === 'tunai' ? `Rp ${paidAmount || '0'}` : rupiah(grandTotal)">
                                            </span>
                                        </p>
                                    </div>

                                    <div class="rounded-2xl bg-[#F7F6F4] p-3">
                                        <p class="text-[11px] font-black uppercase tracking-[0.18em] text-[#6B3E12]">
                                            Kembalian
                                        </p>

                                        <p class="mt-1.5 whitespace-nowrap text-base font-black text-[#1F444C]"
                                            x-text="paymentMethod === 'tunai' ? rupiah(changeAmount) : rupiah(0)">
                                        </p>
                                    </div>
                                </div>
                            </div>

                            {{-- Kolom Kanan --}}
                            <div class="min-w-0 border-l border-[#F4D3B0]/70 pl-4">
                                <label class="mb-2 block text-sm font-black text-[#2B1A10]">
                                    Input Nominal
                                </label>

                                {{-- Display nominal, tidak perlu diklik --}}
                                <div class="flex h-11 items-center rounded-2xl border px-4 transition"
                                    :class="paymentMethod === 'tunai'
                                        ?
                                        'border-[#F4B044] bg-white text-[#2B1A10]' :
                                        'border-[#E8DDD0] bg-[#F7F6F4] text-[#6B3E12]/40'">
                                    <span class="mr-3 text-sm font-black">
                                        Rp
                                    </span>

                                    <span class="text-sm font-black"
                                        x-text="paymentMethod === 'tunai' ? (paidAmount || '0') : rupiah(grandTotal).replace('Rp', '').trim()">
                                    </span>
                                </div>

                                <p class="mt-3 text-sm font-black text-[#2B1A10]">
                                    Nominal Cepat
                                </p>

                                <div class="mt-2 grid grid-cols-2 gap-2">
                                    <button type="button" @click="addPaidAmount(1000)"
                                        :disabled="paymentMethod !== 'tunai'"
                                        class="h-11 rounded-2xl text-sm font-black transition active:scale-95 disabled:cursor-not-allowed disabled:opacity-40"
                                        :class="paymentMethod === 'tunai'
                                            ?
                                            'bg-[#E7EFE4] text-[#2B1A10] hover:-translate-y-0.5 hover:shadow-md' :
                                            'bg-[#F7F6F4] text-[#6B3E12]/40'">
                                        1.000
                                    </button>

                                    <button type="button" @click="addPaidAmount(2000)"
                                        :disabled="paymentMethod !== 'tunai'"
                                        class="h-11 rounded-2xl text-sm font-black transition active:scale-95 disabled:cursor-not-allowed disabled:opacity-40"
                                        :class="paymentMethod === 'tunai'
                                            ?
                                            'bg-[#ECECEC] text-[#2B1A10] hover:-translate-y-0.5 hover:shadow-md' :
                                            'bg-[#F7F6F4] text-[#6B3E12]/40'">
                                        2.000
                                    </button>

                                    <button type="button" @click="addPaidAmount(5000)"
                                        :disabled="paymentMethod !== 'tunai'"
                                        class="h-11 rounded-2xl text-sm font-black transition active:scale-95 disabled:cursor-not-allowed disabled:opacity-40"
                                        :class="paymentMethod === 'tunai'
                                            ?
                                            'bg-[#E8D8C4] text-[#5A3512] hover:-translate-y-0.5 hover:shadow-md' :
                                            'bg-[#F7F6F4] text-[#6B3E12]/40'">
                                        5.000
                                    </button>

                                    <button type="button" @click="addPaidAmount(10000)"
                                        :disabled="paymentMethod !== 'tunai'"
                                        class="h-11 rounded-2xl text-sm font-black transition active:scale-95 disabled:cursor-not-allowed disabled:opacity-40"
                                        :class="paymentMethod === 'tunai'
                                            ?
                                            'bg-[#E6DDF0] text-[#563A75] hover:-translate-y-0.5 hover:shadow-md' :
                                            'bg-[#F7F6F4] text-[#6B3E12]/40'">
                                        10.000
                                    </button>

                                    <button type="button" @click="addPaidAmount(20000)"
                                        :disabled="paymentMethod !== 'tunai'"
                                        class="h-11 rounded-2xl text-sm font-black transition active:scale-95 disabled:cursor-not-allowed disabled:opacity-40"
                                        :class="paymentMethod === 'tunai'
                                            ?
                                            'bg-[#DDF2E4] text-[#176B3A] hover:-translate-y-0.5 hover:shadow-md' :
                                            'bg-[#F7F6F4] text-[#6B3E12]/40'">
                                        20.000
                                    </button>

                                    <button type="button" @click="addPaidAmount(50000)"
                                        :disabled="paymentMethod !== 'tunai'"
                                        class="h-11 rounded-2xl text-sm font-black transition active:scale-95 disabled:cursor-not-allowed disabled:opacity-40"
                                        :class="paymentMethod === 'tunai'
                                            ?
                                            'bg-[#DCEFFC] text-[#155A8A] hover:-translate-y-0.5 hover:shadow-md' :
                                            'bg-[#F7F6F4] text-[#6B3E12]/40'">
                                        50.000
                                    </button>

                                    <button type="button" @click="addPaidAmount(100000)"
                                        :disabled="paymentMethod !== 'tunai'"
                                        class="h-11 rounded-2xl text-sm font-black transition active:scale-95 disabled:cursor-not-allowed disabled:opacity-40"
                                        :class="paymentMethod === 'tunai'
                                            ?
                                            'bg-[#F8D7DA] text-[#A92A35] hover:-translate-y-0.5 hover:shadow-md' :
                                            'bg-[#F7F6F4] text-[#6B3E12]/40'">
                                        100.000
                                    </button>

                                    <button type="button" @click="clearPaidAmount()"
                                        :disabled="paymentMethod !== 'tunai'"
                                        class="h-11 rounded-2xl text-sm font-black transition active:scale-95 disabled:cursor-not-allowed disabled:opacity-40"
                                        :class="paymentMethod === 'tunai'
                                            ?
                                            'bg-[#FBE4E6] text-[#A92A35] hover:-translate-y-0.5 hover:shadow-md' :
                                            'bg-[#F7F6F4] text-[#6B3E12]/40'">
                                        Hapus
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Action --}}
                        <div class="mt-4 border-t border-[#F4D3B0]/70 pt-3">
                            <form method="POST" action="{{ route('cashier.pos.sales.store') }}"
                                @submit="
                            if (!canPay) {
                                $event.preventDefault();
                                return;
                            }

                            isSubmitting = true;
                        ">
                                @csrf

                                <input type="hidden" name="cart_json" :value="cartJson">
                                <input type="hidden" name="payment_method" :value="paymentMethod">
                                <input type="hidden" name="paid_amount"
                                    :value="paymentMethod === 'tunai' ? paidAmountNumber : grandTotal">

                                <button type="submit" :disabled="!canPay || isSubmitting"
                                    class="inline-flex h-11 w-full items-center justify-center gap-2 rounded-2xl px-5 text-sm font-black shadow-lg transition"
                                    :class="(!canPay || isSubmitting) ?
                                    'cursor-not-allowed bg-[#F4D3B0]/50 text-[#6B3E12]/50 shadow-none' :
                                    'bg-[#1F444C] text-[#F4B044] shadow-[#1F444C]/20 hover:-translate-y-0.5 hover:shadow-xl active:scale-95'">

                                    <svg x-show="isSubmitting" x-cloak class="h-5 w-5 animate-spin" fill="none"
                                        viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z">
                                        </path>
                                    </svg>

                                    <span x-text="isSubmitting ? 'Memproses...' : 'Simpan Transaksi'"></span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
@endsection

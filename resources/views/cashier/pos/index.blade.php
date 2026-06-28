@extends('layouts.master')

@section('page_title', 'POS Kasir')

@section('content')
    <div class="space-y-6" x-data="{
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
                this.persistDraft();
                return;
            }
    
            this.cart.push({
                id: product.id,
                nama_produk: product.nama_produk,
                kode_produk: product.kode_produk,
                harga_jual: Number(product.harga_jual),
                qty: 1
            });
    
            this.persistDraft();
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
        {{-- POS Layout --}}

        {{-- Product Area --}}
        <div class="space-y-6">
            {{-- Filter --}}
            <div class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-5 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                <div class="grid gap-3 min-[1024px]:grid-cols-[minmax(0,1fr)_220px_auto]">
                    <div>
                        <label class="sr-only" for="search">Cari produk</label>

                        <input id="search" type="text" x-model="search" placeholder="Cari nama produk atau kode..."
                            class="block h-12 w-full rounded-2xl border border-[#F4D3B0] bg-[#F7F6F4] px-4 text-sm font-medium text-[#2B1A10] shadow-sm transition placeholder:text-[#6B3E12]/45 focus:border-[#F4B044] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#F4B044]/20">
                    </div>

                    <div class="relative">
                        <button type="button" @click="categoryOpen = !categoryOpen"
                            class="flex h-12 w-full items-center justify-between rounded-2xl border border-[#F4D3B0] bg-[#F7F6F4] px-4 text-left text-sm font-black text-[#2B1A10] shadow-sm transition hover:bg-white focus:border-[#F4B044] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#F4B044]/20">
                            <span class="truncate" x-text="selectedCategoryLabel"></span>

                            <svg class="h-5 w-5 shrink-0 text-[#6B3E12] transition"
                                :class="categoryOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                                stroke-width="2.3" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m19 9-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="categoryOpen" x-cloak @click.outside="categoryOpen = false" x-transition
                            class="absolute left-0 right-0 z-40 mt-2 overflow-hidden rounded-2xl border border-[#F4D3B0] bg-white shadow-xl shadow-[#1F444C]/10">
                            <button type="button" @click="chooseCategory('')"
                                class="block w-full px-4 py-3 text-left text-sm font-black text-[#2B1A10] transition hover:bg-[#F7F6F4]">
                                Semua kategori
                            </button>

                            <template x-for="category in categories" :key="category.id">
                                <button type="button" @click="chooseCategory(category.id)"
                                    class="block w-full px-4 py-3 text-left text-sm font-black text-[#2B1A10] transition hover:bg-[#F7F6F4]">
                                    <span x-text="category.nama_kategori"></span>
                                </button>
                            </template>
                        </div>
                    </div>

                    <button type="button" @click="resetFilter()"
                        class="inline-flex h-12 items-center justify-center rounded-2xl border border-[#F4B044] bg-[#F4B044] px-5 text-sm font-black text-[#2B1A10] shadow-lg shadow-[#F4B044]/30 transition duration-150 hover:-translate-y-0.5 hover:bg-[#f7bd5f] hover:shadow-xl active:translate-y-0 active:scale-95 active:bg-[#d99a32] active:shadow-inner focus:outline-none focus:ring-4 focus:ring-[#F4B044]/35">
                        Reset
                    </button>
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
                <div class="space-y-6 min-[1024px]:sticky min-[1024px]:top-6">
                    <div
                        class="flex max-h-[calc(100dvh-6rem)] min-h-[620px] flex-col overflow-hidden rounded-3xl border border-[#F4D3B0]/70 bg-white shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">

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

                                <span
                                    class="inline-flex rounded-xl bg-[#1F444C]/10 px-3 py-1.5 text-xs font-black leading-none text-[#1F444C]">
                                    <span x-text="cartCount"></span>&nbsp;item
                                </span>
                            </div>
                        </div>

                        {{-- Cart Items Scroll Area --}}
                        <div class="min-h-0 flex-1 overflow-y-auto overscroll-contain">
                            <template x-if="cart.length > 0">
                                <div class="divide-y divide-[#F4D3B0]/60">
                                    <template x-for="item in cart" :key="item.id">
                                        <div class="px-4 py-3">
                                            <div class="flex items-start justify-between gap-3">
                                                <div class="min-w-0 flex-1">
                                                    <p class="truncate text-sm font-black leading-tight text-[#2B1A10]"
                                                        x-text="item.nama_produk"></p>

                                                    <p class="mt-1 text-[11px] font-bold leading-none text-[#6B3E12]"
                                                        x-text="rupiah(item.harga_jual)"></p>
                                                </div>

                                                <button type="button" @click="removeItem(item.id)"
                                                    class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-xl bg-[#A92A35]/10 text-xs font-black text-[#A92A35] transition hover:bg-[#A92A35] hover:text-white">
                                                    ×
                                                </button>
                                            </div>

                                            <div class="mt-3 flex items-center justify-between gap-3">
                                                <div class="inline-flex items-center rounded-2xl bg-[#F7F6F4] p-1">
                                                    <button type="button" @click="decrement(item)"
                                                        class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-white text-sm font-black text-[#6B3E12] shadow-sm transition hover:bg-[#F4D3B0]/40">
                                                        -
                                                    </button>

                                                    <span
                                                        class="inline-flex h-8 min-w-9 items-center justify-center px-2 text-sm font-black text-[#2B1A10]"
                                                        x-text="item.qty"></span>

                                                    <button type="button" @click="increment(item)"
                                                        class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-white text-sm font-black text-[#6B3E12] shadow-sm transition hover:bg-[#F4D3B0]/40">
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
                        <div class="shrink-0 border-t border-[#F4D3B0]/70 bg-[#F7F6F4] p-5">
                            <div class="rounded-3xl bg-[#1F444C] p-4 text-white">
                                <div class="grid grid-cols-[minmax(0,1fr)_auto] items-center gap-3">
                                    <div class="min-w-0">
                                        <p class="text-[11px] font-black uppercase tracking-[0.22em] text-[#F4D3B0]">
                                            Total Bayar
                                        </p>

                                        <p class="mt-1 text-sm font-semibold text-white/70">
                                            <span x-text="cartCount"></span> item
                                        </p>
                                    </div>

                                    <p class="shrink-0 whitespace-nowrap text-right text-xl font-black leading-none text-[#F4B044] min-[1280px]:text-2xl"
                                        x-text="rupiah(grandTotal)">
                                    </p>
                                </div>
                            </div>

                            <div class="mt-4 grid gap-3">
                                <button type="button" @click="openPayment()" :disabled="cart.length === 0"
                                    class="inline-flex h-12 w-full items-center justify-center rounded-2xl px-6 text-sm font-black shadow-lg transition"
                                    :class="cart.length > 0 ?
                                        'bg-[#F4B044] text-[#2B1A10] shadow-[#F4B044]/20 hover:-translate-y-0.5 hover:shadow-xl active:scale-95' :
                                        'cursor-not-allowed bg-[#F4D3B0]/50 text-[#6B3E12]/50 shadow-none'">
                                    Bayar
                                </button>

                                <button type="button" x-show="cart.length > 0" x-cloak @click="clearCart()"
                                    class="inline-flex h-11 w-full items-center justify-center rounded-2xl border border-[#F4D3B0] bg-white px-5 text-sm font-black text-[#6B3E12] transition hover:bg-[#F7F6F4]">
                                    Kosongkan Keranjang
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- Payment Modal --}}
        <template x-teleport="body">
            <div x-show="paymentOpen" x-cloak
                class="fixed inset-0 z-[9998] flex items-center justify-center bg-[#1F444C]/55 px-4 backdrop-blur-md"
                x-transition.opacity @keydown.escape.window="closePayment()">
                <div @click.outside="closePayment()" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-3"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 translate-y-3"
                    class="w-full max-w-2xl overflow-hidden rounded-3xl bg-white shadow-2xl shadow-[#1F444C]/25">

                    {{-- Header --}}
                    <div class="bg-[#1F444C] px-5 py-4 text-white">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-xs font-black uppercase tracking-[0.22em] text-[#F4D3B0]">
                                    Pembayaran
                                </p>

                                <h2 class="mt-1 text-xl font-black">
                                    Ringkasan Bayar
                                </h2>
                            </div>

                            <button type="button" @click="closePayment()"
                                class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-white/10 text-xl font-black text-white transition hover:bg-white/15">
                                ×
                            </button>
                        </div>
                    </div>

                    {{-- Body --}}
                    <div class="p-5">
                        {{-- Summary --}}
                        <div class="grid gap-3 sm:grid-cols-2">
                            <div class="rounded-2xl bg-[#F7F6F4] p-4">
                                <div class="flex items-center justify-between gap-3">
                                    <p class="text-sm font-bold text-[#6B3E12]">
                                        Subtotal
                                    </p>

                                    <p class="shrink-0 whitespace-nowrap text-sm font-black text-[#2B1A10]"
                                        x-text="rupiah(subtotal)">
                                    </p>
                                </div>
                            </div>

                            <div class="rounded-2xl bg-[#F7F6F4] p-4">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="text-sm font-bold text-[#6B3E12]">
                                            Diskon Promo
                                        </p>

                                        <p class="mt-1 truncate text-xs font-semibold text-[#6B3E12]/70"
                                            x-text="bestPromo ? bestPromo.nama_promo : 'Tidak ada promo'">
                                        </p>
                                    </div>

                                    <p class="shrink-0 whitespace-nowrap text-sm font-black text-[#A92A35]">
                                        - <span x-text="rupiah(totalDiscount)"></span>
                                    </p>
                                </div>
                            </div>

                            <div class="rounded-2xl bg-[#F7F6F4] p-4">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="text-sm font-bold text-[#6B3E12]">
                                            Pajak
                                        </p>

                                        <p class="mt-1 truncate text-xs font-semibold text-[#6B3E12]/70"
                                            x-text="activeTax ? `${activeTax.nama_pajak} (${activeTax.persentase}%)` : 'Tidak ada pajak aktif'">
                                        </p>
                                    </div>

                                    <p class="shrink-0 whitespace-nowrap text-sm font-black text-[#2B1A10]"
                                        x-text="rupiah(taxTotal)">
                                    </p>
                                </div>
                            </div>

                            <div class="rounded-2xl bg-[#1F444C] p-4 text-white">
                                <div class="flex items-center justify-between gap-3">
                                    <p class="text-sm font-black">
                                        Total Bayar
                                    </p>

                                    <p class="shrink-0 whitespace-nowrap text-xl font-black text-[#F4B044]"
                                        x-text="rupiah(grandTotal)">
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Payment Method --}}
                        <div class="mt-4 grid gap-4 min-[640px]:grid-cols-[minmax(0,1fr)_minmax(0,1fr)]">
                            <div>
                                <label class="mb-2 block text-sm font-black text-[#2B1A10]">
                                    Metode Pembayaran
                                </label>

                                <div class="grid grid-cols-3 gap-2">
                                    <button type="button" @click="paymentMethod = 'tunai'; persistDraft()"
                                        class="h-11 rounded-2xl text-xs font-black transition"
                                        :class="paymentMethod === 'tunai'
                                            ?
                                            'bg-[#1F444C] text-white' :
                                            'bg-[#F7F6F4] text-[#6B3E12]'">
                                        Tunai
                                    </button>

                                    <button type="button"
                                        @click="paymentMethod = 'qris'; paidAmount = ''; persistDraft()"
                                        class="h-11 rounded-2xl text-xs font-black transition"
                                        :class="paymentMethod === 'qris'
                                            ?
                                            'bg-[#1F444C] text-white' :
                                            'bg-[#F7F6F4] text-[#6B3E12]'">
                                        QRIS
                                    </button>

                                    <button type="button"
                                        @click="paymentMethod = 'transfer'; paidAmount = ''; persistDraft()"
                                        class="h-11 rounded-2xl text-xs font-black transition"
                                        :class="paymentMethod === 'transfer'
                                            ?
                                            'bg-[#1F444C] text-white' :
                                            'bg-[#F7F6F4] text-[#6B3E12]'">
                                        Transfer
                                    </button>
                                </div>
                            </div>

                            {{-- Tunai --}}
                            <div x-show="paymentMethod === 'tunai'" x-cloak>
                                <label class="mb-2 block text-sm font-black text-[#2B1A10]">
                                    Uang Diterima
                                </label>

                                <div class="relative">
                                    <span
                                        class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-sm font-black text-[#6B3E12]">
                                        Rp
                                    </span>

                                    <input type="text" x-model="paidAmount" @input="formatPaidAmount($event)"
                                        placeholder="0"
                                        class="block h-11 w-full rounded-2xl border border-[#F4D3B0] bg-[#F7F6F4] py-0 pl-12 pr-4 text-sm font-medium text-[#2B1A10] shadow-sm transition placeholder:text-[#6B3E12]/45 focus:border-[#F4B044] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#F4B044]/20">
                                </div>
                            </div>

                            <div x-show="paymentMethod !== 'tunai'" x-cloak class="rounded-2xl bg-[#F7F6F4] p-4">
                                <p class="text-xs font-black uppercase tracking-[0.18em] text-[#6B3E12]">
                                    Pembayaran Non Tunai
                                </p>

                                <p class="mt-2 text-sm font-semibold text-[#6B3E12]">
                                    Nominal dianggap sesuai total bayar.
                                </p>
                            </div>
                        </div>

                        {{-- Cash Summary --}}
                        <div x-show="paymentMethod === 'tunai'" x-cloak class="mt-4 grid gap-3 sm:grid-cols-2">
                            <div class="rounded-2xl bg-[#F7F6F4] p-4">
                                <p class="text-xs font-black uppercase tracking-[0.18em] text-[#6B3E12]">
                                    Uang Diterima
                                </p>

                                <p class="mt-2 whitespace-nowrap text-base font-black text-[#2B1A10]">
                                    Rp <span x-text="paidAmount || '0'"></span>
                                </p>
                            </div>

                            <div class="rounded-2xl bg-[#F7F6F4] p-4">
                                <p class="text-xs font-black uppercase tracking-[0.18em] text-[#6B3E12]">
                                    Kembalian
                                </p>

                                <p class="mt-2 whitespace-nowrap text-base font-black text-[#1F444C]"
                                    x-text="rupiah(changeAmount)">
                                </p>
                            </div>
                        </div>

                        {{-- Action --}}
                        <div class="mt-4 grid gap-3 border-t border-[#F4D3B0]/70 pt-4 sm:grid-cols-2">
                            <button type="button" @click="closePayment()"
                                class="inline-flex h-12 items-center justify-center rounded-2xl border border-[#F4D3B0] bg-white px-5 text-sm font-black text-[#6B3E12] transition hover:bg-[#F7F6F4]">
                                Batal
                            </button>

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
                                    class="inline-flex h-12 w-full items-center justify-center gap-2 rounded-2xl px-5 text-sm font-black shadow-lg transition"
                                    :class="(!canPay || isSubmitting) ?
                                    'cursor-not-allowed bg-[#F4D3B0]/50 text-[#6B3E12]/50 shadow-none' :
                                    'bg-[#F4B044] text-[#2B1A10] shadow-[#F4B044]/20 hover:-translate-y-0.5 hover:shadow-xl active:scale-95'">

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

@extends('layouts.master')

@section('page_title', 'Manajemen Kasir')

@section('content')
    <div class="space-y-6" x-data="{
    
        resetModalOpen: false,
        resetAction: '',
        resetCashierName: '',
    
    
        openResetModal(action, name) {
            this.resetAction = action;
            this.resetCashierName = name;
            this.resetModalOpen = true;
        },
    
        closeResetModal() {
            this.resetModalOpen = false;
            this.resetAction = '';
            this.resetCashierName = '';
        }
    }" @keydown.escape.window="closeStatusModal(); closeResetModal();">
        {{-- Header --}}
        <div class="overflow-hidden rounded-3xl bg-[#1F444C] p-6 text-white shadow-lg shadow-[#1F444C]/10">
            <div class="relative">
                <div class="absolute -right-10 -top-12 h-32 w-32 rounded-full bg-[#F4B044]/20"></div>
                <div class="absolute -bottom-16 -left-12 h-36 w-36 rounded-full bg-white/10"></div>

                <div
                    class="relative flex flex-col gap-5
           min-[835px]:flex-row
           min-[835px]:items-center
           min-[835px]:justify-between">
                    <div class="min-w-0">
                        <p class="text-xs font-bold uppercase tracking-[0.28em] text-[#F4D3B0]">
                            Admin / Manajemen Kasir
                        </p>

                        <h1 class="mt-2 text-2xl font-black tracking-tight">
                            Manajemen Kasir
                        </h1>

                        <p class="mt-2 max-w-2xl text-sm font-medium leading-relaxed text-[#F7F6F4]/80">
                            Kelola akun kasir aktif. Admin dapat menambah, mengedit, dan reset password kasir aktif.
                        </p>
                    </div>

                    <a href="{{ route('admin.cashiers.create') }}"
                        class="inline-flex items-center justify-center gap-2 rounded-2xl bg-[#F4B044] px-5 py-3 text-sm font-black text-[#2B1A10] shadow-lg shadow-[#F4B044]/20 transition hover:-translate-y-0.5 hover:shadow-xl min-[835px]:shrink-0">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m7-7H5" />
                        </svg>
                        Tambah Kasir
                    </a>
                </div>
            </div>
        </div>

        {{-- Filter --}}
        <div class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-5 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
            <form method="GET" action="{{ route('admin.cashiers.index') }}"
                class="grid gap-3
           min-[835px]:grid-cols-[minmax(0,1fr)_auto]
           min-[835px]:items-center">

                {{-- Search --}}
                <div class="relative min-w-0">
                    <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-[#6B3E12]/60">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21 21l-4.35-4.35m1.35-5.65a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>

                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari nama atau email kasir..."
                        class="block h-12 w-full rounded-2xl border border-[#F4D3B0] bg-[#F7F6F4] py-0 pl-12 pr-4 text-sm font-medium text-[#2B1A10] shadow-sm transition placeholder:text-[#6B3E12]/45 focus:border-[#F4B044] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#F4B044]/20">
                </div>

                {{-- Action --}}
                <div class="flex shrink-0 gap-3 min-[835px]:justify-end">
                    <button type="submit"
                        class="inline-flex h-12 shrink-0 items-center justify-center gap-2 rounded-2xl bg-[#1F444C] px-5 py-0 text-sm font-black text-white shadow-lg shadow-[#1F444C]/20 transition hover:-translate-y-0.5 hover:shadow-xl">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.3" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21 21l-4.35-4.35m1.35-5.65a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Cari
                    </button>

                    <a href="{{ route('admin.cashiers.index') }}"
                        class="inline-flex h-12 shrink-0 items-center justify-center gap-2 rounded-2xl border border-[#F4D3B0] bg-white px-5 py-0 text-sm font-black text-[#6B3E12] transition hover:bg-[#F7F6F4]">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.3" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0013.803-3.7M7.977 14.652H2.985m18.03-5.304-3.181-3.183a8.25 8.25 0 00-13.803 3.7" />
                        </svg>
                        Reset
                    </a>
                </div>
            </form>
        </div>

        {{-- Table --}}
        <div
            class="overflow-hidden rounded-3xl border border-[#F4D3B0]/70 bg-white shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-[#F4D3B0]/70">
                    <thead class="bg-[#F7F6F4]">
                        <tr>
                            <th class="w-16 px-6 py-4 text-left">
                                <span class="text-[11px] font-black uppercase tracking-[0.22em] text-[#6B3E12]/80">
                                    No
                                </span>
                            </th>

                            <th class="px-6 py-4 text-left">
                                <span
                                    class="whitespace-nowrap text-[11px] font-black uppercase tracking-[0.22em] text-[#6B3E12]/80">
                                    Kasir
                                </span>
                            </th>

                            <th class="px-6 py-4 text-left">
                                <span
                                    class="whitespace-nowrap text-[11px] font-black uppercase tracking-[0.22em] text-[#6B3E12]/80">
                                    Email
                                </span>
                            </th>

                            <th class="px-2 py-4 text-center">
                                <span
                                    class="whitespace-nowrap text-[11px] font-black uppercase tracking-[0.22em] text-[#6B3E12]/80">
                                    Status
                                </span>
                            </th>

                            <th class="px-6 py-4 text-left">
                                <span
                                    class="whitespace-nowrap text-[11px] font-black uppercase tracking-[0.22em] text-[#6B3E12]/80">
                                    Dibuat
                                </span>
                            </th>

                            <th class="w-[160px] px-6 py-4 text-right">
                                <span
                                    class="whitespace-nowrap text-[11px] font-black uppercase tracking-[0.22em] text-[#6B3E12]/80">
                                    Aksi
                                </span>
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-[#F4D3B0]/60 bg-white">
                        @forelse ($cashiers as $cashier)
                            @php
                                $statusClass =
                                    $cashier->status === 'aktif'
                                        ? 'bg-[#1F444C]/10 text-[#1F444C]'
                                        : 'bg-[#A92A35]/10 text-[#A92A35]';
                            @endphp

                            <tr class="transition hover:bg-[#F7F6F4]/70">
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-black text-[#6B3E12]">
                                    {{ $cashiers->firstItem() + $loop->index }}
                                </td>

                                <td class="whitespace-nowrap px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-[#1F444C] text-sm font-black text-[#F4B044] shadow-sm">
                                            {{ strtoupper(substr($cashier->name, 0, 1)) }}
                                        </div>

                                        <div>
                                            <p class="whitespace-nowrap font-black text-[#2B1A10]">
                                                {{ $cashier->name }}
                                            </p>

                                            <p class="mt-0.5 whitespace-nowrap text-sm font-medium text-[#6B3E12]">
                                                Role Kasir
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <td class="whitespace-nowrap px-6 py-4 text-sm font-bold text-[#6B3E12]">
                                    {{ $cashier->email }}
                                </td>

                                <td class="px-2 py-4">
                                    <div class="flex items-center justify-center">
                                        <span
                                            class="inline-flex w-fit justify-center whitespace-nowrap rounded-full px-3 py-1 text-center text-xs font-black {{ $statusClass }}">
                                            {{ ucfirst($cashier->status) }}
                                        </span>
                                    </div>
                                </td>

                                <td class="whitespace-nowrap px-6 py-4">
                                    <p class="text-sm font-bold text-[#2B1A10]">
                                        {{ $cashier->created_at->format('d M Y') }}
                                    </p>

                                    <p class="mt-0.5 text-sm font-medium text-[#6B3E12]">
                                        {{ $cashier->created_at->format('H:i') }}
                                    </p>
                                </td>

                                <td class="w-[170px] px-2 py-4">
                                    <div class="flex flex-nowrap items-center justify-end gap-2 whitespace-nowrap">
                                        <a href="{{ route('admin.cashiers.show', $cashier) }}"
                                            class="inline-flex items-center justify-center rounded-xl bg-[#F4B044] px-3 py-2 text-xs font-black text-[#2B1A10] shadow-sm shadow-[#F4B044]/25 transition hover:-translate-y-0.5 hover:bg-[#E7A33D] hover:shadow-md active:scale-95">
                                            Detail
                                        </a>

                                        <a href="{{ route('admin.cashiers.edit', $cashier) }}"
                                            class="inline-flex items-center justify-center rounded-xl bg-[#1F444C] px-3 py-2 text-xs font-black text-white shadow-sm shadow-[#1F444C]/25 transition hover:-translate-y-0.5 hover:bg-[#183941] hover:shadow-md active:scale-95">
                                            Edit
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-14 text-center">
                                    <div class="mx-auto flex max-w-sm flex-col items-center">
                                        <div
                                            class="flex h-16 w-16 items-center justify-center rounded-3xl bg-[#F4B044]/20 text-[#6B3E12]">
                                            <svg class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="2"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.25a8.25 8.25 0 0115 0" />
                                            </svg>
                                        </div>

                                        <h3 class="mt-4 text-lg font-black text-[#2B1A10]">
                                            Belum ada kasir
                                        </h3>

                                        <p class="mt-1 text-sm text-[#6B3E12]">
                                            Tambahkan akun kasir pertama untuk operasional transaksi POS.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($cashiers->hasPages())
                <div class="border-t border-[#F4D3B0]/70 bg-[#F7F6F4] px-6 py-4">
                    {{ $cashiers->links() }}
                </div>
            @endif
        </div>

        {{-- Reset Password Modal --}}
        <template x-teleport="body">
            <div x-show="resetModalOpen" x-cloak x-transition.opacity
                class="fixed inset-0 z-[9998] flex items-center justify-center bg-[#1F444C]/55 px-4 backdrop-blur-md">
                <div @click.outside="closeResetModal()" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    class="w-full max-w-md overflow-hidden rounded-3xl border border-[#F4D3B0]/70 bg-white shadow-[0_30px_90px_-35px_rgba(31,68,76,0.8)]">

                    <div class="bg-[#6B3E12] px-6 py-5 text-white">
                        <h2 class="text-lg font-black">
                            Reset Password Kasir?
                        </h2>
                        <p class="mt-1 text-sm font-medium text-white/80">
                            Password akan dikembalikan ke password default.
                        </p>
                    </div>

                    <div class="p-6">
                        <p class="text-sm font-semibold leading-relaxed text-[#6B3E12]">
                            Password kasir <span class="font-black text-[#2B1A10]" x-text="resetCashierName"></span>
                            akan direset menjadi:
                        </p>

                        <div class="mt-4 rounded-2xl bg-[#F7F6F4] px-4 py-3">
                            <code class="text-sm font-black text-[#2B1A10]">rotimaroshikmah111</code>
                        </div>

                        <div class="mt-6 flex justify-end gap-3">
                            <button type="button" @click="closeResetModal()"
                                class="inline-flex h-11 items-center justify-center rounded-2xl border border-[#F4D3B0] bg-white px-5 text-sm font-black text-[#6B3E12] transition hover:bg-[#F7F6F4]">
                                Batal
                            </button>

                            <form method="POST" :action="resetAction">
                                @csrf
                                @method('PATCH')

                                <button type="submit"
                                    class="inline-flex h-11 items-center justify-center rounded-2xl bg-[#6B3E12] px-5 text-sm font-black text-white shadow-lg shadow-[#6B3E12]/20 transition hover:-translate-y-0.5 hover:shadow-xl">
                                    Ya, Reset
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
@endsection

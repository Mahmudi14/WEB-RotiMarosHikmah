@extends('layouts.master')

@section('page_title', 'Tambah Promo')

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
                            Admin / Promo / Tambah
                        </p>
                        <h1 class="mt-2 text-2xl font-black tracking-tight">
                            Tambah Promo
                        </h1>
                        <p class="mt-2 max-w-2xl text-sm font-medium leading-relaxed text-[#F7F6F4]/80">
                            Buat promo baru untuk semua menu atau hanya produk tertentu.
                        </p>
                    </div>

                    <a href="{{ route('admin.promos.index') }}"
                        class="inline-flex items-center justify-center gap-2 rounded-2xl border border-white/15 bg-white/10 px-5 py-3 text-sm font-black text-white transition hover:bg-white/15">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                        </svg>
                        Kembali
                    </a>
                </div>
            </div>
        </div>

        @include('admin.promos._form', [
            'action' => route('admin.promos.store'),
            'method' => 'POST',
            'submitLabel' => 'Simpan Promo',
            'showStatus' => false,
        ])
    </div>
@endsection
